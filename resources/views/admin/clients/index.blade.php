@extends('admin.layout')
@section('title', 'Customers')

@section('content')

<div class="adm-page-header">
    <div>
        <div class="adm-page-title">Customers</div>
        <div class="adm-page-subtitle">
            {{ $clients->total() }} total
            @if($pendingCount > 0)
                &nbsp;·&nbsp;<span style="color:#ffd54f;">{{ $pendingCount }} pending approval</span>
            @endif
        </div>
    </div>
    <button class="btn-primary" onclick="openModal('add-client-modal')">+ Add Client</button>
</div>

<div class="adm-card">
    {{-- Search & Filter --}}
    <form method="GET" action="{{ route('admin.clients') }}" class="adm-search-bar">
        <input type="text" name="search" placeholder="Search name, email, username..." value="{{ request('search') }}">
        <select name="status">
            <option value="">All Status</option>
            <option value="pending"  {{ request('status')==='pending'  ? 'selected':'' }}>🕐 Pending</option>
            <option value="active"   {{ request('status')==='active'   ? 'selected':'' }}>✅ Active</option>
            <option value="rejected" {{ request('status')==='rejected' ? 'selected':'' }}>❌ Rejected</option>
        </select>
        <button type="submit" class="btn-primary btn-sm">Search</button>
        @if(request('search') || request('status'))
            <a href="{{ route('admin.clients') }}" class="btn-secondary btn-sm">Clear</a>
        @endif
    </form>

    <div class="adm-table-wrap" style="margin-top:16px;">
        <table class="adm-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Contact</th>
                    <th>Plan</th>
                    <th>PPPoE</th>
                    <th>MikroTik</th>
                    <th>Status</th>
                    <th>Registered</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($clients as $client)
                <tr>
                    <td style="color:rgba(255,255,255,0.3);font-size:12px;">{{ $client->id }}</td>

                    <td>
                        <div style="font-weight:600;color:#fff;">{{ $client->full_name }}</div>
                        <div style="font-size:11px;color:rgba(255,255,255,0.35);margin-top:2px;">{{ $client->username }}</div>
                    </td>

                    <td>
                        <div style="font-size:13px;color:rgba(255,255,255,0.6);">{{ $client->email }}</div>
                        <div style="font-size:11px;color:rgba(255,255,255,0.35);">{{ $client->phone_number ?? '—' }}</div>
                    </td>

                    <td>
                        @if($client->plan_interest)
                            <span class="badge badge-yellow">{{ $client->plan_interest }}</span>
                        @else
                            <span style="color:rgba(255,255,255,0.25);">—</span>
                        @endif
                    </td>

                    <td>
                        @if($client->pppoe_username)
                            <span style="font-family:monospace;font-size:12px;color:#81d4fa;">{{ $client->pppoe_username }}</span>
                        @else
                            <span style="color:rgba(255,255,255,0.2);font-size:12px;">Not set</span>
                        @endif
                    </td>

                    <td>
                        @if($client->mikrotik)
                            <a href="{{ route('admin.mikrotik.edit', $client->mikrotik) }}"
                               style="color:#81d4fa;font-size:12px;text-decoration:none;">
                                🔌 {{ Str::limit($client->mikrotik->name, 18) }}
                            </a>
                        @else
                            <span style="color:rgba(255,255,255,0.2);font-size:12px;">—</span>
                        @endif
                    </td>

                    <td>
                        @if($client->status === 'active')
                            <span class="badge badge-green">✓ Active</span>
                        @elseif($client->status === 'pending')
                            <span class="badge badge-yellow">🕐 Pending</span>
                        @else
                            <span class="badge badge-red">✕ Rejected</span>
                        @endif
                    </td>

                    <td style="font-size:12px;color:rgba(255,255,255,0.35);">
                        {{ $client->created_at->format('M d, Y') }}
                    </td>

                    <td>
                        <div style="display:flex;gap:5px;flex-wrap:wrap;align-items:center;">

                            {{-- VIEW --}}
                            <button class="btn-secondary btn-sm"
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
                                    'password'  => $client->password ?? null,
                                    'mikrotik'  => $client->mikrotik?->name ?? '—',
                                    'status'    => $client->status,
                                    'verified'  => $client->email_verified_at?->format('M d, Y') ?? null,
                                    'registered'=> $client->created_at->format('M d, Y h:i A'),
                                    'installation_date' => $client->installation_date?->format('M d, Y') ?? null,
                                    'due_date'  => $client->due_date?->format('M d, Y') ?? null,
                                    'lat'       => $client->latitude,
                                    'lng'       => $client->longitude,
                                ]) }})">
                                👁 View
                            </button>

                            @if($client->status === 'pending')
                                <form action="{{ route('admin.clients.accept', $client) }}" method="POST" style="display:inline;">
                                    @csrf<button type="submit" class="btn-success btn-sm">✓ Accept</button>
                                </form>
                                <form action="{{ route('admin.clients.reject', $client) }}" method="POST" style="display:inline;"
                                      onsubmit="return confirm('Reject {{ addslashes($client->full_name) }}?')">
                                    @csrf<button type="submit" class="btn-danger btn-sm">✕ Reject</button>
                                </form>

                            @elseif($client->status === 'active')
                                {{-- ACTIVATE (send PPPoE to MikroTik) --}}
                                @if($client->pppoe_username && $client->mikrotik_id)
                                <form action="{{ route('admin.clients.activate', $client) }}" method="POST" style="display:inline;"
                                      onsubmit="return confirm('Push PPPoE account for {{ addslashes($client->full_name) }} to MikroTik?')">
                                    @csrf<button type="submit" class="btn-sm" style="background:rgba(129,212,250,0.12);color:#81d4fa;border:1px solid rgba(129,212,250,0.3);">⚡ Activate</button>
                                </form>
                                @endif
                                <a href="{{ route('admin.clients.edit', $client) }}" class="btn-secondary btn-sm">✏️ Edit</a>
                                <form action="{{ route('admin.clients.delete', $client) }}" method="POST" style="display:inline;"
                                      onsubmit="return confirm('Delete {{ addslashes($client->full_name) }}?')">
                                    @csrf<button type="submit" class="btn-danger btn-sm">🗑</button>
                                </form>

                            @elseif($client->status === 'rejected')
                                <form action="{{ route('admin.clients.accept', $client) }}" method="POST" style="display:inline;">
                                    @csrf<button type="submit" class="btn-success btn-sm">↩ Restore</button>
                                </form>
                                <form action="{{ route('admin.clients.delete', $client) }}" method="POST" style="display:inline;"
                                      onsubmit="return confirm('Permanently delete {{ addslashes($client->full_name) }}?')">
                                    @csrf<button type="submit" class="btn-danger btn-sm">🗑</button>
                                </form>
                            @endif

                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" style="text-align:center;padding:48px;color:rgba(255,255,255,0.25);">
                        No customers found.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if($clients->hasPages())
    <div class="adm-pagination">
        @if($clients->onFirstPage())<span>‹ Prev</span>@else<a href="{{ $clients->previousPageUrl() }}">‹ Prev</a>@endif
        @foreach($clients->getUrlRange(max(1,$clients->currentPage()-2),min($clients->lastPage(),$clients->currentPage()+2)) as $page => $url)
            @if($page==$clients->currentPage())<span class="active-page">{{ $page }}</span>
            @else<a href="{{ $url }}">{{ $page }}</a>@endif
        @endforeach
        @if($clients->hasMorePages())<a href="{{ $clients->nextPageUrl() }}">Next ›</a>@else<span>Next ›</span>@endif
    </div>
    @endif
</div>


{{-- ══════════════════════════════════════
     ADD CLIENT MODAL
══════════════════════════════════════ --}}
<div id="add-client-modal" class="modal-backdrop" style="display:none;">
    <div class="modal-box" style="max-width:680px;">

        {{-- Header --}}
        <div class="modal-header">
            <div class="modal-title">➕ Add New Client</div>
            <button class="modal-close" onclick="closeModal('add-client-modal')">✕</button>
        </div>

        {{-- Validation errors --}}
        @if($errors->any())
        <div style="flex-shrink:0;padding:10px 24px 0;">
            <div style="background:rgba(255,82,82,0.1);border:1px solid rgba(255,82,82,0.3);border-radius:10px;padding:10px 14px;color:#ff8a80;font-size:13px;">
                @foreach($errors->all() as $e)<div>• {{ $e }}</div>@endforeach
            </div>
        </div>
        @endif

        <form action="{{ route('admin.clients.store') }}" method="POST" id="add-client-form"
              style="display:flex;flex-direction:column;flex:1;min-height:0;">
            @csrf

            {{-- Scrollable body --}}
            <div style="flex:1;overflow-y:auto;padding:18px 24px;min-height:0;">

                {{-- Personal Info --}}
                <div class="modal-section-label">Personal Info</div>
                <div class="adm-form-row">
                    <div class="adm-form-group">
                        <label>First Name *</label>
                        <input type="text" name="first_name" value="{{ old('first_name') }}" required>
                    </div>
                    <div class="adm-form-group">
                        <label>Last Name *</label>
                        <input type="text" name="last_name" value="{{ old('last_name') }}" required>
                    </div>
                </div>
                <div class="adm-form-row">
                    <div class="adm-form-group">
                        <label>Middle Name</label>
                        <input type="text" name="middle_name" value="{{ old('middle_name') }}">
                    </div>
                    <div class="adm-form-group">
                        <label>Age</label>
                        <input type="number" name="age" value="{{ old('age') }}" min="1" max="120">
                    </div>
                </div>
                <div class="adm-form-row">
                    <div class="adm-form-group">
                        <label>Email *</label>
                        <input type="email" name="email" value="{{ old('email') }}" required>
                    </div>
                    <div class="adm-form-group">
                        <label>Phone Number</label>
                        <input type="text" name="phone_number" value="{{ old('phone_number') }}">
                    </div>
                </div>

                {{-- Account & Service --}}
                <div class="modal-section-label" style="margin-top:18px;">Account & Service</div>
                <div class="adm-form-group">
                    <label>Username *</label>
                    <input type="text" name="username" value="{{ old('username') }}" required>
                </div>
                <div class="adm-form-group">
                    <label>Plan / Subscription</label>
                    <select name="plan_interest">
                        <option value="">— Select Plan —</option>
                        @foreach(\App\Models\LandingSetting::first()?->plans ?? [] as $plan)
                        <option value="{{ $plan['name'] }}" {{ old('plan_interest') === $plan['name'] ? 'selected' : '' }}>
                            {{ $plan['name'] }} — ₱{{ $plan['price'] }}/mo
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="adm-form-group">
                    <label>Assign MikroTik Router</label>
                    <select name="mikrotik_id">
                        <option value="">— Not assigned —</option>
                        @foreach($mikrotiks as $mt)
                        <option value="{{ $mt->id }}" {{ old('mikrotik_id') == $mt->id ? 'selected' : '' }}>
                            {{ $mt->name }}@if($mt->location) — {{ $mt->location }}@endif
                        </option>
                        @endforeach
                    </select>
                </div>

                {{-- Info Box --}}
                <div style="background:rgba(76,175,80,0.08);border:1px solid rgba(76,175,80,0.3);border-radius:10px;padding:12px 14px;margin-top:14px;font-size:12px;color:rgba(255,255,255,0.7);line-height:1.6;">
                    <strong style="color:#81c784;">ℹ️ Auto-Generated Credentials</strong><br>
                    • PPPoE Username will be auto-generated<br>
                    • Password (5 characters) will be auto-generated<br>
                    • Both will be shown after client creation
                </div>

                {{-- Billing Dates --}}
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

                {{-- Address & Location --}}
                <div class="modal-section-label" style="margin-top:18px;">Address & Location</div>
                <div class="adm-form-group">
                    <label>Full Address</label>
                    <textarea name="address" rows="2" placeholder="House No., Street, Barangay, Municipality">{{ old('address') }}</textarea>
                </div>
                <div class="adm-form-group">
                    <label>📍 Pin Location on Map</label>
                    <div id="add-map" style="height:220px;border-radius:10px;border:1px solid rgba(255,255,255,0.1);margin-top:4px;"></div>
                    <div style="font-size:11px;color:rgba(255,255,255,0.3);text-align:center;margin-top:5px;">Click map to drop pin</div>
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-top:8px;">
                        <div class="adm-form-group" style="margin-bottom:0;">
                            <label>Latitude</label>
                            <input type="text" name="latitude" id="add-lat" value="{{ old('latitude') }}" readonly
                                   style="background:rgba(255,255,255,0.02);color:rgba(255,255,255,0.5);">
                        </div>
                        <div class="adm-form-group" style="margin-bottom:0;">
                            <label>Longitude</label>
                            <input type="text" name="longitude" id="add-lng" value="{{ old('longitude') }}" readonly
                                   style="background:rgba(255,255,255,0.02);color:rgba(255,255,255,0.5);">
                        </div>
                    </div>
                </div>

            </div>{{-- end scrollable body --}}

            {{-- Footer always visible --}}
            <div style="flex-shrink:0;padding:14px 24px;border-top:1px solid rgba(255,255,255,0.07);display:flex;justify-content:flex-end;gap:10px;">
                <button type="button" class="btn-secondary" onclick="closeModal('add-client-modal')">Cancel</button>
                <button type="submit" class="btn-primary">💾 Save Client</button>
            </div>

        </form>
    </div>
</div>


{{-- ══════════════════════════════════════
     VIEW CLIENT MODAL
══════════════════════════════════════ --}}
<div id="view-client-modal" class="modal-backdrop" style="display:none;">
    <div class="modal-box" style="max-width:560px;">
        <div class="modal-header">
            <div class="modal-title">👤 Client Details</div>
            <button class="modal-close" onclick="closeModal('view-client-modal')">✕</button>
        </div>
        <div class="modal-body" id="view-modal-body"></div>
        <div class="modal-footer">
            <button class="btn-secondary" onclick="closeModal('view-client-modal')">Close</button>
            <button class="btn-primary" id="edit-btn" onclick="switchToEditMode()">✏️ Edit</button>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════
     EDIT CLIENT MODAL (in view modal)
══════════════════════════════════════ --}}
<div id="edit-client-modal" class="modal-backdrop" style="display:none;">
    <div class="modal-box" style="max-width:680px;">
        <div class="modal-header">
            <div class="modal-title">✏️ Edit Client</div>
            <button class="modal-close" onclick="closeModal('edit-client-modal')">✕</button>
        </div>
        <form id="edit-form" method="POST" style="display:flex;flex-direction:column;flex:1;min-height:0;">
            @csrf
            <div style="flex:1;overflow-y:auto;padding:18px 24px;min-height:0;">
                <div class="modal-section-label">Personal Info</div>
                <div class="adm-form-row">
                    <div class="adm-form-group">
                        <label>First Name *</label>
                        <input type="text" id="edit-first-name" name="first_name" required>
                    </div>
                    <div class="adm-form-group">
                        <label>Last Name *</label>
                        <input type="text" id="edit-last-name" name="last_name" required>
                    </div>
                </div>
                <div class="adm-form-row">
                    <div class="adm-form-group">
                        <label>Middle Name</label>
                        <input type="text" id="edit-middle-name" name="middle_name">
                    </div>
                    <div class="adm-form-group">
                        <label>Age</label>
                        <input type="number" id="edit-age" name="age" min="1" max="120">
                    </div>
                </div>
                <div class="adm-form-row">
                    <div class="adm-form-group">
                        <label>Email *</label>
                        <input type="text" id="edit-email" name="email" required>
                    </div>
                    <div class="adm-form-group">
                        <label>Phone Number</label>
                        <input type="text" id="edit-phone" name="phone_number">
                    </div>
                </div>
                <div class="adm-form-group">
                    <label>Address</label>
                    <textarea id="edit-address" name="address" rows="2"></textarea>
                </div>

                <div class="modal-section-label" style="margin-top:18px;">Account & Service</div>
                <div class="adm-form-group">
                    <label>Username *</label>
                    <input type="text" id="edit-username" name="username" required>
                </div>
                <div class="adm-form-group">
                    <label>Plan / Subscription</label>
                    <select id="edit-plan" name="plan_interest">
                        <option value="">— Select Plan —</option>
                    </select>
                </div>
                <div class="adm-form-group">
                    <label>PPPoE Username</label>
                    <input type="text" id="edit-pppoe" name="pppoe_username" style="font-family:monospace;">
                </div>
                <div class="adm-form-group">
                    <label>Assign MikroTik Router</label>
                    <select id="edit-mikrotik" name="mikrotik_id">
                        <option value="">— Not assigned —</option>
                    </select>
                </div>

                <div class="modal-section-label" style="margin-top:18px;">Billing & Dates</div>
                <div class="adm-form-row">
                    <div class="adm-form-group">
                        <label>Installation Date</label>
                        <input type="date" id="edit-installation-date" name="installation_date">
                    </div>
                    <div class="adm-form-group">
                        <label>Due Date</label>
                        <input type="date" id="edit-due-date" name="due_date">
                    </div>
                </div>
            </div>
            <div style="flex-shrink:0;padding:14px 24px;border-top:1px solid rgba(255,255,255,0.07);display:flex;justify-content:flex-end;gap:10px;">
                <button type="button" class="btn-secondary" onclick="closeModal('edit-client-modal')">Cancel</button>
                <button type="submit" class="btn-primary">💾 Save Changes</button>
            </div>
        </form>
    </div>
</div>


{{-- Leaflet CSS --}}
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>

<style>
.modal-backdrop {
    position:fixed;inset:0;z-index:2000;
    background:rgba(0,0,0,0.72);
    display:flex;align-items:center;justify-content:center;
    padding:20px;backdrop-filter:blur(4px);
}
.modal-box {
    background:#111;border:1px solid rgba(255,255,255,0.1);
    border-radius:20px;width:100%;
    height:90vh;
    max-height:90vh;
    display:flex;flex-direction:column;
    box-shadow:0 24px 80px rgba(0,0,0,0.6);
    animation:modalIn 0.2s ease;
    overflow:hidden;
}
@keyframes modalIn {
    from{opacity:0;transform:scale(0.96) translateY(10px)}
    to{opacity:1;transform:scale(1) translateY(0)}
}
.modal-header {
    display:flex;align-items:center;justify-content:space-between;
    padding:18px 24px 14px;border-bottom:1px solid rgba(255,255,255,0.07);flex-shrink:0;
}
.modal-title{font-size:16px;font-weight:700;color:#fff;}
.modal-close {
    background:rgba(255,255,255,0.06);border:1px solid rgba(255,255,255,0.1);
    color:rgba(255,255,255,0.6);width:28px;height:28px;border-radius:7px;
    cursor:pointer;font-size:12px;display:flex;align-items:center;justify-content:center;transition:all 0.2s;
}
.modal-close:hover{background:rgba(255,82,82,0.2);color:#ff6b6b;border-color:rgba(255,82,82,0.3);}
.modal-body{padding:18px 24px;overflow-y:auto;flex:1;min-height:0;}
.modal-footer{padding:14px 24px;border-top:1px solid rgba(255,255,255,0.07);display:flex;justify-content:flex-end;gap:10px;flex-shrink:0;}
.modal-section-label {
    font-size:11px;text-transform:uppercase;letter-spacing:1.2px;
    color:rgba(255,82,82,0.7);margin-bottom:12px;
    padding-bottom:8px;border-bottom:1px solid rgba(255,255,255,0.05);
}
.vrow{display:flex;gap:10px;margin-bottom:10px;padding-bottom:10px;border-bottom:1px solid rgba(255,255,255,0.05);}
.vrow:last-child{border-bottom:none;margin-bottom:0;padding-bottom:0;}
.vlabel{font-size:11px;text-transform:uppercase;letter-spacing:0.7px;color:rgba(255,255,255,0.38);width:120px;flex-shrink:0;padding-top:2px;}
.vvalue{font-size:13px;color:#fff;flex:1;}
.copy-btn{background:rgba(129,212,250,0.12);color:#81d4fa;border:1px solid rgba(129,212,250,0.3);padding:4px 8px;border-radius:5px;cursor:pointer;font-size:11px;transition:all 0.2s;}
.copy-btn:hover{background:rgba(129,212,250,0.25);border-color:rgba(129,212,250,0.6);}
</style>

@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
// ── Modal helpers ──
function openModal(id) {
    document.getElementById(id).style.display = 'flex';
    document.body.style.overflow = 'hidden';
    if (id === 'add-client-modal') initAddMap();
}
function closeModal(id) {
    document.getElementById(id).style.display = 'none';
    document.body.style.overflow = '';
}
document.querySelectorAll('.modal-backdrop').forEach(el => {
    el.addEventListener('click', e => { if (e.target === el) closeModal(el.id); });
});
document.addEventListener('keydown', e => {
    if (e.key === 'Escape')
        document.querySelectorAll('.modal-backdrop').forEach(m => {
            if (m.style.display !== 'none') closeModal(m.id);
        });
});

// Re-open add modal on validation errors
@if($errors->any()) openModal('add-client-modal'); @endif

// ── Add Client Map ──
let addMap = null, addMarker = null;
function initAddMap() {
    if (addMap) { setTimeout(() => addMap.invalidateSize(), 100); return; }
    setTimeout(() => {
        addMap = L.map('add-map').setView([9.7517, 122.4003], 15);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution:'© OpenStreetMap', maxZoom:19
        }).addTo(addMap);

        const oldLat = document.getElementById('add-lat').value;
        const oldLng = document.getElementById('add-lng').value;
        if (oldLat && oldLng) {
            addMarker = L.marker([parseFloat(oldLat), parseFloat(oldLng)]).addTo(addMap);
            addMap.setView([parseFloat(oldLat), parseFloat(oldLng)], 15);
        }

        addMap.on('click', e => {
            document.getElementById('add-lat').value = e.latlng.lat.toFixed(7);
            document.getElementById('add-lng').value = e.latlng.lng.toFixed(7);
            if (addMarker) addMarker.setLatLng(e.latlng);
            else addMarker = L.marker(e.latlng).addTo(addMap);
        });
    }, 150);
}

// ── Copy to Clipboard ──
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(() => {
        alert('Copied to clipboard!');
    }).catch(err => {
        console.error('Failed to copy:', err);
    });
}

// ── View Modal ──
let viewMap = null, viewMarker = null;
function openViewModal(data) {
    const statusMap = {
        active:   '<span class="badge badge-green">✓ Active</span>',
        pending:  '<span class="badge badge-yellow">🕐 Pending</span>',
        rejected: '<span class="badge badge-red">✕ Rejected</span>',
    };

    const rows = [
        ['Full Name',  data.name],
        ['Username',   data.username],
        ['Email',      data.email],
        ['Phone',      data.phone],
        ['Age',        data.age],
        ['Plan',       data.plan],
        ['PPPoE Username', data.pppoe ? `<span style="font-family:monospace;color:#81d4fa;">${data.pppoe}</span>` : '<span style="color:rgba(255,255,255,0.3);">Not set</span>'],
        ['PPPoE Password', data.password ? `<span style="font-family:monospace;color:#81d4fa;letter-spacing:2px;">${data.password}</span>` : '<span style="color:rgba(255,255,255,0.3);">Not set</span>'],
        ['MikroTik',   data.mikrotik],
        ['Status',     statusMap[data.status] || data.status],
        ['Installation Date', data.installation_date || '<span style="color:rgba(255,255,255,0.3);">Not set</span>'],
        ['Due Date',   data.due_date || '<span style="color:rgba(255,255,255,0.3);">Not set</span>'],
        ['Verified',   data.verified ? '✓ ' + data.verified : '<span style="color:rgba(255,255,255,0.3);">Not verified</span>'],
        ['Registered', data.registered],
    ];

    let html = rows.map(([l,v]) =>
        `<div class="vrow"><div class="vlabel">${l}</div><div class="vvalue">${v}</div></div>`
    ).join('');

    // Address
    html += `<div class="vrow"><div class="vlabel">Address</div><div class="vvalue" style="font-size:12px;color:rgba(255,255,255,0.7);">${data.address}</div></div>`;

    // Map if coords exist
    if (data.lat && data.lng) {
        html += `<div style="margin-top:12px;">
            <div style="font-size:11px;text-transform:uppercase;letter-spacing:0.7px;color:rgba(255,255,255,0.38);margin-bottom:8px;">📍 Location</div>
            <div id="view-map" style="height:200px;border-radius:10px;border:1px solid rgba(255,255,255,0.1);"></div>
        </div>`;
    }

    document.getElementById('view-modal-body').innerHTML = html;
    openModal('view-client-modal');

    if (data.lat && data.lng) {
        setTimeout(() => {
            if (viewMap) { viewMap.remove(); viewMap = null; }
            viewMap = L.map('view-map').setView([data.lat, data.lng], 15);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution:'© OpenStreetMap', maxZoom:19
            }).addTo(viewMap);
            L.marker([data.lat, data.lng])
             .bindPopup(`<b>${data.name}</b><br>${data.address}`)
             .addTo(viewMap)
             .openPopup();
        }, 150);
    }
}
</script>
@endpush
