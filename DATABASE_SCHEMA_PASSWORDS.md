# Database Schema - Password Fields

## Users Table Password Fields

### Before Fix
The system had confusion about password usage:
- `password` field was sometimes used for both login and PPPoE
- `pppoe_password` field existed but wasn't properly utilized
- MikroTik activation used fallback logic: `$client->pppoe_password ?? $client->username`

### After Fix

| Field | Type | Purpose | Storage Format | Usage |
|-------|------|---------|----------------|-------|
| `password` | VARCHAR(255) | System login authentication | Hashed (bcrypt) | Laravel Auth::attempt() |
| `pppoe_password` | VARCHAR(255) | MikroTik PPPoE connection | Plain text | MikroTik API `/ppp/secret/add` |
| `pppoe_username` | VARCHAR(255) | MikroTik PPPoE username | Plain text | MikroTik API `/ppp/secret/add` |

## Migration History

### 1. Add PPPoE Password Column
**File:** `2026_04_18_000001_add_pppoe_password_to_users_table.php`

```php
Schema::table('users', function (Blueprint $table) {
    $table->string('pppoe_password')->nullable()->after('pppoe_username');
});
```

### 2. Populate Missing PPPoE Passwords
**File:** `2026_04_18_000002_populate_missing_pppoe_passwords.php`

```php
$users = DB::table('users')
    ->whereNull('pppoe_password')
    ->orWhere('pppoe_password', '')
    ->get();

foreach ($users as $user) {
    DB::table('users')
        ->where('id', $user->id)
        ->update(['pppoe_password' => Str::random(8)]);
}
```

## Example Data

### New User Creation
```php
User::create([
    'username' => 'johndoe',
    'password' => Hash::make('aB3dE5fG'),  // Hashed for login
    'pppoe_username' => 'client_x7y9z2w4',
    'pppoe_password' => 'pQ8rS2tU',        // Plain text for MikroTik
    // ... other fields
]);
```

### Database Record
```
id: 1
username: johndoe
password: $2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi
pppoe_username: client_x7y9z2w4
pppoe_password: pQ8rS2tU
```

## Security Considerations

### Login Password (Hashed)
- ✅ Stored using Laravel's Hash::make()
- ✅ Uses bcrypt algorithm
- ✅ Cannot be reversed or decrypted
- ✅ Verified using Hash::check()
- ✅ Protected by Laravel's authentication system

### PPPoE Password (Plain Text)
- ⚠️ Stored as plain text (required by MikroTik API)
- ⚠️ Visible to administrators
- ⚠️ Transmitted to MikroTik router
- ✅ Only used for PPPoE connection
- ✅ Separate from system login
- ✅ Can be changed independently

## API Usage

### MikroTik PPPoE Secret Creation
```php
$api->query('/ppp/secret/add', [
    '=name'     => $client->pppoe_username,    // client_x7y9z2w4
    '=password' => $client->pppoe_password,    // pQ8rS2tU (plain text)
    '=service'  => 'pppoe',
    '=profile'  => $client->plan_interest ?? 'default',
    '=comment'  => $client->full_name,
])->read();
```

### Laravel Authentication
```php
Auth::attempt([
    'username' => 'johndoe',
    'password' => 'aB3dE5fG'  // Compared against hashed password
]);
```

## Validation Rules

### Login Password
```php
'password' => 'nullable|string|min:8|confirmed'
```
- Minimum 8 characters
- Requires confirmation field
- Hashed before storage

### PPPoE Password
```php
'pppoe_password' => 'nullable|string|max:64'
```
- Maximum 64 characters (MikroTik limit)
- No confirmation required
- Stored as plain text

## Backward Compatibility

All existing users have been automatically assigned PPPoE passwords:
- Random 8-character passwords generated
- No manual intervention required
- Existing login passwords remain unchanged
- MikroTik activation now works correctly
