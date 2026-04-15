@extends('admin.layout')
@section('title', 'Dashboard')

@section('content')
<div class="adm-page-header">
    <div>
        <div class="adm-page-title">Dashboard</div>
        <div class="adm-page-subtitle">Welcome back, {{ auth()->user()->first_name }}. Here's what's happening.</div>
    </div>
    <a href="{{ route('admin.clients') }}" class="btn-primary">👥 View Customers</a>
</div>

<div class="adm-kpi-grid">
    <div class="adm-kpi">
        <div class="adm-kpi-label">Total Customers</div>
        <div class="adm-kpi-value">{{ $totalCustomers }}</div>
        <div class="adm-kpi-sub">Registered clients</div>
    </div>
    <div class="adm-kpi">
        <div class="adm-kpi-label">Verified Accounts</div>
        <div class="adm-kpi-value">{{ $verifiedUsers }}</div>
        <div class="adm-kpi-sub">Email confirmed</div>
    </div>
    <div class="adm-kpi">
        <div class="adm-kpi-label">Pending Verification</div>
        <div class="adm-kpi-value">{{ $pendingVerifications }}</div>
        <div class="adm-kpi-sub">Awaiting confirmation</div>
    </div>
    <div class="adm-kpi">
        <div class="adm-kpi-label">Admin Users</div>
        <div class="adm-kpi-value">{{ $totalAdmins }}</div>
        <div class="adm-kpi-sub">System administrators</div>
    </div>
</div>

<div class="dashboard-two-col-grid">
    <div class="adm-card">
        <h3 style="font-size:16px; font-weight:600; margin-bottom:16px; color:#fff;">Recent Customers</h3>
        @if($recentClients->count())
        <div class="adm-table-wrap">
            <table class="adm-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recentClients as $client)
                    <tr>
                        <td>{{ $client->full_name }}</td>
                        <td style="color:rgba(255,255,255,0.55); font-size:13px;">{{ $client->email }}</td>
                        <td>
                            @if($client->email_verified_at)
                                <span class="badge badge-green">Verified</span>
                            @else
                                <span class="badge badge-yellow">Pending</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <p style="color:rgba(255,255,255,0.4); font-size:14px;">No customers yet.</p>
        @endif
    </div>

    <div class="adm-card">
        <h3 style="font-size:16px; font-weight:600; margin-bottom:16px; color:#fff;">Quick Actions</h3>
        <div style="display:flex; flex-direction:column; gap:10px;">
            <a href="{{ route('admin.clients') }}" class="btn-secondary" style="justify-content:flex-start;">
                👥 Manage Customers
            </a>
            <a href="{{ route('admin.landing') }}" class="btn-secondary" style="justify-content:flex-start;">
                🌐 Edit Landing Page
            </a>
            <a href="{{ route('profile.edit') }}" class="btn-secondary" style="justify-content:flex-start;">
                👤 Edit Profile
            </a>
        </div>
    </div>
</div>

<div class="adm-card">
    <h3 style="font-size:16px; font-weight:600; margin-bottom:20px; color:#fff;">📅 Account Dues - {{ $now->format('F Y') }}</h3>
    <div class="calendar-grid">
        @php
            $daysOfWeek = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
            $firstDay = $now->copy()->startOfMonth();
            $lastDay = $now->copy()->endOfMonth();
            $startDate = $firstDay->copy()->startOfWeek();
            $endDate = $lastDay->copy()->endOfWeek();
        @endphp
        
        @foreach($daysOfWeek as $day)
            <div style="text-align:center; font-weight:600; color:rgba(255,255,255,0.6); padding:8px; font-size:12px;">
                {{ $day }}
            </div>
        @endforeach
        
        @for($date = $startDate; $date <= $endDate; $date->addDay())
            @php
                $isCurrentMonth = $date->month === $now->month && $date->year === $now->year;
                $dayNum = $date->day;
                $dayClients = $clients->get($dayNum, collect());
            @endphp
            <div style="
                padding: 8px 6px;
                border-radius: 8px;
                text-align: center;
                font-size: 13px;
                font-weight: 600;
                transition: all 0.2s;
                background: {{ $isCurrentMonth ? 'rgba(255,255,255,0.05)' : 'rgba(255,255,255,0.02)' }};
                border: 1px solid {{ $isCurrentMonth ? 'rgba(255,255,255,0.1)' : 'rgba(255,255,255,0.05)' }};
                color: {{ $isCurrentMonth ? 'rgba(255,255,255,0.7)' : 'rgba(255,255,255,0.3)' }};
                min-height: 80px;
                display: flex;
                flex-direction: column;
                gap: 4px;
            ">
                <div style="font-size: 14px; font-weight: 700;">{{ $dayNum }}</div>
                @foreach($dayClients as $client)
                    @php
                        $hasPaidPayment = $client->payments->count() > 0;
                    @endphp
                    <div 
                        onclick="openDueClientModal({{ json_encode([
                            'id' => $client->id,
                            'name' => $client->full_name,
                            'email' => $client->email,
                            'phone' => $client->phone_number ?? '—',
                            'plan' => $client->plan_interest ?? '—',
                            'amount' => $client->plan_amount ?? 0,
                            'status' => $hasPaidPayment ? 'paid' : 'pending',
                            'due_date' => $client->due_date->format('M d, Y'),
                            'paid_date' => $hasPaidPayment ? $client->payments->first()->paid_date?->format('M d, Y') : '—',
                        ]) }})"
                        style="
                            font-size: 10px;
                            padding: 3px 5px;
                            border-radius: 4px;
                            cursor: pointer;
                            background: {{ $hasPaidPayment ? 'rgba(76,175,80,0.2)' : 'rgba(255,82,82,0.2)' }};
                            color: {{ $hasPaidPayment ? '#66bb6a' : '#ff6b6b' }};
                            border: 1px solid {{ $hasPaidPayment ? 'rgba(76,175,80,0.4)' : 'rgba(255,82,82,0.4)' }};
                            text-overflow: ellipsis;
                            overflow: hidden;
                            white-space: nowrap;
                        "
                        title="{{ $client->full_name }} - {{ $hasPaidPayment ? 'Paid' : 'Pending' }}"
                    >
                        {{ $client->first_name }}
                    </div>
                @endforeach
            </div>
        @endfor
    </div>
</div>

{{-- Due Client View Modal --}}
<div id="due-client-modal" style="display:none;position:fixed;inset:0;z-index:2000;align-items:center;justify-content:center;padding:20px;backdrop-filter:blur(4px);">
    <div id="due-modal-container" style="border-radius:20px;width:100%;max-width:500px;box-shadow:0 24px 80px rgba(0,0,0,0.6);">
        <div id="due-modal-header" style="display:flex;align-items:center;justify-content:space-between;padding:18px 24px 14px;">
            <div id="due-modal-title" style="font-size:16px;font-weight:700;">📅 Payment Details</div>
            <button id="due-modal-close-btn" onclick="closeDueModal()" style="width:28px;height:28px;border-radius:7px;cursor:pointer;font-size:12px;display:flex;align-items:center;justify-content:center;transition:all 0.2s;">✕</button>
        </div>
        <div id="due-modal-body" style="padding:18px 24px;overflow-y:auto;max-height:60vh;"></div>
        <div id="due-modal-footer" style="padding:14px 24px;display:flex;justify-content:space-between;align-items:center;">
            <form id="mark-paid-form" method="POST" style="display:inline;">
                @csrf
                <button type="submit" id="mark-paid-btn" style="display:inline-flex;align-items:center;gap:6px;padding:10px 20px;border-radius:10px;font-weight:600;font-size:14px;cursor:pointer;transition:all 0.2s;text-decoration:none;background:rgba(76,175,80,0.15);color:#66bb6a;border:1px solid rgba(76,175,80,0.3);">
                    ✓ Mark as Paid
                </button>
            </form>
            <button onclick="closeDueModal()" class="btn-secondary">Close</button>
        </div>
    </div>
</div>

<style>
/* Dark mode styles (default) */
#due-client-modal {
    background: rgba(0,0,0,0.72);
}
#due-modal-container {
    background: #111;
    border: 1px solid rgba(255,255,255,0.1);
}
#due-modal-header {
    border-bottom: 1px solid rgba(255,255,255,0.07);
}
#due-modal-title {
    color: #fff;
}
#due-modal-close-btn {
    background: rgba(255,255,255,0.06);
    border: 1px solid rgba(255,255,255,0.1);
    color: rgba(255,255,255,0.6);
}
#due-modal-footer {
    border-top: 1px solid rgba(255,255,255,0.07);
}

/* Light mode styles */
body.light-mode #due-client-modal {
    background: rgba(0,0,0,0.3);
}
body.light-mode #due-modal-container {
    background: #fff;
    border: 1px solid rgba(0,0,0,0.1);
}
body.light-mode #due-modal-header {
    border-bottom: 1px solid rgba(0,0,0,0.1);
}
body.light-mode #due-modal-title {
    color: #000;
}
body.light-mode #due-modal-close-btn {
    background: rgba(0,0,0,0.06);
    border: 1px solid rgba(0,0,0,0.1);
    color: rgba(0,0,0,0.6);
}
body.light-mode #due-modal-footer {
    border-top: 1px solid rgba(0,0,0,0.1);
}
body.light-mode #due-modal-body .vrow {
    border-bottom: 1px solid rgba(0,0,0,0.05);
}
body.light-mode #due-modal-body .vlabel {
    color: rgba(0,0,0,0.6);
}
body.light-mode #due-modal-body .vvalue {
    color: #000;
}
</style>

<script>
    function openDueClientModal(data) {
        const statusMap = {
            paid:     '<span class="badge badge-green">✓ Paid</span>',
            pending:  '<span class="badge badge-yellow">🕐 Pending</span>',
            overdue:  '<span class="badge badge-red">✕ Overdue</span>',
        };

        const rows = [
            ['Full Name',  data.name],
            ['Email',      data.email],
            ['Phone',      data.phone],
            ['Plan',       data.plan],
            ['Amount',     '₱' + parseFloat(data.amount).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})],
            ['Payment Status', statusMap[data.status] || data.status],
            ['Due Date',   data.due_date],
            ['Paid Date',  data.paid_date],
        ];

        let html = rows.map(([l,v]) =>
            `<div class="vrow"><div class="vlabel">${l}</div><div class="vvalue">${v}</div></div>`
        ).join('');

        document.getElementById('due-modal-body').innerHTML = html;
        
        const markPaidBtn = document.getElementById('mark-paid-btn');
        if (data.status === 'paid') {
            markPaidBtn.style.display = 'none';
            document.getElementById('mark-paid-form').action = '#';
        } else {
            markPaidBtn.style.display = 'inline-flex';
            document.getElementById('mark-paid-form').action = `/admin/clients/${data.id}/mark-paid`;
        }
        
        document.getElementById('due-client-modal').style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }

    function closeDueModal() {
        document.getElementById('due-client-modal').style.display = 'none';
        document.body.style.overflow = '';
    }

    document.getElementById('due-client-modal').addEventListener('click', e => {
        if (e.target.id === 'due-client-modal') closeDueModal();
    });

    document.addEventListener('keydown', e => {
        if (e.key === 'Escape' && document.getElementById('due-client-modal').style.display !== 'none') {
            closeDueModal();
        }
    });
</script>

<style>
.vrow{display:flex;gap:10px;margin-bottom:10px;padding-bottom:10px;border-bottom:1px solid rgba(255,255,255,0.05);}
.vrow:last-child{border-bottom:none;margin-bottom:0;padding-bottom:0;}
.vlabel{font-size:11px;text-transform:uppercase;letter-spacing:0.7px;color:rgba(255,255,255,0.38);width:120px;flex-shrink:0;padding-top:2px;}
.vvalue{font-size:13px;color:#fff;flex:1;}

.dashboard-two-col-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
    margin-bottom: 28px;
}

.calendar-grid {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    gap: 8px;
}

@media (max-width: 768px) {
    .dashboard-two-col-grid {
        grid-template-columns: 1fr;
        gap: 16px;
    }
    
    .dashboard-two-col-grid .adm-card {
        width: 100%;
        max-width: 100%;
        overflow: hidden;
    }
    
    .calendar-grid {
        gap: 4px;
    }
    
    .calendar-grid > div {
        min-height: 60px !important;
        font-size: 11px !important;
        padding: 6px 4px !important;
    }
    
    .calendar-grid > div > div:first-child {
        font-size: 12px !important;
    }
    
    .adm-table-wrap {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
        margin: 0 -24px;
        padding: 0 24px;
        max-width: calc(100vw - 32px);
    }
    
    .adm-table {
        min-width: 500px;
    }
    
    .dashboard-two-col-grid .adm-card h3 {
        font-size: 15px !important;
        margin-bottom: 14px !important;
    }
    
    .dashboard-two-col-grid .adm-card .btn-secondary {
        width: 100%;
        justify-content: center !important;
    }
}

@media (max-width: 480px) {
    .calendar-grid {
        gap: 2px;
    }
    
    .calendar-grid > div {
        min-height: 50px !important;
        font-size: 10px !important;
        padding: 4px 2px !important;
    }
    
    .calendar-grid > div > div:first-child {
        font-size: 11px !important;
    }
}
</style>
@endsection
