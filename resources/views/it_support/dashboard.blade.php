@extends('layouts.itsupport')

@section('content')
<div class="card">
    <h2 style="margin-top:0;">Welcome to IT Support Dashboard</h2>
    <p style="color: var(--text-secondary);">
        Anda masuk sebagai IT Support. Halaman ini digunakan untuk mengelola konfigurasi sistem dan pemeliharaan teknis aplikasi.
    </p>

    @if(session('success'))
        <div style="background: #dcfce7; color: #166534; padding: 10px; border-radius: 5px; margin-bottom: 20px;">
            {{ session('success') }}
        </div>
    @endif

    <div style="margin-top: 30px; display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px;">
        <div style="border: 1px solid var(--border-color); padding: 15px; border-radius: 8px;">
            <div style="font-size: 24px; color: #3b82f6; margin-bottom: 10px;"><i class="bi bi-hdd-network"></i></div>
            <h3 style="margin: 0 0 10px 0; font-size: 16px;">System Logs</h3>
            <p style="margin: 0; font-size: 13px; color: var(--text-secondary);">Pantau log error dan peringatan aplikasi untuk troubleshooting.</p>
        </div>
        <div style="border: 1px solid var(--border-color); padding: 15px; border-radius: 8px;">
            <div style="font-size: 24px; color: #10b981; margin-bottom: 10px;"><i class="bi bi-database"></i></div>
            <h3 style="margin: 0 0 10px 0; font-size: 16px;">Database Backup</h3>
            <p style="margin: 0; font-size: 13px; color: var(--text-secondary);">Kelola dan buat cadangan database secara manual jika diperlukan.</p>
        </div>
        <div style="border: 1px solid var(--border-color); padding: 15px; border-radius: 8px;">
            <div style="font-size: 24px; color: #f59e0b; margin-bottom: 10px;"><i class="bi bi-shield-lock"></i></div>
            <h3 style="margin: 0 0 10px 0; font-size: 16px;">Access Management</h3>
            <p style="margin: 0; font-size: 13px; color: var(--text-secondary);">Verifikasi hak akses dan reset password pengguna.</p>
        </div>
    </div>
</div>
@endsection
