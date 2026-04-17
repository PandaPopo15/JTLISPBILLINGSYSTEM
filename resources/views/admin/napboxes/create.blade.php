@extends('admin.layout')
@section('title', 'Add NapBox')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
@endpush

@section('content')
<div class="adm-page-header">
    <div>
        <div class="adm-page-title">Add NapBox</div>
        <div class="adm-page-subtitle">Register a new network access point.</div>
    </div>
    <a href="{{ route('admin.napboxes') }}" class="btn-secondary">← Back</a>
</div>

<div style="display:grid; grid-template-columns:1.4fr 0.6fr; gap:20px; align-items:start;">

    <div class="adm-card">
        @if($errors->any())
        <div style="background:rgba(255,82,82,0.1); border:1px solid rgba(255,82,82,0.3); border-radius:10px; padding:12px 16px; margin-bottom:20px; color:#ff8a80; font-size:13px;">
            @foreach($errors->all() as $e)<div>• {{ $e }}</div>@endforeach
        </div>
        @endif

        <form action="{{ route('admin.napboxes.store') }}" method="POST">
            @csrf

            <div class="adm-form-group">
                <label>NapBox Name *</label>
                <input type="text" name="name" value="{{ old('name') }}" placeholder="e.g. NapBox A - Main Street" required>
                @error('name')<div class="adm-form-error">{{ $message }}</div>@enderror
            </div>

            <div class="adm-form-group">
                <label>Location *</label>
                <input type="text" name="location" value="{{ old('location') }}" placeholder="e.g. Corner of Main St. & 5th Ave" required>
                @error('location')<div class="adm-form-error">{{ $message }}</div>@enderror
            </div>

            <div class="adm-form-group">
                <label>Assign to MikroTik Router (Optional)</label>
                <select name="mikrotik_id">
                    <option value="">-- Not Assigned --</option>
                    @foreach($mikrotiks as $mt)
                    <option value="{{ $mt->id }}" {{ old('mikrotik_id') == $mt->id ? 'selected' : '' }}>
                        {{ $mt->name }}
                    </option>
                    @endforeach
                </select>
                @error('mikrotik_id')<div class="adm-form-error">{{ $message }}</div>@enderror
            </div>

            <div class="adm-form-group">
                <label>Notes</label>
                <textarea name="notes" rows="3" placeholder="Optional notes about this NapBox...">{{ old('notes') }}</textarea>
            </div>

            <div class="adm-form-actions">
                <a href="{{ route('admin.napboxes') }}" class="btn-secondary">Cancel</a>
                <button type="submit" class="btn-primary">💾 Save NapBox</button>
            </div>
        </form>
    </div>

    {{-- Quick tips --}}
    <div class="adm-card" style="padding:18px;">
        <div style="font-size:13px; font-weight:700; color:#fff; margin-bottom:10px;">💡 About NapBoxes</div>
        <ul style="font-size:12px; color:rgba(255,255,255,0.5); line-height:1.9; padding-left:16px;">
            <li>NapBoxes are physical network access points in specific locations.</li>
            <li>Each NapBox connects to one MikroTik router.</li>
            <li>Use descriptive names to easily identify them.</li>
            <li>Click on the map to pinpoint exact location.</li>
        </ul>
    </div>

</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
@endpush
