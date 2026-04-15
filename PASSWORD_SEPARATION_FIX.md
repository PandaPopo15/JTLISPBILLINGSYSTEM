# Password Separation Fix

## Overview
This fix separates the user's login password from the PPPoE password used for MikroTik connections.

## Problem
Previously, the system was confusing two different types of passwords:
- **Login Password**: Used for logging into the billing system
- **PPPoE Password**: Used for MikroTik PPPoE connection

The system was either using the same password for both or incorrectly referencing the hashed login password for PPPoE connections.

## Solution

### Database Structure
The `users` table now properly uses two separate password fields:
- `password` - Hashed password for system login (Laravel authentication)
- `pppoe_password` - Plain text password for MikroTik PPPoE connection

### Changes Made

#### 1. DashboardController.php
- **storeClient()**: Now generates two separate passwords:
  - `$generatedLoginPassword` - For system login (hashed)
  - `$generatedPPPoEPassword` - For MikroTik PPPoE (plain text)
  
- **updateClient()**: Added proper password handling:
  - Login password is hashed before storage
  - PPPoE password remains plain text
  - Added validation for password confirmation

- **activateClient()**: Fixed to use `$client->pppoe_password` instead of fallback to username

#### 2. Views (admin/clients/index.blade.php)
- Updated view modal to display `pppoe_password` field
- Updated info box to clarify two separate passwords are generated
- Success message now shows both passwords separately

#### 3. Views (admin/clients/edit.blade.php)
- Added info boxes to distinguish between:
  - PPPoE credentials (for MikroTik)
  - Login credentials (for system access)
- Updated field labels and placeholders for clarity

#### 4. Migration (populate_missing_pppoe_passwords.php)
- Generates PPPoE passwords for existing users who don't have one
- Ensures all users have proper PPPoE credentials

## Usage

### For New Clients
When creating a new client, the system will:
1. Generate a random 8-character login password (hashed in database)
2. Generate a random 8-character PPPoE password (stored as plain text)
3. Generate a unique PPPoE username
4. Display all credentials in the success message

### For Existing Clients
When editing a client:
1. PPPoE credentials can be updated in the "Service & Connection" section
2. Login password can be updated in the "Login Credentials" section
3. Leave login password blank to keep the current password

### For MikroTik Activation
When activating a client on MikroTik:
1. The system uses `pppoe_username` and `pppoe_password`
2. These credentials are pushed to the MikroTik router
3. Client uses these credentials to connect via PPPoE

## Migration Instructions

Run the migration to populate missing PPPoE passwords:
```bash
php artisan migrate
```

This will generate PPPoE passwords for any existing users who don't have one.

## Security Notes

- Login passwords are always hashed using Laravel's Hash facade
- PPPoE passwords are stored as plain text (required for MikroTik API)
- Both passwords are generated with 8 random characters for security
- PPPoE passwords should be treated as sensitive data

## Testing

1. Create a new client and verify two separate passwords are generated
2. Edit a client and update PPPoE password separately from login password
3. Activate a client on MikroTik and verify PPPoE connection works
4. Test login with the system password (not PPPoE password)
