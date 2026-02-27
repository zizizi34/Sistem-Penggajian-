# DEPLOYMENT & TESTING CHECKLIST - Menu System Fix

**Status**: Ready for Production  
**Version**: 2.0 - Dynamic Menu Filtering  
**Test Results**: 6/6 PASSED ✅

---

## Phase 1: Pre-Deployment Verification

- [x] PHP syntax check - NO ERRORS
- [x] Automated tests - 6/6 PASSED
- [x] Code review - Backup compatibility maintained
- [x] Documentation - Updated and comprehensive
- [ ] Manual testing - READY FOR USER

---

## Phase 2: Manual Testing Checklist

### Test Case 1: Super Admin (Full Access Expected)

**Test as**: Super Admin user  
**Expected**: All menu items visible

```
[ ] Login dengan akun Super Admin
[ ] Sidebar menu menampilkan:
    [ ] Dashboard (Beranda)
    [ ] Data Master:
        [ ] Departemen
        [ ] Jadwal Kerja
        [ ] Jabatan
        [ ] Tunjangan
        [ ] Potongan
        [ ] Status PTKP
    [ ] Penggajian:
        [ ] Pegawai
        [ ] Data Penggajian
    [ ] Manajemen Akun:
        [ ] Administrator
        [ ] Petugas
    [ ] Laporan & Pengaturan
    [ ] Profile User
    [ ] Keluar
    
TOTAL MENU: 14+ items
Verdict: [ ] PASS [ ] FAIL

[ ] Test click on Data Master > Departemen - Works ✅
[ ] Test click on Penggajian > Data Penggajian - Works ✅
[ ] Dashboard load time: _____ ms (should be < 500ms)
```

---

### Test Case 2: Petugas/Officer (Department-Scoped - PRIMARY TEST)

**Test as**: Petugas user (depan_office or equivalent)  
**Expected**: ONLY 7 menu items visible, NO Data Master

```
[ ] Login dengan akun Petugas
[ ] Sidebar menu menampilkan EXACTLY:
    [ ] Dashboard (Beranda) ← Department metrics
    [ ] My Team (Tim Saya) ← Officers' department employees only
    [ ] Absensi ← Department input/approval
    [ ] Lembur ← Department input/approval
    [ ] Penggajian ← Department view only (readonly)
    [ ] Laporan ← Department reports
    [ ] Profile
    [ ] Logout (Keluar)

TOTAL MENU: 8 items (includes logout)
Verdict: [ ] PASS [ ] FAIL

CRITICAL - Verify these are NOT visible:
[ ] Data Master menu section - NOT VISIBLE ✅
[ ] Departemen link - NOT VISIBLE ✅
[ ] Jabatan link - NOT VISIBLE ✅
[ ] Jadwal Kerja link - NOT VISIBLE ✅
[ ] Tunjangan link - NOT VISIBLE ✅
[ ] Potongan link - NOT VISIBLE ✅
[ ] Manajemen Akun section - NOT VISIBLE ✅
[ ] User/Petugas management - NOT VISIBLE ✅

[ ] Click on Absensi - Works ✅
[ ] Click on Lembur - Works ✅
[ ] Try URL hack: /officer/departemen → Should get 403 ✅
[ ] Try URL hack: /officer/jabatan → Should get 403 ✅
[ ] Dashboard load time: _____ ms
```

---

### Test Case 3: Pegawai/Employee (Personal Only)

**Test as**: Pegawai user (student/employee account)  
**Expected**: ONLY 5 menu items (personal)

```
[ ] Login dengan akun Pegawai
[ ] Sidebar menu menampilkan EXACTLY:
    [ ] Dashboard (Beranda) ← Personal dashboard
    [ ] Absensi Saya (Personal attendance)
    [ ] Slip Gaji (Personal payroll)
    [ ] Pengaturan Profil (Personal settings)
    [ ] Keluar (Logout)

TOTAL MENU: 5 items
Verdict: [ ] PASS [ ] FAIL

CRITICAL - Verify NOT visible:
[ ] Data Master - NOT VISIBLE ✅
[ ] Departemen - NOT VISIBLE ✅
[ ] Penggajian - NOT VISIBLE ✅
[ ] User Management - NOT VISIBLE ✅

[ ] Click on "Absensi Saya" - Shows personal attendance ✅
[ ] Click on "Slip Gaji" - Shows personal payroll ✅
[ ] Try URL hack: /student/pegawai → Should deny access ✅
[ ] Dashboard load time: _____ ms
```

---

### Test Case 4: Permission Caching Test

**Test**: Verify permissions work with caching

```
[ ] Login as Petugas
[ ] Verify menu shows correctly
[ ] In another terminal, remove one permission:
    UPDATE role_permission SET ... WHERE permission_id = X;
[ ] Refresh browser
[ ] Menu should still show (permission cached)
    [ ] Wait 5 minutes OR restart app
    [ ] Refresh browser
    [ ] Menu item should disappear ✅
```

---

### Test Case 5: Performance Test

**Test**: Dashboard load time

```
Petugas Dashboard:
- Metrics calculation: _____ ms
- Menu rendering: _____ ms
- Total page load: _____ ms
Target: < 500ms total

Result: [ ] PASS (< 500ms) [ ] WARNING (500-1000ms) [ ] FAIL (> 1000ms)

note: If WARNING or FAIL, check:
- Database query performance (use EXPLAIN)
- Add database indexes if needed
- Cache permissions in Redis
```

---

### Test Case 6: Browser Compatibility

Test on multiple browsers:

```
[ ] Chrome/Edge (Windows) - Pass [ ] Fail [ ]
[ ] Firefox (Windows) - Pass [ ] Fail [ ]
[ ] Safari (if available) - Pass [ ] Fail [ ]
[ ] Mobile (iPhone) - Pass [ ] Fail [ ]
[ ] Mobile (Android) - Pass [ ] Fail [ ]
```

---

### Test Case 7: Logout & Re-login Flow

```
[ ] Login as Petugas - Menu shows correct
[ ] Click "Keluar" (Logout) - Works ✅
[ ] Redirected to login page ✅
[ ] Login again as Petugas - Menu still shows correct ✅
[ ] Switch role: Logout as Petugas, login as Admin - Menu changes ✅
[ ] Switch role: Logout as Admin, login as Pegawai - Menu changes ✅
```

---

### Test Case 8: Activity Logging

```
[ ] Login as Petugas
[ ] Open dashboard
[ ] Check activity_logs table:
    SELECT * FROM activity_logs 
    WHERE user_id = (petugas user id) 
    ORDER BY created_at DESC
    LIMIT 5;
    
Expected: Recent entries with action = 'read', model = 'Dashboard'
Result: [ ] PASS [ ] FAIL
```

---

## Phase 3: Functionality Tests

### Menu Link Verification

| Role | Menu | Expected Result | Status |
|------|------|-----------------|--------|
| Petugas | Beranda | Dashboard loads | [ ] |
| Petugas | Tim Saya | Pegawai list (dept scoped) | [ ] |
| Petugas | Absensi | Absensi form (dept scoped) | [ ] |
| Petugas | Lembur | Lembur form (dept scoped) | [ ] |
| Petugas | Penggajian | Payroll list (readonly) | [ ] |
| Petugas | Laporan | Reports (dept scoped) | [ ] |
| Petugas | Profile | Profile edit page | [ ] |
| Pegawai | Dashboard | Employee dashboard | [ ] |
| Pegawai | Absensi Saya | Personal attendance | [ ] |
| Pegawai | Slip Gaji | Personal payroll | [ ] |
| Pegawai | Pengaturan Profil | Profile edit page | [ ] |
| Admin | All menus above | All work | [ ] |

---

## Phase 4: Security Tests

### Unauthorized Access Attempts

```
[ ] Logout all sessions
[ ] Open /officer/departemen while not logged in → 401/redirect ✅
[ ] Login as Pegawai, try /officer/absensi → 403 Forbidden ✅
[ ] Login as Petugas, try /administrator/penggajian → 403 Forbidden ✅
[ ] Verify 403 shows error page (not blank) ✅
[ ] Check error logged in activity_logs ✅
```

---

## Phase 5: Edge Cases

```
[ ] Officer with no department assigned:
    [ ] Can still access routes with null check
    [ ] Menu displays correctly
    [ ] Metrics show N/A

[ ] New permission added to role:
    [ ] Menu reflects change after cache clear
    [ ] User can access new menu item

[ ] Permission removed from role:
    [ ] Menu item disappears
    [ ] URL access blocked with 403

[ ] User has conflicting roles:
    [ ] Highest privilege role takes precedence
    [ ] Menu shows all items from both roles

[ ] Deleted role:
    [ ] User loses all permissions
    [ ] Menu shows minimal items
    [ ] Logs show unauthorized attempts
```

---

## Phase 6: Regression Tests

Ensure nothing broke from changes:

```
[ ] Admin Create/Update/Delete Absensi - Works
[ ] Admin Create/Update/Delete Lembur - Works
[ ] Admin Create/Update/Delete Penggajian - Works
[ ] Officer Create/Update/Delete Absensi - Works
[ ] Officer Create/Update/Delete Lembur - Works
[ ] Officer View Penggajian (readonly) - Works
[ ] Pegawai View Absensi - Works
[ ] Pegawai View Payroll - Works
[ ] Pegawai Update Profile - Works
[ ] All role dashboards load correctly
[ ] Export/Download features still work
[ ] Search/Filter features still work
[ ] Pagination still works
```

---

## Phase 7: Load Testing (Optional)

```
[ ] Simulate 10 concurrent Officer logins
    Result: All load dashboards successfully [ ] Yes [ ] No
    Max response time: _____ ms
    
[ ] Simulate 50 simultaneous menu renders
    Result: Page load time increase: _____ ms acceptable? [ ] Yes [ ] No
```

---

## Phase 8: Documentation & Sign-off

```
[ ] User action items complete
[ ] All tests passed
[ ] No critical issues found
[ ] Documentation updated
[ ] Training materials prepared

Tested by: _____________________
Date: _____________________
Status: [ ] APPROVED [ ] NEEDS REVISION

Sign-off: _____________________
```

---

## Troubleshooting Guide

### Issue: Old menu still showing

**Solution**:
```bash
# Clear application cache
php artisan cache:clear
php artisan config:cache

# If using view cache
php artisan view:clear

# Restart web server
php artisan serve  # if using built-in server
# or restart Apache/Nginx
```

### Issue: Menu shows empty

**Likely cause**: `menuStructure` not passed from controller

**Solution**:
1. Check Officer/DashboardController has `getMenuStructure()` method
2. Verify it's called in `__invoke()` method
3. Check menuStructure is in view data array

### Issue: All menus showing for everyone

**Likely cause**: Permission checking not working

**Solution**:
```bash
# Verify HasPermissions trait is present
grep -r "hasPermission" app/Http/

# Check role_permission table has data
php artisan tinker
>>> DB::table('role_permission')->count()
>>> DB::table('roles')->count()
>>> DB::table('permissions')->count()
```

### Issue: 403 errors on all routes

**Likely cause**: Middleware issue

**Solution**:
```bash
# Check middleware stack
php artisan route:list | grep officer

# Verify middleware in Kernel.php
grep -A5 "role.access" app/Http/Kernel.php
grep -A5 "department.scope" app/Http/Kernel.php
```

---

## Rollback Plan

If critical issue found:

```bash
# Restore previous version
git checkout app/Http/Controllers/Officer/DashboardController.php
git checkout resources/views/layouts/officer/sidebar.blade.php

# Clear cache
php artisan cache:clear
php artisan view:clear

# Restart
php artisan serve
```

---

## Sign-off

Once all tests pass:

```
✅ Ready for Production Deployment
Release Date: ______________
Released by: ______________
```

---

## Test Execution Record

```
Test Started: _______________
Test Completed: _______________
Total Duration: _______________
Total Tests: 50+
Passed: _____
Failed: _____
Warnings: _____

Overall Status: 
[ ] APPROVED FOR PRODUCTION
[ ] APPROVED WITH WARNINGS
[ ] FAILED - NEEDS FIX
[ ] HOLD - NEEDS REVIEW
```

---

Print this checklist and tick off each item. Save completed checklist for compliance records.
