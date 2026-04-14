<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile - ISP Billing</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #0a0a0a 0%, #1a1a1a 50%, #0a0a0a 100%);
            color: #f5f5f5;
            font-family: 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .container {
            width: 100%;
            max-width: 500px;
            background: rgba(10, 10, 10, 0.95);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 28px;
            padding: 40px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        }
        .header {
            text-align: center;
            margin-bottom: 32px;
        }
        .header h1 {
            font-size: 28px;
            margin-bottom: 8px;
            color: #ffffff;
            letter-spacing: 1px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 8px;
            font-size: 14px;
            color: rgba(255, 255, 255, 0.8);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 500;
        }
        input[type="text"],
        input[type="email"],
        input[type="number"],
        input[type="tel"],
        textarea {
            width: 100%;
            padding: 12px 16px;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            color: #f5f5f5;
            font-family: inherit;
            font-size: 14px;
            transition: all 0.3s;
        }
        input[type="text"]:focus,
        input[type="email"]:focus,
        input[type="number"]:focus,
        input[type="tel"]:focus,
        textarea:focus {
            outline: none;
            border-color: rgba(255, 107, 107, 0.5);
            background: rgba(255, 107, 107, 0.05);
            box-shadow: 0 0 10px rgba(255, 107, 107, 0.1);
        }
        textarea {
            resize: vertical;
            min-height: 80px;
        }
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }
        .profile-image-section {
            text-align: center;
            margin-bottom: 24px;
            padding-bottom: 24px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        }
        .profile-image-display {
            width: 100px;
            height: 100px;
            margin: 0 auto 16px;
            border-radius: 50%;
            background: linear-gradient(135deg, #ff6b6b, #ff0000);
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            border: 3px solid rgba(255, 107, 107, 0.3);
        }
        .profile-image-display img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .profile-image-display span {
            font-size: 36px;
            font-weight: bold;
        }
        .file-input-wrapper {
            position: relative;
            display: inline-block;
            width: 100%;
        }
        input[type="file"] {
            display: none;
        }
        .file-input-label {
            display: block;
            padding: 12px 16px;
            background: rgba(255, 107, 107, 0.18);
            border: 1px solid rgba(255, 107, 107, 0.35);
            color: #f5f5f5;
            border-radius: 12px;
            cursor: pointer;
            text-align: center;
            transition: all 0.3s;
            font-size: 14px;
        }
        .file-input-label:hover {
            background: rgba(255, 107, 107, 0.3);
            border-color: rgba(255, 107, 107, 0.5);
        }
        .button-group {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
            margin-top: 32px;
        }
        button {
            padding: 12px 24px;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }
        .btn-submit {
            background: linear-gradient(135deg, #ff6b6b, #ff0000);
            color: #ffffff;
            box-shadow: 0 8px 24px rgba(255, 0, 0, 0.25);
        }
        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 36px rgba(255, 0, 0, 0.35);
        }
        .btn-cancel {
            background: rgba(255, 255, 255, 0.1);
            color: #f5f5f5;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        .btn-cancel:hover {
            background: rgba(255, 255, 255, 0.15);
            border-color: rgba(255, 255, 255, 0.4);
        }
        .error {
            color: #ff5252;
            font-size: 12px;
            margin-top: 6px;
        }
        .toast-container {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 999;
            display: flex;
            flex-direction: column;
            gap: 12px;
            width: min(380px, calc(100vw - 40px));
        }
        .toast {
            background: rgba(15, 15, 15, 0.98);
            border: 1px solid rgba(76, 175, 80, 0.8);
            border-left: 4px solid #4caf50;
            color: #ffffff;
            padding: 16px 18px;
            border-radius: 14px;
            box-shadow: 0 16px 50px rgba(0, 0, 0, 0.35);
            position: relative;
            overflow: hidden;
            font-size: 14px;
            line-height: 1.5;
        }
        .toast-success {
            border-left-color: #4caf50;
            border-color: rgba(76, 175, 80, 0.8);
        }
        .toast-error {
            border-left-color: #ff5252;
            border-color: rgba(255, 82, 82, 0.8);
        }
        .toast-progress {
            position: absolute;
            left: 0;
            bottom: 0;
            height: 3px;
            background: linear-gradient(90deg, #4caf50, #81c784);
            width: 100%;
            animation: progressBar 5s linear forwards;
        }
        .toast-error .toast-progress {
            background: linear-gradient(90deg, #ff5252, #ff8a80);
        }
        @keyframes progressBar {
            from { width: 100%; }
            to { width: 0%; }
        }
    </style>
</head>
<body>
    @if ($errors->any())
        <div class="toast-container" id="error-toast-container">
            @foreach ($errors->all() as $error)
                <div class="toast toast-error" id="error-toast-{{ $loop->index }}">
                    {{ $error }}
                    <div class="toast-progress"></div>
                </div>
            @endforeach
        </div>
    @endif

    @if (session('success'))
        <div class="toast-container" id="success-toast-container">
            <div class="toast toast-success" id="success-toast">
                {{ session('success') }}
                <div class="toast-progress"></div>
            </div>
        </div>
    @endif

    <div class="container">
        <div class="header">
            <h1>Edit Profile</h1>
        </div>

        <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="profile-image-section">
                <div class="profile-image-display">
                    @if ($user->profile_image)
                        <img src="{{ asset('storage/' . $user->profile_image) }}" alt="Profile">
                    @else
                        <span>ISP</span>
                    @endif
                </div>
                <div class="file-input-wrapper">
                    <input type="file" id="profile_image" name="profile_image" accept="image/*">
                    <label for="profile_image" class="file-input-label">
                        📷 Choose Profile Picture
                    </label>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="first_name">First Name *</label>
                    <input type="text" id="first_name" name="first_name" value="{{ old('first_name', $user->first_name) }}" required>
                    @error('first_name')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="middle_name">Middle Name</label>
                    <input type="text" id="middle_name" name="middle_name" value="{{ old('middle_name', $user->middle_name) }}">
                    @error('middle_name')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="form-group">
                <label for="last_name">Last Name *</label>
                <input type="text" id="last_name" name="last_name" value="{{ old('last_name', $user->last_name) }}" required>
                @error('last_name')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="email">Email *</label>
                <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required>
                @error('email')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="phone_number">Phone Number</label>
                    <input type="tel" id="phone_number" name="phone_number" value="{{ old('phone_number', $user->phone_number) }}">
                    @error('phone_number')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="age">Age</label>
                    <input type="number" id="age" name="age" value="{{ old('age', $user->age) }}" min="1" max="120">
                    @error('age')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="form-group">
                <label for="address">Address</label>
                <textarea id="address" name="address">{{ old('address', $user->address) }}</textarea>
                @error('address')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>

            <div class="button-group">
                <a href="{{ route('admin.dashboard') }}" class="btn-cancel" style="display: flex; align-items: center; justify-content: center; text-decoration: none;">
                    Cancel
                </a>
                <button type="submit" class="btn-submit">Save Changes</button>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const toasts = document.querySelectorAll('.toast');
            toasts.forEach(toast => {
                setTimeout(() => {
                    toast.style.animation = 'fadeOut 0.5s ease forwards';
                    setTimeout(() => toast.remove(), 500);
                }, 5000);
            });
        });
    </script>
    <style>
        @keyframes fadeOut {
            from { opacity: 1; transform: translateX(0); }
            to { opacity: 0; transform: translateX(100px); }
        }
    </style>
</body>
</html>
