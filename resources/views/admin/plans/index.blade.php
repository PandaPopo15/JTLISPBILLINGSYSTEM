@extends('admin.layout')
@section('title', 'Plans & Pricing')

@section('content')
<div class="adm-page-header">
    <div>
        <div class="adm-page-title">Plans & Pricing</div>
        <div class="adm-page-subtitle">Manage internet service plans and pricing</div>
    </div>
    <button class="btn-primary" onclick="openPlanModal()">+ Add Plan</button>
</div>

<div class="adm-card">
    @if($plans->count())
    <div class="adm-table-wrap">
        <table class="adm-table">
            <thead>
                <tr>
                    <th>Plan Name</th>
                    <th>Speed</th>
                    <th>Price</th>
                    <th>Installation Fee</th>
                    <th>Popular</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($plans as $plan)
                <tr>
                    <td style="font-weight:600;">{{ $plan->name }}</td>
                    <td>{{ $plan->speed }}</td>
                    <td>₱{{ number_format($plan->price, 2) }}</td>
                    <td>₱{{ number_format($plan->installation_fee, 2) }}</td>
                    <td>
                        @if($plan->is_popular)
                            <span class="badge badge-yellow">⭐ Popular</span>
                        @else
                            <span style="color:rgba(255,255,255,0.3); font-size:12px;">—</span>
                        @endif
                    </td>
                    <td>
                        @if($plan->is_active)
                            <span class="badge badge-green">✓ Active</span>
                        @else
                            <span class="badge badge-red">✕ Inactive</span>
                        @endif
                    </td>
                    <td>
                        <div style="display:flex; gap:6px;">
                            <button type="button" class="btn-secondary btn-sm" onclick="editPlanModal({{ $plan->id }}, '{{ $plan->name }}', '{{ $plan->speed }}', {{ $plan->price }}, {{ $plan->installation_fee }}, '{{ $plan->description }}', {{ $plan->is_active ? 'true' : 'false' }}, {{ $plan->is_popular ? 'true' : 'false' }})">✏️ Edit</button>
                            <form action="{{ route('admin.plans.delete', $plan) }}" method="POST" style="display:inline;"
                                  onsubmit="return confirm('Delete {{ addslashes($plan->name) }}?')">
                                @csrf
                                <button type="submit" class="btn-danger btn-sm">🗑</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @else
    <div style="text-align:center; padding:48px; color:rgba(255,255,255,0.3);">
        <p style="font-size:16px; margin-bottom:16px;">No plans yet</p>
        <button type="button" class="btn-primary" onclick="openPlanModal()">Create First Plan</button>
    </div>
    @endif
</div>

{{-- PLAN MODAL --}}
<div id="plan-modal" class="modal-backdrop" style="display:none;">
    <div class="modal-box" style="max-width:500px;">
        <div class="modal-header">
            <div class="modal-title" id="modal-title">➕ Add New Plan</div>
            <button class="modal-close" onclick="closePlanModal()">✕</button>
        </div>

        <form id="plan-form" style="display:flex; flex-direction:column; flex:1; min-height:0;">
            @csrf
            <input type="hidden" id="plan-id" name="plan_id">

            <div style="flex:1; overflow-y:auto; padding:18px 24px; min-height:0;">
                <div class="adm-form-group">
                    <label>Plan Name *</label>
                    <input type="text" id="plan-name" name="name" required placeholder="e.g. Basic Plan">
                    <div class="adm-form-error" id="error-name" style="display:none;"></div>
                </div>

                <div class="adm-form-group">
                    <label>Speed *</label>
                    <input type="text" id="plan-speed" name="speed" required placeholder="e.g. 50 Mbps">
                    <div class="adm-form-error" id="error-speed" style="display:none;"></div>
                </div>

                <div style="display:grid; grid-template-columns:1fr 1fr; gap:14px;">
                    <div class="adm-form-group">
                        <label>Monthly Price (₱) *</label>
                        <input type="number" id="plan-price" name="price" required step="0.01" min="0" placeholder="999.00">
                        <div class="adm-form-error" id="error-price" style="display:none;"></div>
                    </div>
                    <div class="adm-form-group">
                        <label>Installation Fee (₱)</label>
                        <input type="number" id="plan-installation-fee" name="installation_fee" step="0.01" min="0" placeholder="0.00">
                        <div class="adm-form-error" id="error-installation_fee" style="display:none;"></div>
                    </div>
                </div>

                <div class="adm-form-group">
                    <label>Description</label>
                    <textarea id="plan-description" name="description" rows="3" placeholder="Brief description of this plan..."></textarea>
                    <div class="adm-form-error" id="error-description" style="display:none;"></div>
                </div>

                <div style="display:grid; grid-template-columns:1fr 1fr; gap:14px;">
                    <div class="adm-form-group">
                        <label style="display:flex; align-items:center; gap:8px; cursor:pointer;">
                            <input type="checkbox" id="plan-is-active" name="is_active" value="1" checked style="width:auto;">
                            <span>Active</span>
                        </label>
                    </div>
                    <div class="adm-form-group">
                        <label style="display:flex; align-items:center; gap:8px; cursor:pointer;">
                            <input type="checkbox" id="plan-is-popular" name="is_popular" value="1" style="width:auto;">
                            <span>Mark as Popular</span>
                        </label>
                    </div>
                </div>
            </div>

            <div style="flex-shrink:0; padding:14px 24px; border-top:1px solid rgba(255,255,255,0.07); display:flex; justify-content:flex-end; gap:10px;">
                <button type="button" class="btn-secondary" onclick="closePlanModal()">Cancel</button>
                <button type="submit" class="btn-primary" id="submit-btn">💾 Save Plan</button>
            </div>
        </form>
    </div>
</div>

<style>
.modal-backdrop {
    position: fixed;
    inset: 0;
    z-index: 2000;
    background: rgba(0,0,0,0.72);
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 20px;
    backdrop-filter: blur(4px);
}
.modal-box {
    background: #111;
    border: 1px solid rgba(255,255,255,0.1);
    border-radius: 20px;
    width: 100%;
    height: auto;
    max-height: 90vh;
    display: flex;
    flex-direction: column;
    box-shadow: 0 24px 80px rgba(0,0,0,0.6);
    animation: modalIn 0.2s ease;
    overflow: hidden;
}
@keyframes modalIn {
    from { opacity: 0; transform: scale(0.96) translateY(10px); }
    to { opacity: 1; transform: scale(1) translateY(0); }
}
.modal-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 18px 24px 14px;
    border-bottom: 1px solid rgba(255,255,255,0.07);
    flex-shrink: 0;
}
.modal-title {
    font-size: 16px;
    font-weight: 700;
    color: #fff;
}
.modal-close {
    background: rgba(255,255,255,0.06);
    border: 1px solid rgba(255,255,255,0.1);
    color: rgba(255,255,255,0.6);
    width: 28px;
    height: 28px;
    border-radius: 7px;
    cursor: pointer;
    font-size: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s;
}
.modal-close:hover {
    background: rgba(255,82,82,0.2);
    color: #ff6b6b;
    border-color: rgba(255,82,82,0.3);
}
.adm-form-group {
    margin-bottom: 16px;
}
.adm-form-group label {
    display: block;
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.8px;
    color: rgba(255,255,255,0.6);
    margin-bottom: 8px;
}
.adm-form-group input,
.adm-form-group textarea {
    width: 100%;
    padding: 12px 14px;
    border-radius: 10px;
    border: 1px solid rgba(255,255,255,0.1);
    background: rgba(255,255,255,0.04);
    color: #fff;
    font-size: 14px;
    font-family: inherit;
    transition: border-color 0.2s;
}
.adm-form-group input:focus,
.adm-form-group textarea:focus {
    outline: none;
    border-color: rgba(255,82,82,0.5);
    background: rgba(255,255,255,0.06);
}
.adm-form-group textarea {
    resize: vertical;
    min-height: 80px;
}
.adm-form-error {
    color: #ff6b6b;
    font-size: 12px;
    margin-top: 5px;
}
</style>

<script>
function openPlanModal() {
    document.getElementById('plan-modal').style.display = 'flex';
    document.body.style.overflow = 'hidden';
    document.getElementById('modal-title').textContent = '➕ Add New Plan';
    document.getElementById('submit-btn').textContent = '💾 Save Plan';
    document.getElementById('plan-form').reset();
    document.getElementById('plan-id').value = '';
    clearErrors();
}

function closePlanModal() {
    document.getElementById('plan-modal').style.display = 'none';
    document.body.style.overflow = '';
    document.getElementById('plan-form').reset();
    clearErrors();
}

function editPlanModal(id, name, speed, price, installationFee, description, isActive, isPopular) {
    document.getElementById('plan-modal').style.display = 'flex';
    document.body.style.overflow = 'hidden';
    document.getElementById('modal-title').textContent = '✏️ Edit Plan';
    document.getElementById('submit-btn').textContent = '💾 Update Plan';
    
    document.getElementById('plan-id').value = id;
    document.getElementById('plan-name').value = name;
    document.getElementById('plan-speed').value = speed;
    document.getElementById('plan-price').value = price;
    document.getElementById('plan-installation-fee').value = installationFee;
    document.getElementById('plan-description').value = description;
    document.getElementById('plan-is-active').checked = isActive;
    document.getElementById('plan-is-popular').checked = isPopular;
    
    clearErrors();
}

function clearErrors() {
    document.querySelectorAll('.adm-form-error').forEach(el => {
        el.style.display = 'none';
        el.textContent = '';
    });
}

document.getElementById('plan-form').addEventListener('submit', async function(e) {
    e.preventDefault();
    clearErrors();
    
    const planId = document.getElementById('plan-id').value;
    const formData = new FormData(this);
    
    const url = planId 
        ? `/admin/plans/${planId}/update`
        : '/admin/plans';
    
    try {
        const response = await fetch(url, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
            }
        });
        
        const data = await response.json();
        
        if (data.success) {
            showToast(data.message, 'success');
            closePlanModal();
            setTimeout(() => location.reload(), 1500);
        } else {
            if (data.errors) {
                Object.keys(data.errors).forEach(field => {
                    const errorEl = document.getElementById(`error-${field}`);
                    if (errorEl) {
                        errorEl.textContent = data.errors[field][0];
                        errorEl.style.display = 'block';
                    }
                });
            }
        }
    } catch (error) {
        showToast('An error occurred. Please try again.', 'error');
    }
});

function showToast(message, type = 'success') {
    const toastWrap = document.querySelector('.adm-toast-wrap') || createToastWrap();
    const toast = document.createElement('div');
    toast.className = `adm-toast ${type === 'error' ? 'error' : ''}`;
    toast.innerHTML = `
        ${type === 'success' ? '✅' : '❌'} ${message}
        <div class="adm-toast-bar"></div>
    `;
    toastWrap.appendChild(toast);
    setTimeout(() => toast.remove(), 4500);
}

function createToastWrap() {
    const wrap = document.createElement('div');
    wrap.className = 'adm-toast-wrap';
    wrap.style.cssText = 'position:fixed; top:72px; right:20px; z-index:1100; display:flex; flex-direction:column; gap:10px; width:min(360px,calc(100vw - 40px));';
    document.body.appendChild(wrap);
    return wrap;
}

// Close modal on backdrop click
document.getElementById('plan-modal').addEventListener('click', function(e) {
    if (e.target === this) closePlanModal();
});

// Close on Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape' && document.getElementById('plan-modal').style.display !== 'none') {
        closePlanModal();
    }
});
</script>
@endsection
