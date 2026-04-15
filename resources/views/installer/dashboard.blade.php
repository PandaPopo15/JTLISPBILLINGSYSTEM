@extends('installer.layout')
@section('title', 'My Job Orders')

@section('content')

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>

<div class="adm-page-header">
    <div>
        <div class="adm-page-title">My Job Orders</div>
        <div class="adm-page-subtitle">{{ $jobOrders->total() }} total assignments</div>
    </div>
</div>

<div class="adm-kpi-grid">
    <div class="adm-kpi">
        <div class="adm-kpi-label">🕐 Pending</div>
        <div class="adm-kpi-value">{{ $stats['pending'] }}</div>
    </div>
    <div class="adm-kpi">
        <div class="adm-kpi-label">⚡ Ongoing</div>
        <div class="adm-kpi-value">{{ $stats['ongoing'] }}</div>
    </div>
    <div class="adm-kpi">
        <div class="adm-kpi-label">✓ Completed</div>
        <div class="adm-kpi-value">{{ $stats['completed'] }}</div>
    </div>
</div>

<div class="adm-card">
    <div class="adm-table-wrap">
        <table class="adm-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Client</th>
                    <th>Contact</th>
                    <th>Status</th>
                    <th>Installation Date</th>
                    <th>Notes</th>
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
                    <td style="font-size:13px;color:rgba(255,255,255,0.6);">
                        {{ $job->client->phone_number ?? '—' }}
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
                    <td style="font-size:12px;color:rgba(255,255,255,0.5);max-width:200px;">
                        {{ $job->notes ? Str::limit($job->notes, 50) : '—' }}
                    </td>
                    <td>
                        <div style="display:flex;gap:5px;">
                            <button class="btn-secondary btn-sm" type="button" onclick='openViewModal({{ json_encode([
                                "id" => $job->id,
                                "client" => $job->client->full_name,
                                "email" => $job->client->email,
                                "phone" => $job->client->phone_number,
                                "address" => $job->client->address,
                                "location" => $job->client->location,
                                "plan" => $job->client->plan_interest,
                                "status" => $job->status,
                                "installation_date" => $job->installation_date?->format("M d, Y"),
                                "notes" => $job->notes,
                                "created" => $job->created_at->format("M d, Y h:i A"),
                            ]) }}); return false;'>👁️ View</button>
                            <button class="btn-secondary btn-sm" type="button" onclick='openUpdateModal({{ json_encode([
                                "id" => $job->id,
                                "client" => $job->client->full_name,
                                "status" => $job->status,
                                "notes" => $job->notes,
                            ]) }}); return false;'>✏️ Update</button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="text-align:center;padding:48px;color:rgba(255,255,255,0.25);">
                        No job orders assigned to you yet.
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

{{-- VIEW CLIENT DETAILS MODAL --}}
<div id="view-modal" class="modal-backdrop" style="display:none;">
    <div class="modal-box" style="max-width:900px;">
        <div class="modal-header">
            <div class="modal-title">👁️ Job Order Details</div>
            <button class="modal-close" onclick="closeModal('view-modal')">✕</button>
        </div>

        <div class="modal-body" style="padding:0;">
            <div style="padding:18px 24px;">
                <div style="display:grid;gap:16px;">
                    <div class="detail-section">
                        <div class="detail-label">Job Order ID</div>
                        <div class="detail-value" id="view-job-id"></div>
                    </div>
                    
                    <div style="border-top:1px solid rgba(255,255,255,0.08);padding-top:16px;">
                        <div style="font-size:13px;font-weight:600;color:rgba(255,255,255,0.7);margin-bottom:12px;text-transform:uppercase;letter-spacing:1px;">Client Information</div>
                        <div style="display:grid;gap:12px;">
                            <div class="detail-section">
                                <div class="detail-label">Full Name</div>
                                <div class="detail-value" id="view-client-name"></div>
                            </div>
                            <div class="detail-section">
                                <div class="detail-label">Email Address</div>
                                <div class="detail-value" id="view-client-email"></div>
                            </div>
                            <div class="detail-section">
                                <div class="detail-label">Phone Number</div>
                                <div class="detail-value" id="view-client-phone"></div>
                            </div>
                            <div class="detail-section">
                                <div class="detail-label">Address</div>
                                <div class="detail-value" id="view-client-address"></div>
                            </div>
                            <div class="detail-section">
                                <div class="detail-label">Plan Interest</div>
                                <div class="detail-value" id="view-client-plan"></div>
                            </div>
                        </div>
                    </div>

                    <div style="border-top:1px solid rgba(255,255,255,0.08);padding-top:16px;">
                        <div style="font-size:13px;font-weight:600;color:rgba(255,255,255,0.7);margin-bottom:12px;text-transform:uppercase;letter-spacing:1px;">Job Details</div>
                        <div style="display:grid;gap:12px;">
                            <div class="detail-section">
                                <div class="detail-label">Status</div>
                                <div class="detail-value" id="view-status"></div>
                            </div>
                            <div class="detail-section">
                                <div class="detail-label">Installation Date</div>
                                <div class="detail-value" id="view-installation-date"></div>
                            </div>
                            <div class="detail-section">
                                <div class="detail-label">Created At</div>
                                <div class="detail-value" id="view-created"></div>
                            </div>
                            <div class="detail-section">
                                <div class="detail-label">Notes</div>
                                <div class="detail-value" id="view-notes" style="white-space:pre-wrap;"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            {{-- INLINE MAP SECTION --}}
            <div id="location-map-section" style="display:none;">
                <div style="padding:0 24px 16px;">
                    <div style="font-size:13px;font-weight:600;color:rgba(255,255,255,0.7);margin-bottom:12px;text-transform:uppercase;letter-spacing:1px;border-top:1px solid rgba(255,255,255,0.08);padding-top:16px;">📍 Client Location</div>
                </div>
                <div id="inline-map" style="width:100%;height:350px;border-top:1px solid rgba(255,255,255,0.08);border-bottom:1px solid rgba(255,255,255,0.08);"></div>
                <div style="padding:16px 24px;display:flex;gap:10px;">
                    <button type="button" class="btn-secondary" onclick="openMapModal()" style="flex:1;">🗺️ View Fullscreen</button>
                    <a id="inline-directions-btn" href="" target="_blank" class="btn-primary" style="flex:1;text-decoration:none;display:flex;align-items:center;justify-content:center;">🧭 Get Directions</a>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn-secondary" onclick="closeModal('view-modal')">Close</button>
        </div>
    </div>
</div>

{{-- MAP LOCATION MODAL --}}
<div id="map-modal" class="modal-backdrop" style="display:none;">
    <div class="modal-box" style="max-width:900px;">
        <div class="modal-header">
            <div class="modal-title">🗺️ Client Location</div>
            <button class="modal-close" onclick="closeModal('map-modal')">✕</button>
        </div>

        <div class="modal-body" style="padding:0;">
            <div style="padding:16px 24px;border-bottom:1px solid rgba(255,255,255,0.08);">
                <div style="font-size:13px;font-weight:600;margin-bottom:4px;" class="map-client-name" id="map-client-name"></div>
                <div style="font-size:12px;" class="map-client-address" id="map-client-address"></div>
            </div>
            <div id="map-container" style="width:100%;height:450px;background:rgba(255,255,255,0.05);position:relative;border-radius:0 0 12px 12px;overflow:hidden;">
                <div id="map-view" style="width:100%;height:100%;"></div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn-secondary" onclick="closeModal('map-modal')">Close</button>
            <a id="map-directions-btn" href="" target="_blank" class="btn-primary">🧭 Get Directions</a>
        </div>
    </div>
</div>

{{-- UPDATE STATUS MODAL --}}
<div id="update-modal" class="modal-backdrop" style="display:none;">
    <div class="modal-box" style="max-width:560px;">
        <div class="modal-header">
            <div class="modal-title">✏️ Update Job Status</div>
            <button class="modal-close" onclick="closeModal('update-modal')">✕</button>
        </div>

        <form id="update-form" method="POST">
            @csrf
            <div class="modal-body">
                <div style="background:rgba(255,255,255,0.05);padding:12px;border-radius:10px;margin-bottom:16px;">
                    <div style="font-size:11px;text-transform:uppercase;letter-spacing:1px;margin-bottom:4px;" class="client-label">Client</div>
                    <div style="font-size:15px;font-weight:600;" class="client-name" id="modal-client-name"></div>
                </div>
                <div class="adm-form-group">
                    <label>Status *</label>
                    <select name="status" id="update-status" required>
                        <option value="pending">🕐 Pending</option>
                        <option value="ongoing">⚡ Ongoing</option>
                        <option value="completed">✓ Completed</option>
                    </select>
                </div>
                <div class="adm-form-group">
                    <label>Notes</label>
                    <textarea name="notes" id="update-notes" rows="4"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-secondary" onclick="closeModal('update-modal')">Cancel</button>
                <button type="submit" class="btn-primary">💾 Update Status</button>
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
.adm-form-group { margin-bottom: 16px; }
.adm-form-group label {
    display: block; font-size: 12px; font-weight: 600;
    text-transform: uppercase; letter-spacing: 0.8px;
    color: rgba(255,255,255,0.6); margin-bottom: 8px;
}
.adm-form-group select,
.adm-form-group textarea {
    width: 100%;
    padding: 12px;
    border-radius: 10px;
    border: 1px solid rgba(255,255,255,0.1);
    background: rgba(255,255,255,0.04);
    color: #fff;
    font-size: 14px;
    font-family: inherit;
}
.adm-form-group select:focus,
.adm-form-group textarea:focus {
    outline: none;
    border-color: rgba(255,82,82,0.5);
    background: rgba(255,255,255,0.06);
}
.adm-form-group select option {
    background: #1a1a1a;
    color: #fff;
    padding: 8px;
}
.adm-form-group textarea {
    resize: vertical;
}
.detail-section {
    display: flex;
    flex-direction: column;
    gap: 4px;
}
.detail-label {
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: 1px;
    color: rgba(255,255,255,0.5);
    font-weight: 600;
}
.detail-value {
    font-size: 14px;
    color: #fff;
    font-weight: 500;
}
.client-label {
    color: rgba(255,255,255,0.5);
}
.client-name {
    color: #fff;
}
.map-client-name {
    color: #fff;
}
.map-client-address {
    color: rgba(255,255,255,0.6);
}
.modal-body .detail-section > div:first-child {
    color: rgba(255,255,255,0.5);
}
.modal-body .detail-section > div:last-child {
    color: #fff;
}
#view-modal .modal-body > div > div > div {
    color: rgba(255,255,255,0.7);
}
#update-modal .modal-body > div:first-child {
    background: rgba(255,255,255,0.05);
}
#update-modal .modal-body > div:first-child > div:first-child {
    color: rgba(255,255,255,0.5);
}
#update-modal .modal-body > div:first-child > div:last-child {
    color: #fff;
}

/* Leaflet map styling */
.leaflet-container {
    background: #1a1a1a;
    font-family: inherit;
}
.leaflet-popup-content-wrapper {
    background: #111;
    color: #fff;
    border: 1px solid rgba(255,255,255,0.1);
    border-radius: 8px;
}
.leaflet-popup-tip {
    background: #111;
}
.leaflet-popup-content {
    margin: 12px;
    font-size: 13px;
}

body.light-mode .modal-backdrop { background: rgba(0,0,0,0.5); }
body.light-mode .modal-box { background: #fff; border: 1px solid rgba(0,0,0,0.1); }
body.light-mode .modal-title { color: #000; }
body.light-mode .modal-close { background: rgba(0,0,0,0.06); border: 1px solid rgba(0,0,0,0.1); color: rgba(0,0,0,0.6); }
body.light-mode .modal-close:hover { background: rgba(255,82,82,0.2); color: #ff6b6b; border-color: rgba(255,82,82,0.3); }
body.light-mode .modal-header, body.light-mode .modal-footer { border-color: rgba(0,0,0,0.1); }
body.light-mode .adm-form-group label { color: #000; }
body.light-mode .adm-form-group select,
body.light-mode .adm-form-group textarea {
    background: rgba(0,0,0,0.04);
    color: #000;
    border: 1px solid rgba(0,0,0,0.1);
}
body.light-mode .adm-form-group select:focus,
body.light-mode .adm-form-group textarea:focus {
    border-color: rgba(0,123,255,0.5);
    background: rgba(0,0,0,0.06);
}
body.light-mode .adm-form-group select option {
    background: #fff;
    color: #000;
}
body.light-mode .detail-label { color: #666; }
body.light-mode .detail-value { color: #000; }
body.light-mode .client-label { color: #666 !important; }
body.light-mode .client-name { color: #000 !important; }
body.light-mode .map-client-name { color: #000 !important; }
body.light-mode .map-client-address { color: #666 !important; }
body.light-mode #map-container { background: rgba(0,0,0,0.05); }
body.light-mode .adm-table td { color: #000 !important; }
body.light-mode .adm-table td > div { color: #000 !important; }
body.light-mode .adm-table td > div:last-child { color: #666 !important; }
body.light-mode .btn-secondary {
    color: #000 !important;
}
body.light-mode .btn-secondary:hover {
    color: #000 !important;
}
body.light-mode .btn-primary {
    color: #fff !important;
}
body.light-mode .btn-primary:hover {
    color: #fff !important;
}
body.light-mode .modal-body .detail-section > div:first-child {
    color: #666 !important;
}
body.light-mode .modal-body .detail-section > div:last-child {
    color: #000 !important;
}
body.light-mode #view-modal .modal-body > div > div > div {
    color: #000 !important;
}
body.light-mode #update-modal .modal-body > div:first-child {
    background: rgba(0,0,0,0.05) !important;
}
body.light-mode #update-modal .modal-body > div:first-child > div:first-child {
    color: #666 !important;
}
body.light-mode #update-modal .modal-body > div:first-child > div:last-child {
    color: #000 !important;
}
body.light-mode #inline-map {
    border-color: rgba(0,0,0,0.1);
}
body.light-mode #location-map-section > div:first-child > div {
    color: #000 !important;
    border-color: rgba(0,0,0,0.08);
}
body.light-mode .leaflet-container {
    background: #f0f0f0;
}
body.light-mode .leaflet-popup-content-wrapper {
    background: #fff;
    color: #000;
    border: 1px solid rgba(0,0,0,0.1);
}
body.light-mode .leaflet-popup-tip {
    background: #fff;
}

/* Mobile Responsive Styles */
@media (max-width: 768px) {
    .adm-page-header {
        flex-direction: column;
        align-items: stretch;
        gap: 12px;
    }
    
    .adm-page-title {
        font-size: 22px;
    }
    
    .adm-kpi-grid {
        grid-template-columns: 1fr;
        gap: 12px;
    }
    
    .adm-kpi {
        padding: 16px;
    }
    
    .adm-kpi-value {
        font-size: 28px;
    }
    
    .adm-table-wrap {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }
    
    .adm-table {
        font-size: 12px;
        min-width: 800px;
    }
    
    .adm-table th,
    .adm-table td {
        padding: 10px 8px;
        font-size: 12px;
    }
    
    .btn-secondary.btn-sm {
        padding: 8px 10px;
        font-size: 11px;
        white-space: nowrap;
    }
    
    .modal-backdrop {
        padding: 10px;
        align-items: flex-start;
    }
    
    .modal-box {
        max-width: 100% !important;
        max-height: 95vh;
        margin-top: 10px;
    }
    
    .modal-header {
        padding: 14px 16px 12px;
    }
    
    .modal-title {
        font-size: 14px;
    }
    
    .modal-body {
        padding: 14px 16px;
    }
    
    #view-modal .modal-body {
        padding: 0;
    }
    
    #view-modal .modal-body > div:first-child {
        padding: 14px 16px;
    }
    
    .modal-footer {
        padding: 12px 16px 14px;
        flex-wrap: wrap;
    }
    
    .modal-footer button,
    .modal-footer a {
        flex: 1;
        min-width: 120px;
    }
    
    .detail-section {
        gap: 6px;
    }
    
    .detail-label {
        font-size: 10px;
    }
    
    .detail-value {
        font-size: 13px;
    }
    
    #inline-map {
        height: 280px !important;
    }
    
    #map-view,
    #map-container {
        height: 350px !important;
    }
    
    #location-map-section > div:first-child {
        padding: 0 16px 12px;
    }
    
    #location-map-section > div:last-child {
        padding: 12px 16px;
        flex-direction: column;
    }
    
    #location-map-section button,
    #location-map-section a {
        width: 100%;
    }
    
    .adm-form-group {
        margin-bottom: 14px;
    }
    
    .adm-form-group label {
        font-size: 11px;
    }
    
    .adm-form-group select,
    .adm-form-group textarea {
        font-size: 13px;
        padding: 10px;
    }
    
    .adm-pagination {
        justify-content: center;
        gap: 4px;
    }
    
    .adm-pagination a,
    .adm-pagination span {
        padding: 6px 10px;
        font-size: 12px;
    }
}

@media (max-width: 480px) {
    .adm-page-title {
        font-size: 20px;
    }
    
    .adm-page-subtitle {
        font-size: 12px;
    }
    
    .adm-kpi-label {
        font-size: 10px;
    }
    
    .adm-kpi-value {
        font-size: 24px;
    }
    
    .adm-card {
        padding: 16px;
        border-radius: 14px;
    }
    
    .modal-title {
        font-size: 13px;
    }
    
    .modal-close {
        width: 24px;
        height: 24px;
        font-size: 11px;
    }
    
    .btn-primary,
    .btn-secondary {
        font-size: 12px;
        padding: 10px 14px;
    }
    
    .btn-sm {
        font-size: 11px;
        padding: 6px 10px;
    }
    
    body.light-mode .btn-secondary.btn-sm {
        color: #000 !important;
    }
    
    #inline-map {
        height: 240px !important;
    }
    
    #map-view,
    #map-container {
        height: 300px !important;
    }
    
    .badge {
        font-size: 10px;
        padding: 2px 8px;
    }
}
</style>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
let mapInstance = null;
let mapMarker = null;
let inlineMapInstance = null;
let inlineMapMarker = null;

function openModal(id) {
    document.getElementById(id).style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

function closeModal(id) {
    document.getElementById(id).style.display = 'none';
    document.body.style.overflow = '';
}

document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.modal-backdrop').forEach(el => {
        el.addEventListener('click', e => { if (e.target === el) closeModal(el.id); });
    });
    
    document.addEventListener('keydown', e => {
        if (e.key === 'Escape')
            document.querySelectorAll('.modal-backdrop').forEach(m => {
                if (m.style.display !== 'none') closeModal(m.id);
            });
    });
});

function openViewModal(data) {
    window.currentJobData = data;
    
    console.log('Opening view modal with data:', data);
    console.log('Location data:', data.location);
    
    document.getElementById('view-job-id').textContent = '#' + data.id;
    document.getElementById('view-client-name').textContent = data.client;
    document.getElementById('view-client-email').textContent = data.email;
    document.getElementById('view-client-phone').textContent = data.phone || '—';
    document.getElementById('view-client-address').textContent = data.address || '—';
    document.getElementById('view-client-plan').textContent = data.plan || '—';
    
    // Handle location map
    const locationSection = document.getElementById('location-map-section');
    if (data.location && data.location !== null && data.location !== '') {
        console.log('Location exists, processing...');
        const coords = data.location.split(',').map(coord => parseFloat(coord.trim()));
        const [lat, lng] = coords;
        
        console.log('Parsed coordinates:', lat, lng);
        
        if (!isNaN(lat) && !isNaN(lng)) {
            console.log('Valid coordinates, showing map');
            locationSection.style.display = 'block';
            
            // Set directions link
            const directionsUrl = `https://www.google.com/maps/dir/?api=1&destination=${lat},${lng}`;
            document.getElementById('inline-directions-btn').href = directionsUrl;
            
            // Destroy existing map instance if it exists
            if (inlineMapInstance) {
                console.log('Removing existing map instance');
                inlineMapInstance.remove();
                inlineMapInstance = null;
                inlineMapMarker = null;
            }
            
            // Initialize inline map after modal is visible
            setTimeout(() => {
                console.log('Initializing map...');
                try {
                    inlineMapInstance = L.map('inline-map').setView([lat, lng], 15);
                    
                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        attribution: '© OpenStreetMap',
                        maxZoom: 19,
                    }).addTo(inlineMapInstance);
                    
                    inlineMapMarker = L.marker([lat, lng]).addTo(inlineMapInstance);
                    inlineMapMarker.bindPopup(`<b>${data.client}</b><br>${data.address || 'Client Location'}`).openPopup();
                    
                    console.log('Map initialized successfully');
                } catch (error) {
                    console.error('Error initializing map:', error);
                }
            }, 400);
        } else {
            console.log('Invalid coordinates');
            locationSection.style.display = 'none';
        }
    } else {
        console.log('No location data available');
        locationSection.style.display = 'none';
    }
    
    let statusBadge = '';
    if (data.status === 'completed') {
        statusBadge = '<span class="badge badge-green">✓ Completed</span>';
    } else if (data.status === 'ongoing') {
        statusBadge = '<span class="badge badge-yellow">⚡ Ongoing</span>';
    } else {
        statusBadge = '<span class="badge badge-red">🕐 Pending</span>';
    }
    document.getElementById('view-status').innerHTML = statusBadge;
    document.getElementById('view-installation-date').textContent = data.installation_date || '—';
    document.getElementById('view-created').textContent = data.created;
    document.getElementById('view-notes').textContent = data.notes || 'No notes';
    openModal('view-modal');
}

function openMapModal() {
    const data = window.currentJobData;
    if (!data || !data.location) {
        alert('Location not available for this client');
        return;
    }
    
    document.getElementById('map-client-name').textContent = data.client;
    document.getElementById('map-client-address').textContent = data.address || 'Address not provided';
    
    const coords = data.location.split(',').map(coord => parseFloat(coord.trim()));
    const [lat, lng] = coords;
    
    if (isNaN(lat) || isNaN(lng)) {
        alert('Invalid location coordinates');
        return;
    }
    
    const directionsUrl = `https://www.google.com/maps/dir/?api=1&destination=${lat},${lng}`;
    document.getElementById('map-directions-btn').href = directionsUrl;
    
    openModal('map-modal');
    
    setTimeout(() => {
        if (!mapInstance) {
            mapInstance = L.map('map-view').setView([lat, lng], 16);
            
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors',
                maxZoom: 19,
            }).addTo(mapInstance);
            
            mapMarker = L.marker([lat, lng]).addTo(mapInstance);
            mapMarker.bindPopup(`<b>${data.client}</b><br>${data.address || 'Client Location'}`).openPopup();
        } else {
            mapInstance.setView([lat, lng], 16);
            if (mapMarker) {
                mapMarker.setLatLng([lat, lng]);
                mapMarker.bindPopup(`<b>${data.client}</b><br>${data.address || 'Client Location'}`).openPopup();
            } else {
                mapMarker = L.marker([lat, lng]).addTo(mapInstance);
                mapMarker.bindPopup(`<b>${data.client}</b><br>${data.address || 'Client Location'}`).openPopup();
            }
        }
        
        mapInstance.invalidateSize();
    }, 100);
}

function openUpdateModal(data) {
    document.getElementById('update-form').action = `/installer/job-orders/${data.id}/update-status`;
    document.getElementById('modal-client-name').textContent = data.client;
    document.getElementById('update-status').value = data.status;
    document.getElementById('update-notes').value = data.notes || '';
    openModal('update-modal');
}
</script>

@endsection
