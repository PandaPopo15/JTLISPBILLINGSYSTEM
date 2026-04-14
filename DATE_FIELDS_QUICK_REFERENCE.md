# Quick Reference - Date Fields Implementation

## 1. CONTROLLER VALIDATION RULES

### Store Method (Creating New Client)
```php
$validated = $request->validate([
    'installation_date' => 'nullable|date',
    'due_date'          => 'nullable|date|after_or_equal:installation_date',
]);
```

### Update Method (Editing Client)
```php
$validated = $request->validate([
    'installation_date' => 'nullable|date',
    'due_date'          => 'nullable|date|after_or_equal:installation_date',
]);
```

---

## 2. BLADE FORM FIELDS

### Add Client Modal (index.blade.php)
```blade
<div class="modal-section-label" style="margin-top:18px;">Billing & Dates</div>
<div class="adm-form-row">
    <div class="adm-form-group">
        <label>Installation Date</label>
        <input type="date" name="installation_date" value="{{ old('installation_date') }}">
    </div>
    <div class="adm-form-group">
        <label>Due Date</label>
        <input type="date" name="due_date" value="{{ old('due_date') }}">
    </div>
</div>
```

### Edit Client Form (edit.blade.php)
```blade
<div style="font-size:11px;text-transform:uppercase;letter-spacing:1.2px;color:rgba(255,82,82,0.7);margin:4px 0 14px;">Billing & Dates</div>
<div class="adm-form-row">
    <div class="adm-form-group">
        <label>Installation Date</label>
        <input type="date" name="installation_date" value="{{ old('installation_date', $client->installation_date?->format('Y-m-d')) }}">
        @error('installation_date')<div class="adm-form-error">{{ $message }}</div>@enderror
    </div>
    <div class="adm-form-group">
        <label>Due Date</label>
        <input type="date" name="due_date" value="{{ old('due_date', $client->due_date?->format('Y-m-d')) }}">
        @error('due_date')<div class="adm-form-error">{{ $message }}</div>@enderror
    </div>
</div>
```

---

## 3. VIEW MODAL DISPLAY

### JavaScript for View Modal
```javascript
const rows = [
    ['Full Name',  data.name],
    ['Username',   data.username],
    ['Email',      data.email],
    ['Phone',      data.phone],
    ['Age',        data.age],
    ['Plan',       data.plan],
    ['PPPoE',      data.pppoe ? `<span style="font-family:monospace;color:#81d4fa;">${data.pppoe}</span>` : '<span style="color:rgba(255,255,255,0.3);">Not set</span>'],
    ['MikroTik',   data.mikrotik],
    ['Status',     statusMap[data.status] || data.status],
    ['Installation Date', data.installation_date || '<span style="color:rgba(255,255,255,0.3);">Not set</span>'],
    ['Due Date',   data.due_date || '<span style="color:rgba(255,255,255,0.3);">Not set</span>'],
    ['Verified',   data.verified ? '✓ ' + data.verified : '<span style="color:rgba(255,255,255,0.3);">Not verified</span>'],
    ['Registered', data.registered],
];
```

### Data Passed to View Modal
```blade
onclick="openViewModal({{ json_encode([
    'id'        => $client->id,
    'name'      => $client->full_name,
    'username'  => $client->username,
    'email'     => $client->email,
    'phone'     => $client->phone_number ?? '—',
    'address'   => $client->address ?? '—',
    'age'       => $client->age ?? '—',
    'plan'      => $client->plan_interest ?? '—',
    'pppoe'     => $client->pppoe_username ?? null,
    'mikrotik'  => $client->mikrotik?->name ?? '—',
    'status'    => $client->status,
    'verified'  => $client->email_verified_at?->format('M d, Y') ?? null,
    'registered'=> $client->created_at->format('M d, Y h:i A'),
    'installation_date' => $client->installation_date?->format('M d, Y') ?? null,
    'due_date'  => $client->due_date?->format('M d, Y') ?? null,
    'lat'       => $client->latitude,
    'lng'       => $client->longitude,
]) }})"
```

---

## 4. MODEL CONFIGURATION

### Fillable Array
```php
protected $fillable = [
    'first_name', 'middle_name', 'last_name',
    'username', 'email', 'phone_number',
    'address', 'latitude', 'longitude',
    'age', 'plan_interest', 'mikrotik_id',
    'pppoe_username', 'status',
    'password', 'is_admin',
    'profile_image',
    'due_date',
    'installation_date',
];
```

### Casts
```php
protected function casts(): array
{
    return [
        'email_verified_at' => 'datetime',
        'due_date' => 'date',
        'installation_date' => 'date',
        'password' => 'hashed',
        'is_admin' => 'boolean',
    ];
}
```

---

## 5. MIGRATION

### Schema Definition
```php
Schema::table('users', function (Blueprint $table) {
    $table->date('due_date')->nullable()->after('status');
    $table->date('installation_date')->nullable()->after('due_date');
});
```

### Rollback
```php
Schema::table('users', function (Blueprint $table) {
    $table->dropColumn(['due_date', 'installation_date']);
});
```

---

## 6. VALIDATION ERROR MESSAGES

### Custom Messages (Optional)
```php
$messages = [
    'installation_date.date' => 'Installation date must be a valid date.',
    'due_date.date' => 'Due date must be a valid date.',
    'due_date.after_or_equal' => 'Due date must be after or equal to installation date.',
];

$validated = $request->validate([
    'installation_date' => 'nullable|date',
    'due_date'          => 'nullable|date|after_or_equal:installation_date',
], $messages);
```

---

## 7. USAGE IN CONTROLLER

### Creating Client
```php
public function store(Request $request)
{
    $this->adminGuard();

    $validated = $request->validate([
        // ... other fields ...
        'installation_date' => 'nullable|date',
        'due_date'          => 'nullable|date|after_or_equal:installation_date',
    ]);

    $validated['password'] = Hash::make($validated['password']);
    $validated['is_admin'] = false;
    $validated['status'] = 'pending';

    User::create($validated);

    return redirect()->route('admin.clients')->with('success', 'Client created successfully.');
}
```

### Updating Client
```php
public function update(Request $request, User $client)
{
    $this->adminGuard();

    $validated = $request->validate([
        // ... other fields ...
        'installation_date' => 'nullable|date',
        'due_date'          => 'nullable|date|after_or_equal:installation_date',
        'password'     => 'nullable|string|min:8|confirmed',
    ]);

    if (!empty($validated['password'])) {
        $validated['password'] = Hash::make($validated['password']);
    } else {
        unset($validated['password']);
    }

    $client->update($validated);

    return redirect()->route('admin.clients')->with('success', 'Client updated successfully.');
}
```

---

## 8. ACCESSING DATES IN CODE

### In Controller
```php
$client = User::find($id);

// Get formatted dates
$installationDate = $client->installation_date?->format('M d, Y');
$dueDate = $client->due_date?->format('M d, Y');

// Get raw dates
$installationDateRaw = $client->installation_date; // Carbon instance
$dueDateRaw = $client->due_date; // Carbon instance

// Check if dates exist
if ($client->installation_date) {
    // Do something
}
```

### In Blade Template
```blade
<!-- Display formatted dates -->
{{ $client->installation_date?->format('M d, Y') }}
{{ $client->due_date?->format('M d, Y') }}

<!-- Check if date exists -->
@if($client->installation_date)
    Installation: {{ $client->installation_date->format('M d, Y') }}
@endif

<!-- Format for date input -->
<input type="date" value="{{ $client->installation_date?->format('Y-m-d') }}">
```

---

## 9. QUERYING BY DATES

### Find Clients with Upcoming Due Dates
```php
$upcomingDue = User::where('due_date', '<=', now()->addDays(7))
    ->where('due_date', '>=', now())
    ->get();
```

### Find Overdue Clients
```php
$overdue = User::where('due_date', '<', now())
    ->whereNotNull('due_date')
    ->get();
```

### Find Recently Installed
```php
$recentlyInstalled = User::where('installation_date', '>=', now()->subDays(30))
    ->get();
```

---

## 10. TESTING EXAMPLES

### Test Creating Client with Dates
```php
public function test_create_client_with_dates()
{
    $response = $this->post('/admin/clients', [
        'first_name' => 'John',
        'last_name' => 'Doe',
        'email' => 'john@example.com',
        'username' => 'johndoe',
        'password' => 'password123',
        'password_confirmation' => 'password123',
        'installation_date' => '2024-01-15',
        'due_date' => '2024-02-15',
    ]);

    $this->assertDatabaseHas('users', [
        'email' => 'john@example.com',
        'installation_date' => '2024-01-15',
        'due_date' => '2024-02-15',
    ]);
}
```

### Test Validation - Due Date Before Installation Date
```php
public function test_due_date_must_be_after_installation_date()
{
    $response = $this->post('/admin/clients', [
        'first_name' => 'John',
        'last_name' => 'Doe',
        'email' => 'john@example.com',
        'username' => 'johndoe',
        'password' => 'password123',
        'password_confirmation' => 'password123',
        'installation_date' => '2024-02-15',
        'due_date' => '2024-01-15', // Before installation date
    ]);

    $response->assertSessionHasErrors('due_date');
}
```

---

## 11. COMMON ISSUES & SOLUTIONS

### Issue: Dates not saving
**Solution:** Ensure fields are in fillable array and migration has been run
```php
php artisan migrate
```

### Issue: Date format incorrect in form
**Solution:** Use Y-m-d format for date inputs
```blade
{{ $client->installation_date?->format('Y-m-d') }}
```

### Issue: Validation error not showing
**Solution:** Ensure error display in blade
```blade
@error('due_date')
    <div class="adm-form-error">{{ $message }}</div>
@enderror
```

### Issue: Dates showing as null in view
**Solution:** Use null coalescing operator
```blade
{{ $client->due_date?->format('M d, Y') ?? 'Not set' }}
```

---

## 12. ARTISAN COMMANDS

### Run Migrations
```bash
php artisan migrate
```

### Rollback Migrations
```bash
php artisan migrate:rollback
```

### Fresh Migration (Warning: Deletes all data)
```bash
php artisan migrate:fresh
```

### Check Migration Status
```bash
php artisan migrate:status
```

---

## Summary

All date field functionality is now integrated into the ISP Billing system:
- ✅ Database columns created
- ✅ Model configured
- ✅ Controller validation implemented
- ✅ Frontend forms updated
- ✅ View modal displays dates
- ✅ Error handling in place
- ✅ Production-ready code

Ready for deployment!
