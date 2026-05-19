<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Giriş - Alfabe Mail</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; background: #f3f4f6; min-height: 100vh; display: flex; align-items: center; justify-content: center; }
        .login-box { background: white; padding: 2rem; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); width: 100%; max-width: 400px; }
        h1 { color: #1f2937; margin-bottom: 1.5rem; text-align: center; }
        .form-group { margin-bottom: 1rem; }
        label { display: block; margin-bottom: 0.5rem; color: #374151; font-weight: 500; }
        input { width: 100%; padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 4px; font-size: 1rem; }
        input:focus { outline: none; border-color: #3b82f6; box-shadow: 0 0 0 3px rgba(59,130,246,0.1); }
        button { width: 100%; padding: 0.75rem; background: #3b82f6; color: white; border: none; border-radius: 4px; font-size: 1rem; font-weight: 500; cursor: pointer; }
        button:hover { background: #2563eb; }
        .error { color: #dc2626; background: #fee2e2; padding: 0.75rem; border-radius: 4px; margin-bottom: 1rem; }
        .back { display: block; text-align: center; margin-top: 1rem; color: #6b7280; text-decoration: none; }
    </style>
</head>
<body>
    <div class="login-box">
        <h1>🔐 Admin Giriş</h1>
        
        @if(session('error'))
            <div class="error">{{ session('error') }}</div>
        @endif
        
        <form method="POST" action="/admin-giris">
            @csrf
            <div class="form-group">
                <label for="email">E-posta</label>
                <input type="email" id="email" name="email" value="admin@test.com" required>
            </div>
            
            <div class="form-group">
                <label for="password">Şifre</label>
                <input type="password" id="password" name="password" value="admin123" required>
            </div>
            
            <button type="submit">Giriş Yap</button>
        </form>
        
        <a href="/" class="back">← Ana Sayfaya Dön</a>
    </div>
</body>
</html>