@extends('admin.layout')
@section('title', 'MikroTik Routers')

@section('content')
<div class="adm-page-header">
    <div>
        <div class="adm-page-title">🔌 MikroTik Routers</div>
        <div class="adm-page-subtitle">{{ $mikrotiks->count() }} router(s) registered — each linked to their assigned clients.</div>
    </div>
    <a href="{{ route('admin.mikrotik.create') }}" class="btn-primary">+ Add Router</a>
</div>

@if($mikrotiks->isEmpty())
<div class="adm-card" style="text-align:center; padding:48px;">
    <div style="font-size:48px; margin-bottom:16px;">🔌</div>
    <div style="font-size:16px; font-weight:600; color:#fff; margin-bottom:8px;">No routers yet</div>
    <div style="font-size:13px; color:rgba(255,255,255,0.4); margin-bottom:24px;">Add your first MikroTik router to start assigning clients.</div>
    <a href="{{ route('admin.mikrotik.create') }}" class="btn-primary">+ Add First Router</a>
</div>
@else
<div style="display:grid; grid-template-columns:repeat(auto-fill, minmax(340px, 1fr)); gap:18px;">
    @foreach($mikrotiks as $mt)
    <div class="adm-card" style="display:flex; flex-direction:column; gap:0;">

        {{-- Header --}}
        <div style="display:flex; align-items:flex-start; justify-content:space-between; margin-bottom:16px;">
            <div style="display:flex; align-items:center; gap:12px;">
                <div style="width:42px; height:42px; border-radius:12px; flex-shrink:0;
                            background:{{ $mt->is_active ? 'rgba(76,175,80,0.15)' : 'rgba(255,82,82,0.12)' }};
                            border:1px solid {{ $mt->is_active ? 'rgba(76,175,80,0.3)' : 'rgba(255,82,82,0.25)' }};
                            display:flex; align-items:center; justify-content:center; font-size:20px;">
                    🔌
                </div>
                <div>
                    <div style="font-weight:700; font-size:15px; color:#fff;">{{ $mt->name }}</div>
                    @if($mt->location)
                    <div style="font-size:12px; color:rgba(255,255,255,0.4); margin-top:2px;">📍 {{ $mt->location }}</div>
                    @endif
                </div>
            </div>
            <span class="badge {{ $mt->is_active ? 'badge-green' : 'badge-red' }}">
                {{ $mt->is_active ? 'Active' : 'Inactive' }}
            </span>
        </div>

        {{-- Details --}}
        <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px; margin-bottom:16px;">
            <div style="background:rgba(255,255,255,0.03); border:1px solid rgba(255,255,255,0.06); border-radius:10px; padding:10px 12px;">
                <div style="font-size:10px; text-transform:uppercase; letter-spacing:1px; color:rgba(255,255,255,0.35); margin-bottom:4px;">IP Address</div>
                <div style="font-size:13px; font-family:monospace; color:#81d4fa;">{{ $mt->ip_address }}:{{ $mt->port }}</div>
            </div>
            <div style="background:rgba(255,255,255,0.03); border:1px solid rgba(255,255,255,0.06); border-radius:10px; padding:10px 12px;">
                <div style="font-size:10px; text-transform:uppercase; letter-spacing:1px; color:rgba(255,255,255,0.35); margin-bottom:4px;">Clients Assigned</div>
                <div style="font-size:20px; font-weight:700; color:#fff; line-height:1;">{{ $mt->clients_count }}</div>
            </div>
            @if($mt->zerotier_network_id)
            <div style="background:rgba(255,255,255,0.03); border:1px solid rgba(255,255,255,0.06); border-radius:10px; padding:10px 12px; grid-column:1/-1;">
                <div style="font-size:10px; text-transform:uppercase; letter-spacing:1px; color:rgba(255,255,255,0.35); margin-bottom:4px;">ZeroTier Network</div>
                <div style="font-size:12px; font-family:monospace; color:rgba(255,255,255,0.7);">{{ $mt->zerotier_network_id }}</div>
            </div>
            @endif
        </div>

        @if($mt->last_connected_at)
        <div style="font-size:11px; color:rgba(255,255,255,0.3); margin-bottom:14px;">
            Last connected: {{ $mt->last_connected_at->diffForHumans() }}
        </div>
        @endif

        {{-- Connection test result --}}
        <div id="test-result-{{ $mt->id }}" style="display:none; margin-bottom:12px; padding:10px 14px; border-radius:8px; font-size:13px;"></div>

        {{-- Actions --}}
        <div style="display:flex; gap:8px; flex-wrap:wrap; margin-top:auto; padding-top:14px; border-top:1px solid rgba(255,255,255,0.06);">
            <a href="{{ route('admin.mikrotik.edit', $mt) }}" class="btn-secondary btn-sm">✏️ Edit</a>
            <button class="btn-success btn-sm" onclick="testRouter({{ $mt->id }}, '{{ $mt->name }}')">⚡ Test</button>
            <form action="{{ route('admin.mikrotik.delete', $mt) }}" method="POST" style="margin-left:auto;"
                  onsubmit="return confirm('Delete {{ addslashes($mt->name) }}? Clients will be unlinked.')">
                @csrf
                <button type="submit" class="btn-danger btn-sm">🗑 Delete</button>
            </form>
        </div>
    </div>
    @endforeach
</div>
@endif
@endsection

@push('scripts')
<script>
async function testRouter(id, name) {
    const el = document.getElementById('test-result-' + id);
    el.style.display    = 'block';
    el.style.background = 'rgba(255,255,255,0.04)';
    el.style.border     = '1px solid rgba(255,255,255,0.08)';
    el.style.color      = 'rgba(255,255,255,0.6)';
    el.textContent      = '⏳ Testing connection to ' + name + '...';

    try {
        const res  = await fetch(`/admin/mikrotik/${id}/test`, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
        });
        const data = await res.json();

        if (data.success) {
            el.style.background = 'rgba(76,175,80,0.1)';
            el.style.border     = '1px solid rgba(76,175,80,0.25)';
            el.style.color      = '#66bb6a';
            el.textContent      = '✅ ' + data.message;
        } else {
            el.style.background = 'rgba(255,82,82,0.1)';
            el.style.border     = '1px solid rgba(255,82,82,0.25)';
            el.style.color      = '#ff6b6b';
            el.textContent      = '❌ ' + data.message;
        }
    } catch(e) {
        el.style.background = 'rgba(255,82,82,0.1)';
        el.style.border     = '1px solid rgba(255,82,82,0.25)';
        el.style.color      = '#ff6b6b';
        el.textContent      = '❌ Request failed: ' + e.message;
    }
}
</script>
@endpush
