@extends('admin.layout')
@section('title', 'MikroTik Settings')

@section('content')
<div class="adm-page-header">
    <div>
        <div class="adm-page-title">🔌 MikroTik Settings</div>
        <div class="adm-page-subtitle">Connect your MikroTik router to this system via ZeroTier VPN.</div>
    </div>
    <button class="btn-primary" id="test-btn" onclick="testConnection()">
        ⚡ Test Connection
    </button>
</div>

{{-- Connection Status Banner --}}
<div id="conn-status" style="display:none; margin-bottom:20px; padding:14px 18px; border-radius:12px; font-size:14px; font-weight:500;"></div>

<div style="display:grid; grid-template-columns:1.2fr 0.8fr; gap:20px; align-items:start;">

    {{-- LEFT: Settings Form --}}
    <div style="display:flex; flex-direction:column; gap:20px;">

        {{-- ZeroTier Config --}}
        <div class="adm-card">
            <h3 style="font-size:15px; font-weight:700; color:#fff; margin-bottom:6px;">
                🌐 ZeroTier Network
            </h3>
            <p style="font-size:13px; color:rgba(255,255,255,0.45); margin-bottom:20px;">
                Your MikroTik and this server must both be joined to the same ZeroTier network.
            </p>

            <form action="{{ route('admin.settings.mikrotik.save') }}" method="POST" id="mikrotik-form">
                @csrf

                <div class="adm-form-row">
                    <div class="adm-form-group">
                        <label>ZeroTier Network ID</label>
                        <input type="text" name="zerotier_network_id"
                               value="{{ old('zerotier_network_id', $config['zerotier_network_id'] ?? '') }}"
                               placeholder="e.g. 8056c2e21c000001" maxlength="16"
                               style="font-family:monospace;">
                        @error('zerotier_network_id')<div class="adm-form-error">{{ $message }}</div>@enderror
                    </div>
                    <div class="adm-form-group">
                        <label>MikroTik ZeroTier IP</label>
                        <input type="text" name="mikrotik_ip"
                               value="{{ old('mikrotik_ip', $config['mikrotik_ip'] ?? '') }}"
                               placeholder="e.g. 172.25.0.5"
                               style="font-family:monospace;">
                        @error('mikrotik_ip')<div class="adm-form-error">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="adm-form-row">
                    <div class="adm-form-group">
                        <label>MikroTik API Port</label>
                        <input type="number" name="mikrotik_port"
                               value="{{ old('mikrotik_port', $config['mikrotik_port'] ?? '8728') }}"
                               placeholder="8728 (default API port)">
                        @error('mikrotik_port')<div class="adm-form-error">{{ $message }}</div>@enderror
                    </div>
                    <div class="adm-form-group">
                        <label>MikroTik API Username</label>
                        <input type="text" name="mikrotik_user"
                               value="{{ old('mikrotik_user', $config['mikrotik_user'] ?? 'admin') }}"
                               placeholder="admin">
                        @error('mikrotik_user')<div class="adm-form-error">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="adm-form-group">
                    <label>MikroTik API Password</label>
                    <input type="password" name="mikrotik_password"
                           value="{{ old('mikrotik_password', $config['mikrotik_password'] ?? '') }}"
                           placeholder="Leave blank to keep existing password"
                           autocomplete="new-password">
                    @error('mikrotik_password')<div class="adm-form-error">{{ $message }}</div>@enderror
                </div>

                <div class="adm-form-actions">
                    <button type="submit" class="btn-primary">💾 Save Settings</button>
                </div>
            </form>
        </div>

        {{-- Connection Log --}}
        <div class="adm-card">
            <h3 style="font-size:15px; font-weight:700; color:#fff; margin-bottom:16px;">📋 Connection Log</h3>
            <div id="conn-log"
                 style="background:rgba(0,0,0,0.4); border:1px solid rgba(255,255,255,0.07); border-radius:10px;
                        padding:14px; font-family:monospace; font-size:12px; color:rgba(255,255,255,0.6);
                        min-height:100px; max-height:200px; overflow-y:auto; line-height:1.8;">
                <span style="color:rgba(255,255,255,0.25);">No tests run yet. Click "Test Connection" to begin.</span>
            </div>
        </div>
    </div>

    {{-- RIGHT: Instructions --}}
    <div style="display:flex; flex-direction:column; gap:20px;">

        {{-- Step by step --}}
        <div class="adm-card">
            <h3 style="font-size:15px; font-weight:700; color:#fff; margin-bottom:16px;">
                📖 How to Connect MikroTik via ZeroTier
            </h3>

            <div style="display:flex; flex-direction:column; gap:14px;">

                <div style="display:flex; gap:12px; align-items:flex-start;">
                    <div style="width:26px; height:26px; border-radius:50%; background:rgba(255,82,82,0.2); border:1px solid rgba(255,82,82,0.4); color:#ff6b6b; font-size:12px; font-weight:700; display:flex; align-items:center; justify-content:center; flex-shrink:0; margin-top:1px;">1</div>
                    <div>
                        <div style="font-size:13px; font-weight:600; color:#fff; margin-bottom:3px;">Install ZeroTier on this server</div>
                        <code style="font-size:11px; background:rgba(0,0,0,0.4); padding:4px 8px; border-radius:6px; color:#81d4fa; display:block; margin-top:4px;">curl -s https://install.zerotier.com | sudo bash</code>
                        <code style="font-size:11px; background:rgba(0,0,0,0.4); padding:4px 8px; border-radius:6px; color:#81d4fa; display:block; margin-top:4px;">sudo zerotier-cli join &lt;NETWORK_ID&gt;</code>
                    </div>
                </div>

                <div style="display:flex; gap:12px; align-items:flex-start;">
                    <div style="width:26px; height:26px; border-radius:50%; background:rgba(255,82,82,0.2); border:1px solid rgba(255,82,82,0.4); color:#ff6b6b; font-size:12px; font-weight:700; display:flex; align-items:center; justify-content:center; flex-shrink:0; margin-top:1px;">2</div>
                    <div>
                        <div style="font-size:13px; font-weight:600; color:#fff; margin-bottom:3px;">Install ZeroTier on MikroTik</div>
                        <div style="font-size:12px; color:rgba(255,255,255,0.5); line-height:1.6;">
                            Go to <strong style="color:#fff;">System → Packages</strong> in WinBox, download and install the <strong style="color:#fff;">zerotier</strong> package, then reboot.
                        </div>
                        <code style="font-size:11px; background:rgba(0,0,0,0.4); padding:4px 8px; border-radius:6px; color:#81d4fa; display:block; margin-top:6px;">/zerotier enable</code>
                        <code style="font-size:11px; background:rgba(0,0,0,0.4); padding:4px 8px; border-radius:6px; color:#81d4fa; display:block; margin-top:4px;">/zerotier join-network &lt;NETWORK_ID&gt;</code>
                    </div>
                </div>

                <div style="display:flex; gap:12px; align-items:flex-start;">
                    <div style="width:26px; height:26px; border-radius:50%; background:rgba(255,82,82,0.2); border:1px solid rgba(255,82,82,0.4); color:#ff6b6b; font-size:12px; font-weight:700; display:flex; align-items:center; justify-content:center; flex-shrink:0; margin-top:1px;">3</div>
                    <div>
                        <div style="font-size:13px; font-weight:600; color:#fff; margin-bottom:3px;">Authorize both devices</div>
                        <div style="font-size:12px; color:rgba(255,255,255,0.5); line-height:1.6;">
                            Log in to <a href="https://my.zerotier.com" target="_blank" style="color:#81d4fa;">my.zerotier.com</a>, go to your network, and authorize both this server and your MikroTik under <strong style="color:#fff;">Members</strong>.
                        </div>
                    </div>
                </div>

                <div style="display:flex; gap:12px; align-items:flex-start;">
                    <div style="width:26px; height:26px; border-radius:50%; background:rgba(255,82,82,0.2); border:1px solid rgba(255,82,82,0.4); color:#ff6b6b; font-size:12px; font-weight:700; display:flex; align-items:center; justify-content:center; flex-shrink:0; margin-top:1px;">4</div>
                    <div>
                        <div style="font-size:13px; font-weight:600; color:#fff; margin-bottom:3px;">Enable MikroTik API</div>
                        <div style="font-size:12px; color:rgba(255,255,255,0.5); line-height:1.6;">In WinBox go to <strong style="color:#fff;">IP → Services</strong>, enable <strong style="color:#fff;">api</strong> (port 8728). Restrict it to the ZeroTier subnet for security.</div>
                        <code style="font-size:11px; background:rgba(0,0,0,0.4); padding:4px 8px; border-radius:6px; color:#81d4fa; display:block; margin-top:6px;">/ip service set api disabled=no address=172.25.0.0/16</code>
                    </div>
                </div>

                <div style="display:flex; gap:12px; align-items:flex-start;">
                    <div style="width:26px; height:26px; border-radius:50%; background:rgba(255,82,82,0.2); border:1px solid rgba(255,82,82,0.4); color:#ff6b6b; font-size:12px; font-weight:700; display:flex; align-items:center; justify-content:center; flex-shrink:0; margin-top:1px;">5</div>
                    <div>
                        <div style="font-size:13px; font-weight:600; color:#fff; margin-bottom:3px;">Enter credentials & test</div>
                        <div style="font-size:12px; color:rgba(255,255,255,0.5); line-height:1.6;">
                            Fill in the ZeroTier IP assigned to your MikroTik, the API port, username and password, save, then click <strong style="color:#fff;">Test Connection</strong>.
                        </div>
                    </div>
                </div>

            </div>
        </div>

        {{-- Security tip --}}
        <div style="background:rgba(255,193,7,0.07); border:1px solid rgba(255,193,7,0.2); border-radius:12px; padding:16px 18px;">
            <div style="font-size:13px; font-weight:600; color:#ffd54f; margin-bottom:6px;">⚠️ Security Tip</div>
            <div style="font-size:12px; color:rgba(255,255,255,0.55); line-height:1.7;">
                Create a dedicated API-only user on MikroTik with limited permissions instead of using the <code style="background:rgba(0,0,0,0.3); padding:1px 5px; border-radius:4px;">admin</code> account.
                <br><br>
                <code style="background:rgba(0,0,0,0.3); padding:4px 8px; border-radius:6px; color:#81d4fa; display:block; margin-top:4px; font-size:11px;">/user add name=ispapi group=read password=&lt;strong_pass&gt;</code>
            </div>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script>
async function testConnection() {
    const btn    = document.getElementById('test-btn');
    const status = document.getElementById('conn-status');
    const log    = document.getElementById('conn-log');

    btn.disabled    = true;
    btn.textContent = '⏳ Testing...';
    status.style.display = 'none';

    const now = new Date().toLocaleTimeString();
    log.innerHTML += `<div><span style="color:rgba(255,255,255,0.3);">[${now}]</span> Initiating connection test...</div>`;
    log.scrollTop = log.scrollHeight;

    try {
        const res  = await fetch('{{ route("admin.settings.mikrotik.test") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
                'Content-Type': 'application/json',
            }
        });
        const data = await res.json();

        if (data.success) {
            status.style.display    = 'block';
            status.style.background = 'rgba(76,175,80,0.12)';
            status.style.border     = '1px solid rgba(76,175,80,0.3)';
            status.style.color      = '#66bb6a';
            status.innerHTML        = '✅ <strong>Connected!</strong> ' + data.message;
            log.innerHTML += `<div style="color:#66bb6a;">[${now}] ✅ SUCCESS — ${data.message}</div>`;
        } else {
            status.style.display    = 'block';
            status.style.background = 'rgba(255,82,82,0.1)';
            status.style.border     = '1px solid rgba(255,82,82,0.3)';
            status.style.color      = '#ff6b6b';
            status.innerHTML        = '❌ <strong>Failed:</strong> ' + data.message;
            log.innerHTML += `<div style="color:#ff6b6b;">[${now}] ❌ FAILED — ${data.message}</div>`;
        }
    } catch (e) {
        status.style.display    = 'block';
        status.style.background = 'rgba(255,82,82,0.1)';
        status.style.border     = '1px solid rgba(255,82,82,0.3)';
        status.style.color      = '#ff6b6b';
        status.innerHTML        = '❌ <strong>Error:</strong> Could not reach the server.';
        log.innerHTML += `<div style="color:#ff6b6b;">[${now}] ❌ ERROR — ${e.message}</div>`;
    }

    log.scrollTop   = log.scrollHeight;
    btn.disabled    = false;
    btn.textContent = '⚡ Test Connection';
}
</script>
@endpush
