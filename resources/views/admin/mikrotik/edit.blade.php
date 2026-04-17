@extends('admin.layout')
@section('title', 'Edit MikroTik Router')

@push('styles')
<style>
    .mikrotik-edit-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
        align-items: start;
    }
    
    .mikrotik-edit-grid > .adm-card {
        margin-bottom: 0;
    }
    
    .mikrotik-right-column {
        display: flex;
        flex-direction: column;
        gap: 20px;
    }
    
    @media (max-width: 1024px) {
        .mikrotik-edit-grid {
            grid-template-columns: 1fr;
            gap: 24px;
        }
        
        .mikrotik-right-column {
            gap: 24px;
        }
    }
    
    @media (max-width: 768px) {
        .mikrotik-edit-grid {
            gap: 20px;
        }
        
        .mikrotik-right-column {
            gap: 20px;
        }
        
        .adm-form-actions {
            flex-direction: column;
        }
        
        .adm-form-actions button {
            width: 100%;
            margin-left: 0 !important;
        }
        
        .napbox-list label,
        .client-list label {
            padding: 14px 12px !important;
        }
        
        .napbox-list input[type="radio"],
        .client-list input[type="checkbox"] {
            width: 20px !important;
            height: 20px !important;
        }
        
        #client-search {
            font-size: 16px !important;
        }
    }
</style>
@endpush

@section('content')
<div class="adm-page-header">
    <div>
        <div class="adm-page-title">✏️ {{ $mikrotik->name }}</div>
        <div class="adm-page-subtitle">Edit router settings, test connection, and manage NapBoxes.</div>
    </div>
    <a href="{{ route('admin.mikrotik') }}" class="btn-secondary">← Back</a>
</div>

{{-- Test Connection Modal --}}
<div id="test-modal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.7); z-index:9999; align-items:center; justify-content:center;">
    <div style="background:rgba(18,18,18,0.98); border:1px solid rgba(255,255,255,0.1); border-radius:18px; padding:32px; max-width:480px; width:90%; box-shadow:0 8px 32px rgba(0,0,0,0.5);">
        <div id="test-modal-content" style="text-align:center;">
            <div id="test-modal-icon" style="font-size:64px; margin-bottom:16px;">⏳</div>
            <div id="test-modal-title" style="font-size:20px; font-weight:700; color:#fff; margin-bottom:8px;">Testing Connection...</div>
            <div id="test-modal-message" style="font-size:14px; color:rgba(255,255,255,0.6); line-height:1.6;">Connecting to {{ $mikrotik->ip_address }}:8728</div>
        </div>
    </div>
</div>

<div class="mikrotik-edit-grid">

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
                    <label>ZeroTier IP Address *</label>
                    <input type="text" name="ip_address" value="{{ old('ip_address', $mikrotik->ip_address) }}"
                           style="font-family:monospace;" required>
                    <div style="font-size:11px; color:rgba(255,255,255,0.4); margin-top:4px;">
                        Use ZeroTier IP or any IP that can reach the MikroTik API
                    </div>
                </div>
            </div>

            <div class="adm-form-group">
                <label>Deployment Location (Area Identifier)</label>
                <input type="text" name="location" value="{{ old('location', $mikrotik->location) }}">
                <div style="font-size:11px; color:rgba(255,255,255,0.4); margin-top:4px;">
                    Optional identifier for where this router is physically deployed
                </div>
            </div>

            <h3 style="font-size:14px; font-weight:700; color:rgba(255,255,255,0.5); text-transform:uppercase; letter-spacing:1px; margin:20px 0 16px;">Connection & Credentials</h3>

            <div class="adm-form-row">
                <div class="adm-form-group">
                    <label>MikroTik Username *</label>
                    <input type="text" name="username" value="{{ old('username', $mikrotik->username) }}" required>
                </div>
                <div class="adm-form-group">
                    <label>MikroTik Password</label>
                    <input type="password" name="password" placeholder="Leave blank to keep current" autocomplete="new-password">
                </div>
            </div>

            <input type="hidden" name="port" value="{{ $mikrotik->port }}">
            <input type="hidden" name="is_active" value="1">

            <div class="adm-form-group">
                <label>Notes</label>
                <textarea name="notes" rows="2">{{ old('notes', $mikrotik->notes) }}</textarea>
            </div>

            <div class="adm-form-actions">
                <button type="button" class="btn-success" onclick="testRouter()">⚡ Test Connection</button>
                <button type="submit" class="btn-primary" style="margin-left:auto;">💾 Update Router</button>
            </div>
        </form>
    </div>

    <div class="mikrotik-right-column">
        {{-- Assign NapBoxes --}}
        <div class="adm-card">
        <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:18px;">
            <h3 style="font-size:14px; font-weight:700; color:rgba(255,255,255,0.5); text-transform:uppercase; letter-spacing:1px;">
                Assign NapBoxes
            </h3>
            <span class="badge badge-yellow">{{ $mikrotik->napboxes->count() }} assigned</span>
        </div>

        <p style="font-size:12px; color:rgba(255,255,255,0.4); margin-bottom:16px; line-height:1.6;">
            Select NapBoxes that are connected to this MikroTik router. These will appear in the location served.
        </p>

        <form action="{{ route('admin.mikrotik.assign-napboxes', $mikrotik) }}" method="POST">
            @csrf

            <div style="max-height:340px; overflow-y:auto; display:flex; flex-direction:column; gap:6px; padding-right:4px;" class="napbox-list">
                @php
                    $napboxes = \App\Models\Napbox::orderBy('location')->orderBy('name')->get();
                @endphp
                @forelse($napboxes as $napbox)
                <label style="display:flex; align-items:center; gap:12px; padding:10px 12px; border-radius:10px;
                              border:1px solid rgba(255,255,255,0.06); background:rgba(255,255,255,0.03);
                              cursor:pointer; transition:background 0.15s;"
                       onmouseover="this.style.background='rgba(255,255,255,0.06)'"
                       onmouseout="this.style.background='rgba(255,255,255,0.03)'">
                    <input type="radio" name="napbox_{{ $napbox->id }}" value="1"
                           {{ $napbox->mikrotik_id == $mikrotik->id ? 'checked' : '' }}
                           style="width:16px; height:16px; accent-color:#ff5252; flex-shrink:0;">
                    <div style="min-width:0;">
                        <div style="font-size:13px; font-weight:600; color:#fff;">
                            {{ $napbox->name }}
                        </div>
                        <div style="font-size:11px; color:rgba(255,255,255,0.4); margin-top:1px;">
                            📍 {{ $napbox->location }}
                        </div>
                    </div>
                    @if($napbox->mikrotik_id == $mikrotik->id)
                    <span class="badge badge-green" style="margin-left:auto; flex-shrink:0; font-size:10px;">Assigned</span>
                    @elseif($napbox->mikrotik_id)
                    <span class="badge badge-yellow" style="margin-left:auto; flex-shrink:0; font-size:10px;">Other Router</span>
                    @endif
                </label>
                @empty
                <div style="text-align:center; padding:24px; color:rgba(255,255,255,0.3); font-size:13px;">
                    No NapBoxes available. <a href="#" style="color:#81d4fa;">Create one first</a>.
                </div>
                @endforelse
            </div>

            @if($napboxes->isNotEmpty())
            <div style="display:flex; gap:8px; margin-top:12px; padding-top:12px; border-top:1px solid rgba(255,255,255,0.06);">
                <button type="submit" class="btn-primary btn-sm" style="margin-left:auto;">💾 Save NapBoxes</button>
            </div>
            @endif
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
                        padding-right:4px;" class="client-list">
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

</div>
@endsection

@push('scripts')
<script>
let testModalTimeout;

async function testRouter() {
    const modal = document.getElementById('test-modal');
    const icon = document.getElementById('test-modal-icon');
    const title = document.getElementById('test-modal-title');
    const message = document.getElementById('test-modal-message');
    
    // Clear any existing timeout
    if (testModalTimeout) clearTimeout(testModalTimeout);
    
    // Show modal with loading state
    modal.style.display = 'flex';
    icon.textContent = '⏳';
    title.textContent = 'Testing Connection...';
    title.style.color = '#fff';
    message.textContent = 'Connecting to {{ $mikrotik->ip_address }}:8728';
    message.style.color = 'rgba(255,255,255,0.6)';

    try {
        const res = await fetch('{{ route("admin.mikrotik.test", $mikrotik) }}', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
        });
        const data = await res.json();
        
        if (data.success) {
            icon.textContent = '✅';
            title.textContent = 'Connection Successful!';
            title.style.color = '#66bb6a';
            message.textContent = data.message;
            message.style.color = 'rgba(102,187,106,0.8)';
        } else {
            icon.textContent = '❌';
            title.textContent = 'Connection Failed';
            title.style.color = '#ff6b6b';
            message.textContent = data.message;
            message.style.color = 'rgba(255,107,107,0.8)';
        }
    } catch(e) {
        icon.textContent = '❌';
        title.textContent = 'Connection Failed';
        title.style.color = '#ff6b6b';
        message.textContent = 'Request failed: ' + e.message;
        message.style.color = 'rgba(255,107,107,0.8)';
    }
    
    // Auto-close after 4.5 seconds
    testModalTimeout = setTimeout(() => {
        modal.style.display = 'none';
    }, 4500);
}

// Close modal on click outside
document.getElementById('test-modal')?.addEventListener('click', (e) => {
    if (e.target.id === 'test-modal') {
        if (testModalTimeout) clearTimeout(testModalTimeout);
        e.target.style.display = 'none';
    }
});

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
