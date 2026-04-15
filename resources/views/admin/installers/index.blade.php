@extends('admin.layout')
@section('title', 'Installers')

@section('content')

<div class="adm-page-header">
    <div>
        <div class="adm-page-title">Installers / Technicians</div>
        <div class="adm-page-subtitle">{{ $installers->total() }} total</div>
    </div>
    <button class="btn-primary" onclick="openModal('add-installer-modal')">+ Add Installer</button>
</div>

<div class="adm-card">
    <div class="adm-table-wrap">
        <table class="adm-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Username</th>
                    <th>Job Orders</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($installers as $installer)
                <tr>
                    <td style="color:rgba(255,255,255,0.3);font-size:12px;">{{ $installer->id }}</td>
                    <td>
                        <div style="font-weight:600;color:#fff;">{{ $installer->full_name }}</div>
                    </td>
                    <td style="font-size:13px;color:rgba(255,255,255,0.6);">{{ $installer->email }}</td>
                    <td style="font-size:13px;color:rgba(255,255,255,0.6);">{{ $installer->phone_number ?? '—' }}</td>
                    <td style="font-family:monospace;font-size:12px;color:#81d4fa;">{{ $installer->username }}</td>
                    <td>
                        <span class="badge badge-yellow">{{ $installer->jobOrders()->count() }} orders</span>
                    </td>
                    <td>
                        <div style="display:flex;gap:5px;">
                            <a href="{{ route('admin.installers.edit', $installer) }}" class="btn-secondary btn-sm">✏️ Edit</a>
                            <form action="{{ route('admin.installers.delete', $installer) }}" method="POST" style="display:inline;"
                                  onsubmit="return confirm('Delete {{ addslashes($installer->full_name) }}?')">
                                @csrf
                                <button type="submit" class="btn-danger btn-sm">🗑</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="text-align:center;padding:48px;color:rgba(255,255,255,0.25);">
                        No installers found.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($installers->hasPages())
    <div class="adm-pagination">
        @if($installers->onFirstPage())<span>‹ Prev</span>@else<a href="{{ $installers->previousPageUrl() }}">‹ Prev</a>@endif
        @foreach($installers->getUrlRange(max(1,$installers->currentPage()-2),min($installers->lastPage(),$installers->currentPage()+2)) as $page => $url)
            @if($page==$installers->currentPage())<span class="active-page">{{ $page }}</span>
            @else<a href="{{ $url }}">{{ $page }}</a>@endif
        @endforeach
        @if($installers->hasMorePages())<a href="{{ $installers->nextPageUrl() }}">Next ›</a>@else<span>Next ›</span>@endif
    </div>
    @endif
</div>

{{-- ADD INSTALLER MODAL --}}
<div id="add-installer-modal" class="modal-backdrop" style="display:none;">
    <div class="modal-box" style="max-width:560px;">
        <div class="modal-header">
            <div class="modal-title">➕ Add New Installer</div>
            <button class="modal-close" onclick="closeModal('add-installer-modal')">✕</button>
        </div>

        @if($errors->any())
        <div style="padding:0 24px;">
            <div style="background:rgba(255,82,82,0.1);border:1px solid rgba(255,82,82,0.3);border-radius:10px;padding:10px 14px;color:#ff8a80;font-size:13px;">
                @foreach($errors->all() as $e)<div>• {{ $e }}</div>@endforeach
            </div>
        </div>
        @endif

        <form action="{{ route('admin.installers.store') }}" method="POST">
            @csrf
            <div class="modal-body">
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
                <div class="adm-form-group">
                    <label>Middle Name</label>
                    <input type="text" name="middle_name" value="{{ old('middle_name') }}">
                </div>
                <div class="adm-form-group">
                    <label>Email *</label>
                    <input type="email" name="email" value="{{ old('email') }}" required>
                </div>
                <div class="adm-form-group">
                    <label>Phone Number</label>
                    <input type="text" name="phone_number" value="{{ old('phone_number') }}">
                </div>
                <div class="adm-form-group">
                    <label>Password *</label>
                    <input type="password" name="password" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-secondary" onclick="closeModal('add-installer-modal')">Cancel</button>
                <button type="submit" class="btn-primary">💾 Save Installer</button>
            </div>
        </form>
    </div>
</div>

<style>
.modal-backdrop {
    position:fixed;inset:0;z-index:2000;
    background:rgba(0,0,0,0.72);
    display:flex;align-items:center;justify-content:center;
    padding:20px;backdrop-filter:blur(4px);
}
.modal-box {
    background:#111;border:1px solid rgba(255,255,255,0.1);
    border-radius:20px;width:100%;max-height:90vh;
    display:flex;flex-direction:column;
    box-shadow:0 24px 80px rgba(0,0,0,0.6);
    animation:modalIn 0.2s ease;overflow:hidden;
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

body.light-mode .modal-backdrop { background: rgba(0,0,0,0.3); }
body.light-mode .modal-box { background: #fff; border: 1px solid rgba(0,0,0,0.1); }
body.light-mode .modal-title { color: #000; }
body.light-mode .modal-close { background: rgba(0,0,0,0.06); border: 1px solid rgba(0,0,0,0.1); color: rgba(0,0,0,0.6); }
body.light-mode .modal-close:hover { background: rgba(255,82,82,0.2); color: #ff6b6b; border-color: rgba(255,82,82,0.3); }
body.light-mode .modal-header, body.light-mode .modal-footer { border-color: rgba(0,0,0,0.1); }
</style>

<script>
function openModal(id) {
    document.getElementById(id).style.display = 'flex';
    document.body.style.overflow = 'hidden';
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

@if($errors->any()) openModal('add-installer-modal'); @endif
</script>

@endsection
