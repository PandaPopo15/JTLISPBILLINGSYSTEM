# Task: Add Due Date & Installation Date to Add Client Modal

## Information Gathered:
- Add client in modal within resources/views/admin/clients/index.blade.php
- ClientController.php store() validation
- User model fillable - add 'due_date', 'installation_date'
- edit.blade.php needs matching fields
- Migration needed for DB columns

## Steps:
- [x] Step 1: Create migration for due_date, installation_date on users table (database/migrations/2026_04_14_020337_add_due_date_installation_date_to_users_table.php)

- [x] Step 2: Update User model $fillable + casts for dates (app/Models/User.php)

- [x] Step 3: Update ClientController validation (store, update) - added 'due_date' nullable date after today, 'installation_date' after due_date

- [ ] Step 4: Add fields to add-client-modal in index.blade.php
- [ ] Step 5: Add fields to edit.blade.php
- [x] Step 6: Run migration (`php artisan migrate`), test form/DB - Due Date/Installation Date fields added to add/edit client forms with validation, DB columns live.


Current: Starting Step 1.

