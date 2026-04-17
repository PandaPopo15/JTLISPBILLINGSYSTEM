@extends('admin.layout')
@section('title', 'NapBoxes')

@section('content')
<div class="adm-page-header">
    <div>
        <div class="adm-page-title">📦 NapBoxes</div>
        <div class="adm-page-subtitle">{{ $napboxes->count() }} NapBox(es) registered — physical network access points in specific locations.</div>
    </div>
    <a href="{{ route('admin.napboxes.create') }}" class="btn-primary">+ Add NapBox</a>
</div>

@if($napboxes->isEmpty())
<div class="adm-card" style="text-align:center; padding:48px;">
    <div style="font-size:48px; margin-bottom:16px;">📦</div>
    <div style="font-size:16px; font-weight:600; color:#fff; margin-bottom:8px;">No NapBoxes yet</div>
    <div style="font-size:13px; color:rgba(255,255,255,0.4); margin-bottom:24px;">Add your first NapBox to organize network access points.</div>
    <a href="{{ route('admin.napboxes.create') }}" class="btn-primary">+ Add First NapBox</a>
</div>
@else
<div style="display:grid; grid-template-columns:repeat(auto-fill, minmax(340px, 1fr)); gap:18px;">
    @foreach($napboxes as $napbox)
    <div class="adm-card" style="display:flex; flex-direction:column; gap:0;">

        {{-- Header --}}
        <div style="display:flex; align-items:flex-start; justify-content:space-between; margin-bottom:16px;">
            <div style="display:flex; align-items:center; gap:12px;">
                <div style="width:42px; height:42px; border-radius:12px; flex-shrink:0;
                            background:rgba(33,150,243,0.15);
                            border:1px solid rgba(33,150,243,0.3);
                            display:flex; align-items:center; justify-content:center; font-size:20px;">
                    📦
                </div>
                <div>
                    <div style="font-weight:700; font-size:15px; color:#fff;">{{ $napbox->name }}</div>
                    <div style="font-size:12px; color:rgba(255,255,255,0.4); margin-top:2px;">📍 {{ $napbox->location }}</div>
                </div>
            </div>
            @if($napbox->mikrotik)
            <span class="badge badge-green">Assigned</span>
            @else
            <span class="badge badge-gray">Unassigned</span>
            @endif
        </div>

        {{-- Details --}}
        <div style="display:grid; gap:10px; margin-bottom:16px;">
            <div style="background:rgba(255,255,255,0.03); border:1px solid rgba(255,255,255,0.06); border-radius:10px; padding:10px 12px;">
                <div style="font-size:10px; text-transform:uppercase; letter-spacing:1px; color:rgba(255,255,255,0.35); margin-bottom:4px;">Assigned Router</div>
                @if($napbox->mikrotik)
                <div style="font-size:13px; color:#81d4fa;">🔌 {{ $napbox->mikrotik->name }}</div>
                @else
                <div style="font-size:13px; color:rgba(255,255,255,0.3);">Not assigned</div>
                @endif
            </div>
            @if($napbox->notes)
            <div style="background:rgba(255,255,255,0.03); border:1px solid rgba(255,255,255,0.06); border-radius:10px; padding:10px 12px;">
                <div style="font-size:10px; text-transform:uppercase; letter-spacing:1px; color:rgba(255,255,255,0.35); margin-bottom:4px;">Notes</div>
                <div style="font-size:12px; color:rgba(255,255,255,0.7);">{{ $napbox->notes }}</div>
            </div>
            @endif
        </div>

        {{-- Actions --}}
        <div style="display:flex; gap:8px; flex-wrap:wrap; margin-top:auto; padding-top:14px; border-top:1px solid rgba(255,255,255,0.06);">
            <a href="{{ route('admin.napboxes.edit', $napbox) }}" class="btn-secondary btn-sm">✏️ Edit</a>
            <form action="{{ route('admin.napboxes.delete', $napbox) }}" method="POST" style="margin-left:auto;"
                  onsubmit="return confirm('Delete {{ addslashes($napbox->name) }}?')">
                @csrf
                <button type="submit" class="btn-danger btn-sm">🗑 Delete</button>
            </form>
        </div>
    </div>
    @endforeach
</div>
@endif
@endsection
