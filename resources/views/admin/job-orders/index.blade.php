@extends('admin.layout')
@section('title', 'Job Orders')

@section('content')

<div class="adm-page-header">
    <div>
        <div class="adm-page-title">Job Orders</div>
        <div class="adm-page-subtitle">{{ $jobOrders->total() }} total</div>
    </div>
    <button class="btn-primary" onclick="openModal('add-job-modal')">+ Create Job Order</button>
</div>

<div class="adm-card">
    <div class="adm-table-wrap">
        <table class="adm-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Client</th>
                    <th>Assigned To</th>
                    <th>Status</th>
                    <th>Installation Date</th>
                    <th>Created</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($jobOrders as $job)
                <tr>
                    <td style="color:rgba(255,255,255,0.3);font-size:12px;">{{ $job->id }}</td>
                    <td>
                        <div style="font-weight:600;color:#fff;">{{ $job->client->full_name }}</div>
                        <div style="font-size:11px;color:rgba(255,255,255,0.35);">{{ $job->client->email }}</div>
                    </td>
                    <td>
                        @if($job->installer)
                            <span style="color:#81d4fa;">{{ $job->installer->full_name }}</span>
                        @else
                            <span style="color:rgba(255,255,255,0.3);">Not assigned</span>
                        @endif
                    </td>
                    <td>
                        @if($job->status === 'completed')
                            <span class="badge badge-green">✓ Completed</span>
                        @elseif($job->status === 'ongoing')
                            <span class="badge badge-yellow">⚡ Ongoing</span>
                        @else
                            <span class="badge badge-red">🕐 Pending</span>
                        @endif
                    </td>
                    <td style="font-size:13px;color:rgba(255,255,255,0.6);">
                        {{ $job->installation_date ? $job->installation_date->format('M d, Y') : '—' }}
                    </td>
                    <td style="font-size:12px;color:rgba(255,255,255,0.35);">
                        {{ $job->created_at->format('M d, Y') }}
                    </td>
                    <td>
                        <div style="display:flex;gap:5px;">
                            <button class="btn-secondary btn-sm" onclick="openEditModal({{ json_encode([
                                'id' => $job->id,
                                'client_id' => $job->client_id,
                                'assigned_to' => $job->assigned_to,
                                'status' => $job->status,
                                'installation_date' => $job->installation_date?->format('Y-m-d'),
                                'notes' => $job->notes,
                            ]) }})">✏️ Edit</button>
                            <form action="{{ route('admin.job-orders.delete', $job) }}" method="POST" style="display:inline;"
                                  onsubmit="return confirm('Delete this job order?')">
                                @csrf
                                <button type="submit" class="btn-danger btn-sm">🗑</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="text-align:center;padding:48px;color:rgba(255,255,255,0.25);">
                        No job orders found.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($jobOrders->hasPages())
    <div class="adm-pagination">
        @if($jobOrders->onFirstPage())<span>‹ Prev</span>@else<a href="{{ $jobOrders->previousPageUrl() }}">‹ Prev</a>@endif
        @foreach($jobOrders->getUrlRange(max(1,$jobOrders->currentPage()-2),min($jobOrders->lastPage(),$jobOrders->currentPage()+2)) as $page => $url)
            @if($page==$jobOrders->currentPage())<span class="active-page">{{ $page }}</span>
            @else<a href="{{ $url }}">{{ $page }}</a>@endif
        @endforeach
        @if($jobOrders->hasMorePages())<a href="{{ $jobOrders->nextPageUrl() }}">Next ›</a>@else<span>Next ›</span>@endif
    </div>
    @endif
</div>

{{-- ADD JOB ORDER MODAL --}}
<div id="add-job-modal" class="modal-backdrop" style="display:none;">
    <div class="modal-box" style="max-width:560px;">
        <div class="modal-header">
            <div class="modal-title">➕ Create Job Order</div>
            <button class="modal-close" onclick="closeModal('add-job-modal')">✕</button>
        </div>

        <form action="{{ route('admin.job-orders.store') }}" method="POST">
            @csrf
            <div class="modal-body">
                <div class="adm-form-group">
                    <label>Client *</label>
                    <select name="client_id" required>
                        <option value="">— Select Client —</option>
                        @foreach($clients as $client)
                        <option value="{{ $client->id }}">{{ $client->full_name }} ({{ $client->email }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="adm-form-group">
                    <label>Assign Installer</label>
                    <select name="assigned_to">
                        <option value="">— Not assigned —</option>
                        @foreach($installers as $installer)
                        <option value="{{ $installer->id }}">{{ $installer->full_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="adm-form-group">
                    <label>Installation Date</label>
                    <input type="date" name="installation_date">
                </div>
                <div class="adm-form-group">
                    <label>Notes</label>
                    <textarea name="notes" rows="3"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-secondary" onclick="closeModal('add-job-modal')">Cancel</button>
                <button type="submit" class="btn-primary">💾 Create Job Order</button>
            </div>
        </form>
    </div>
</div>

{{-- EDIT JOB ORDER MODAL --}}
<div id="edit-job-modal" class="modal-backdrop" style="display:none;">
    <div class="modal-box" style="max-width:560px;">
        <div class="modal-header">
            <div class="modal-title">✏️ Edit Job Order</div>
            <button class="modal-close" onclick="closeModal('edit-job-modal')">✕</button>
        </div>

        <form id="edit-job-form" method="POST">
            @csrf
            <div class="modal-body">
                <div class="adm-form-group">
                    <label>Assign Installer</label>
                    <select name="assigned_to" id="edit-assigned-to">
                        <option value="">— Not assigned —</option>
                        @foreach($installers as $installer)
                        <option value="{{ $installer->id }}">{{ $installer->full_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="adm-form-group">
                    <label>Status *</label>
                    <select name="status" id="edit-status" required>
                        <option value="pending">🕐 Pending</option>
                        <option value="ongoing">⚡ Ongoing</option>
                        <option value="completed">✓ Completed</option>
                    </select>
                </div>
                <div class="adm-form-group">
                    <label>Installation Date</label>
                    <input type="date" name="installation_date" id="edit-installation-date">
                </div>
                <div class="adm-form-group">
                    <label>Notes</label>
                    <textarea name="notes" id="edit-notes" rows="3"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-secondary" onclick="closeModal('edit-job-modal')">Cancel</button>
                <button type="submit" class="btn-primary">💾 Update Job Order</button>
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
.modal-footer{padding:14px 24px 18px;border-top:1px solid rgba(255,255,255,0.07);display:flex;justify-content:flex-end;gap:10px;flex-shrink:0;}

#add-job-modal .adm-form-group,
#edit-job-modal .adm-form-group {
    margin-bottom: 16px;
}
#add-job-modal .adm-form-group label,
#edit-job-modal .adm-form-group label {
    display: block;
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.8px;
    color: rgba(255,255,255,0.6);
    margin-bottom: 8px;
}
#add-job-modal .adm-form-group select,
#add-job-modal .adm-form-group input,
#add-job-modal .adm-form-group textarea,
#edit-job-modal .adm-form-group select,
#edit-job-modal .adm-form-group input,
#edit-job-modal .adm-form-group textarea {
    width: 100%;
    padding: 12px;
    border-radius: 10px;
    border: 1px solid rgba(255,255,255,0.1);
    background: rgba(255,255,255,0.04);
    color: #fff;
    font-size: 14px;
    font-family: inherit;
}
#add-job-modal .adm-form-group select:focus,
#add-job-modal .adm-form-group input:focus,
#add-job-modal .adm-form-group textarea:focus,
#edit-job-modal .adm-form-group select:focus,
#edit-job-modal .adm-form-group input:focus,
#edit-job-modal .adm-form-group textarea:focus {
    outline: none;
    border-color: rgba(255,82,82,0.5);
    background: rgba(255,255,255,0.06);
}
#add-job-modal .adm-form-group select option,
#edit-job-modal .adm-form-group select option {
    background: #1a1a1a;
    color: #fff;
}
#add-job-modal .adm-form-group textarea,
#edit-job-modal .adm-form-group textarea {
    resize: vertical;
    min-height: 80px;
}

body.light-mode .modal-backdrop { background: rgba(0,0,0,0.3); }
body.light-mode .modal-box { background: #fff; border: 1px solid rgba(0,0,0,0.1); }
body.light-mode .modal-title { color: #000; }
body.light-mode .modal-close { background: rgba(0,0,0,0.06); border: 1px solid rgba(0,0,0,0.1); color: rgba(0,0,0,0.6); }
body.light-mode .modal-close:hover { background: rgba(255,82,82,0.2); color: #ff6b6b; border-color: rgba(255,82,82,0.3); }
body.light-mode .modal-header, body.light-mode .modal-footer { border-color: rgba(0,0,0,0.1); }
body.light-mode #add-job-modal .adm-form-group label,
body.light-mode #edit-job-modal .adm-form-group label { color: #000; }
body.light-mode #add-job-modal .adm-form-group select,
body.light-mode #add-job-modal .adm-form-group input,
body.light-mode #add-job-modal .adm-form-group textarea,
body.light-mode #edit-job-modal .adm-form-group select,
body.light-mode #edit-job-modal .adm-form-group input,
body.light-mode #edit-job-modal .adm-form-group textarea {
    background: rgba(0,0,0,0.04);
    color: #000;
    border: 1px solid rgba(0,0,0,0.1);
}
body.light-mode #add-job-modal .adm-form-group select:focus,
body.light-mode #add-job-modal .adm-form-group input:focus,
body.light-mode #add-job-modal .adm-form-group textarea:focus,
body.light-mode #edit-job-modal .adm-form-group select:focus,
body.light-mode #edit-job-modal .adm-form-group input:focus,
body.light-mode #edit-job-modal .adm-form-group textarea:focus {
    border-color: rgba(0,123,255,0.5);
    background: rgba(0,0,0,0.06);
}
body.light-mode #add-job-modal .adm-form-group select option,
body.light-mode #edit-job-modal .adm-form-group select option {
    background: #fff;
    color: #000;
}
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

function openEditModal(data) {
    document.getElementById('edit-job-form').action = `/admin/job-orders/${data.id}/update`;
    document.getElementById('edit-assigned-to').value = data.assigned_to || '';
    document.getElementById('edit-status').value = data.status;
    document.getElementById('edit-installation-date').value = data.installation_date || '';
    document.getElementById('edit-notes').value = data.notes || '';
    openModal('edit-job-modal');
}
</script>

@endsection
