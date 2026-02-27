# QUICK REFERENCE - PRODUCTION DEPLOYMENT GUIDE

## 📋 DEPLOYMENT CHECKLIST

### Pre-Deployment (1 Week Before)

- [ ] **Database Backup**
  ```bash
  # Create full backup
  mysqldump -u root -p sistem_penggajian > backup_$(date +%Y%m%d).sql
  ```

- [ ] **Code Review**
  - [ ] Review all changes in feature branches
  - [ ] Run automated tests
  - [ ] Security audit
  - [ ] Performance testing

- [ ] **Staging Deployment**
  - [ ] Deploy to staging environment
  - [ ] Run UAT (User Acceptance Testing)
  - [ ] Test all role functionality
  - [ ] Test all workflows

- [ ] **Staff Training**
  - [ ] Super Admin training (2 hours)
  - [ ] Officer training (1 hour)
  - [ ] Employee training (30 mins)
  - [ ] Prepare support documentation

---

## 🚀 DEPLOYMENT DAY (Maintenance Window: 02:00-04:00)

### Step 1: Pre-Deployment Checks (00:30)

```bash
# Stop application
# Notify all users about maintenance

# Final backup
mysqldump -u root -p sistem_penggajian > final_backup_$(date +%Y%m%d_%H%M%S).sql

# Verify backup integrity
mysql -u root -p sistem_penggajian < final_backup_*.sql --test
```

### Step 2: Database Migrations (01:00)

```bash
# SSH to production server
cd /var/www/sistem-penggajian

# Pull latest code
git pull origin main

# Install dependencies
composer install --no-dev --optimize-autoloader

# Run migrations (if any)
php artisan migrate --force

# Run seeders (RoleAndPermissionSeeder)
php artisan db:seed --class=RoleAndPermissionSeeder --force
```

### Step 3: Cache & Configuration (01:30)

```bash
# Clear old caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Rebuild cache
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Optimize autoloader
composer dump-autoload --optimize --no-dev
```

### Step 4: Environment Setup (01:45)

```bash
# Verify .env configuration
cat .env | grep -E "APP_|DB_|MAIL_"

# Verify logs directory permissions
chmod -R 755 storage/logs

# Verify upload directory
chmod -R 755 storage/app/public

# Create symlink for storage
php artisan storage:link
```

### Step 5: Application Start (02:00)

```bash
# Restart queue workers (if any)
supervisorctl restart laravel-worker

# Restart PHP-FPM
sudo systemctl restart php-fpm

# Restart Web Server
sudo systemctl restart nginx

# Monitor logs
tail -f storage/logs/laravel.log
```

### Step 6: Post-Deployment Verification (02:30)

```bash
# Verify application is up
curl -I https://sistem-penggajian.com/administrator/dashboard

# Check error logs
tail -n 50 storage/logs/laravel.log

# Verify database connectivity
php artisan tinker
# Then test: DB::connection()->getPdo() ? 'OK' : 'FAILED'

# Verify permissions seeding
php artisan tinker
# Then test: App\Models\Role::count() # Should be 3
```

---

## ✅ VERIFICATION CHECKLIST (Post-Deployment)

### Authentication
- [ ] Super Admin dapat login
- [ ] Officer dapat login
- [ ] Employee dapat login
- [ ] Redirect to appropriate dashboard sesuai role

### Dashboard Visibility
- [ ] Super Admin dashboard menampilkan semua metrics
- [ ] Officer dashboard menampilkan hanya departemen mereka
- [ ] Employee dashboard menampilkan hanya data pribadi

### Menu Accessibility
- [ ] Super Admin sidebar: semua menu visible
- [ ] Officer sidebar: hanya menu sesuai akses
- [ ] Employee sidebar: hanya self-service menu
- [ ] Tidak ada "broken" menu links

### Feature Access
- [ ] Super Admin dapat input absensi untuk semua pegawai
- [ ] Officer dapat input absensi untuk departemen mereka SAJA
- [ ] Officer TIDAK dapat akses departemen lain
- [ ] Employee HANYA bisa lihat data pribadi
- [ ] Employee TIDAK bisa edit data orang lain

### Approval Workflow
- [ ] Officer dapat approve absensi (own dept)
- [ ] Officer dapat approve lembur (own dept)
- [ ] Officer TIDAK bisa approve gaji
- [ ] Super Admin dapat approve semua
- [ ] Approval status berubah dengan benar

### Payroll Process
- [ ] Super Admin dapat calculate gaji
- [ ] Calculation menggunakan data absensi yang sudah di-approve
- [ ] Calculation menggunakan data lembur yang sudah di-approve
- [ ] PPh 21 calculated correctly
- [ ] Payslip dapat di-print
- [ ] Employee dapat lihat slip gaji mereka

### Reports & Export
- [ ] Super Admin dapat generate semua reports
- [ ] Officer dapat generate report untuk departemen mereka
- [ ] Employee TIDAK bisa access reports
- [ ] Export functionality bekerja (CSV, Excel, PDF)
- [ ] Data exported dengan benar

### Audit & Logging
- [ ] Setiap action tercatat di activity log
- [ ] Approval tercatat dengan timestamp dan user
- [ ] Calculation tercatat dengan detail
- [ ] Posting tercatat permanent

### Security
- [ ] HTTPS is working
- [ ] Session timeout working (15 min)
- [ ] Password change working
- [ ] Error messages tidak expose system info
- [ ] Failed login logged

---

## 🔍 MONITORING (24/7 First Week)

### Metrics to Monitor

```bash
# Server Health
- CPU Usage
- Memory Usage
- Disk Space
- Database Connection Pool

# Application Health
- Response Time (target: <500ms)
- Error Rate (target: 0%)
- Active Users
- Request/sec

# Business Metrics
- Payroll Calculations processed
- Approvals pending
- System usage by role
```

### Alert Thresholds

```
🔴 CRITICAL (Alert immediately)
- Application down/unreachable
- Database connection error
- Disk space < 5%
- Error rate > 5%
- Response time > 5s

🟠 WARNING (Alert and investigate)
- CPU > 80%
- Memory > 85%
- Disk space < 20%
- Error rate > 1%
- Response time > 1s

🟡 INFO (Monitor)
- CPU > 60%
- Memory > 70%
```

### Support Contact

```
🆘 PRODUCTION SUPPORT HOTLINE
- Primary: [IT Manager] - +62-XXX-XXXX-XXXX
- Secondary: [IT Staff] - +62-XXX-XXXX-XXXX
- Email: support@company.com
- Call Center: 1500-500-500 (Ext. 123)

⏱️ RESPONSE TIME SLA
- Critical: 15 minutes
- High: 1 hour
- Medium: 4 hours
- Low: 1 business day
```

---

## 🔧 TROUBLESHOOTING QUICK GUIDE

### Problem: Officer dapat akses departemen lain

**Solution:**
```bash
# Check middleware application
grep -r "department.scope" routes/

# Verify query filter in controller
grep -r "id_departemen" app/Http/Controllers/Officer/

# Test with:
php artisan tinker
# $officer = App\Models\Officer::first();
# $officer->absensi() # should filter by department
```

### Problem: Employee dapat lihat slip gaji orang lain

**Solution:**
```bash
# Verify route protection
grep -r "permission:gaji.view_own" routes/

# Check controller filter
grep -r "getPegawaiIdFilter" app/Http/Controllers/Student/

# Test detail:
# Login as Employee
# Try direct URL: /student/penggajian/[other_employee_id]
# Should get 403 Unauthorized
```

### Problem: Calculation salah

**Solution:**
```bash
# Check SalaryCalculationService
cat app/Services/SalaryCalculationService.php

# Verify calculation detail
php artisan tinker
# $calculation = App\Models\Penggajian::first();
# $calculation->detail_breakdown # should show all components

# Compare with expected result
# Spot check dengan manual calculation

# If still wrong:
# 1. Backup current data
# 2. Delete calculation (if still DRAFT)
# 3. Recalculate with verified data
```

### Problem: Performance slow

**Solution:**
```bash
# Check slow queries
php artisan tinker
# DB::enableQueryLog();
# Run problematic action
# dd(DB::getQueryLog());

# Add indexes if needed
# ALTER TABLE absensi ADD INDEX idx_pegawai (id_pegawai);
# ALTER TABLE penggajian ADD INDEX idx_status (status);

# Clear cache
php artisan cache:clear

# Restart PHP-FPM
sudo systemctl restart php-fpm
```

### Problem: Permission denied error

**Solution:**
```bash
# Check user role assignment
php artisan tinker
# $user = App\Models\User::find(1);
# $user->role # should return Role model
# $user->hasPermission('gaji.view') # should be true/false

# Verify RolePermission relationship
# $role = App\Models\Role::find(1);
# $role->permissions()->count() # should be > 0

# Reset permissions if needed
# Run: php artisan db:seed --class=RoleAndPermissionSeeder
```

---

## 📊 FIRST WEEK MONITORING REPORT

### Day 1 Checklist
- [ ] 00:00 - System deployed successfully
- [ ] 06:00 - First morning staff arrives - test login
- [ ] 12:00 - Mid-day check - monitor usage
- [ ] 18:00 - End of day - backup and review logs
- [ ] 22:00 - Night check - all systems running

### Daily Tasks (After Deployment)

```
EVERY MORNING (08:00)
☐ Check system health dashboard
☐ Review error logs
☐ Verify no critical alerts
☐ Check database size
☐ Check disk space

EVERY AFTERNOON (14:00)
☐ Check active sessions count
☐ Monitor resource usage
☐ Review user feedback/issues
☐ Verify approvals are processing
☐ Check data integrity samples

EVERY EVENING (18:00)
☐ Review daily summary
☐ Document any issues
☐ Backup system
☐ Review logs for anomalies
☐ Prepare tomorrow's report
```

### Weekly Report (Friday 17:00)

```
PRODUCTION SYSTEM WEEKLY REPORT

System Uptime: ___% (Target: 99.5%+)
Error Rate: ___% (Target: <1%)
Average Response Time: ___ms (Target: <500ms)
Active Users: ___
Data Integrity: ✓ No issues / ☐ Issues found

User Issues Reported: ___
Issues Resolved: ___
Issues Pending: ___

Database Size: ___GB
Backup Status: ✓ Complete
Last Restore Test: ___

Critical Issues: ___
Warnings: ___
Recommendations:
- ___
- ___
```

---

## 🎓 USER DOCUMENTATION

### Super Admin Quick Start

```
LOGIN
1. Go to https://sistem-penggajian.com/administrator/login
2. Enter email & password
3. Click "Login"
   → Redirects to /administrator/dashboard

DASHBOARD
- Cards: Total Pegawai, Gaji Diproses, Budget Status
- Charts: Absensi Trend, Gaji Distribution
- Quick Actions: Pending items

FIRST ACTION: Setup Payroll Period
1. Go to: Settings → Payroll Period
2. Set Month/Year
3. Define dates
4. Save

SECOND ACTION: Verify Absensi & Lembur
1. Go to: Absensi → List
2. Review all items APPROVED
3. Same for Lembur section

THIRD ACTION: Calculate Payroll
1. Go to: Penggajian → Dashboard
2. Click "Calculate Salary"
3. Select Month & Department(s)
4. Click "Calculate All"
5. Review Summary
6. Save

FOURTH ACTION: Approve Payroll
1. Go to: Penggajian → Pending Approval
2. Review each salary
3. Click "Approve" or "Reject"
4. Save

FIFTH ACTION: Post to Payroll
1. Go to: Penggajian → Ready to Post
2. Final check
3. Click "Post All Salaries"
4. Confirm

SIXTH ACTION: Distribute Payslips
1. Go to: Penggajian → Posted
2. Select Employee(s)
3. Click "Email Slip" or "Print Slip"
```

### Officer Quick Start

```
LOGIN
1. Go to https://sistem-penggajian.com/officer/login
2. Enter email & password
3. Click "Login"
   → Redirects to /officer/dashboard (Your Department Only)

DASHBOARD
- Shows: Team member count, You see only your department
- Pending Absensi count from your team
- Pending Lembur count from your team

DAILY TASK: Review Absensi

1. Go to: Absensi → List
2. Add filter: "This Month"
3. For each employee:
   - Verify Status (H/S/I/L/C/A)
   - Check notes
   - Mark correct/incorrect
4. When all verified:
   - Select All
   - Click "Approve All"
   - Confirm

DAILY TASK: Review Lembur

1. Go to: Lembur → List
2. For each overtime entry:
   - Check date and hours
   - Verify activity description
   - Check if reasonable
3. Approve or Reject with reason
   - Approved → Goes to Payroll
   - Rejected → Back to HR for correction

MID-MONTH TASK: Review Team Salary

1. Go to: Penggajian → List (Your Dept)
2. Status should show: DRAFT → CALCULATED → APPROVED → POSTED
3. If any issues:
   - Contact Super Admin
   - Provide detail of issue
   - Do NOT try to edit salary

END OF MONTH: Generate Report

1. Go to: Reports → Attendance Report
2. Select Month
3. View/Download report
4. Same for Overtime Report
5. Send to management if needed
```

### Employee Quick Start

```
LOGIN
1. Go to https://sistem-penggajian.com/login
2. Enter email & password
3. Click "Login"
   → Redirects to /student/dashboard

DASHBOARD
- Shows: Your name, department, job title
- This month attendance rate
- This month overtime hours
- Latest salary status

VIEW MY ATTENDANCE

1. Go to: My Attendance → View
2. See all attendance records
3. Green = Present (H)
   Orange = Leave/Sick/Permission
   Red = Absent (not approved)
4. If you see error:
   - Click "Request Correction"
   - Provide reason
   - Contact your manager

VIEW MY OVERTIME

1. Go to: My Overtime → View
2. See all overtime records
3. Status: Pending / Approved / Rejected
4. If overtime is pending:
   - Wait for manager approval
   - Check back tomorrow

VIEW MY SALARY

1. Go to: My Salary → Payslip
2. Select month
3. View breakdown:
   - Gaji Pokok
   - Tunjangan (detail list)
   - Lembur
   - Potongan (detail list)
   - PPh 21 (Tax)
   - Gaji Netto (Take-home)
4. Download PDF
5. Print if needed

EDIT MY PROFILE

1. Go to: My Profile → Edit
2. Can update:
   - Email address
   - Phone number
   - Home address
   - Bank account info
3. Click "Save"

CHANGE PASSWORD

1. Go to: My Profile → Change Password
2. Enter current password
3. Enter new password (min 8 char, mix of upper/lower/number)
4. Confirm new password
5. Click "Change"
   → Logout & login dengan password baru
```

---

## 📋 SUPPORT TICKET TEMPLATE

### For: HR Support Team

```
TICKET INFORMATION
Ticket ID: ___________
Date Reported: ___________
Reported By: ___________
Priority: [ ] Critical [ ] High [ ] Medium [ ] Low

ISSUE DESCRIPTION
Role: [ ] Super Admin [ ] Officer [ ] Employee
Issue: _________________________________________________________________
Details: _______________________________________________________________

REPRODUCTION STEPS
1. _____________________________________________________________________
2. _____________________________________________________________________
3. _____________________________________________________________________

EXPECTED vs ACTUAL
Expected: _______________________________________________________________
Actual: _________________________________________________________________

SUPPORTING INFO
- Employee Name / ID: ___________________________________________________
- Department: ___________________________________________________________
- Date when issue occurred: __________________________________________
- Screenshots: [ ] Attached [ ] Not available

TROUBLESHOOTING DONE
[ ] Refreshed page
[ ] Cleared browser cache
[ ] Logout & login again
[ ] Tried different browser
[ ] Contacted supervisor

RESOLUTION
Status: [ ] Open [ ] In Progress [ ] Resolved [ ] Closed
Resolution: _____________________________________________________________
Resolved By: ________________________ Date: ____________________
User Confirmation: ✓ Issue resolved / ☐ Issue not resolved
```

---

## 🎯 BUSINESS CONTINUITY PLAN

### In Case of System Failure

**IMMEDIATE (First 15 minutes)**
1. Notify IT Manager
2. Stop acceptance of new data input
3. Inform staff: "System maintenance in progress"

**INVESTIGATION (Next 30 minutes)**
1. Analyze error logs
2. Identify root cause
3. Determine if rollback needed

**RECOVERY OPTIONS**

**Option A: Restart Service (if successful)**
```bash
sudo systemctl restart nginx
sudo systemctl restart php-fpm
supervisorctl restart laravel-worker
```

**Option B: Rollback to Last Known Good State**
```bash
# 1. Restore from backup
mysql -u root -p sistem_penggajian < backup_YYYYMMDD.sql

# 2. Restart services
sudo systemctl restart nginx
sudo systemctl restart php-fpm

# 3. Verify system is back online
curl -I https://sistem-penggajian.com
```

**Option C: Parallel System (Pre-Compiled)**
```bash
# If primary is down >2 hours
# Switch to backup server (pre-configured standby)
# Update DNS to point to backup
# Users reconnect to backup system
```

### Recovery Time Objectives (RTO)

```
- Data Loss: 0 (continuous backup)
- System Down: < 30 minutes
- Full Functionality: < 2 hours
- Data Consistency Check: < 4 hours
```

### Disaster Recovery Contact

```
🆘 23/7 EMERGENCY CONTACT

Primary: [IT Manager Name]
Phone: +62-XXX-XXXX-XXXX
Available: Anytime (24/7)

Secondary: [Senior IT Staff]
Phone: +62-XXX-XXXX-XXXX
Available: 06:00-22:00 (Weekdays)

Escalation (> 2 hours down):
CEO / Director
Email: director@company.com
```

---

## ✨ SUCCESS CRITERIA

### 1 Week Post-Deployment

- ✅ 99% system uptime
- ✅ Zero critical errors
- ✅ All users successfully logged in
- ✅ All roles can access appropriate features
- ✅ Super Admin can calculate full payroll
- ✅ Officers can approve data for their department
- ✅ Employees can view their salary slip
- ✅ All approvals working correctly
- ✅ All reports generating correctly

### 1 Month Post-Deployment

- ✅ First full payroll cycle successfully processed
- ✅ All payslips generated and distributed
- ✅ No data loss or corruption
- ✅ Performance metrics within SLA
- ✅ Staff training completed
- ✅ Documentation finalized
- ✅ Support procedures established
- ✅ Backup & disaster recovery tested

### 3 Months Post-Deployment

- ✅ System stable in production
- ✅ No critical issues reported
- ✅ User adoption rate: >95%
- ✅ Data accuracy: 100% (spot checks)
- ✅ Performance optimization complete
- ✅ Advanced features fully utilized
- ✅ Continuous improvement process established

---

**Document**: Production Deployment Guide v1.0  
**Last Updated**: February 2026  
**Status**: READY FOR DEPLOYMENT  
**Approved**: ✓ By IT Director & HR Manager

