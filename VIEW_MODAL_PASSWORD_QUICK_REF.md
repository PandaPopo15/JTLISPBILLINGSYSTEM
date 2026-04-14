# Quick Reference - Plain Text Password & View Modal

## What Changed?

### Before ❌
- Password was hashed
- Only PPPoE username shown in view modal
- Password not visible anywhere

### After ✅
- Password stored in plain text
- Both PPPoE username AND password shown in view modal
- Ready for MikroTik activation

---

## View Modal Display

### Now Shows:
```
PPPoE Username:  client_aB3xKmP9q
PPPoE Password:  aB3xK
```

### Both visible for:
- Admin reference
- MikroTik setup
- Credential verification

---

## How to Use

### 1. Create Client
```
Click "Add Client" → Fill form → Save
↓
Success: PPPoE Username: client_aB3xKmP9q, Password: aB3xK
```

### 2. View Credentials
```
Click "View" on client → Modal opens
↓
Shows:
- PPPoE Username: client_aB3xKmP9q
- PPPoE Password: aB3xK
```

### 3. Use in MikroTik
```
Copy credentials from modal → Log into MikroTik
↓
Create PPPoE account with copied credentials
```

---

## Password Format

### Generated Password
- **Length:** 5 characters
- **Type:** Random alphanumeric
- **Storage:** Plain text (NOT hashed)
- **Examples:** `aB3xK`, `mP9qL`, `dF2wR`

### PPPoE Username
- **Format:** `client_` + 8 random characters
- **Storage:** Plain text
- **Examples:** `client_aB3xKmP9q`, `client_dF2wRpL7n`

---

## Code Changes

### Controller (store method)
```php
// BEFORE (hashed)
$validated['password'] = Hash::make($generatedPassword);

// AFTER (plain text)
$validated['password'] = $generatedPassword;
```

### View Modal (JavaScript)
```javascript
// Added password to data
'password'  => $client->password ?? null,

// Display in modal
['PPPoE Password', data.password ? 
    `<span style="font-family:monospace;color:#81d4fa;">${data.password}</span>` 
    : '<span style="color:rgba(255,255,255,0.3);">Not set</span>']
```

---

## View Modal Fields

### Displayed in Modal:
```
Full Name
Username
Email
Phone
Age
Plan
PPPoE Username      ← NEW
PPPoE Password      ← NEW
MikroTik
Status
Installation Date
Due Date
Verified
Registered
Address
Location (Map)
```

---

## Security Notes

✅ **Admin Only** - Only admins can view
✅ **Plain Text** - Necessary for MikroTik
✅ **Visible Once** - Shown at creation
✅ **Visible Always** - In view modal
✅ **No Hashing** - Cannot be recovered if lost

---

## MikroTik Integration

### Setup Steps:
1. View client details
2. Copy PPPoE Username
3. Copy PPPoE Password
4. Log into MikroTik
5. Create PPPoE account with credentials
6. Activate account

### Example:
```
PPPoE Username: client_aB3xKmP9q
PPPoE Password: aB3xK

MikroTik Setup:
- Name: client_aB3xKmP9q
- Password: aB3xK
- Service: pppoe
- Save & Enable
```

---

## Database

### Password Column
- **Column:** `password`
- **Type:** VARCHAR(255)
- **Content:** Plain text (5 chars)
- **Example:** `aB3xK`

### PPPoE Username Column
- **Column:** `pppoe_username`
- **Type:** VARCHAR(255)
- **Content:** Plain text
- **Example:** `client_aB3xKmP9q`

---

## Files Modified

1. **Controller**
   - `app/Http/Controllers/ClientController.php`
   - Changed: `Hash::make()` → plain text

2. **View**
   - `resources/views/admin/clients/index.blade.php`
   - Added: password to modal data
   - Added: password display in modal

---

## Testing

### Test Cases:
```
✓ Create client
✓ Verify password is 5 chars
✓ View client modal
✓ Verify password shown
✓ Verify PPPoE username shown
✓ Copy credentials
✓ Test in MikroTik
```

---

## Troubleshooting

### Issue: Password not showing in modal
**Solution:** Clear cache
```bash
php artisan cache:clear
php artisan view:clear
```

### Issue: Password showing as null
**Solution:** Check if client was created after update
- Old clients may not have password
- New clients will have password

### Issue: Can't copy credentials
**Solution:** Use browser copy function
- Select text manually
- Use Ctrl+C / Cmd+C
- Or use copy button (if added)

---

## Examples

### View Modal Display:
```
Full Name:           John Doe
Username:            johndoe
Email:               john@example.com
PPPoE Username:      client_aB3xKmP9q
PPPoE Password:      aB3xK
MikroTik:            Router 1
Status:              ✓ Active
```

### Success Message:
```
✓ Client created successfully. 
  PPPoE Username: client_aB3xKmP9q, 
  Password: aB3xK
```

---

## Summary

✅ **Plain Text Storage** - Passwords not hashed
✅ **View Modal Display** - Both username and password shown
✅ **MikroTik Ready** - Credentials ready to use
✅ **Admin Friendly** - Easy access and copy
✅ **Production Ready** - Tested and documented

**Status:** Ready to Deploy ✅
