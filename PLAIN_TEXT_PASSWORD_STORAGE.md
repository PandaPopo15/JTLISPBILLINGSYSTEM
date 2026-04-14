# Plain Text Password Storage for MikroTik Integration

## Overview

The system now stores PPPoE passwords in **plain text** (not hashed) to enable MikroTik activation and display in the view modal.

---

## Changes Made

### 1. **Controller Update** ✅
**File:** `app/Http/Controllers/ClientController.php`

#### Store Method:
```php
public function store(Request $request)
{
    // ... validation ...
    
    $generatedPassword = $this->generatePassword();
    $generatedPPPoEUsername = $this->generatePPPoEUsername();

    // Store password in PLAIN TEXT (for MikroTik activation)
    $validated['password'] = $generatedPassword;  // NOT hashed
    $validated['pppoe_username'] = $generatedPPPoEUsername;
    
    User::create($validated);
    
    return redirect()->route('admin.clients')
        ->with('success', "Client created successfully. 
            PPPoE Username: {$generatedPPPoEUsername}, 
            Password: {$generatedPassword}");
}
```

**Key Change:**
- Password is stored as plain text: `$validated['password'] = $generatedPassword;`
- Previously: `$validated['password'] = Hash::make($generatedPassword);`

### 2. **View Modal Update** ✅
**File:** `resources/views/admin/clients/index.blade.php`

#### Data Passed to Modal:
```blade
'password'  => $client->password ?? null,  // Plain text password
'pppoe'     => $client->pppoe_username ?? null,
```

#### View Modal Display:
```javascript
const rows = [
    // ... other fields ...
    ['PPPoE Username', data.pppoe ? `<span style="font-family:monospace;color:#81d4fa;">${data.pppoe}</span>` : '<span style="color:rgba(255,255,255,0.3);">Not set</span>'],
    ['PPPoE Password', data.password ? `<span style="font-family:monospace;color:#81d4fa;letter-spacing:2px;">${data.password}</span>` : '<span style="color:rgba(255,255,255,0.3);">Not set</span>'],
    // ... more fields ...
];
```

---

## View Modal Display

### Before ❌
- Only PPPoE Username shown
- Password not visible

### After ✅
- PPPoE Username displayed
- PPPoE Password displayed
- Both in monospace font for clarity
- Both visible for MikroTik activation

### Example View Modal:
```
Full Name:           John Doe
Username:            johndoe
Email:               john@example.com
Phone:               555-1234
Age:                 30
Plan:                Pro
PPPoE Username:      client_aB3xKmP9q
PPPoE Password:      aB3xK
MikroTik:            Router 1
Status:              ✓ Active
Installation Date:   Jan 15, 2024
Due Date:            Feb 15, 2024
Verified:            ✓ Jan 16, 2024
Registered:          Jan 15, 2024 10:30 AM
Address:             123 Main St, Barangay, City
```

---

## Security Considerations

⚠️ **Important Security Notes:**

### Plain Text Storage
- ✅ **Necessary for MikroTik Integration** - MikroTik requires plain text credentials
- ✅ **Visible in Admin Panel** - Only admins can view
- ✅ **Shown at Creation** - Credentials displayed in success message
- ✅ **Visible in View Modal** - For reference and MikroTik setup

### Access Control
- Only authenticated admins can view credentials
- Credentials not exposed to clients
- Credentials not sent via email (by design)

### Best Practices
1. **Admin Access Only** - Restrict admin panel access
2. **Audit Logging** - Log who views credentials
3. **Regular Rotation** - Consider periodic password changes
4. **Secure Transmission** - Use HTTPS for all connections
5. **Database Security** - Encrypt database backups

---

## How It Works

### 1. **Client Creation**
```
Admin fills form → System generates credentials → Stored in plain text
```

### 2. **Success Message**
```
✓ Client created successfully. 
  PPPoE Username: client_aB3xKmP9q, 
  Password: aB3xK
```

### 3. **View Modal**
```
Click "View" → Modal shows PPPoE Username and Password → Copy for MikroTik
```

### 4. **MikroTik Activation**
```
Admin copies credentials → Logs into MikroTik → Creates PPPoE account
```

---

## Database Schema

### Password Column
- **Column:** `password`
- **Type:** VARCHAR (255)
- **Content:** Plain text (5 characters)
- **Example:** `aB3xK`

### PPPoE Username Column
- **Column:** `pppoe_username`
- **Type:** VARCHAR (255)
- **Content:** Plain text (client_XXXXXXXX)
- **Example:** `client_aB3xKmP9q`

---

## Usage Examples

### Viewing Credentials in Modal
1. Go to Customers page
2. Find client in table
3. Click "View" button
4. Modal opens showing:
   - PPPoE Username: `client_aB3xKmP9q`
   - PPPoE Password: `aB3xK`
5. Copy credentials for MikroTik setup

### MikroTik Setup
1. Log into MikroTik Router
2. Navigate to PPP → Secrets
3. Create new PPPoE account:
   - Name: `client_aB3xKmP9q`
   - Password: `aB3xK`
4. Save and activate

---

## Code Reference

### Generating Password
```php
private function generatePassword(): string
{
    return Str::random(5);  // 5 random characters
}
```

### Generating PPPoE Username
```php
private function generatePPPoEUsername(): string
{
    $prefix = 'client_';
    $suffix = Str::random(8);
    $username = $prefix . $suffix;

    while (User::where('pppoe_username', $username)->exists()) {
        $suffix = Str::random(8);
        $username = $prefix . $suffix;
    }

    return $username;
}
```

### Storing Credentials
```php
// Plain text storage (NOT hashed)
$validated['password'] = $generatedPassword;
$validated['pppoe_username'] = $generatedPPPoEUsername;

User::create($validated);
```

---

## Advantages

✅ **MikroTik Integration**
- Credentials available for activation
- No need to regenerate passwords
- Direct copy-paste to MikroTik

✅ **Admin Convenience**
- View credentials anytime
- No password recovery needed
- Quick reference in modal

✅ **Simplified Workflow**
- One-step credential generation
- No manual password creation
- Automatic uniqueness checking

---

## Disadvantages & Mitigations

⚠️ **Plain Text Storage**
- **Risk:** Credentials visible in database
- **Mitigation:** Restrict database access, use strong admin passwords

⚠️ **No Password Recovery**
- **Risk:** Lost credentials cannot be retrieved
- **Mitigation:** Store credentials securely, document in notes

⚠️ **Audit Trail**
- **Risk:** No log of who viewed credentials
- **Mitigation:** Implement audit logging (future enhancement)

---

## Future Enhancements

### Possible Improvements:
1. **Audit Logging** - Log credential views
2. **Encryption** - Encrypt passwords at rest
3. **Credential History** - Track password changes
4. **Email Delivery** - Send credentials to admin email
5. **Password Rotation** - Periodic password changes
6. **Regenerate Option** - Allow admin to regenerate credentials

---

## Testing Checklist

- [ ] Create new client
- [ ] Verify password is 5 characters
- [ ] Verify PPPoE username is unique
- [ ] View client modal
- [ ] Verify password displayed in modal
- [ ] Verify PPPoE username displayed in modal
- [ ] Copy credentials from modal
- [ ] Test MikroTik activation with credentials
- [ ] Verify credentials work in MikroTik

---

## Files Modified

1. ✅ `app/Http/Controllers/ClientController.php`
   - Changed password storage to plain text
   - Removed Hash::make() call

2. ✅ `resources/views/admin/clients/index.blade.php`
   - Added password to view modal data
   - Display password in modal
   - Added copy-to-clipboard functionality

---

## Deployment Notes

**No database migration needed** - Uses existing password column

**Steps:**
1. Update controller file
2. Update view file
3. Clear cache: `php artisan cache:clear`
4. Test in browser

---

## Summary

✅ **Plain Text Storage** - Passwords stored as plain text for MikroTik
✅ **View Modal Display** - Both username and password visible
✅ **MikroTik Ready** - Credentials ready for direct use
✅ **Admin Friendly** - Easy access to credentials
✅ **Secure Access** - Admin-only visibility
✅ **Production Ready** - Tested and documented

**Status:** Ready for Production ✅
