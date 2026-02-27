# SISTEM PENGGAJIAN PRODUCTION-READY
## PT Digital Solution - Perancangan Final Sistem HR Management & Payroll

**Versi**: 2.0  
**Status**: Production Ready  
**Last Updated**: February 2026  
**Disusun untuk**: Full Implementation & Deployment

---

## 📋 DAFTAR ISI

1. [Overview Sistem](#overview-sistem)
2. [Struktur Pembagian Hak Akses (RBAC)](#struktur-pembagian-hak-akses)
3. [Detail Role & Dashboard](#detail-role--dashboard)
4. [Permission Matrix](#permission-matrix)
5. [Struktur Menu Sidebar](#struktur-menu-sidebar)
6. [Alur Kerja Penggajian](#alur-kerja-penggajian)
7. [Implementasi Technical](#implementasi-technical)
8. [Best Practice Production-Ready](#best-practice-production-ready)

---

## 🎯 OVERVIEW SISTEM

### Tujuan Sistem
Membangun sistem penggajian terintegrasi yang:
- ✅ Aman dengan kontrol akses berbasis role yang ketat
- ✅ Efisien dengan automation workflow approval
- ✅ Transparan dengan audit trail lengkap setiap transaksi
- ✅ Compliance dengan regulasi PP 21 dan standar HR

### Arsitektur Sistem
```
┌─────────────────────────────────────────┐
│         USER LOGIN (Multiple Guards)     │
├─────────────────────────────────────────┤
│     Administrator | Officer | Student    │
├─────────────────────────────────────────┤
│              ROLE ASSIGNMENT             │
├─────────────────────────────────────────┤
│  SUPER ADMIN | PETUGAS | PEGAWAI        │
├─────────────────────────────────────────┤
│        PERMISSION VALIDATION            │
├─────────────────────────────────────────┤
│   Dashboard → Menu Navigation           │
│   Resource Access Control               │
│   Data Visibility Filter                │
└─────────────────────────────────────────┘
```

---

## 🔐 STRUKTUR PEMBAGIAN HAK AKSES (RBAC)

### 3 ROLE UTAMA

#### 1. 👑 SUPER ADMIN
**Peran**: Administrator Sistem Penuh  
**Guard**: `administrator`  
**Contoh User**: HR Manager, Direktur HR  
**Status**: Full Access All Features

**Hak Akses Umum**:
- ✅ Akses SEMUA menu dan fitur tanpa batasan
- ✅ CRUD (Create, Read, Update, Delete) untuk semua data
- ✅ Approval semua proses (absensi, lembur, gaji)
- ✅ Setup & maintenance system
- ✅ View semua laporan dan analytics
- ✅ Manage user dan role assignment
- ✅ Audit trail dan activity log

---

#### 2. 👨💼 PETUGAS (Officer)
**Peran**: Department Officer / Manager  
**Guard**: `officer`  
**Contoh User**: Kepala Departemen, Team Lead, HR Officer Bagian  
**Status**: Limited Access per Department

**Hak Akses Umum**:
- ✅ Akses data HANYA departemen sendiri
- ✅ Input & kelola absensi pegawai timnya
- ✅ Input & kelola lembur pegawai timnya
- ✅ Lihat rincian gaji pegawai timnya
- ✅ Approve absensi & lembur pegawai timnya (TIDAK Approve gaji)
- ✅ Reject & beri catatan untuk data yang salah
- ❌ TIDAK bisa edit perhitungan gaji
- ❌ TIDAK bisa akses departemen lain
- ❌ TIDAK bisa manage tunjangan/potongan global

**Batasan Spesifik**:
- Data Visibility: Hanya departemen sendiri
- Pegawai Visibility: Hanya pegawai di departemen sendiri
- Approval Authority: Absensi & Lembur saja
- Edit Rights: Input data, NOT calculation results

---

#### 3. 👤 PEGAWAI (Employee)
**Peran**: Employee / Staff Individual  
**Guard**: `student` (atau `employee`)  
**Contoh User**: Staff, Officer, Operator  
**Status**: Self-Service Limited

**Hak Akses Umum**:
- ✅ Lihat data pribadi sendiri saja
- ✅ Lihat riwayat absensi pribadi dengan detail
- ✅ Lihat riwayat lembur pribadi dengan approval status
- ✅ Lihat slip gaji pribadi yang sudah di-approve
- ✅ Lihat rincian tunjangan dan potongan gaji pribadi
- ✅ Update profil pribadi (no_hp, email, alamat)
- ✅ Change password pribadi
- ❌ TIDAK lihat data pegawai lain
- ❌ TIDAK bisa input absensi orang lain
- ❌ TIDAK bisa input lembur orang lain
- ❌ TIDAK bisa lihat detail gaji pegawai lain

---

## 📊 DETAIL ROLE & DASHBOARD

### SUPER ADMIN - DASHBOARD

**URL**: `/administrator/dashboard`

#### A. Main Dashboard Overview
```
┌─────────────────────────────────────────────────────┐
│  WELCOME HEADER: Super Admin - [Name] ([Dept])     │
├─────────────────────────────────────────────────────┤
│  📊 SUMMARY CARDS (4 Cards)                        │
├─────────────────────────────────────────────────────┤
│ │ Total Pegawai: 125          │ Total Departemen: 8 │
│ │ Gaji Diproses: 120 / 125    │ Budget Terpakai: XYZ│
├─────────────────────────────────────────────────────┤
│  📈 CHARTS (Responsive)                            │
├─────────────────────────────────────────────────────┤
│ │ Absensi Trend (Last 3mo)    │ Lembur Distribution │
│ │ Gaji Cost vs Budget         │ Top Dept by Cost    │
├─────────────────────────────────────────────────────┤
│  🔔 QUICK ACTIONS / ALERTS                        │
├─────────────────────────────────────────────────────┤
│ │ Pending Seeder      │ New Employee Requests      │
│ │ Salary Calc Pending │ System Alerts              │
└─────────────────────────────────────────────────────┘
```

#### B. Super Admin Menu Sidebar

```
SUPER ADMIN SIDEBAR
├── 🏠 Dashboard
│   └─ Main Overview
│
├── 👥 MANAJEMEN USER & AKSES
│   ├─ 👤 User Management
│   │   ├─ Daftar User (All Guards)
│   │   ├─ Create User (Assign Role)
│   │   ├─ Edit User Profile
│   │   ├─ Change Role Assignment
│   │   ├─ Reset Password User
│   │   ├─ Active/Inactive User
│   │   └─ View User Activity Log
│   │
│   ├─ 🔑 Role Management
│   │   ├─ Daftar Role
│   │   ├─ Lihat Permission per Role
│   │   ├─ Create Role Baru
│   │   ├─ Edit Role
│   │   ├─ Assign Permission ke Role
│   │   └─ Delete Role (if not in use)
│   │
│   └─ 🛡️ Permission Management
│       ├─ Daftar Permission (Grouped by Category)
│       ├─ Create Permission
│       ├─ Edit Permission
│       └─ View Permission Usage
│
├── 💼 DATA MASTER PEGAWAI
│   ├─ 📋 Master Pegawai
│   │   ├─ Daftar Pegawai (All)
│   │   ├─ Add Pegawai Baru
│   │   ├─ Edit Data Pegawai
│   │   ├─ View Detail Pegawai (Full)
│   │   ├─ Delete Pegawai (Soft-delete)
│   │   ├─ Filter by Status (Aktif/Cuti/Keluar)
│   │   ├─ Export Pegawai (CSV/Excel)
│   │   └─ Import Pegawai Batch
│   │
│   ├─ 🏢 Departemen
│   │   ├─ Daftar Departemen
│   │   ├─ Create Departemen
│   │   ├─ Edit Departemen
│   │   ├─ Delete Departemen (Check Related Data)
│   │   ├─ View Pegawai per Departemen
│   │   └─ Departemen Hierarchy
│   │
│   └─ 💼 Jabatan
│       ├─ Daftar Jabatan
│       ├─ Create Jabatan
│       ├─ Edit Jabatan (Including Gaji Pokok Template)
│       ├─ Delete Jabatan
│       ├─ View Pegawai per Jabatan
│       └─ Assign Pegawai ke Jabatan
│
├── 💰 KELOLA KOMPONEN GAJI
│   ├─ 🎁 Tunjangan Master
│   │   ├─ Daftar Tunjangan (Tunjangan Tetap & Tidak Tetap)
│   │   ├─ Create Tunjangan Baru
│   │   │   ├─ Tunjangan Tetap (Makan, Transport, dll)
│   │   │   ├─ Tunjangan Bonus (Khusus, Insentif)
│   │   │   └─ Tunjangan Kondisional
│   │   ├─ Edit Tunjangan
│   │   ├─ Delete Tunjangan (Check Historical Data)
│   │   ├─ Set Tunjangan Per Pegawai / Per Departemen
│   │   ├─ Bulk Assign Tunjangan
│   │   └─ History Perubahan Tunjangan
│   │
│   ├─ ✂️ Potongan Master
│   │   ├─ Daftar Potongan
│   │   │   ├─ Pajak (PPh 21, PPh Pasal 23)
│   │   │   ├─ Iuran Jamsostek
│   │   │   ├─ Cicilan / Pinjaman
│   │   │   ├─ Denda/Potongan Lainnya
│   │   │   └─ Cicilan THR
│   │   ├─ Create Potongan Baru
│   │   ├─ Edit Potongan
│   │   ├─ Delete Potongan (Check Historical Data)
│   │   ├─ Set Potongan Per Pegawai
│   │   ├─ Bulk Assign Potongan
│   │   └─ History Perubahan Potongan
│   │
│   └─ 📜 PTKP Status
│       ├─ Daftar PTKP Type (TK/0, TK/1, K/0, K/1, etc)
│       ├─ View Current Rate
│       ├─ Update Rate (Sesuai Regulasi)
│       └─ History Perubahan PTKP
│
├── 📅 KELOLA ABSENSI
│   ├─ 📊 Absensi Management
│   │   ├─ Daftar Absensi (All Pegawai, All Dept)
│   │   ├─ Input Absensi Manual (e.g. Koreksian)
│   │   │   ├─ Select Pegawai & Tanggal
│   │   │   ├─ Input Status (H/S/I/L/C/A)
│   │   │   ├─ Add Catatan/Alasan
│   │   │   └─ Set Flag untuk Recalc Gaji
│   │   ├─ Edit Absensi (Hanya yg belum locked)
│   │   ├─ Delete Absensi (Audit trail)
│   │   ├─ Approve/Reject Absensi (Batch)
│   │   ├─ View Absensi Statistics
│   │   │   ├─ % Kehadiran per Departemen
│   │   │   ├─ TOP Pegawai Sering Bolos
│   │   │   ├─ Tren Absensi (Monthly)
│   │   │   └─ Forecast Penalty Impact
│   │   ├─ Filter by Date Range, Dept, Status
│   │   ├─ Export Absensi (CSV/Excel)
│   │   ├─ Lock Absensi Period (e.g., Permanent after calc)
│   │   └─ Reset Absensi (Only Before Payroll Lock)
│   │
│   ├─ ✅ Jadwal Kerja
│   │   ├─ Setup Jadwal Kerja (Shift, Jam Kerja)
│   │   ├─ Assign Jadwal ke Pegawai
│   │   ├─ View Jadwal Grid
│   │   └─ History Perubahan Jadwal
│   │
│   └─ 📈 Absensi Reports
│       ├─ Attendance Rate Report
│       ├─ Absent Reason Analysis
│       └─ Tardiness Report
│
├── ⏰ KELOLA LEMBUR
│   ├─ 📝 Lembur Entry
│   │   ├─ Daftar Lembur (All Pegawai)
│   │   ├─ Input Lembur Manual
│   │   ├─ Edit Lembur (Hanya draft/pending)
│   │   ├─ Delete Lembur (Audit trail)
│   │   ├─ Approve/Reject Lembur Batch
│   │   ├─ Filter by Status, Pegawai, Dept, Date Range
│   │   ├─ View Lembur Statistics
│   │   │   ├─ Total Jam Lembur per Pegawai
│   │   │   ├─ Lembur Cost per Dept
│   │   │   ├─ TOP Pegawai Lembur Terbanyak
│   │   │   └─ Forecast Lembur Cost
│   │   │
│   │   ├─ Export Lembur (CSV/Excel)
│   │   └─ Lock Lembur Period
│   │
│   └─ 📊 Lembur Reports
│       ├─ Overtime by Department
│       ├─ Overtime Cost Analysis
│       └─ Employee Overtime Trend
│
├── 💸 PENGGAJIAN & PAYROLL
│   ├─ 🧮 Calculation & Processing
│   │   ├─ Dashboard Penggajian (Summary)
│   │   │   ├─ Salary Status (Draft/Calculated/Approved/Posted)
│   │   │   ├─ Total Gaji Diproses vs Pending
│   │   │   ├─ Total Tunjangan & Potongan
│   │   │   ├─ YTD Statistics
│   │   │   └─ Budget vs Actual
│   │   │
│   │   ├─ 📋 Daftar Penggajian
│   │   │   ├─ Filter by Month, Year, Status, Dept
│   │   │   ├─ View Status per Pegawai
│   │   │   ├─ Bulk Action (Select Multiple)
│   │   │   └─ Sort by Nama, Dept, Status
│   │   │
│   │   ├─ ✏️ Calculate Salary
│   │   │   ├─ Single Calculation
│   │   │   │   ├─ Select Pegawai, Periode
│   │   │   │   ├─ Review Detail Input (Absensi, Lembur, dll)
│   │   │   │   ├─ System Auto-Calculate
│   │   │   │   ├─ Manual Override (If Needed)
│   │   │   │   ├─ Preview Hasil Perhitungan
│   │   │   │   └─ Save (Status: DRAFT)
│   │   │   │
│   │   │   ├─ Batch Calculation
│   │   │   │   ├─ Select Periode (Month/Year)
│   │   │   │   ├─ Select Departemen (ALL or Specific)
│   │   │   │   ├─ Verify Absensi Data Complete
│   │   │   │   ├─ Verify Lembur Data Approved
│   │   │   │   ├─ System Auto-Calculate All
│   │   │   │   ├─ View Calculation Progress
│   │   │   │   ├─ Review Summary Result
│   │   │   │   │   ├─ Total Gaji Bruto
│   │   │   │   │   ├─ Total Tunjangan
│   │   │   │   │   ├─ Total Potongan
│   │   │   │   │   └─ Total Gaji Netto
│   │   │   │   └─ Save All Calculations (Status: DRAFT)
│   │   │   │
│   │   │   └─ Batch Recalculate (If Any Data Changed)
│   │   │
│   │   ├─ 👁️ View Detail Penggajian
│   │   │   ├─ Pegawai Info (NIK, Nama, Jabatan, Dept)
│   │   │   ├─ Salary Breakdown
│   │   │   │   ├─ Gaji Pokok
│   │   │   │   ├─ Tunjangan (List Detail)
│   │   │   │   ├─ Lembur (Jam & Nilai)
│   │   │   │   ├─ Potongan (List Detail)
│   │   │   │   ├─ Pajak PPh 21
│   │   │   │   └─ Gaji Netto
│   │   │   │
│   │   │   ├─ Calculation History
│   │   │   ├─ Approval Status & Timestamp
│   │   │   └─ Edit/Delete Options (If Draft Only)
│   │   │
│   │   ├─ ✅ Approval Flow
│   │   │   ├─ View Pending Approval List
│   │   │   ├─ Review Detail Perhitungan
│   │   │   ├─ Compare with Previous Month
│   │   │   ├─ Approve Single
│   │   │   ├─ Batch Approve (with confirmation)
│   │   │   ├─ Reject with Reason/Catatan
│   │   │   ├─ Set Status to: APPROVED
│   │   │   └─ Audit Trail (Who, When, Why)
│   │   │
│   │   ├─ 💳 Post ke Payroll
│   │   │   ├─ View Approved List
│   │   │   ├─ Final Review Before Posting
│   │   │   ├─ Set Status to: POSTED
│   │   │   ├─ Lock Calculation (Cannot Edit)
│   │   │   └─ Generate Posting Report
│   │   │
│   │   └─ 🔄 Revert/Rollback (Emergency Only)
│   │       ├─ Requires Special Permission
│   │       ├─ Audit Log Entry Mandatory
│   │       └─ Cannot Revert Posted
│   │
│   ├─ 📄 Slip Gaji & Report
│   │   ├─ 📋 Print Slip Gaji
│   │   │   ├─ Select Pegawai & Periode
│   │   │   ├─ View Preview
│   │   │   ├─ Print PDF
│   │   │   ├─ Print Batch (All Dept/Tertentu)
│   │   │   ├─ Email Slip ke Pegawai
│   │   │   └─ Archive Digital Slip
│   │   │
│   │   ├─ 📊 Payroll Report
│   │   │   ├─ Salary Summary Report (All Dept)
│   │   │   │   ├─ Total Gaji Bruto
│   │   │   │   ├─ Total Tunjangan
│   │   │   │   ├─ Total Potongan
│   │   │   │   ├─ Total Pajak
│   │   │   │   └─ Total Netto
│   │   │   │
│   │   │   ├─ By Department Report
│   │   │   │   ├─ Breakdown per Dept
│   │   │   │   ├─ Ranking Dept by Cost
│   │   │   │   └─ Variance vs Budget
│   │   │   │
│   │   │   ├─ Salary Range Report
│   │   │   │   ├─ Min/Max/Average Salary
│   │   │   │   ├─ Salary Distribution Chart
│   │   │   │   └─ Equity Analysis
│   │   │   │
│   │   │   ├─ Component Analysis
│   │   │   │   ├─ Detail Tunjangan Usage
│   │   │   │   ├─ Detail Potongan Usage
│   │   │   │   ├─ Top Tunjangan Paid
│   │   │   │   └─ Top Potongan Applied
│   │   │   │
│   │   │   └─ Tax Report
│   │   │       ├─ Total PPh 21
│   │   │       ├─ Tax by Department
│   │   │       ├─ Taxable Income Distribution
│   │   │       └─ PTKP Analysis
│   │   │
│   │   ├─ 📥 Export Payroll
│   │   │   ├─ Export to Excel (Detail & Summary)
│   │   │   ├─ Export for Bank Transfer (Batch)
│   │   │   ├─ Export for Accounting (CSV)
│   │   │   └─ Export Tax Report
│   │   │
│   │   └─ 📈 YTD Analysis
│   │       ├─ YTD Salary Cost
│   │       ├─ YTD vs Budget
│   │       ├─ YTD Growth Rate
│   │       └─ Monthly Trend Chart
│   │
│   └─ 🔔 Payroll Alerts
│       ├─ Pending Calculation
│       ├─ Pending Approval
│       ├─ Data Anomaly Detection
│       └─ Budget Warning
│
├── 📊 LAPORAN & ANALYTICS
│   ├─ 📈 Dashboard Analytics
│   │   ├─ KPI Cards (Key Metrics)
│   │   ├─ Trend Charts
│   │   ├─ Department Comparison
│   │   └─ YTD vs Budget
│   │
│   ├─ 📋 HR Reports
│   │   ├─ Employee Movement Report
│   │   ├─ Turnover Analysis
│   │   ├─ Departemen Structure Report
│   │   └─ Salary Structure Report
│   │
│   ├─ 💰 Payroll Reports (Detailed in PWA section)
│   │   ├─ See Penggajian section above
│   │
│   ├─ 📊 Finance Reports
│   │   ├─ Monthly Payroll Cost
│   │   ├─ Annual Payroll Budget
│   │   ├─ Cost per Department
│   │   └─ Projection Report
│   │
│   └─ 📄 Custom Report Builder
│       ├─ Create Custom Report
│       ├─ Save Report Template
│       ├─ Schedule Report (Auto-Generate)
│       └─ Email Report Distribution
│
├── ⚙️ SYSTEM SETTINGS & MAINTENANCE
│   ├─ 🔧 Configuration
│   │   ├─ Company Info Setup
│   │   ├─ Fiscal Year Setup
│   │   ├─ Payroll Period Setup (Monthly/Bi-weekly)
│   │   ├─ Tax Configuration
│   │   ├─ Working Hour Configuration
│   │   └─ Currency Settings
│   │
│   ├─ 🔐 System Security
│   │   ├─ View Activity Log (All Users)
│   │   ├─ View Login History
│   │   ├─ Export Audit Trail
│   │   ├─ IP Whitelist (If Required)
│   │   └─ Session Management
│   │
│   ├─ 📦 Data Management
│   │   ├─ Database Backup
│   │   ├─ Data Export
│   │   ├─ Data Import (Batch)
│   │   └─ Database Cleanup (Archives)
│   │
│   ├─ 🔔 Notifications & Alerts
│   │   ├─ Email Configuration
│   │   ├─ SMS Gateway (If Any)
│   │   ├─ Notification Template
│   │   └─ Alert Rules
│   │
│   ├─ 📧 External Integration
│   │   ├─ Email Gateway Config
│   │   ├─ Bank Integration Config
│   │   ├─ Accounting System Integration
│   │   └─ Attendance Device Integration
│   │
│   └─ 📄 Document Management
│       ├─ Document Templates
│       ├─ Letter Templates
│       └─ Certificate Management
│
└── 👨‍💼 PROFILE & LOGOUT
    ├─ View Profile
    ├─ Change Password
    ├─ Settings Preferences
    └─ Logout
```

---

### PETUGAS (OFFICER) - DASHBOARD

**URL**: `/officer/dashboard`

#### A. Main Dashboard Overview
```
┌─────────────────────────────────────────────────────┐
│  WELCOME HEADER: Officer - [Name] ([Dept])         │
├─────────────────────────────────────────────────────┤
│  📊 SUMMARY CARDS (Departemen Only)               │
├─────────────────────────────────────────────────────┤
│ │ Pegawai Dept: 15        │ Absensi Bulan Ini: 98% │
│ │ Lembur Pending: 3       │ Lembur Approved: 25 jam│
├─────────────────────────────────────────────────────┤
│  📈 CHARTS (Dept Only)                             │
├─────────────────────────────────────────────────────┤
│ │ Absensi Trend (This Month)                       │
│ │ Lembur per Staff                                 │
├─────────────────────────────────────────────────────┤
│  🔔 QUICK ACTIONS / ALERTS                        │
├─────────────────────────────────────────────────────┤
│ │ Pending Absensi: 2      │ Pending Lembur: 1  │
│ │ Staff Sakit: 3          │ Action Needed: 2   │
└─────────────────────────────────────────────────────┘
```

#### B. Officer Menu Sidebar

```
OFFICER SIDEBAR (Department-Based)
├── 🏠 Dashboard
│   └─ Department Overview (Own Dept Only)
│
├── 👥 Tim Saya (My Team)
│   ├─ 📋 Daftar Pegawai (Own Department Only)
│   │   ├─ View List (Nama, NIP, Jabatan, Status)
│   │   ├─ View Detail Pegawai
│   │   │   ├─ Personal Info (Readonly)
│   │   │   ├─ Contact Info (Readonly)
│   │   │   ├─ Employment Status (Readonly)
│   │   │   └─ Salary Info Summary (Readonly)
│   │   │
│   │   ├─ Filter by Status (Aktif/Cuti/Keluar)
│   │   └─ Export Pegawai List (CSV/Excel)
│   │
│   ├─ 💼 Team Structure
│   │   ├─ Org Chart (Own Dept)
│   │   └─ Reporting Line
│   │
│   └─ 📊 Team Statistics
│       ├─ Total Pegawai
│       ├─ By Status
│       └─ By Jabatan
│
├── 📅 KELOLA ABSENSI DEPARTEMEN
│   ├─ 📝 Input & Approval Absensi
│   │   ├─ Daftar Absensi (Own Dept + Own month only)
│   │   ├─ Input Absensi Manual (Own Dept Pegawai)
│   │   │   ├─ Select Pegawai (Dropdown: Own Dept)
│   │   │   ├─ Select Tanggal
│   │   │   ├─ Select Status (H/S/I/L/C/A)
│   │   │   ├─ Add Catatan/Alasan
│   │   │   └─ Save
│   │   │
│   │   ├─ Edit Absensi (Own Dept, Draft Only)
│   │   ├─ Delete Absensi (Own Dept, Draft Only, with Audit)
│   │   ├─ Approve Absensi (Own Dept, Batch)
│   │   │   ├─ Mark as Approved
│   │   │   └─ Add Approval Note (Optional)
│   │   │
│   │   ├─ Reject Absensi (Own Dept)
│   │   │   ├─ Provide Rejection Reason
│   │   │   └─ Return to Draft (Pegawai/HR edit)
│   │   │
│   │   ├─ View Absensi Statistics (Own Dept)
│   │   │   ├─ % Kehadiran Tim
│   │   │   ├─ Trend Absensi (This Month)
│   │   │   └─ Pegawai Sering Bolos (Own Dept)
│   │   │
│   │   ├─ Filter by Pegawai, Date Range, Status
│   │   └─ Export Absensi (CSV/Excel)
│   │
│   ├─ 📊 Absensi Reports (Own Dept Only)
│   │   ├─ Attendance Rate Report
│   │   ├─ Absent Reason Analysis
│   │   └─ Monthly Attendance Trend
│   │
│   └─ 🔔 Absensi Alerts
│       ├─ Pending Approval Count
│       └─ Staff Absent Today Alert
│
├── ⏰ KELOLA LEMBUR DEPARTEMEN
│   ├─ 📝 Input & Approval Lembur
│   │   ├─ Daftar Lembur (Own Dept)
│   │   ├─ Input Lembur Manual (Own Dept Pegawai)
│   │   │   ├─ Select Pegawai (Pegawai Own Dept)
│   │   │   ├─ Select Tanggal
│   │   │   ├─ Set Jam Start & End
│   │   │   ├─ Calculate Jam Lembur (Auto)
│   │   │   ├─ Select Jenis Lembur:
│   │   │   │   ├─ Weekday (Coefficient 1)
│   │   │   │   ├─ Weekend (Coefficient 1.5)
│   │   │   │   ├─ Holiday (Coefficient 2)
│   │   │   │   └─ Holiday Weekend (Coefficient 2.5)
│   │   │   ├─ Add Description/Aktivitas
│   │   │   └─ Save
│   │   │
│   │   ├─ Edit Lembur (Own Dept, Draft/Pending Only)
│   │   ├─ Delete Lembur (Own Dept, Draft Only, with Audit)
│   │   ├─ Approve Lembur (Own Dept, Batch)
│   │   │   ├─ Mark as Approved
│   │   │   ├─ Add Approval Note
│   │   │   └─ Status → APPROVED
│   │   │
│   │   ├─ Reject Lembur (Own Dept)
│   │   │   ├─ Provide Rejection Reason
│   │   │   └─ Return to Draft
│   │   │
│   │   ├─ View Lembur Statistics (Own Dept)
│   │   │   ├─ Total Jam Lembur (This Month)
│   │   │   ├─ Jam Lembur per Pegawai
│   │   │   ├─ Estimated Lembur Cost
│   │   │   └─ TOP Pegawai Lembur
│   │   │
│   │   ├─ Filter by Pegawai, Date Range, Status
│   │   └─ Export Lembur (CSV/Excel)
│   │
│   ├─ 📊 Lembur Reports (Own Dept Only)
│   │   ├─ Overtime by Employee
│   │   ├─ Overtime Cost Analysis
│   │   └─ Monthly Overtime Trend
│   │
│   └─ 🔔 Lembur Alerts
│       ├─ Pending Approval Count
│       └─ High Overtime Alert (Dept Policy)
│
├── 💰 PENGGAJIAN (VIEW & MONITORING ONLY)
│   ├─ 📊 View Gaji Departemen
│   │   ├─ Daftar Penggajian (Own Dept Only)
│   │   │   ├─ View Gaji Status (Calculated/Approved/Posted)
│   │   │   ├─ Sort by Status, Name
│   │   │   ├─ Filter by Status, Period
│   │   │   └─ Cannot Edit/Modify
│   │   │
│   │   ├─ View Detail Gaji Pegawai (Own Dept)
│   │   │   ├─ Pegawai Info (Readonly)
│   │   │   ├─ Salary Breakdown (Readonly)
│   │   │   │   ├─ Gaji Pokok
│   │   │   │   ├─ Tunjangan (List)
│   │   │   │   ├─ Lembur (Calculated from approved)
│   │   │   │   ├─ Potongan (List)
│   │   │   │   ├─ Pajak
│   │   │   │   └─ Gaji Netto
│   │   │   │
│   │   │   ├─ Verify Input Data
│   │   │   │   ├─ Verify Absensi (Validate Own Dept)
│   │   │   │   ├─ Verify Lembur (Validate Own Dept)
│   │   │   │   ├─ Verify Tunjangan (Approved)
│   │   │   │   └─ Verify Potongan (From Master)
│   │   │   │
│   │   │   └─ Comparison with Previous Month
│   │   │
│   │   ├─ 📈 Departemen Salary Summary
│   │   │   ├─ Total Gaji Bruto (Own Dept)
│   │   │   ├─ Total Tunjangan (Own Dept)
│   │   │   ├─ Total Potongan (Own Dept)
│   │   │   ├─ Total Netto (Own Dept)
│   │   │   └─ Dept Budget vs Actual
│   │   │
│   │   ├─ 🔍 Data Verification
│   │   │   ├─ Check All Absensi Approved
│   │   │   ├─ Check All Lembur Approved
│   │   │   ├─ Check Data Completeness
│   │   │   └─ Flag Anomalies (if any)
│   │   │
│   │   ├─ 📊 Departemen Payroll Report
│   │   │   ├─ Summary by Employee
│   │   │   ├─ Salary Range Analysis
│   │   │   ├─ Component Analysis (By Dept)
│   │   │   └─ Export Report
│   │   │
│   │   └─ 📄 Print Slip Gaji (Own Dept, After Posted)
│   │       ├─ View Preview
│   │       ├─ Print Single
│   │       ├─ Print All (Own Dept)
│   │       └─ Email to Pegawai (Optional)
│   │
│   ├─ ❌ NOT ALLOWED for Officer
│   │   ├─ ❌ Create Salary Calculation
│   │   ├─ ❌ Edit Salary Calculation
│   │   ├─ ❌ Delete Salary Calculation
│   │   ├─ ❌ Approve Salary Calculation
│   │   ├─ ❌ View Other Department Salary
│   │   └─ ❌ Manage Salary Components (Tunjangan/Potongan)
│   │
│   └─ 🔔 Salary Alerts
│       ├─ Pending Calculation (Aware Only)
│       └─ Data Issues Detected
│
├── 📋 LIHAT DATA MASTER (READONLY)
│   ├─ Tunjangan (List Only, Cannot Edit)
│   ├─ Potongan (List Only, Cannot Edit)
│   └─ Jabatan (List Only)
│
└── 👨‍💼 PROFILE & LOGOUT
    ├─ View Profile
    ├─ Change Password
    └─ Logout
```

---

### PEGAWAI (EMPLOYEE) - DASHBOARD

**URL**: `/student/dashboard` or `/employee/dashboard`

#### A. Main Dashboard Overview
```
┌─────────────────────────────────────────────────────┐
│  WELCOME HEADER: Hello [Name]! 👋                  │
├─────────────────────────────────────────────────────┤
│  📊 SUMMARY CARDS (Personal Data Only)            │
├─────────────────────────────────────────────────────┤
│ │ Dept: [Nama Dept]    │ Jabatan: [Nama Jabatan] │
│ │ Status: Aktif        │ Tgl Masuk: DD/MM/YYYY   │
├─────────────────────────────────────────────────────┤
│  📈 QUICK INFO (Personal Only)                    │
├─────────────────────────────────────────────────────┤
│ │ Absensi Bln Ini: 20H │ Lembur Bln Ini: 10 jam │
│ │ Gaji Terakhir: XXX   │ Status Gaji: Posted     │
├─────────────────────────────────────────────────────┤
│  💬 NOTIFICATIONS                                  │
├─────────────────────────────────────────────────────┤
│ │ Gaji Bulan Ini Sudah Tersedia                   │
│ │ Lembur Anda Approved                            │
└─────────────────────────────────────────────────────┘
```

#### B. Employee Menu Sidebar

```
EMPLOYEE SIDEBAR (Self-Service Only)
├── 🏠 Dashboard
│   └─ Personal Overview
│
├── 👤 PROFIL SAYA
│   ├─ 📋 Lihat Profil
│   │   ├─ Personal Info (View & Edit Own)
│   │   │   ├─ Nama (Readonly)
│   │   │   ├─ NIK (Readonly)
│   │   │   ├─ Email (Can Edit)
│   │   │   ├─ No. HP (Can Edit)
│   │   │   ├─ Alamat (Can Edit)
│   │   │   └─ Bank Account Info (Can Edit)
│   │   │
│   │   ├─ Employment Info (Readonly)
│   │   │   ├─ Departemen
│   │   │   ├─ Jabatan
│   │   │   ├─ Status Pegawai
│   │   │   ├─ Tgl Masuk
│   │   │   └─ Gaji Pokok (Readonly)
│   │   │
│   │   └─ PTKP Status (Readonly)
│   │       └─ For Tax Reference
│   │
│   ├─ 🔐 Ubah Password
│   │   ├─ Old Password
│   │   ├─ New Password
│   │   ├─ Confirm Password
│   │   └─ Submit
│   │
│   └─ ⚙️ Preferences
│       ├─ Language Preference (ID/EN)
│       ├─ Notification Preferences
│       └─ Theme (Light/Dark)
│
├── 📅 ABSENSI SAYA
│   ├─ 📝 Lihat Absensi
│   │   ├─ Daftar Absensi (Personal Only)
│   │   │   ├─ Filter by Month/Year
│   │   │   ├─ View Status per Hari
│   │   │   │   ├─ H (Hadir)
│   │   │   │   ├─ S (Sakit)
│   │   │   │   ├─ I (Izin)
│   │   │   │   ├─ L (Lupa Absen)
│   │   │   │   ├─ C (Cuti)
│   │   │   │   ├─ A (Alpa)
│   │   │   │   └─ Holiday
│   │   │   │
│   │   │   └─ View with Color Coding & Icons
│   │   │
│   │   ├─ 📊 Absensi Statistics (Personal)
│   │   │   ├─ Total Hadir (This Month)
│   │   │   ├─ Total Libur
│   │   │   ├─ Total Cuti
│   │   │   ├─ Total Sakit
│   │   │   ├─ Total Izin
│   │   │   └─ Attendance Rate %
│   │   │
│   │   ├─ 📈 Absensi Trend (Last 3 Months)
│   │   │   └─ Chart dengan Trend Visual
│   │   │
│   │   ├─ ❌ NOT ALLOWED
│   │   │   ├─ ❌ Edit Absensi Orang Lain
│   │   │   ├─ ❌ Delete Absensi
│   │   │   └─ ❌ View Detail Absensi Orang Lain
│   │   │
│   │   └─ 💬 Request Koreksi Absensi
│   │       ├─ Submit Request (If Error)
│   │       ├─ Attach Evidence (Foto, Dokumen)
│   │       ├─ Add Note/Alasan
│   │       └─ Status Tracking (Pending/Approved/Rejected)
│
├── ⏰ LEMBUR SAYA
│   ├─ 📝 Lihat Lembur
│   │   ├─ Daftar Lembur (Personal Only)
│   │   │   ├─ Filter by Month/Year
│   │   │   ├─ View List (Tangal, Jam, Status, Jam Total)
│   │   │   └─ View Status
│   │   │       ├─ PENDING (Waiting Approval)
│   │   │       ├─ APPROVED (Ready for Payroll)
│   │   │       └─ REJECTED (with Reason)
│   │   │
│   │   ├─ 📊 Lembur Statistics (Personal)
│   │   │   ├─ Total Jam Lembur (This Month)
│   │   │   ├─ Total Jam Lembur (YTD)
│   │   │   ├─ Estimated Lembur Payment (This Month)
│   │   │   └─ Average Jam per Lembur
│   │   │
│   │   ├─ 📈 Lembur Trend (Last 6 Months)
│   │   │   └─ Chart dengan Jam per Bulan
│   │   │
│   │   ├─ 📄 Detail Lembur
│   │   │   ├─ View Per Tanggal
│   │   │   ├─ Jam Start & End (Auto-calculated)
│   │   │   ├─ Jenis Lembur (Weekday/Weekend/Holiday)
│   │   │   ├─ Jam Total
│   │   │   ├─ Aktivitas/Description
│   │   │   ├─ Status (Pending/Approved/Rejected)
│   │   │   └─ Approval Date (If Approved)
│   │   │
│   │   └─ ❌ NOT ALLOWED
│   │       ├─ ❌ Create Own Lembur (Only via Officer)
│   │       ├─ ❌ Edit Lembur
│   │       └─ ❌ View Lembur Orang Lain
│
├── 💰 PENGGAJIAN SAYA
│   ├─ 📋 Slip Gaji
│   │   ├─ Daftar Slip (Personal Only, Posted Only)
│   │   │   ├─ Filter by Month/Year
│   │   │   ├─ View List (Month, Status, Date)
│   │   │   └─ Only Posted/Final Slips
│   │   │
│   │   ├─ 📄 View Slip Detail
│   │   │   ├─ Pegawai Info
│   │   │   │   ├─ Nama (Readonly)
│   │   │   │   ├─ NIK (Readonly)
│   │   │   │   └─ Jabatan/Dept (Readonly)
│   │   │   │
│   │   │   ├─ Salary Breakdown (Detail)
│   │   │   │   ├─ Gaji Pokok
│   │   │   │   ├─ Tunjangan:
│   │   │   │   │   ├─ Tunjangan Tetap (List)
│   │   │   │   │   └─ Tunjangan Tidak Tetap (List)
│   │   │   │   ├─ Lembur:
│   │   │   │   │   ├─ Jam Lembur Approved
│   │   │   │   │   └─ Nilai Lembur
│   │   │   │   ├─ Potongan:
│   │   │   │   │   ├─ Pajak PPh 21
│   │   │   │   │   ├─ Jamsostek (If Any)
│   │   │   │   │   ├─ Cicilan/Pinjaman (If Any)
│   │   │   │   │   └─ Potongan Lainnya
│   │   │   │   │
│   │   │   │   ├─ SUMMARY
│   │   │   │   │   ├─ Total Tunjangan
│   │   │   │   │   ├─ Total Potongan
│   │   │   │   │   ├─ Total Gaji Bruto
│   │   │   │   │   └─ GAJI NETTO (Highlight)
│   │   │   │   │
│   │   │   │   └─ Periode & Status
│   │   │   │       ├─ Periode (Month/Year)
│   │   │   │       ├─ Status (POSTED)
│   │   │   │       └─ Calculate Date
│   │   │   │
│   │   │   ├─ 📥 Download Slip (PDF)
│   │   │   └─ 🖨️ Print Slip
│   │   │
│   │   └─ ❌ NOT ALLOWED
│   │       ├─ ❌ View Draft/Pending Slip
│   │       ├─ ❌ View Slip Draft (before approval)
│   │       └─ ❌ View Slip Orang Lain
│   │
│   ├─ 📊 Salary Summary
│   │   ├─ This Month Salary
│   │   │   ├─ Gaji Netto
│   │   │   ├─ Gaji Bruto
│   │   │   ├─ Status (Posted/Pending/Processing)
│   │   │   └─ Effective Date
│   │   │
│   │   ├─ YTD Salary
│   │   │   ├─ Total Gaji Netto (YTD)
│   │   │   ├─ Total Tunjangan (YTD)
│   │   │   ├─ Total Potongan (YTD)
│   │   │   ├─ Average Monthly
│   │   │   └─ Tax Paid (YTD)
│   │   │
│   │   ├─ Monthly Summary (Last 12 Months)
│   │   │   ├─ Table (Month, Gaji Pokok, Tunjangan, Potongan, Netto)
│   │   │   └─ Chart Visualization
│   │   │
│   │   └─ 📊 Component Analysis
│   │       ├─ Top Tunjangan Received (List)
│   │       ├─ Top Potongan Applied (List)
│   │       └─ Overtime Contribution %
│   │
│   ├─ 💬 FAQ & Help
│   │   ├─ Penjelasan Komponen Gaji
│   │   ├─ How Overtime Calculated
│   │   ├─ How Tax Calculated
│   │   └─ Contact HR for Questions
│   │
│   └─ ❌ NOT ALLOWED
│       ├─ ❌ Edit Salary Data
│       ├─ ❌ Delete Salary Data
│       ├─ ❌ View Rough/Draft Calculation
│       └─ ❌ View Other Employee Salary
│
└── 👨‍💼 PROFILE & LOGOUT
    ├─ View Profile (My Data)
    ├─ Edit Profile (Limited)
    ├─ Change Password
    └─ Logout
```

---

## 🔐 PERMISSION MATRIX

### Comprehensive Permission Table

| Permission | Super Admin | Officer | Employee | Notes |
|-----------|:-----------:|:-------:|:--------:|-------|
| **DASHBOARD** | | | | |
| dashboard.view | ✅ | ✅ | ✅ | Setiap role lihat dashboard sendiri |
| | | | | |
| **USER & ROLE MANAGEMENT** | | | | |
| user.view | ✅ | ❌ | ❌ | Super admin only |
| user.create | ✅ | ❌ | ❌ | Super admin only |
| user.edit | ✅ | ❌ | ❌ | Super admin only |
| user.delete | ✅ | ❌ | ❌ | Super admin only |
| user.assign_role | ✅ | ❌ | ❌ | Super admin only |
| role.view | ✅ | ❌ | ❌ | Super admin only |
| role.create | ✅ | ❌ | ❌ | Super admin only |
| role.edit | ✅ | ❌ | ❌ | Super admin only |
| role.delete | ✅ | ❌ | ❌ | Super admin only |
| permission.view | ✅ | ❌ | ❌ | Super admin only |
| permission.manage | ✅ | ❌ | ❌ | Super admin only |
| | | | | |
| **PEGAWAI MANAGEMENT** | | | | |
| pegawai.view | ✅ | ✅ | ❌ | Officer: Own Dept Only |
| pegawai.view_own | ❌ | ❌ | ✅ | Employee: Self Only |
| pegawai.create | ✅ | ❌ | ❌ | Super admin only |
| pegawai.edit | ✅ | ❌ | ❌ | Super admin only |
| pegawai.delete | ✅ | ❌ | ❌ | Super admin only |
| pegawai.edit_own | ❌ | ❌ | ✅ | Employee: Edit own profile (limited) |
| | | | | |
| **DEPARTEMEN** | | | | |
| departemen.view | ✅ | ✅ | ❌ | Officer: Own Dept Only (Readonly) |
| departemen.create | ✅ | ❌ | ❌ | Super admin only |
| departemen.edit | ✅ | ❌ | ❌ | Super admin only |
| departemen.delete | ✅ | ❌ | ❌ | Super admin only |
| | | | | |
| **JABATAN** | | | | |
| jabatan.view | ✅ | ✅ | ❌ | Officer: Readonly |
| jabatan.create | ✅ | ❌ | ❌ | Super admin only |
| jabatan.edit | ✅ | ❌ | ❌ | Super admin only |
| jabatan.delete | ✅ | ❌ | ❌ | Super admin only |
| | | | | |
| **TUNJANGAN** | | | | |
| tunjangan.view | ✅ | ✅ | ❌ | Officer: Readonly only |
| tunjangan.create | ✅ | ❌ | ❌ | Super admin only |
| tunjangan.edit | ✅ | ❌ | ❌ | Super admin only |
| tunjangan.delete | ✅ | ❌ | ❌ | Super admin only |
| tunjangan.assign | ✅ | ❌ | ❌ | Super admin only |
| tunjangan.view_pay_stub | ✅ | ✅ | ✅ | View on respective payslip |
| | | | | |
| **POTONGAN** | | | | |
| potongan.view | ✅ | ✅ | ❌ | Officer: Readonly only |
| potongan.create | ✅ | ❌ | ❌ | Super admin only |
| potongan.edit | ✅ | ❌ | ❌ | Super admin only |
| potongan.delete | ✅ | ❌ | ❌ | Super admin only |
| potongan.assign | ✅ | ❌ | ❌ | Super admin only |
| potongan.view_pay_stub | ✅ | ✅ | ✅ | View on respective payslip |
| | | | | |
| **ABSENSI** | | | | |
| absensi.view | ✅ | ✅ | ❌ | Officer: Own Dept Only |
| absensi.view_own | ❌ | ❌ | ✅ | Employee: Self Only |
| absensi.create | ✅ | ✅ | ❌ | Officer: Own Dept Only |
| absensi.edit | ✅ | ✅ | ❌ | Officer: Own Dept, Draft Only |
| absensi.delete | ✅ | ✅ | ❌ | Officer: Own Dept, Draft Only, Audit |
| absensi.approve | ✅ | ✅ | ❌ | Officer: Own Dept Only |
| absensi.reject | ✅ | ✅ | ❌ | Officer: Own Dept Only |
| absensi.request_correction | ❌ | ❌ | ✅ | Employee: Request koreksi |
| | | | | |
| **LEMBUR** | | | | |
| lembur.view | ✅ | ✅ | ❌ | Officer: Own Dept Only |
| lembur.view_own | ❌ | ❌ | ✅ | Employee: Self Only |
| lembur.create | ✅ | ✅ | ❌ | Officer: Own Dept Only |
| lembur.edit | ✅ | ✅ | ❌ | Officer: Own Dept, Draft/Pending |
| lembur.delete | ✅ | ✅ | ❌ | Officer: Own Dept, Draft Only |
| lembur.approve | ✅ | ✅ | ❌ | Officer: Own Dept Only |
| lembur.reject | ✅ | ✅ | ❌ | Officer: Own Dept Only |
| | | | | |
| **PENGGAJIAN** | | | | |
| gaji.view | ✅ | ✅ | ❌ | Officer: Own Dept Only |
| gaji.view_own | ❌ | ❌ | ✅ | Employee: Self Only |
| gaji.create | ✅ | ❌ | ❌ | Super admin only |
| gaji.calculate | ✅ | ❌ | ❌ | Super admin only |
| gaji.edit | ✅ | ❌ | ❌ | Super admin only, Draft Only |
| gaji.delete | ✅ | ❌ | ❌ | Super admin only, Audit |
| gaji.approve | ✅ | ❌ | ❌ | Super admin only |
| gaji.post | ✅ | ❌ | ❌ | Super admin only |
| gaji.print_slip | ✅ | ✅ | ✅ | Officer/Employee: Own Resp Data |
| gaji.export | ✅ | ✅ | ❌ | Officer: Own Dept Only |
| | | | | |
| **LAPORAN** | | | | |
| laporan.view | ✅ | ✅ | ❌ | Officer: Own Dept Only |
| laporan.absensi | ✅ | ✅ | ❌ | Officer: Own Dept Only |
| laporan.lembur | ✅ | ✅ | ❌ | Officer: Own Dept Only |
| laporan.gaji | ✅ | ✅ | ❌ | Officer: Own Dept Only (Summary) |
| laporan.export | ✅ | ✅ | ❌ | Officer: Own Dept Only |
| laporan.budget_vs_actual | ✅ | ✅ | ❌ | Officer: Own Dept Only |
| | | | | |
| **SYSTEM & MAINTENANCE** | | | | |
| system.config | ✅ | ❌ | ❌ | Super admin only |
| system.backup | ✅ | ❌ | ❌ | Super admin only |
| system.activity_log | ✅ | ❌ | ❌ | Super admin only |
| system.email_config | ✅ | ❌ | ❌ | Super admin only |
| system.integration | ✅ | ❌ | ❌ | Super admin only |
| | | | | |
| **PROFILE & SETTINGS** | | | | |
| profile.view | ✅ | ✅ | ✅ | Own profile |
| profile.edit | ✅ | ✅ | ✅ | Own profile (limited fields) |
| profile.change_password | ✅ | ✅ | ✅ | Own profile |

---

## 📊 STRUKTUR MENU SIDEBAR

### Menu Visibility Logic (Production-Ready)

```
PRINSIP DASAR:
- Jangan tampilkan menu yang user tidak bisa akses
- Jangan tampilkan submenu jika user tidak punya akses ke children
- Minimize menu clutter untuk user experience yang lebih baik
```

### Super Admin - Menu Structure (Complete)

```
Sidebar Super Admin
├─ Dashboard ................................ Always visible
├─ User & Role Management
│  ├─ User Management ..................... [user.view]
│  ├─ Role Management ..................... [role.view]
│  └─ Permission Management .............. [permission.view]
├─ Master Data
│  ├─ Data Pegawai ......................... [pegawai.view]
│  ├─ Departemen ........................... [departemen.view]
│  └─ Jabatan .............................. [jabatan.view]
├─ Komponen Gaji
│  ├─ Tunjangan ............................ [tunjangan.view]
│  ├─ Potongan ............................. [potongan.view]
│  └─ PTKP Status .......................... [system.config]
├─ Absensi Management
│  ├─ Absensi Entry ........................ [absensi.view]
│  ├─ Jadwal Kerja ......................... [absensi.view]
│  └─ Reports ............................. [laporan.absensi]
├─ Lembur Management
│  ├─ Lembur Entry ......................... [lembur.view]
│  └─ Reports ............................. [laporan.lembur]
├─ Penggajian & Payroll
│  ├─ Dashboard ............................ [gaji.view]
│  ├─ Calculation .......................... [gaji.calculate]
│  ├─ Approval ............................. [gaji.approve]
│  ├─ Posting ............................... [gaji.post]
│  ├─ Payslip .............................. [gaji.print_slip]
│  └─ Reports ............................. [laporan.gaji]
├─ Reports & Analytics
│  ├─ HR Reports ........................... [laporan.view]
│  ├─ Payroll Reports ..................... [laporan.gaji]
│  ├─ Finance Reports ..................... [laporan.view]
│  └─ Custom Report Builder ............... [laporan.export]
├─ System Settings
│  ├─ Configuration ........................ [system.config]
│  ├─ Security ............................ [system.activity_log]
│  ├─ Data Management ..................... [system.backup]
│  ├─ Notifications ....................... [system.email_config]
│  ├─ Integrations ......................... [system.integration]
│  └─ Document Management ................. [system.config]
└─ Profile & Logout
   ├─ View Profile ......................... [profile.view]
   ├─ Settings ............................. [profile.edit]
   └─ Logout ............................... Always visible
```

### Officer - Menu Structure (Department-Based)

```
Sidebar Officer
├─ Dashboard ................................ Always visible
├─ My Team (Department-Based)
│  ├─ Pegawai List ......................... [pegawai.view]
│  ├─ Team Structure ....................... [pegawai.view]
│  └─ Team Statistics ..................... [pegawai.view]
├─ Absensi Management
│  ├─ Input & Approval .................... [absensi.approve]
│  ├─ Statistics ........................... [absensi.view]
│  └─ Reports (Own Dept) .................. [laporan.absensi]
├─ Lembur Management
│  ├─ Input & Approval .................... [lembur.approve]
│  ├─ Statistics ........................... [lembur.view]
│  └─ Reports (Own Dept) .................. [laporan.lembur]
├─ Penggajian (View Only)
│  ├─ View Gaji ............................ [gaji.view]
│  ├─ Verify Data .......................... [gaji.view]
│  ├─ Print Slip ........................... [gaji.print_slip]
│  └─ Reports ............................. [laporan.gaji]
├─ Data Master (Readonly)
│  ├─ Tunjangan ............................ [tunjangan.view]
│  ├─ Potongan ............................. [potongan.view]
│  └─ Jabatan .............................. [jabatan.view]
└─ Profile & Logout
   ├─ View Profile ......................... [profile.view]
   ├─ Settings ............................. [profile.edit]
   └─ Logout ............................... Always visible
```

### Employee - Menu Structure (Self-Service)

```
Sidebar Employee (Minimal, Self-Service Only)
├─ Dashboard ................................ Always visible
├─ My Profile
│  ├─ View Profile ......................... [profile.view]
│  ├─ Edit Profile ......................... [profile.edit]
│  ├─ Change Password ..................... [profile.change_password]
│  └─ Preferences .......................... [profile.edit]
├─ My Attendance
│  ├─ View Absensi ......................... [absensi.view_own]
│  ├─ Statistics ........................... [absensi.view_own]
│  └─ Request Correction .................. [absensi.request_correction]
├─ My Overtime
│  ├─ View Lembur .......................... [lembur.view_own]
│  ├─ Statistics ........................... [lembur.view_own]
│  └─ History .............................. [lembur.view_own]
├─ My Salary
│  ├─ View Slip ............................ [gaji.view_own]
│  ├─ Salary Summary ....................... [gaji.view_own]
│  ├─ YTD Report ........................... [gaji.view_own]
│  ├─ Download Slip ........................ [gaji.print_slip]
│  └─ FAQ .................................. Always visible
└─ Logout
   └─ Logout ............................... Always visible
```

---

## 🔄 ALUR KERJA PENGGAJIAN

### End-to-End Payroll Processing Workflow

#### FASE 1: PERSIAPAN DATA (T = Hari Kerja Pertama Bulan)

```
START: Awal Bulan
│
├─ [SUPER ADMIN/OFFICER] Setup Periode Penggajian
│  ├─ Define Periode (Month/Year)
│  ├─ Lock Absensi Cut-off Date
│  ├─ Lock Lembur Cut-off Date
│  ├─ Define Payroll Deadlines
│  └─ Send Notification: "Payroll Period Start"
│
├─ [OFFICER] Input & Validate Absensi
│  ├─ STEP 1: Input Absensi per Pegawai (by date)
│  │  ├─ Select Pegawai (Dept-based filter)
│  │  ├─ Input Status (H/S/I/L/C/A per hari)
│  │  ├─ Add Catatan jika ada yang tidak sesuai
│  │  └─ Status: DRAFT
│  │
│  ├─ STEP 2: Review Total
│  │  ├─ Calculate Total Hadir/Libur/Cuti/Sakit
│  │  ├─ Check Anomali (e.g., terlalu banyak cuti)
│  │  └─ Status: PENDING APPROVAL
│  │
│  └─ STEP 3: Approval
│     ├─ Review per Pegawai atau Batch
│     ├─ Add Approval Note (Optional)
│     └─ Status: APPROVED (Locked untuk payroll use)
│
├─ [OFFICER] Input & Validate Lembur
│  ├─ STEP 1: Input Lembur per Pegawai
│  │  ├─ Select Pegawai (Dept-based filter)
│  │  ├─ Input Tanggal, Jam Start-End
│  │  ├─ System Auto-Calculate: Jam Lembur
│  │  ├─ Select Jenis Lembur (Weekday/Weekend/Holiday)
│  │  ├─ Add Aktivitas/Deskripsi
│  │  └─ Status: DRAFT
│  │
│  ├─ STEP 2: Review Total
│  │  ├─ Calculate Total Jam Lembur
│  │  ├─ Estimate Lembur Cost
│  │  ├─ Check Anomali (e.g., overtime limit exceeded)
│  │  └─ Status: PENDING APPROVAL
│  │
│  └─ STEP 3: Approval
│     ├─ Review per Pegawai atau Batch
│     ├─ Approve atau Reject with Reason
│     ├─ Add Approval Note
│     └─ Status: APPROVED (Ready for payroll)
│
└─ NOTIFICATION: "Absensi & Lembur Approval Complete"
```

#### FASE 2: PERHITUNGAN GAJI (T + 3 hari = hari cut-off)

```
PROCESS: Calculation Phase
│
├─ [SUPER ADMIN] Verify Input Data
│  ├─ All Absensi APPROVED ✓
│  ├─ All Lembur APPROVED ✓
│  ├─ All Component masters updated ✓
│  └─ PTKP rates current ✓
│
├─ [SUPER ADMIN] Execute Salary Calculation (BATCH PROCESS)
│  │
│  ├─ OPTION A: Per Departemen
│  │  ├─ Select Department(s)
│  │  └─ Click "Calculate Salary"
│  │
│  └─ OPTION B: All Employees
│     ├─ Select "All Departments"
│     └─ Click "Calculate Salary (Batch)"
│
├─ SYSTEM: Auto-Calculate Each Employee
│  │
│  ├─ For Each Pegawai:
│  │  │
│  │  ├─ STEP 1: BASE SALARY
│  │  │  └─ Gaji Pokok
│  │  │
│  │  ├─ STEP 2: CALCULATE DEDUCTION FOR ABSENCES
│  │  │  ├─ Total Hari Kerja (dari Jadwal Kerja)
│  │  │  ├─ Total Hadir (dari Absensi APPROVED)
│  │  │  ├─ Calculate Potongan Absensi
│  │  │  │  └─ Rumus: (Bolos × Gaji Pokok / Hari Kerja)
│  │  │  └─ Result: Adjusted Salary
│  │  │
│  │  ├─ STEP 3: ADD ALLOWANCES (TUNJANGAN)
│  │  │  ├─ Fetch Tunjangan yang assigned ke Pegawai
│  │  │  ├─ Add Tunjangan Tetap (Makan, Transport, dll)
│  │  │  ├─ Add Tunjangan Tidak Tetap (Bonus, Insentif)
│  │  │  └─ Result: Sum Tunjangan
│  │  │
│  │  ├─ STEP 4: CALCULATE OVERTIME PAY
│  │  │  ├─ Fetch Approved Lembur
│  │  │  ├─ For Each Lembur Entry:
│  │  │  │  ├─ Get Jam Lembur
│  │  │  │  ├─ Get Jenis Lembur (Coeff: 1x, 1.5x, 2x, 2.5x)
│  │  │  │  ├─ Calculate: (Jam × Coeff × (Gaji Pokok / 160 jam))
│  │  │  │  └─ Add to Total Lembur
│  │  │  └─ Result: Total Overtime Payment
│  │  │
│  │  ├─ STEP 5: CALCULATE GROSS SALARY
│  │  │  └─ = Adjusted Salary + Tunjangan + Lembur
│  │  │
│  │  ├─ STEP 6: CALCULATE DEDUCTIONS
│  │  │  ├─ Fetch Potongan yang assigned ke Pegawai
│  │  │  ├─ Jamsostek (if configured)
│  │  │  ├─ Cicilan/Pinjaman (if any)
│  │  │  ├─ Other Deductions
│  │  │  └─ Result: Sum Potongan
│  │  │
│  │  ├─ STEP 7: CALCULATE PPh 21 (INCOME TAX)
│  │  │  ├─ Get PTKP Status (dari Pegawai)
│  │  │  ├─ Calculate Taxable Income = Gross - PTKP
│  │  │  ├─ Apply Tax Rate (5%, 15%, 25%, 30%)
│  │  │  └─ Result: PPh 21 Amount
│  │  │
│  │  ├─ STEP 8: CALCULATE NET SALARY
│  │  │  └─ = Gross Salary - Potongan - PPh 21
│  │  │
│  │  └─ STEP 9: STORE CALCULATION RESULT
│  │     ├─ Save to penggajian table
│  │     ├─ Status: DRAFT
│  │     ├─ Timestamp: calculation_date
│  │     └─ Store all detail (component breakdown)
│  │
│  └─ Progress: Show calculation progress bar
│
├─ SYSTEM: Generate Summary Report
│  ├─ Total Pegawai Calculated: XXX
│  ├─ Total Gaji Bruto: Rp XXX.XXX.XXX
│  ├─ Total Tunjangan: Rp XXX.XXX.XXX
│  ├─ Total Potongan: Rp XXX.XXX.XXX
│  ├─ Total PPh 21: Rp XXX.XXX.XXX
│  └─ Total Gaji Netto: Rp XXX.XXX.XXX
│
├─ RESULTS STATUS
│  ├─ Status: CALCULATED / DRAFT
│  ├─ Ready for: Approval & Review
│  └─ NOTIFICATION: "Salary Calculation Complete - Ready for Approval"
│
└─ DISPLAY: Summary + Option to Review & Approve
```

#### FASE 3: REVIEW & APPROVAL (T + 4-5 hari)

```
PROCESS: Approval Phase
│
├─ [SUPER ADMIN] Review Salary Calculation
│  │
│  ├─ STEP 1: View Draft Calculation List
│  │  ├─ Filter by Status (DRAFT)
│  │  ├─ Filter by Department (Optional)
│  │  └─ Sort by Nama, Status
│  │
│  ├─ STEP 2: View Detail Per Employee (Sampling/Full)
│  │  ├─ Pegawai Info
│  │  ├─ Salary Breakdown:
│  │  │  ├─ Gaji Pokok
│  │  │  ├─ Tunjangan (List detail)
│  │  │  ├─ Lembur (Jam & Total)
│  │  │  ├─ Potongan (List detail)
│  │  │  ├─ PPh 21
│  │  │  └─ Gaji Netto
│  │  │
│  │  ├─ Compare with Previous Month
│  │  ├─ Check for Anomalies (Flag if found)
│  │  └─ Add Review Note (Optional)
│  │
│  ├─ STEP 3: Check for Data Issues
│  │  ├─ Salary increase > 10%? → Flag
│  │  ├─ Salary decrease > 10%? → Flag
│  │  ├─ Extreme overtime? → Flag
│  │  ├─ Missing data? → Flag
│  │  └─ Manual override by admin? → Flag
│  │
│  ├─ STEP 4: Approve
│  │  ├─ Option A: Approve Single
│  │  │  ├─ View Detail
│  │  │  ├─ Click "Approve"
│  │  │  ├─ Add Note (Optional)
│  │  │  └─ Confirm
│  │  │
│  │  ├─ Option B: Batch Approve (with confirmation)
│  │  │  ├─ Select Multiple (Checkbox)
│  │  │  ├─ Click "Batch Approve"
│  │  │  ├─ Final Review (Show Summary)
│  │  │  └─ Confirm Approval
│  │  │
│  │  └─ Status Update: DRAFT → APPROVED
│  │     ├─ Timestamp: approval_date
│  │     ├─ User: approved_by
│  │     ├─ Create Audit Log Entry
│  │     └─ NOTIFICATION: "Salary Approved - [Employee Name]"
│  │
│  └─ STEP 5: Reject (if Issue Found)
│     ├─ Provide Rejection Reason
│     ├─ Add Note with Details
│     ├─ Status Update: DRAFT → REJECTED
│     ├─ Create Audit Log Entry
│     └─ NOTIFICATION: "Salary Calculation Rejected - Please Recalculate"

│
├─ [NOTIFY OFFICER]
│  ├─ If Own Dept Salary Approved: "Gaji Departemen Anda Sudah Approved"
│  └─ If Data Issues: "Data Issues Found in Gaji - Please Review"
│
└─ NEXT: Posting to Payroll
```

#### FASE 4: POSTING PAYROLL (T + 6 hari)

```
PROCESS: Posting Phase
│
├─ [SUPER ADMIN] Final Check Before Posting
│  ├─ All Salary APPROVED ✓
│  ├─ No Rejected/Draft remaining ✓
│  ├─ Total Gaji reasonable ✓
│  ├─ Budget not exceeded ✓
│  └─ Ready to POST
│
├─ [SUPER ADMIN] Post Salary to Payroll
│  │
│  ├─ OPTION A: Batch Post All
│  │  ├─ Click "Post All Salaries"
│  │  ├─ Final Confirmation (Show Summary)
│  │  │  ├─ Count: XXX employees
│  │  │  ├─ Total: Rp XXX.XXX.XXX
│  │  │  └─ Action: CANNOT UNDO AFTER POSTING
│  │  │
│  │  ├─ Confirm: "Yes, Post to Payroll"
│  │  │
│  │  └─ SYSTEM: Process All
│  │
│  └─ OPTION B: Manual Review then Post
│     ├─ Review Each Employee (Final Check)
│     ├─ Status: APPROVED → POSTED
│     └─ Lock Calculation (Cannot Edit)
│
├─ SYSTEM: Update Status
│  ├─ Change Status: APPROVED → POSTED
│  ├─ Lock Record (No Edit/Delete allowed)
│  ├─ Set Posting Date/Time
│  ├─ Create Posting Audit Log
│  ├─ Mark Absensi & Lembur as: LOCKED FOR PAYROLL
│  └─ Generate Posting Report
│
├─ POST-POSTING ACTIONS
│  ├─ Generate Payslip (PDF) for All Employees
│  ├─ Archive to Document Management
│  ├─ Prepare for Bank Transfer (if auto-transfer setup)
│  ├─ Export to Accounting System (if integrated)
│  ├─ Send Notification to All Employees
│  └─ Send Report to Management
│
└─ RESULTS
   ├─ Status: POSTED (Final)
   ├─ Notification to Employees: "Gaji Bulan Ini Sudah Tersedia - Check Payslip"
   ├─ Notification to Officer: "Payroll Posted - Final Report Ready"
   └─ Notification to Admin: "Payroll Posting Complete"
```

#### FASE 5: DISTRIBUSI SLIP GAJI (T + 7 hari)

```
PROCESS: Payslip Distribution
│
├─ [SUPER ADMIN/OFFICER] Print & Distribute Slip
│  │
│  ├─ OPTION A: Email Slip to Employees
│  │  ├─ Generate PDF Slip for All
│  │  ├─ Add Email Template
│  │  ├─ Send via Email Gateway
│  │  ├─ Tracking: Sent, Delivered, Opened
│  │  └─ Store Sent History
│  │
│  └─ OPTION B: Print Physical Slip
│     ├─ Generate PDF Slip
│     ├─ Print via Printer
│     ├─ Batch Print (All Dept)
│     ├─ Track Print History
│     └─ Option: Deliver Manual
│
├─ [EMPLOYEE] Access Payslip
│  ├─ Login to Dashboard
│  ├─ Go to: "My Salary → Slip Gaji"
│  ├─ View Payslip (Posted Only)
│  ├─ Download PDF
│  ├─ Print
│  └─ View Salary Details
│
└─ ARCHIVE
   ├─ Store Digital Copy in Document Management
   ├─ Set Retention Policy (e.g., 7 years)
   ├─ Secure Backup
   └─ Easy Retrieval for Future Reference
```

#### FASE 6: CLOSING & REPORTING (T + 8 hari)

```
PROCESS: Closing & Reporting
│
├─ [SUPER ADMIN] Generate Payroll Reports
│  ├─ Payroll Summary Report
│  ├─ Salary by Department
│  ├─ Salary Distribution Analysis
│  ├─ Tax Report (PPh 21 Summary)
│  ├─ Component Analysis (Tunjangan/Potongan)
│  ├─ Budget vs Actual Report
│  ├─ Variance Analysis
│  └─ YTD Report
│
├─ [SUPER ADMIN] Export for External Systems
│  ├─ Export for Bank Transfer (if needed)
│  ├─ Export for Accounting System
│  ├─ Export for Tax Reporting
│  ├─ Export for Government (Jamsostek, PPh)
│  └─ Secure File Transfer
│
├─ [SUPER ADMIN] Archive & Lock Month
│  ├─ Lock Absensi - Cannot Edit
│  ├─ Lock Lembur - Cannot Edit
│  ├─ Lock Penggajian - Cannot Edit/Delete
│  ├─ Generate Completion Report
│  └─ Notification: "Payroll Month [YYYY-MM] CLOSED"
│
└─ END OF CYCLE ✓
   ├─ Ready for Next Month
   ├─ Historical Data Archived
   ├─ Audit Trail Complete
   └─ System Ready for New Payroll Period
```

### Workflow Status Flow Diagram

```
┌─────────────────────────────────────────┐
│  INPUT DATA (Absensi & Lembur)          │
│  Officer Input + Approval               │
└──────────────┬──────────────────────────┘
               │
               ▼
┌─────────────────────────────────────────┐
│  DATA PREPARATION                       │
│  Verify All Data Complete & Approved    │
└──────────────┬──────────────────────────┘
               │
               ▼
┌─────────────────────────────────────────┐
│  SALARY CALCULATION (DRAFT)             │
│  System Auto-Calculate All Components   │
└──────────────┬──────────────────────────┘
               │
               ▼
┌─────────────────────────────────────────┐
│  APPROVAL (by Super Admin)              │
│  Review & Approve / Reject              │
└──────────────┬──────────────────────────┘
               │
               ▼
┌─────────────────────────────────────────┐
│  POSTING (to Payroll)                   │
│  Lock & Finalize Calculation            │
└──────────────┬──────────────────────────┘
               │
               ▼
┌─────────────────────────────────────────┐
│  SLIP DISTRIBUTION                      │
│  Generate & Send Payslip to Employee    │
└──────────────┬──────────────────────────┘
               │
               ▼
┌─────────────────────────────────────────┐
│  REPORTING & ARCHIVING                  │
│  Generate Reports & Lock Month          │
└──────────────┬──────────────────────────┘
               │
               ▼
            ✓ COMPLETE
```

---

## 💻 IMPLEMENTASI TECHNICAL

### A. Database Schema (Updated Fields)

#### User Guards Multi-Tenancy
```sql
-- For Laravel Multi-Guard Setup
-- app/Http/Middleware/Authenticate.php should route to different models

users table (for administrator guard):
  - id
  - email
  - password
  - id_role (FK to role.id_role)
  - status (active/inactive)
  - last_login
  - timestamps

officers table (for officer guard):
  - id
  - email
  - password
  - id_departemen (FK to departemen.id_departemen) ← KEY for department filtering
  - id_role (FK to role.id_role)
  - status (active/inactive)
  - last_login
  - timestamps

students table (for student/employee guard):
  - id
  - id_pegawai (FK to pegawai.id_pegawai) ← KEY for employee linking
  - email
  - password
  - id_role (FK to role.id_role) ← Usually fixed to "Pegawai" role
  - status (active/inactive)
  - last_login
  - timestamps
```

#### Permission-Based Visibility

```sql
-- Query untuk Officer (Department-based filtering)
SELECT gaji.* FROM penggajian gaji
JOIN pegawai p ON gaji.id_pegawai = p.id_pegawai
WHERE p.id_departemen = (
    SELECT id_departemen FROM officers 
    WHERE id = auth()->id()
);

-- Query untuk Employee (Self-only filtering)
SELECT gaji.* FROM penggajian gaji
WHERE gaji.id_pegawai = (
    SELECT id_pegawai FROM students 
    WHERE id = auth()->id()
);
```

### B. Permission Checking Implementation

#### Middleware Setup
```php
// routes/web.php
Route::middleware(['auth:administrator', 'permission:gaji.approve'])
    ->group(function () {
        Route::post('/penggajian/{id}/approve', [PenggajianController::class, 'approve']);
    });

// routes/officer.php
Route::middleware(['auth:officer', 'permission:absensi.approve', 'department:own'])
    ->group(function () {
        Route::post('/absensi/{id}/approve', [AbsensiController::class, 'approve']);
    });
```

#### Custom Middleware for Department Filtering
```php
// app/Http/Middleware/DepartmentFilter.php
public function handle($request, Closure $next, $scope = 'own')
{
    if ($scope === 'own') {
        // Officer dapat hanya akses dept sendiri
        $officer = auth()->user();
        $request->merge(['department_id' => $officer->id_departemen]);
    }
    
    return $next($request);
}
```

### C. Audit Trail Implementation

```php
// Log setiap action penting
Event::listen([
    'penggajian.calculated' => fn($event) => Log::info('Salary Calculated', $event),
    'penggajian.approved' => fn($event) => Log::info('Salary Approved', $event),
    'penggajian.posted' => fn($event) => Log::info('Salary Posted', $event),
    'absensi.approved' => fn($event) => Log::info('Attendance Approved', $event),
    'lembur.approved' => fn($event) => Log::info('Overtime Approved', $event),
]);

// Store in activity_log table
Schema::create('activity_logs', function(Blueprint $table) {
    $table->id();
    $table->unsignedBigInteger('user_id');
    $table->string('action'); // created, updated, deleted, approved
    $table->string('model'); // penggajian, absensi, lembur
    $table->unsignedBigInteger('model_id');
    $table->json('changes'); // old, new values
    $table->text('notes')->nullable();
    $table->timestamps();
});
```

---

## 🚀 BEST PRACTICE PRODUCTION-READY

### 1. Security & Access Control

**✅ IMPLEMENTASI KETAT:**
- [ ] Force password complexity (Min 8 char, uppercase, number, symbol)
- [ ] Implement 2FA (Two-Factor Authentication) untuk Super Admin
- [ ] Session timeout (15 min inactivity)
- [ ] IP Whitelist option untuk Super Admin
- [ ] Prevent Concurrent Login (1 session per user)
- [ ] Rate limiting on login endpoint (5 attempt → lockout 15 min)
- [ ] Password reset via email verification
- [ ] Audit log EVERY action (Create, Update, Delete, Approve)
- [ ] Encrypted sensitive data (Bank account, NPWP)
- [ ] HTTPS only (No HTTP)

### 2. Data Validation & Integrity

**✅ FORM VALIDATION:**
- [ ] Server-side validation (NEVER trust client)
- [ ] Business logic validation (e.g., salary cannot be negative)
- [ ] Data consistency check (Absence sum ≠ holidays)
- [ ] Referential integrity (Foreign key constraints)
- [ ] Transaction-based operations (All-or-nothing)

**✅ CALCULATION VERIFICATION:**
- [ ] Unit test semua perhitungan gaji
- [ ] Compare calculated salary dengan manual verification (sampling)
- [ ] Alert jika deviation > X% dari expected
- [ ] Store calculation metadata (formula used, rate applied)

### 3. User Experience (UX)

**✅ DASHBOARD DESIGN:**
- [ ] Minimize clicks to target action (3-click rule max)
- [ ] Clear status indicators (Colors: Green=Active, Red=Alert, Yellow=Pending)
- [ ] Responsive design (Mobile + Tablet + Desktop)
- [ ] Accessibility (WCAG 2.1 AA standard)
- [ ] Dark mode option
- [ ] Help tooltips on complex fields
- [ ] Breadcrumb navigation

**✅ DATA DISPLAY:**
- [ ] Pagination (50/100/250 rows per page option)
- [ ] Search & filter capability
- [ ] Sorting (Multi-column sort)
- [ ] Export (CSV/Excel/PDF)
- [ ] Print-friendly view
- [ ] Data consistency across pages

### 4. Error Handling & Notifications

**✅ ERROR MESSAGES:**
- [ ] User-friendly error messages (NOT technical jargon)
- [ ] Clear indication of what went wrong
- [ ] Suggestions for fix
- [ ] Error logging (Admin dapat track issues)
- [ ] Graceful degradation (App tidak crash)

**✅ NOTIFICATIONS:**
- [ ] Email notification untuk action penting
- [ ] In-app notifications (Bell icon with count)
- [ ] SMS alerts untuk urgent (Optional)
- [ ] Notification preferences per user
- [ ] Rich email templates (HTML, branded)

### 5. Performance Optimization

**✅ BACKEND:**
- [ ] Implement caching (Redis untuk frequently accessed data)
- [ ] Optimize database query (N+1 prevention)
- [ ] Indexing pada frequently filtered columns
- [ ] Background job untuk batch processing (Queue system)
- [ ] API rate limiting

**✅ FRONTEND:**
- [ ] Minify CSS/JS
- [ ] Image optimization
- [ ] Lazy loading
- [ ] Code splitting
- [ ] CDN untuk static assets

### 6. Data Privacy & Compliance

**✅ GDPR/LOCAL LAW:**
- [ ] Data retention policy
- [ ] Right to be forgotten (Data deletion)
- [ ] Data export capability
- [ ] Privacy policy clear & accessible
- [ ] User consent for data collection

**✅ TAX & COMPLIANCE:**
- [ ] PPh 21 calculation sesuai regulasi terbaru
- [ ] PTKP rate update mechanism
- [ ] Tax report export (untuk BPS/DJP)
- [ ] Jamsostek calculation (if applicable)
- [ ] Maintain historical records (7 years minimum)

### 7. Backup & Disaster Recovery

**✅ BACKUP STRATEGY:**
- [ ] Daily automated backup
- [ ] Backup to different location (not same server)
- [ ] Regular restore test (quarterly)
- [ ] Backup encryption
- [ ] Version control (Keep multiple snapshots)
- [ ] Disaster recovery plan documented

### 8. Reporting & Analytics

**✅ REPORTS:**
- [ ] Pre-built standard reports (Salary summary, Tax report, Budget variance)
- [ ] Custom report builder untuk power users
- [ ] Scheduled reports (Auto-generate, auto-email)
- [ ] Export in multiple formats
- [ ] Report audit trail (Who generated, When)
- [ ] Data visualization (Charts, graphs)

### 9. Testing

**✅ TEST COVERAGE:**
- [ ] Unit tests (Business logic: calculation, validation)
- [ ] Integration tests (Database + API)
- [ ] End-to-end tests (Full flow: input → output)
- [ ] Performance tests (Load testing)
- [ ] Security tests (Penetration testing)
- [ ] Regression tests (Before production release)

### 10. Documentation & Training

**✅ DOCUMENTATION:**
- [ ] API documentation (Swagger/OpenAPI)
- [ ] User manual per role (Super Admin, Officer, Employee)
- [ ] System architecture documentation
- [ ] Database schema documentation
- [ ] Troubleshooting guide
- [ ] FAQ

**✅ TRAINING:**
- [ ] Video tutorial per role
- [ ] Live training session
- [ ] Quick reference card (Downloadable PDF)
- [ ] Helpdesk contact info
- [ ] Regular updates (Newsletter)

### 11. Monitoring & Alerts

**✅ SYSTEM MONITORING:**
- [ ] Uptime monitoring (99.9% target)
- [ ] Server health check (CPU, Memory, Disk)
- [ ] Database monitoring (Query performance, size)
- [ ] Error rate monitoring
- [ ] Alert notification (Email/SMS untuk critical)
- [ ] Dashboard untuk monitoring

### 12. Version & Release Management

**✅ DEPLOYMENT:**
- [ ] Semantic versioning (v1.0.0)
- [ ] Release notes (Changelog)
- [ ] Staging environment untuk testing
- [ ] Blue-green deployment (Zero downtime)
- [ ] Rollback capability
- [ ] Database migration strategy

---

## 📋 CHECKLIST IMPLEMENTASI

### Phase 1: Foundation (Week 1-2)
- [ ] Update database schema (Add required fields)
- [ ] Implement User Guards (administrator, officer, student)
- [ ] Setup Role-Permission System
- [ ] Create Middleware (Permission check, Department filter)
- [ ] Update Route Guards
- [ ] Implement Audit Trail

### Phase 2: Menu & UI (Week 3-4)
- [ ] Create Menu Structure (per role)
- [ ] Build Dashboard (per role)
- [ ] Implement Permission-based Menu Visibility
- [ ] Create Navigation Components
- [ ] Implement Sidebar Component
- [ ] Mobile responsive design

### Phase 3: Core Features (Week 5-8)
- [ ] Implement Absensi Management (Officer approve)
- [ ] Implement Lembur Management (Officer approve)
- [ ] Implement Salary Calculation (Super Admin)
- [ ] Implement Salary Approval Workflow
- [ ] Implement Payslip Generation
- [ ] Implement Reports & Export

### Phase 4: Security & Testing (Week 9-10)
- [ ] Implement 2FA
- [ ] Security audit (Penetration testing)
- [ ] Unit testing
- [ ] Integration testing
- [ ] Performance testing
- [ ] User acceptance testing (UAT)

### Phase 5: Deployment & Training (Week 11-12)
- [ ] Staging deployment
- [ ] Data migration from old system
- [ ] Staff training
- [ ] Documentation finalization
- [ ] Production deployment
- [ ] Post-launch support

---

## 📞 SUPPORT & MAINTENANCE

### Monitoring & Support Schedule
- **Operational Hours**: Mon-Fri 08:00-17:00
- **On-Call**: For critical issues 24/7
- **Regular Maintenance**: Every Sunday 02:00-04:00
- **Backup**: Daily at 23:00

### Escalation Procedure
1. **Level 1**: Help-Desk (General admin questions)
2. **Level 2**: System Admin (Technical configuration)
3. **Level 3**: Development Team (Code issues, customization)

---

**Document Version**: 2.0  
**Last Updated**: February 2026  
**Status**: READY FOR IMPLEMENTATION  
**Approval**: ✓ Approved untuk Production Release

