<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Kata Sandi - Sistem Peminjaman Alat</title>
    <link href="https://fonts.googleapis.com/css2?family=Archivo:wght@600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root{
            --page-bg: #05070d;
            --panel-bg: #0b1220;
            --field-bg: #061838;
            --field-border: #16366f;
            --amber: #eda63a;
            --amber-deep: #c9822a;
            --text-light: #f2efe6;
            --text-muted: #8b93a8;
        }
        *{ box-sizing: border-box; }
        html, body{ margin: 0; min-height: 100vh; font-family: 'Inter', sans-serif; background: var(--page-bg); }
        body{ display: flex; align-items: center; justify-content: center; min-height: 100vh; padding: 32px 16px; }
        .card{
            width: 100%; max-width: 440px;
            background: var(--panel-bg);
            border-radius: 20px;
            border: 1px solid rgba(255,255,255,0.06);
            padding: 40px 36px;
            text-align: center;
        }
        .icon-circle{
            width: 56px; height: 56px;
            border-radius: 50%;
            background: rgba(237,166,58,0.12);
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 20px;
            color: var(--amber);
            font-size: 22px;
        }
        h2{
            font-family: 'Archivo', sans-serif;
            font-weight: 700; font-size: 22px;
            color: var(--text-light);
            margin: 0 0 10px;
        }
        p.sub{
            font-size: 14px; line-height: 1.6;
            color: var(--text-muted);
            margin: 0 0 24px;
        }
        label{
            display: block; text-align: left;
            font-size: 13px; font-weight: 500;
            color: var(--text-light);
            margin-bottom: 7px;
        }
        input[type="email"]{
            width: 100%;
            background: var(--field-bg);
            border: 1px solid var(--field-border);
            border-radius: 9px;
            padding: 11px 13px;
            font-size: 14px;
            color: var(--text-light);
            font-family: 'Inter', sans-serif;
            outline: none;
            margin-bottom: 20px;
        }
        input:focus{ border-color: var(--amber); }
        .alert-success{
            margin-bottom: 18px; padding: 14px;
            border-radius: 12px;
            background: rgba(34,197,94,0.1);
            border: 1px solid rgba(34,197,94,0.3);
            color: #86efac;
            font-size: 13.5px; text-align: left;
        }
        .alert-error{
            margin-bottom: 18px; padding: 10px 12px;
            border-radius: 10px;
            background: rgba(239,68,68,0.1);
            border: 1px solid rgba(239,68,68,0.3);
            color: #fca5a5;
            font-size: 12.5px; text-align: left;
        }
        button.submit{
            width: 100%;
            background: linear-gradient(160deg, var(--amber), var(--amber-deep));
            color: #241203;
            border: none; border-radius: 9px;
            padding: 12px 16px;
            font-size: 14px; font-weight: 600;
            cursor: pointer;
            margin-bottom: 14px;
        }
        a.back-link{
            display: inline-flex; align-items: center; gap: 6px;
            color: var(--text-muted);
            font-size: 13px;
            text-decoration: none;
        }
        a.back-link:hover{ color: var(--text-light); }
    </style>
</head>
<body>
    <div class="card">
        <div class="icon-circle">
            <i class="fa-solid fa-key"></i>
        </div>
        <h2>Lupa Kata Sandi?</h2>
        <p class="sub">Masukkan email akunmu. Admin akan menerima permintaan ini dan mengatur ulang kata sandimu.</p>

        @if(session('success'))
            <div class="alert-success">
                <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="alert-error">
                <i class="fa-solid fa-circle-exclamation"></i> {{ session('error') }}
            </div>
        @endif

        <form action="{{ route('password.ajukan') }}" method="POST">
            @csrf
            <label for="email">Email Terdaftar</label>
            <input type="email" id="email" name="email" placeholder="nama@email.com" required value="{{ old('email') }}">
            <button type="submit" class="submit">Kirim Permintaan Reset</button>
        </form>

        <a href="{{ route('login') }}" class="back-link">
            <i class="fa-solid fa-arrow-left"></i> Kembali ke Login
        </a>
    </div>
</body>
</html>