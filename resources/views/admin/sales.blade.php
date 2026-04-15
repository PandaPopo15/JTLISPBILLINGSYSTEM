@extends('admin.layout')
@section('title', 'Sales')

@section('content')
<div class="adm-page-header">
    <div>
        <div class="adm-page-title">Sales & Revenue</div>
        <div class="adm-page-subtitle">Track your subscription payments and WiFi hotspot sales</div>
    </div>
    <div style="display: flex; gap: 10px;">
        <button class="btn-primary" onclick="document.getElementById('addSaleModal').style.display='flex'">➕ Add Hotspot Sale</button>
        <button class="btn-secondary" onclick="document.getElementById('addExpenseModal').style.display='flex'">💸 Add Expense</button>
    </div>
</div>

<div class="adm-kpi-grid">
    <div class="adm-kpi">
        <div class="adm-kpi-label">Total Revenue</div>
        <div class="adm-kpi-value">₱{{ number_format($totalRevenue, 2) }}</div>
        <div class="adm-kpi-sub">All time earnings</div>
    </div>
    <div class="adm-kpi">
        <div class="adm-kpi-label">Total Expenses</div>
        <div class="adm-kpi-value" style="color: #ff6b6b;">₱{{ number_format($totalExpenses, 2) }}</div>
        <div class="adm-kpi-sub">All time costs</div>
    </div>
    <div class="adm-kpi">
        <div class="adm-kpi-label">Net Profit</div>
        <div class="adm-kpi-value" style="color: {{ $netProfit >= 0 ? '#66bb6a' : '#ff6b6b' }};">₱{{ number_format($netProfit, 2) }}</div>
        <div class="adm-kpi-sub">Revenue - Expenses</div>
    </div>
    <div class="adm-kpi">
        <div class="adm-kpi-label">This Month Profit</div>
        <div class="adm-kpi-value" style="color: {{ $monthProfit >= 0 ? '#66bb6a' : '#ff6b6b' }};">₱{{ number_format($monthProfit, 2) }}</div>
        <div class="adm-kpi-sub">{{ $now->format('F Y') }}</div>
    </div>
</div>

<div class="sales-two-col-grid">
    <div class="adm-card">
        <h3 style="font-size:16px; font-weight:600; margin-bottom:20px;">📈 Revenue Trend (Last 6 Months)</h3>
        <canvas id="revenueChart" style="max-height: 300px;"></canvas>
    </div>

    <div class="adm-card">
        <h3 style="font-size:16px; font-weight:600; margin-bottom:16px;">💰 Revenue Summary</h3>
        <div style="display: flex; flex-direction: column; gap: 16px;">
            <div style="padding: 16px; background: rgba(76,175,80,0.1); border: 1px solid rgba(76,175,80,0.3); border-radius: 12px;">
                <div style="font-size: 12px; opacity: 0.6; margin-bottom: 4px;">Subscription Revenue</div>
                <div style="font-size: 24px; font-weight: 700; color: #66bb6a;">
                    ₱{{ number_format(\App\Models\Payment::where('status', 'paid')->sum('amount'), 2) }}
                </div>
            </div>
            <div style="padding: 16px; background: rgba(33,150,243,0.1); border: 1px solid rgba(33,150,243,0.3); border-radius: 12px;">
                <div style="font-size: 12px; opacity: 0.6; margin-bottom: 4px;">Hotspot Revenue</div>
                <div style="font-size: 24px; font-weight: 700; color: #42a5f5;">
                    ₱{{ number_format(\App\Models\Sale::sum('amount'), 2) }}
                </div>
            </div>
            <div style="padding: 16px; background: rgba(255,193,7,0.1); border: 1px solid rgba(255,193,7,0.3); border-radius: 12px;">
                <div style="font-size: 12px; opacity: 0.6; margin-bottom: 4px;">Pending Revenue</div>
                <div style="font-size: 24px; font-weight: 700; color: #ffd54f;">
                    ₱{{ number_format($pendingPayments * ($totalPayments > 0 ? \App\Models\Payment::where('status', 'paid')->sum('amount') / $totalPayments : 0), 2) }}
                </div>
            </div>
            <div style="padding: 16px; background: rgba(255,82,82,0.1); border: 1px solid rgba(255,82,82,0.3); border-radius: 12px;">
                <div style="font-size: 12px; opacity: 0.6; margin-bottom: 4px;">Total Expenses</div>
                <div style="font-size: 24px; font-weight: 700; color: #ff6b6b;">
                    ₱{{ number_format($totalExpenses, 2) }}
                </div>
            </div>
        </div>
    </div>
</div>

<div class="adm-card" style="margin-bottom: 28px;">
    <h3 style="font-size:16px; font-weight:600; margin-bottom:16px;">💸 Expenses</h3>
    @if($expenses->count())
    <div class="adm-table-wrap">
        <table class="adm-table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Purpose</th>
                    <th>Item</th>
                    <th>Amount</th>
                    <th>Notes</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($expenses as $expense)
                <tr>
                    <td style="font-size:13px;">{{ $expense->expense_date->format('M d, Y') }}</td>
                    <td style="font-weight:600;">{{ $expense->purpose }}</td>
                    <td style="font-size:13px;">{{ $expense->item }}</td>
                    <td style="font-weight:700; color:#ff6b6b;">₱{{ number_format($expense->amount, 2) }}</td>
                    <td style="font-size:12px;opacity:0.7;">{{ Str::limit($expense->notes, 30) }}</td>
                    <td>
                        <form action="{{ route('admin.expenses.delete', $expense) }}" method="POST" onsubmit="return confirm('Delete this expense?')">
                            @csrf
                            <button type="submit" class="btn-danger btn-sm">🗑️</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @else
    <p style="opacity:0.5; font-size:14px; text-align:center; padding:40px;">No expenses yet.</p>
    @endif
</div>

<div class="adm-card" style="margin-bottom: 28px;">
    <h3 style="font-size:16px; font-weight:600; margin-bottom:16px;">📶 WiFi Hotspot Sales</h3>
    @if($hotspotSales->count())
    <div class="adm-table-wrap">
        <table class="adm-table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Description</th>
                    <th>Customer</th>
                    <th>Amount</th>
                    <th>Notes</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($hotspotSales as $sale)
                <tr>
                    <td style="font-size:13px;">{{ $sale->sale_date->format('M d, Y') }}</td>
                    <td style="font-weight:600;">{{ $sale->description }}</td>
                    <td style="font-size:13px;">{{ $sale->customer_name ?? '—' }}</td>
                    <td style="font-weight:700; color:#66bb6a;">₱{{ number_format($sale->amount, 2) }}</td>
                    <td style="font-size:12px;opacity:0.7;">{{ Str::limit($sale->notes, 30) }}</td>
                    <td>
                        <form action="{{ route('admin.sales.delete', $sale) }}" method="POST" onsubmit="return confirm('Delete this sale?')">
                            @csrf
                            <button type="submit" class="btn-danger btn-sm">🗑️</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @else
    <p style="opacity:0.5; font-size:14px; text-align:center; padding:40px;">No hotspot sales yet.</p>
    @endif
</div>

<div class="adm-card">
    <h3 style="font-size:16px; font-weight:600; margin-bottom:16px;">📋 Recent Payments</h3>
    @if($recentPayments->count())
    <div class="adm-table-wrap">
        <table class="adm-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Client</th>
                    <th>Amount</th>
                    <th>Due Date</th>
                    <th>Paid Date</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($recentPayments as $payment)
                <tr>
                    <td style="font-size:12px;">{{ $payment->id }}</td>
                    <td>
                        <div style="font-weight:600;">{{ $payment->user->full_name }}</div>
                        <div style="font-size:11px;opacity:0.5;">{{ $payment->user->email }}</div>
                    </td>
                    <td style="font-weight:700; color:#66bb6a;">₱{{ number_format($payment->amount, 2) }}</td>
                    <td style="font-size:13px;opacity:0.7;">
                        {{ $payment->due_date->format('M d, Y') }}
                    </td>
                    <td style="font-size:13px;opacity:0.7;">
                        {{ $payment->paid_date ? $payment->paid_date->format('M d, Y') : '—' }}
                    </td>
                    <td>
                        <span class="badge badge-green">✓ Paid</span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @else
    <p style="opacity:0.5; font-size:14px; text-align:center; padding:40px;">No payments yet.</p>
    @endif
</div>

<style>
.sales-two-col-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
    margin-bottom: 28px;
}

@media (max-width: 768px) {
    .sales-two-col-grid {
        grid-template-columns: 1fr;
        gap: 16px;
    }
    
    .sales-two-col-grid .adm-card {
        width: 100%;
        max-width: 100%;
        overflow: hidden;
    }
    
    .adm-page-header {
        flex-direction: column;
        align-items: stretch;
        gap: 12px;
    }
    
    .adm-page-header > div:last-child {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }
    
    .adm-page-header button,
    .adm-page-header a {
        width: 100%;
        justify-content: center;
    }
    
    .adm-table-wrap {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
        margin: 0 -24px;
        padding: 0 24px;
        max-width: calc(100vw - 32px);
    }
    
    .adm-table {
        min-width: 700px;
        font-size: 12px;
    }
    
    .adm-table th,
    .adm-table td {
        padding: 10px 8px;
        font-size: 12px;
    }
    
    #revenueChart {
        max-height: 250px !important;
    }
    
    .adm-card h3 {
        font-size: 15px !important;
    }
    
    .sales-two-col-grid .adm-card > div {
        max-width: 100%;
    }
}

@media (max-width: 480px) {
    .adm-table {
        font-size: 11px;
    }
    
    .adm-table th,
    .adm-table td {
        padding: 8px 6px;
        font-size: 11px;
    }
    
    #revenueChart {
        max-height: 200px !important;
    }
}
</style>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    const ctx = document.getElementById('revenueChart');
    
    function getChartColors() {
        const isDarkMode = !document.body.classList.contains('light-mode');
        return {
            textColor: isDarkMode ? 'rgba(255,255,255,0.7)' : 'rgba(0,0,0,0.7)',
            gridColor: isDarkMode ? 'rgba(255,255,255,0.1)' : 'rgba(0,0,0,0.1)',
            tooltipBg: isDarkMode ? 'rgba(0,0,0,0.9)' : 'rgba(255,255,255,0.9)',
            tooltipText: isDarkMode ? '#fff' : '#000'
        };
    }
    
    const colors = getChartColors();
    
    const chartData = {
        labels: {!! json_encode(array_column($monthlyRevenue, 'month')) !!},
        datasets: [{
            label: 'Revenue (₱)',
            data: {!! json_encode(array_column($monthlyRevenue, 'revenue')) !!},
            borderColor: '#ff5252',
            backgroundColor: function(context) {
                const ctx = context.chart.ctx;
                const gradient = ctx.createLinearGradient(0, 0, 0, 300);
                gradient.addColorStop(0, 'rgba(255, 82, 82, 0.3)');
                gradient.addColorStop(1, 'rgba(255, 82, 82, 0.01)');
                return gradient;
            },
            borderWidth: 3,
            fill: true,
            tension: 0.4,
            pointRadius: 6,
            pointHoverRadius: 8,
            pointBackgroundColor: '#ff5252',
            pointBorderColor: '#fff',
            pointBorderWidth: 2,
            pointHoverBackgroundColor: '#ff5252',
            pointHoverBorderColor: '#fff',
            pointHoverBorderWidth: 3,
        }]
    };
    
    const config = {
        type: 'line',
        data: chartData,
        options: {
            responsive: true,
            maintainAspectRatio: true,
            aspectRatio: 2,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: colors.tooltipBg,
                    titleColor: colors.tooltipText,
                    bodyColor: colors.tooltipText,
                    borderColor: '#ff5252',
                    borderWidth: 1,
                    padding: 12,
                    displayColors: false,
                    callbacks: {
                        label: function(context) {
                            return '₱' + context.parsed.y.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: colors.gridColor,
                        drawBorder: false
                    },
                    ticks: {
                        color: colors.textColor,
                        font: {
                            size: 11,
                            weight: '600'
                        },
                        callback: function(value) {
                            return '₱' + value.toLocaleString();
                        }
                    }
                },
                x: {
                    grid: {
                        display: false,
                        drawBorder: false
                    },
                    ticks: {
                        color: colors.textColor,
                        font: {
                            size: 11,
                            weight: '600'
                        }
                    }
                }
            },
            interaction: {
                intersect: false,
                mode: 'index'
            }
        }
    };
    
    const revenueChart = new Chart(ctx, config);
    
    // Listen for theme changes to update chart
    document.body.addEventListener('themeChanged', function() {
        const colors = getChartColors();
        
        revenueChart.options.scales.y.grid.color = colors.gridColor;
        revenueChart.options.scales.y.ticks.color = colors.textColor;
        revenueChart.options.scales.x.ticks.color = colors.textColor;
        revenueChart.options.plugins.tooltip.backgroundColor = colors.tooltipBg;
        revenueChart.options.plugins.tooltip.titleColor = colors.tooltipText;
        revenueChart.options.plugins.tooltip.bodyColor = colors.tooltipText;
        revenueChart.update();
    });
</script>

{{-- Add Sale Modal --}}
<div id="addSaleModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.7); z-index:9999; align-items:center; justify-content:center;">
    <div class="adm-card" style="width:90%; max-width:500px; max-height:90vh; overflow-y:auto;">
        <h3 style="font-size:18px; font-weight:700; margin-bottom:20px;">Add WiFi Hotspot Sale</h3>
        <form action="{{ route('admin.sales.store') }}" method="POST">
            @csrf
            <div class="adm-form-group">
                <label>Description *</label>
                <input type="text" name="description" placeholder="e.g., 1 Hour WiFi Access" required>
            </div>
            <div class="adm-form-group">
                <label>Amount *</label>
                <input type="number" name="amount" step="0.01" min="0" placeholder="0.00" required>
            </div>
            <div class="adm-form-group">
                <label>Sale Date *</label>
                <input type="date" name="sale_date" value="{{ date('Y-m-d') }}" required>
            </div>
            <div class="adm-form-group">
                <label>Customer Name</label>
                <input type="text" name="customer_name" placeholder="Optional">
            </div>
            <div class="adm-form-group">
                <label>Notes</label>
                <textarea name="notes" rows="3" placeholder="Additional details..."></textarea>
            </div>
            <div class="adm-form-actions">
                <button type="button" class="btn-secondary" onclick="document.getElementById('addSaleModal').style.display='none'">Cancel</button>
                <button type="submit" class="btn-primary">💾 Save Sale</button>
            </div>
        </form>
    </div>
</div>

{{-- Add Expense Modal --}}
<div id="addExpenseModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.7); z-index:9999; align-items:center; justify-content:center;">
    <div class="adm-card" style="width:90%; max-width:500px; max-height:90vh; overflow-y:auto;">
        <h3 style="font-size:18px; font-weight:700; margin-bottom:20px;">Add Expense</h3>
        <form action="{{ route('admin.expenses.store') }}" method="POST">
            @csrf
            <div class="adm-form-group">
                <label>Purpose *</label>
                <input type="text" name="purpose" placeholder="e.g., Equipment Purchase" required>
            </div>
            <div class="adm-form-group">
                <label>Item *</label>
                <input type="text" name="item" placeholder="e.g., Router, Cable, etc." required>
            </div>
            <div class="adm-form-group">
                <label>Amount *</label>
                <input type="number" name="amount" step="0.01" min="0" placeholder="0.00" required>
            </div>
            <div class="adm-form-group">
                <label>Expense Date *</label>
                <input type="date" name="expense_date" value="{{ date('Y-m-d') }}" required>
            </div>
            <div class="adm-form-group">
                <label>Notes</label>
                <textarea name="notes" rows="3" placeholder="Additional details..."></textarea>
            </div>
            <div class="adm-form-actions">
                <button type="button" class="btn-secondary" onclick="document.getElementById('addExpenseModal').style.display='none'">Cancel</button>
                <button type="submit" class="btn-primary">💾 Save Expense</button>
            </div>
        </form>
    </div>
</div>
@endsection
