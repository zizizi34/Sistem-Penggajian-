<?php

namespace App\Services;

use App\Models\Pegawai;
use App\Models\Penggajian;
use App\Models\Absensi;
use App\Models\Lembur;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

/**
 * Service untuk menghitung gaji bulanan pegawai
 * 
 * Alur Perhitungan Gaji:
 * 1. Validasi data pegawai dan periode gaji
 * 2. Hitung absensi (kehadiran, keterlambatan, alpha)
 * 3. Hitung tunjangan yang diterimakan
 * 4. Hitung lembur (overtime)
 * 5. Hitung potongan (asuransi, zakat, denda, dll)
 * 6. Hitung Pajak Penghasilan (PPh 21) berdasarkan PTKP
 * 7. Hitung gaji bersih (Gaji Pokok - Potongan + Tunjangan + Lembur - Pajak)
 */
class SalaryCalculationService
{
    /**
     * Hitung gaji bulanan pegawai
     * 
     * @param Pegawai $pegawai
     * @param string $periode (format: YYYY-MM)
     * @return array
     */
    public function calculateMonthlySalary(Pegawai $pegawai, $periode)
    {
        try {
            // Validasi periode
            if (!$this->isValidPeriode($periode)) {
                throw new \Exception('Format periode tidak valid. Gunakan format YYYY-MM');
            }

            // Ambil tanggal awal dan akhir bulan
            $startDate = Carbon::createFromFormat('Y-m', $periode)->startOfMonth();
            $endDate = Carbon::createFromFormat('Y-m', $periode)->endOfMonth();

            // 1. Hitung Absensi
            $absenceData = $this->calculateAbsence($pegawai, $startDate, $endDate);

            // 2. Hitung Tunjangan
            $totalAllowance = $this->calculateAllowances($pegawai);

            // 3. Hitung Lembur
            $overtimeData = $this->calculateOvertime($pegawai, $startDate, $endDate);

            // 4. Hitung Potongan (selain PPh)
            $totalDeduction = $this->calculateDeductions($pegawai);

            // 5. Hitung Gaji Bruto (sebelum pajak)
            $baseSalary = $pegawai->gaji_pokok;
            $grossSalary = $baseSalary + $totalAllowance + $overtimeData['nominal'] - $absenceData['deduction'];

            // 6. Hitung Pajak PPh 21
            $taxData = $this->calculateIncomeTax($pegawai, $grossSalary);

            // 7. Hitung Gaji Bersih
            $netSalary = $grossSalary - $totalDeduction - $taxData['pph21'];

            // Return hasil perhitungan
            return [
                'status' => 'success',
                'pegawai_id' => $pegawai->id_pegawai,
                'periode' => $periode,
                'gaji_pokok' => $baseSalary,
                'absensi' => $absenceData,
                'tunjangan' => [
                    'total' => $totalAllowance,
                    'detail' => $this->getDetailAllowances($pegawai)
                ],
                'lembur' => $overtimeData,
                'potongan' => [
                    'non_pajak' => $totalDeduction,
                    'detail' => $this->getDetailDeductions($pegawai)
                ],
                'bruto' => $grossSalary,
                'pajak_pph21' => $taxData['pph21'],
                'ptkp_status' => $taxData['ptkp_info'],
                'gaji_bersih' => $netSalary,
                'keterangan' => [
                    'hari_kerja_seharusnya' => $absenceData['working_days'],
                    'hari_hadir' => $absenceData['present'],
                    'hari_alpha' => $absenceData['alpha'],
                    'hari_izin' => $absenceData['leave'],
                    'hari_sakit' => $absenceData['sick'],
                ]
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Hitung Absensi
     * 
     * Logika:
     * - Hari kerja: Senin-Jumat (5 hari kerja)
     * - Hari hadir: Pegawai masuk pada waktunya
     * - Keterlambatan: Jika jam masuk melebihi jam standar (misal: 08:00)
     * - Alpha: Tidak absen, tidak ada keterangan
     * - Izin: Ada keterangan izin
     * - Sakit: Ada keterangan sakit
     * - Deduction: Jika alpha atau keterlambatan, gaji dikurangi
     */
    private function calculateAbsence(Pegawai $pegawai, $startDate, $endDate)
    {
        // Hitung total hari kerja (Senin-Jumat saja)
        $workingDays = $this->countWorkingDays($startDate, $endDate);

        // Ambil data absensi dari database
        $absences = Absensi::where('id_pegawai', $pegawai->id_pegawai)
            ->whereBetween('tanggal_absensi', [$startDate, $endDate])
            ->get();

        $present = 0;
        $alpha = 0;
        $leave = 0;
        $sick = 0;
        $lateCount = 0;
        $standardTime = '08:00:00'; // Jam standar masuk
        $deduction = 0;

        foreach ($absences as $absence) {
            if ($absence->status === 'hadir') {
                // Cek keterlambatan (jika jam masuk > 08:00)
                if ($absence->jam_masuk && strtotime($absence->jam_masuk) > strtotime($standardTime)) {
                    $lateCount++;
                    // Potongan untuk keterlambatan: Rp. 50.000 per kali
                    $deduction += 50000;
                } else {
                    $present++;
                }
            } elseif ($absence->status === 'izin') {
                $leave++;
            } elseif ($absence->status === 'sakit') {
                $sick++;
            } elseif ($absence->status === 'alpha') {
                $alpha++;
                // Potongan untuk alpha: Rp. 100.000 per hari
                $deduction += 100000;
            }
        }

        return [
            'working_days' => $workingDays,
            'present' => $present,
            'alpha' => $alpha,
            'leave' => $leave,
            'sick' => $sick,
            'late' => $lateCount,
            'deduction' => $deduction
        ];
    }

    /**
     * Hitung Tunjangan
     * 
     * Tunjangan diterima pegawai berdasarkan:
     * - Tunjangan tetap (tunjangan fungsi, tunjangan transport, dll)
     * - Disesuaikan per pegawai melalui tabel pegawai_tunjangan
     */
    private function calculateAllowances(Pegawai $pegawai)
    {
        // Ambil semua tunjangan yang diberikan ke pegawai ini
        $allowances = $pegawai->tunjangans()->get();

        $total = 0;
        foreach ($allowances as $allowance) {
            $total += $allowance->nominal;
        }

        return $total;
    }

    /**
     * Get detail tunjangan
     */
    private function getDetailAllowances(Pegawai $pegawai)
    {
        $allowances = $pegawai->tunjangans()->get();
        $detail = [];

        foreach ($allowances as $allowance) {
            $detail[] = [
                'nama' => $allowance->nama_tunjangan,
                'nominal' => $allowance->nominal
            ];
        }

        return $detail;
    }

    /**
     * Hitung Lembur (Overtime)
     * 
     * Logika:
     * - Hitung durasi lembur per hari
     * - Rate lembur: 1 jam pertama = 1.5x upah per jam
     * - Jam berikutnya = 2x upah per jam
     * - Upah per jam = Gaji Pokok / 173 (asumsi jam kerja per bulan)
     */
    private function calculateOvertime(Pegawai $pegawai, $startDate, $endDate)
    {
        // Ambil data lembur dari database
        $overtimes = Lembur::where('id_pegawai', $pegawai->id_pegawai)
            ->whereBetween('tanggal_lembur', [$startDate, $endDate])
            ->get();

        $totalOvertimeNominal = 0;
        $totalOvertimeHours = 0;
        $details = [];

        // Upah per jam = Gaji Pokok / 173
        $hourlyRate = $pegawai->gaji_pokok / 173;

        foreach ($overtimes as $overtime) {
            // Hitung durasi dalam jam (durasi dalam format HH:MM)
            $durationHours = $this->convertDurationToHours($overtime->durasi);

            $overtimeNominal = 0;

            // Jam pertama: 1.5x
            if ($durationHours <= 1) {
                $overtimeNominal = $durationHours * $hourlyRate * 1.5;
            } else {
                // Jam pertama: 1.5x, sisanya 2x
                $overtimeNominal = (1 * $hourlyRate * 1.5) + (($durationHours - 1) * $hourlyRate * 2);
            }

            $totalOvertimeNominal += $overtimeNominal;
            $totalOvertimeHours += $durationHours;

            $details[] = [
                'tanggal' => $overtime->tanggal_lembur,
                'durasi' => $overtime->durasi,
                'durasi_jam' => $durationHours,
                'nominal' => round($overtimeNominal, 2)
            ];
        }

        return [
            'total_jam' => round($totalOvertimeHours, 2),
            'nominal' => round($totalOvertimeNominal, 2),
            'detail' => $details
        ];
    }

    /**
     * Hitung Potongan (Deduction)
     * 
     * Potongan - potongan pegawai:
     * - Asuransi kesehatan
     * - Zakat / iuran keagamaan
     * - Denda / penalti
     * - Cicilan hutang koperasi
     * - Dll
     */
    private function calculateDeductions(Pegawai $pegawai)
    {
        // Ambil semua potongan yang diberikan ke pegawai ini
        $deductions = $pegawai->potongans()->get();

        $total = 0;
        foreach ($deductions as $deduction) {
            $total += $deduction->nominal;
        }

        return $total;
    }

    /**
     * Get detail potongan
     */
    private function getDetailDeductions(Pegawai $pegawai)
    {
        $deductions = $pegawai->potongans()->get();
        $detail = [];

        foreach ($deductions as $deduction) {
            $detail[] = [
                'nama' => $deduction->nama_potongan,
                'nominal' => $deduction->nominal
            ];
        }

        return $detail;
    }

    /**
     * Hitung Pajak Penghasilan (PPh 21)
     * 
     * Rumus PPh 21:
     * Pajak Penghasilan = (Gaji Bruto - PTKP) x Tarif Pajak
     * 
     * Tarif Pajak:
     * - 0% untuk penghasilan sampai Rp. 60 juta per tahun
     * - 5% untuk penghasilan di atas Rp. 60 juta - Rp. 250 juta per tahun
     * - 15% untuk penghasilan di atas Rp. 250 juta - Rp. 500 juta per tahun
     * - 25% untuk penghasilan di atas Rp. 500 juta - Rp. 5 miliar per tahun
     * - 30% untuk penghasilan di atas Rp. 5 miliar per tahun
     * 
     * PTKP (Penghasilan Tidak Kena Pajak) per tahun:
     * - TK/0: Rp. 54.000.000 (lajang, tanpa tanggungan)
     * - K/0: Rp. 58.500.000 (kawin, tanpa tanggungan)
     * - K/1: Rp. 63.000.000 (kawin, 1 tanggungan)
     * - K/3: Rp. 70.500.000 (kawin, 3 tanggungan)
     * dll
     */
    private function calculateIncomeTax(Pegawai $pegawai, $grossSalary)
    {
        // Ambil PTKP status pegawai
        $ptkpStatus = $pegawai->ptkpStatus;

        // PTKP per tahun
        $ptkpPerYear = $ptkpStatus->nominal ?? 54000000;

        // Gaji bruto per tahun (asumsi 12 bulan)
        $annualGrossSalary = $grossSalary * 12;

        // Penghasilan Kena Pajak (PKP) per tahun
        $pkpPerYear = max(0, $annualGrossSalary - $ptkpPerYear);

        // Hitung PPh 21 per tahun dengan tarif progresif
        $annualTax = $this->calculateProgressiveTax($pkpPerYear);

        // PPh 21 per bulan
        $monthlyTax = round($annualTax / 12, 2);

        return [
            'ptkp_info' => [
                'kode' => $ptkpStatus->kode_ptkp_status,
                'deskripsi' => $ptkpStatus->deskripsi,
                'nominal_per_tahun' => $ptkpPerYear
            ],
            'pkp_per_tahun' => $pkpPerYear,
            'pajak_per_tahun' => $annualTax,
            'pph21' => $monthlyTax
        ];
    }

    /**
     * Hitung pajak dengan tarif progresif
     */
    private function calculateProgressiveTax($pkp)
    {
        $tax = 0;

        // Tarif pajak progresif PPh 21 (per tahun 2024)
        if ($pkp <= 60000000) {
            $tax = $pkp * 0.05;
        } elseif ($pkp <= 250000000) {
            $tax = (60000000 * 0.05) + (($pkp - 60000000) * 0.15);
        } elseif ($pkp <= 500000000) {
            $tax = (60000000 * 0.05) + ((250000000 - 60000000) * 0.15) + (($pkp - 250000000) * 0.25);
        } elseif ($pkp <= 5000000000) {
            $tax = (60000000 * 0.05) + ((250000000 - 60000000) * 0.15) + ((500000000 - 250000000) * 0.25) + (($pkp - 500000000) * 0.30);
        } else {
            $tax = (60000000 * 0.05) + ((250000000 - 60000000) * 0.15) + ((500000000 - 250000000) * 0.25) + ((5000000000 - 500000000) * 0.30) + (($pkp - 5000000000) * 0.35);
        }

        return $tax;
    }

    /**
     * Hitung jumlah hari kerja (Senin-Jumat)
     */
    private function countWorkingDays($startDate, $endDate)
    {
        $workingDays = 0;
        $period = CarbonPeriod::create($startDate, $endDate);

        foreach ($period as $date) {
            // 1 = Senin, 5 = Jumat
            if ($date->dayOfWeek >= 1 && $date->dayOfWeek <= 5) {
                $workingDays++;
            }
        }

        return $workingDays;
    }

    /**
     * Konversi durasi (HH:MM) ke jam (decimal)
     */
    private function convertDurationToHours($duration)
    {
        if (empty($duration)) {
            return 0;
        }

        // Asumsi format duration: "02:30" (2 jam 30 menit)
        $parts = explode(':', $duration);
        $hours = (int)$parts[0];
        $minutes = isset($parts[1]) ? (int)$parts[1] : 0;

        return $hours + ($minutes / 60);
    }

    /**
     * Validasi format periode (YYYY-MM)
     */
    private function isValidPeriode($periode)
    {
        return preg_match('/^\d{4}-\d{2}$/', $periode);
    }

    /**
     * Simpan hasil perhitungan ke database
     */
    public function saveSalaryCalculation($pegawai, $periode, $calculationResult)
    {
        if ($calculationResult['status'] !== 'success') {
            return false;
        }

        try {
            Penggajian::create([
                'id_pegawai' => $calculationResult['pegawai_id'],
                'periode' => $periode,
                'gaji_pokok' => $calculationResult['gaji_pokok'],
                'total_tunjangan' => $calculationResult['tunjangan']['total'],
                'total_potongan' => $calculationResult['potongan']['non_pajak'],
                'lembur' => $calculationResult['lembur']['nominal'],
                'pajak_pph21' => $calculationResult['pajak_pph21'],
                'gaji_bersih' => $calculationResult['gaji_bersih'],
                'status' => 'draft'
            ]);

            return true;
        } catch (\Exception $e) {
            throw new \Exception('Gagal menyimpan data gaji: ' . $e->getMessage());
        }
    }
}
