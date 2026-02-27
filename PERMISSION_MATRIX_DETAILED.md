# PERMISSION MATRIX - DETAILED REFERENCE

## 📊 COMPREHENSIVE PERMISSION TABLE (ALL ROLES)

```
ROLE                 | SUPER ADMIN | OFFICER | EMPLOYEE
─────────────────────┼─────────────┼─────────┼──────────
```

### 🏠 DASHBOARD

| Feature | Super Admin | Officer | Employee | Notes |
|---------|:----------:|:-------:|:--------:|-------|
| Dashboard Access | ✓ | ✓ | ✓ | Each role sees their own dashboard |
| View Metrics | ✓ (All) | ✓ (Own Dept) | ✓ (Personal) | Data scoped by role |
| System Health | ✓ | ✗ | ✗ | Admin only |
| Activity Log | ✓ | ✗ | ✗ | Admin only |

---

### 👥 USER MANAGEMENT

| Feature | Super Admin | Officer | Employee | Notes |
|---------|:----------:|:-------:|:--------:|-------|
| View All Users | ✓ | ✗ | ✗ | Multiple guards (admin, officer, employee) |
| Create User | ✓ | ✗ | ✗ | Assign role & guard |
| Edit User | ✓ | ✗ | ✗ | Update profile, role, status |
| Delete User | ✓ | ✗ | ✗ | Soft delete for audit trail |
| Assign Role | ✓ | ✗ | ✗ | Assign from available roles |
| Reset Password | ✓ | ✗ | ✗ | Send reset email to user |
| View User Activity | ✓ | ✗ | ✗ | Track user actions |
| Lock/Unlock Account | ✓ | ✗ | ✗ | For security/discipline |

---

### 🔑 ROLE MANAGEMENT

| Feature | Super Admin | Officer | Employee | Notes |
|---------|:----------:|:-------:|:--------:|-------|
| View All Roles | ✓ | ✗ | ✗ | 3 Main: Super Admin, Petugas, Pegawai |
| Create New Role | ✓ | ✗ | ✗ | For future expansion |
| Edit Role | ✓ | ✗ | ✗ | Update name, description |
| Delete Role | ✓ | ✗ | ✗ | Only if not in use |
| Assign Permissions | ✓ | ✗ | ✗ | Bulk assign or per-permission |
| View Permissions | ✓ | ✗ | ✗ | See permission for each role |

---

### 🛡️ PERMISSION MANAGEMENT

| Feature | Super Admin | Officer | Employee | Notes |
|---------|:----------:|:-------:|:--------:|-------|
| View All Permissions | ✓ | ✗ | ✗ | Grouped by category |
| Create Permission | ✓ | ✗ | ✗ | For system customization |
| Edit Permission | ✓ | ✗ | ✗ | Update name, description |
| Delete Permission | ✓ | ✗ | ✗ | Only if not assigned to roles |
| View Permission Usage | ✓ | ✗ | ✗ | Which roles have this permission |

---

### 👤 PEGAWAI (EMPLOYEE DATA)

| Feature | Super Admin | Officer | Employee | Notes |
|---------|:----------:|:-------:|:--------:|-------|
| View All Pegawai | ✓ | ✗ | ✗ | All employees in system |
| View Dept Pegawai | ✓ | ✓ | ✗ | Officer: Own departemen only |
| View Own Profile | ✓ | ✓ | ✓ | Everyone can view own |
| Create Pegawai | ✓ | ✗ | ✗ | New employee entry |
| Edit Pegawai | ✓ | ✗ | ✗ | Update employee data |
| Delete Pegawai | ✓ | ✗ | ✗ | Soft-delete, retain history |
| Edit Own Profile | ✓ | ✓ | ✓ | Limited: Phone, Email, Address |
| View Employment History | ✓ | ✓ | ✓ | Starting date, status changes |

---

### 🏢 DEPARTEMEN

| Feature | Super Admin | Officer | Employee | Notes |
|---------|:----------:|:-------:|:--------:|-------|
| View All Departemen | ✓ | ✗ | ✗ | Full list |
| View Own Departemen | ✓ | ✓ | ✗ | Officer: Read-only |
| Create Departemen | ✓ | ✗ | ✗ | New department |
| Edit Departemen | ✓ | ✗ | ✗ | Update dept info |
| Delete Departemen | ✓ | ✗ | ✗ | Check for dependencies |
| View Dept Members | ✓ | ✓ | ✗ | Officer: Own dept, Employee: Can see own dept |
| Dept Hierarchy | ✓ | ✓ | ✗ | Organizational structure |

---

### 💼 JABATAN (POSITION/TITLE)

| Feature | Super Admin | Officer | Employee | Notes |
|---------|:----------:|:-------:|:--------:|-------|
| View All Jabatan | ✓ | ✓ | ✗ | Read-only for Officer |
| Create Jabatan | ✓ | ✗ | ✗ | New position title |
| Edit Jabatan | ✓ | ✗ | ✗ | Update position details |
| Delete Jabatan | ✓ | ✗ | ✗ | Check employees assigned |
| View Gaji Template | ✓ | ✓ | ✗ | Reference salary for position |

---

### 🎁 TUNJANGAN (ALLOWANCES)

| Feature | Super Admin | Officer | Employee | Notes |
|---------|:----------:|:-------:|:--------:|-------|
| View All Tunjangan | ✓ | ✓ | ✗ | Read-only for Officer |
| View Types | ✓ | ✓ | ✗ | Tetap (Fixed), Tidak Tetap (Variable) |
| Create Tunjangan | ✓ | ✗ | ✗ | New allowance template |
| Edit Tunjangan | ✓ | ✗ | ✗ | Update allowance details |
| Delete Tunjangan | ✓ | ✗ | ✗ | Archive if historical data |
| Assign to Employee | ✓ | ✗ | ✗ | Pegawai-Tunjangan mapping |
| Bulk Assign | ✓ | ✗ | ✗ | Multiple employees same allowance |
| See on Payslip | ✓ | ✓ | ✓ | View breakdown in salary |

---

### ✂️ POTONGAN (DEDUCTIONS)

| Feature | Super Admin | Officer | Employee | Notes |
|---------|:----------:|:-------:|:--------:|-------|
| View All Potongan | ✓ | ✓ | ✗ | Read-only for Officer |
| View Types | ✓ | ✓ | ✗ | Tax, Insurance, Loan, Fines |
| Create Potongan | ✓ | ✗ | ✗ | New deduction template |
| Edit Potongan | ✓ | ✗ | ✗ | Update deduction details |
| Delete Potongan | ✓ | ✗ | ✗ | Archive if historical |
| Assign to Employee | ✓ | ✗ | ✗ | Pegawai-Potongan mapping |
| Bulk Assign | ✓ | ✗ | ✗ | Multiple employees same deduction |
| See on Payslip | ✓ | ✓ | ✓ | View breakdown in salary |

---

### 📅 ABSENSI (ATTENDANCE)

| Feature | Super Admin | Officer | Employee | Notes |
|---------|:----------:|:-------:|:--------:|-------|
| **View** | | | | |
| View All Absensi | ✓ | ✗ | ✗ | All departments |
| View Absensi (Own Dept) | ✓ | ✓ | ✗ | Officer: Department-based |
| View Own Absensi | ✓ | ✓ | ✓ | Personal records |
| **Create/Edit** | | | | |
| Input Absensi | ✓ | ✓ | ✗ | Manual entry for data entry |
| Create Manual Entry | ✓ | ✓ | ✗ | Correction/missing entry |
| Edit Absensi (Draft) | ✓ | ✓ | ✗ | Before approval |
| Edit Absensi (Approved) | ✗ | ✗ | ✗ | Locked for payroll |
| Delete Absensi (Draft) | ✓ | ✓ | ✗ | With audit trail |
| Delete Absensi (Approved) | ✓ | ✗ | ✗ | Super Admin only (emergency) |
| **Approval** | | | | |
| Approve Absensi | ✓ | ✓ | ✗ | Officer: Own dept |
| Reject Absensi | ✓ | ✓ | ✗ | With reason |
| Batch Approve | ✓ | ✓ | ✗ | Multiple records |
| **Other** | | | | |
| Request Correction | ✓ | ✓ | ✓ | Employee: Request fix |
| View Statistics | ✓ | ✓ | ✗ | Attendance rate, trends |
| Export Absensi | ✓ | ✓ | ✗ | CSV/Excel |
| Lock Absensi Period | ✓ | ✗ | ✗ | Prevent further edits |

---

### ⏱️ LEMBUR (OVERTIME)

| Feature | Super Admin | Officer | Employee | Notes |
|---------|:----------:|:-------:|:--------:|-------|
| **View** | | | | |
| View All Lembur | ✓ | ✗ | ✗ | All departments |
| View Lembur (Own Dept) | ✓ | ✓ | ✗ | Officer: Department-based |
| View Own Lembur | ✓ | ✓ | ✓ | Personal overtime records |
| **Create/Edit** | | | | |
| Input Lembur | ✓ | ✓ | ✗ | Manual entry |
| Create Entry | ✓ | ✓ | ✗ | Date, time, hours calc auto |
| Edit Lembur (Draft) | ✓ | ✓ | ✗ | Before approval |
| Edit Lembur (Pending) | ✓ | ✓ | ✗ | Awaiting approval |
| Delete Lembur (Draft) | ✓ | ✓ | ✗ | With audit trail |
| **Approval** | | | | |
| Approve Lembur | ✓ | ✓ | ✗ | Officer: Own dept |
| Reject Lembur | ✓ | ✓ | ✗ | With reason |
| Batch Approve | ✓ | ✓ | ✗ | Multiple records |
| **Other** | | | | |
| View Statistics | ✓ | ✓ | ✗ | Hours, trends, cost |
| Export Lembur | ✓ | ✓ | ✗ | CSV/Excel |
| Lock Lembur Period | ✓ | ✗ | ✗ | Prevent further entry |

---

### 💰 PENGGAJIAN (SALARY & PAYROLL)

| Feature | Super Admin | Officer | Employee | Notes |
|---------|:----------:|:-------:|:--------:|-------|
| **View** | | | | |
| View All Salary | ✓ | ✗ | ✗ | All employees |
| View Salary (Own Dept) | ✓ | ✓ | ✗ | Officer: Department-based |
| View Own Salary | ✓ | ✓ | ✓ | POSTED only for Employee |
| View Salary Status | ✓ | ✓ | ✗ | Draft/Calculated/Approved/Posted |
| **Calculation** | | | | |
| Calculate Single | ✓ | ✗ | ✗ | One employee |
| Calculate Batch | ✓ | ✗ | ✗ | All or by department |
| Recalculate | ✓ | ✗ | ✗ | If data changed |
| Manual Override | ✓ | ✗ | ✗ | Emergency case (logged) |
| **Approval** | | | | |
| Approve Single | ✓ | ✗ | ✗ | Review & approve salary |
| Approve Batch | ✓ | ✗ | ✗ | Multiple employees |
| Reject Salary | ✓ | ✗ | ✗ | Return to calculation |
| **Posting** | | | | |
| Post to Payroll | ✓ | ✗ | ✗ | Final, cannot undo |
| Lock Calculation | ✓ | ✗ | ✗ | Prevent editing |
| **Payslip** | | | | |
| Print Payslip | ✓ | ✓ | ✓ | PDF generation |
| View Payslip Detail | ✓ | ✓ | ✓ | Breakdown of salary |
| Email Payslip | ✓ | ✓ | ✓ | Send to employee |
| Download Payslip | ✓ | ✓ | ✓ | PDF download |
| Export Payslip (Batch) | ✓ | ✓ | ✗ | Officer: Own dept |
| **Analysis** | | | | |
| View Summary | ✓ | ✓ | ✗ | Total gaji, components |
| YTD Analysis | ✓ | ✓ | ✓ | Year-to-date metrics |
| Compare Month | ✓ | ✓ | ✓ | Previous month comparison |
| View Calculation Detail | ✓ | ✓ | ✓ | All components breakdown |

---

### 📊 LAPORAN (REPORTS)

| Feature | Super Admin | Officer | Employee | Notes |
|---------|:----------:|:-------:|:--------:|-------|
| **Attendance Reports** | | | | |
| Attendance Rate | ✓ | ✓ | ✗ | By dept, per employee |
| Absent Reasons | ✓ | ✓ | ✗ | Analysis of absence types |
| Tardiness Report | ✓ | ✓ | ✗ | Late arrivals data |
| Absence Trend | ✓ | ✓ | ✗ | Monthly trend visualization |
| **Overtime Reports** | | | | |
| Overtime by Employee | ✓ | ✓ | ✗ | Total hours per person |
| Overtime Cost Analysis | ✓ | ✓ | ✗ | Cost breakdown |
| Overtime Trend | ✓ | ✓ | ✗ | Monthly trend |
| **Salary Reports** | | | | |
| Salary Summary | ✓ | ✓ | ✗ | All employees |
| Salary by Department | ✓ | ✓ | ✗ | Officer: Own dept |
| Salary Range Analysis | ✓ | ✓ | ✗ | Min/Max/Average |
| Component Analysis | ✓ | ✓ | ✗ | Allowances & Deductions |
| Tax Report | ✓ | ✓ | ✗ | PPh 21 summary |
| **Financial Reports** | | | | |
| Monthly Payroll Cost | ✓ | ✓ | ✗ | Total cost per month |
| Budget vs Actual | ✓ | ✓ | ✗ | Variance analysis |
| Salary Cost Projection | ✓ | ✗ | ✗ | Forecast |
| **Export** | | | | |
| Export to Excel | ✓ | ✓ | ✗ | All reports |
| Export to CSV | ✓ | ✓ | ✗ | For system integration |
| Export to PDF | ✓ | ✓ | ✗ | For printing |

---

### ⚙️ SYSTEM & MAINTENANCE

| Feature | Super Admin | Officer | Employee | Notes |
|---------|:----------:|:-------:|:--------:|-------|
| **Configuration** | | | | |
| Company Info | ✓ | ✗ | ✗ | Company settings |
| Fiscal Year | ✓ | ✗ | ✗ | Year configuration |
| Payroll Period | ✓ | ✗ | ✗ | Monthly/bi-weekly setup |
| Tax Configuration | ✓ | ✗ | ✗ | PTKP rates, PPh settings |
| Working Hours | ✓ | ✗ | ✗ | Daily/weekly hours |
| **Security** | | | | |
| View Activity Log | ✓ | ✗ | ✗ | All system activities |
| View Login History | ✓ | ✗ | ✗ | User login records |
| IP Whitelist | ✓ | ✗ | ✗ | Restrict access IPs |
| Session Management | ✓ | ✗ | ✗ | Force logout, timeout |
| **Data Management** | | | | |
| Database Backup | ✓ | ✗ | ✗ | Manual backup |
| Restore Backup | ✓ | ✗ | ✗ | Disaster recovery |
| Data Export | ✓ | ✗ | ✗ | Full system export |
| Data Import | ✓ | ✗ | ✗ | Batch upload |
| **Notifications** | | | | |
| Email Configuration | ✓ | ✗ | ✗ | SMTP setup |
| Notification Template | ✓ | ✗ | ✗ | Email templates |
| Alert Rules | ✓ | ✗ | ✗ | When to send alerts |
| **Integration** | | | | |
| Bank Integration | ✓ | ✗ | ✗ | For salary transfer |
| Accounting System | ✓ | ✗ | ✗ | Export to accounting |
| Tax Reporting | ✓ | ✗ | ✗ | Government submission |

---

### 👨‍💼 PROFILE & PERSONAL

| Feature | Super Admin | Officer | Employee | Notes |
|---------|:----------:|:-------:|:--------:|-------|
| View Own Profile | ✓ | ✓ | ✓ | Personal data |
| Edit Own Profile | ✓ | ✓ | ✓ | Limited fields |
| Change Password | ✓ | ✓ | ✓ | Everyone |
| Set Preferences | ✓ | ✓ | ✓ | Language, theme, etc |
| Two-Factor Auth | ✓ | ✗ | ✗ | Admin only (recommended) |
| View Security Info | ✓ | ✓ | ✓ | Recent logins, devices |

---

## 🔒 ACCESS CONTROL RULES

### Data Filtering by Role

```
SUPER ADMIN (administrator guard)
├─ Sees: ALL data from all departments
├─ Can Edit: Any record
├─ Can Delete: Any record (with audit)
├─ Filter: None (full access)
└─ Department Scope: GLOBAL

OFFICER (officer guard)
├─ Sees: Data only from own department
├─ Can Edit: Own department data only
├─ Can Delete: Own department data only
├─ Filter: WHERE departemen.id_departemen = $officer->id_departemen
├─ Linked Table: officers.id_departemen
└─ Department Scope: OWN_DEPARTMENT

EMPLOYEE (student guard)
├─ Sees: Only own personal data
├─ Can Edit: Only own profile (limited)
├─ Can Delete: No delete access
├─ Filter: WHERE pegawai.id_pegawai = $employee->id_pegawai
├─ Linked Table: students.id_pegawai
└─ Department Scope: NONE (Self only)
```

### Query Patterns

```php
// SUPER ADMIN - No filter
$query = Absensi::query();

// OFFICER - Filter by department
$officer = auth('officer')->user();
$query = Absensi::whereHas('pegawai', function ($q) use ($officer) {
    $q->where('id_departemen', $officer->id_departemen);
});

// EMPLOYEE - Filter by self
$employee = auth('student')->user();
$query = Absensi::where('id_pegawai', $employee->id_pegawai);
```

---

## ✅ PERMISSION CHECKING CHECKLIST

### Before Every Data Display

```
[ ] User authenticated
[ ] User has view permission for this resource
[ ] Data filtered according to user's scope
[ ] Audit logging enabled
[ ] Response contains correct data only
```

### Before Every Edit/Delete Operation

```
[ ] User authenticated
[ ] User has edit/delete permission
[ ] User has access to this specific record
[ ] Record status allows editing (e.g., not POSTED)
[ ] Audit trail entry created
[ ] Confirmation required (if critical action)
[ ] Related data validation done
```

### Before Every Approval Operation

```
[ ] User authenticated
[ ] User has approve permission
[ ] Current status allows approval
[ ] Required data verified (Absensi before gaji, etc)
[ ] Status audit trail created
[ ] Notification sent to related users
[ ] Email confirmation logged
```

---

## 📝 IMPLEMENTATION NOTES

### For Developers

1. **Always use BaseController `applyDataScope()`**
   ```php
   $query = $this->applyDataScope(Absensi::query());
   ```

2. **Always check permission before action**
   ```php
   // In controller
   if (!auth()->user()->hasPermission('absensi.approve')) {
       abort(403);
   }
   ```

3. **Always log critical actions**
   ```php
   activity()
       ->performedOn($model)
       ->withProperties(['action' => 'approved'])
       ->log('Message');
   ```

4. **Test with all 3 roles**
   - Super Admin (should see all)
   - Officer (should see own dept only)
   - Employee (should see self only)

### For QA/Testing

1. **Test Role Access (Who can access what)**
   - [ ] Super Admin can access all
   - [ ] Officer cannot access other depts
   - [ ] Employee cannot access other employees

2. **Test Permission Verification (Can do what)**
   - [ ] User with permission: action succeeds
   - [ ] User without permission: action forbidden

3. **Test Data Filtering (See only what they should)**
   - [ ] Super Admin: all data
   - [ ] Officer: own dept data
   - [ ] Employee: own data

4. **Test Workflow Restrictions**
   - [ ] Cannot edit APPROVED records
   - [ ] Cannot delete POSTED records
   - [ ] Approval only possible from DRAFT/CALCULATED

---

**Reference Document**: Permission Matrix v2.0  
**Last Updated**: February 2026  
**Status**: Production Ready

