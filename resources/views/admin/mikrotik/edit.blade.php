@extends('admin.layout')
@section('title', 'Edit MikroTik Router')

@section('content')
<div class="adm-page-header">
    <div>
        <div class="adm-page-title">✏️ {{ $mikrotik->name }}</div>
        <div class="adm-page-subtitle">Edit router settings and manage assigned clients.</div>
    </div>
    <div style="display:flex; gap:10px;">
        <button class="btn-success" id="test-btn" onclick="testRouter()">⚡ Test Connection</button>
        <a href="{{ route('admin.mikrotik') }}" class="btn-secondary">← Back</a>
    </div>
</div>

{{-- Test result --}}
<div id="test-result" style="display:none; margin-bottom:20px; padding:14px 18px; border-radius:12px; font-size:14px;"></div>

<div style="display:grid; grid-template-columns:1fr 1fr; gap:20px; align-items:start;">

    {{-- Router Settings --}}
    <div class="adm-card">
        <h3 style="font-size:14px; font-weight:700; color:rgba(255,255,255,0.5); text-transform:uppercase; letter-spacing:1px; margin-bottom:18px;">Router Settings</h3>

        @if($errors->any())
        <div style="background:rgba(255,82,82,0.1); border:1px solid rgba(255,82,82,0.3); border-radius:10px; padding:12px 16px; margin-bottom:18px; color:#ff8a80; font-size:13px;">
            @foreach($errors->all() as $e)<div>• {{ $e }}</div>@endforeach
        </div>
        @endif

        <form action="{{ route('admin.mikrotik.update', $mikrotik) }}" method="POST">
            @csrf

            <div class="adm-form-row">
                <div class="adm-form-group">
                    <label>Router Name *</label>
                    <input type="text" name="name" value="{{ old('name', $mikrotik->name) }}" required>
                </div>
                <div class="adm-form-group">
                    <label>Location</label>
                    <input type="text" name="location" value="{{ old('location', $mikrotik->location) }}">
                </div>
            </div>

            <div class="adm-form-row">
                <div class="adm-form-group">
                    <label>ZeroTier Network ID</label>
                    <input type="text" name="zerotier_network_id" value="{{ old('zerotier_network_id', $mikrotik->zerotier_network_id) }}"
                           style="font-family:monospace;" maxlength="16">
                </div>
                <div class="adm-form-group">
                    <label>ZeroTier IP *</label>
                    <input type="text" name="ip_address" value="{{ old('ip_address', $mikrotik->ip_address) }}"
                           style="font-family:monospace;" required>
                </div>
            </div>

            <div class="adm-form-row">
                <div class="adm-form-group">
                    <label>API Port *</label>
                    <input type="number" name="port" value="{{ old('port', $mikrotik->port) }}" required>
                </div>
                <div class="adm-form-group">
                    <label>Status</label>
                    <select name="is_active">
                        <option value="1" {{ old('is_active', $mikrotik->is_active ? '1' : '0') == '1' ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ old('is_active', $mikrotik->is_active ? '1' : '0') == '0' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
            </div>

            <div class="adm-form-row">
                <div class="adm-form-group">
                    <label>API Username *</label>
                    <input type="text" name="username" value="{{ old('username', $mikrotik->username) }}" required>
                </div>
                <div class="adm-form-group">
                    <label>API Password</label>
                    <input type="password" name="password" placeholder="Leave blank to keep current" autocomplete="new-password">
                </div>
            </div>

            <div class="adm-form-group">
                <label>Notes</label>
                <textarea name="notes" rows="2">{{ old('notes', $mikrotik->notes) }}</textarea>
            </div>

            <div class="adm-form-actions">
                <button type="submit" class="btn-primary">💾 Update Router</button>
            </div>
        </form>
    </div>

    {{-- Assign Clients --}}
    <div class="adm-card">
        <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:18px;">
            <h3 style="font-size:14px; font-weight:700; color:rgba(255,255,255,0.5); text-transform:uppercase; letter-spacing:1px;">
                Assign Clients
            </h3>
            <span class="badge badge-yellow">{{ $mikrotik->clients_count }} assigned</span>
        </div>

        <p style="font-size:12px; color:rgba(255,255,255,0.4); margin-bottom:16px; line-height:1.6;">
            Select all clients that connect through this MikroTik router. Saving will replace the current assignment.
        </p>

        <form action="{{ route('admin.mikrotik.assign', $mikrotik) }}" method="POST">
            @csrf

            {{-- Search filter --}}
            <input type="text" id="client-search" placeholder="🔍 Filter clients..."
                   oninput="filterClients(this.value)"
                   style="width:100%; padding:10px 14px; border-radius:10px; border:1px solid rgba(255,255,255,0.1);
                          background:rgba(255,255,255,0.04); color:#fff; font-size:13px; margin-bottom:12px;">

            <div id="client-list"
                 style="max-height:340px; overflow-y:auto; display:flex; flex-direction:column; gap:6px;
                        padding-right:4px;">
                @forelse($clients as $client)
                <label id="cl-{{ $client->id }}"
                       style="display:flex; align-items:center; gap:12px; padding:10px 12px; border-radius:10px;
                              border:1px solid rgba(255,255,255,0.06); background:rgba(255,255,255,0.03);
                              cursor:pointer; transition:background 0.15s;"
                       onmouseover="this.style.background='rgba(255,255,255,0.06)'"
                       onmouseout="this.style.background='rgba(255,255,255,0.03)'">
                    <input type="checkbox" name="client_ids[]" value="{{ $client->id }}"
                           {{ $client->mikrotik_id == $mikrotik->id ? 'checked' : '' }}
                           style="width:16px; height:16px; accent-color:#ff5252; flex-shrink:0;">
                    <div style="min-width:0;">
                        <div style="font-size:13px; font-weight:600; color:#fff; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                            {{ $client->full_name }}
                        </div>
                        <div style="font-size:11px; color:rgba(255,255,255,0.4); margin-top:1px;">
                            {{ $client->username }} · {{ $client->email }}
                        </div>
                    </div>
                    @if($client->mikrotik_id == $mikrotik->id)
                    <span class="badge badge-green" style="margin-left:auto; flex-shrink:0; font-size:10px;">Assigned</span>
                    @elseif($client->mikrotik_id)
                    <span class="badge badge-yellow" style="margin-left:auto; flex-shrink:0; font-size:10px;">Other</span>
                    @endif
                </label>
                @empty
                <div style="text-align:center; padding:24px; color:rgba(255,255,255,0.3); font-size:13px;">
                    No clients available.
                </div>
                @endforelse
            </div>

            @if($clients->isNotEmpty())
            <div style="display:flex; gap:8px; margin-top:12px; padding-top:12px; border-top:1px solid rgba(255,255,255,0.06);">
                <button type="button" onclick="selectAll(true)" class="btn-secondary btn-sm">Select All</button>
                <button type="button" onclick="selectAll(false)" class="btn-secondary btn-sm">Clear All</button>
                <button type="submit" class="btn-primary btn-sm" style="margin-left:auto;">💾 Save Assignment</button>
            </div>
            @endif
        </form>
    </div>

</div>
@endsection

@push('scripts')
<script>
async function testRouter() {
    const btn = document.getElementById('test-btn');
    const el  = document.getElementById('test-result');
    btn.disabled    = true;
    btn.textContent = '⏳ Testing...';
    el.style.display    = 'block';
    el.style.background = 'rgba(255,255,255,0.04)';
    el.style.border     = '1px solid rgba(255,255,255,0.08)';
    el.style.color      = 'rgba(255,255,255,0.6)';
    el.textContent      = 'Connecting to {{ $mikrotik->ip_address }}:{{ $mikrotik->port }}...';

    try {
        const res  = await fetch('{{ route("admin.mikrotik.test", $mikrotik) }}', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
        });
        const data = await res.json();
        if (data.success) {
            el.style.background = 'rgba(76,175,80,0.1)';
            el.style.border     = '1px solid rgba(76,175,80,0.3)';
            el.style.color      = '#66bb6a';
            el.textContent      = '✅ ' + data.message;
        } else {
            el.style.background = 'rgba(255,82,82,0.1)';
            el.style.border     = '1px solid rgba(255,82,82,0.3)';
            el.style.color      = '#ff6b6b';
            el.textContent      = '❌ ' + data.message;
        }
    } catch(e) {
        el.style.background = 'rgba(255,82,82,0.1)';
        el.style.border     = '1px solid rgba(255,82,82,0.3)';
        el.style.color      = '#ff6b6b';
        el.textContent      = '❌ Request failed: ' + e.message;
    }
    btn.disabled    = false;
    btn.textContent = '⚡ Test Connection';
}

function filterClients(q) {
    q = q.toLowerCase();
    document.querySelectorAll('#client-list label').forEach(el => {
        el.style.display = el.textContent.toLowerCase().includes(q) ? '' : 'none';
    });
}

function selectAll(state) {
    document.querySelectorAll('#client-list input[type=checkbox]').forEach(cb => cb.checked = state);
}
</script>
@endpush
