@extends('layouts.itsupport')

@section('content')
<div class="animate-in" style="animation: slideIn 0.5s ease-out;">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 style="margin: 0; font-weight: 700; color: #111827;">System Overview</h2>
            <p style="margin: 5px 0 0 0; color: #6b7280; font-size: 14px;">Selamat datang kembali, IT Support. Berikut adalah status sistem saat ini.</p>
        </div>
        <div style="background: white; padding: 8px 15px; border-radius: 10px; border: 1px solid #e5e7eb; display: flex; align-items: center; gap: 10px;">
            <div style="width: 10px; height: 10px; background: #10b981; border-radius: 50%; box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.2);"></div>
            <span style="font-size: 13px; font-weight: 600; color: #374151;">System Status: Optimal</span>
        </div>
    </div>

    @if(session('success'))
        <div style="background: #ecfdf5; color: #065f46; padding: 15px; border-radius: 12px; margin-bottom: 25px; border: 1px solid #a7f3d0; display: flex; align-items: center; gap: 10px;">
            <i class="bi bi-check-circle-fill" style="font-size: 18px;"></i>
            <span style="font-size: 14px; font-weight: 500;">{{ session('success') }}</span>
        </div>
    @endif

    {{-- Stats Grid --}}
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 20px; margin-bottom: 30px;">
        <div class="stat-card">
            <div class="stat-icon" style="background: #eff6ff; color: #3b82f6;"><i class="bi bi-cpu"></i></div>
            <div class="stat-info">
                <span class="stat-label">CPU Usage</span>
                <span class="stat-value">12.5%</span>
            </div>
            <div class="stat-progress"><div style="width: 12.5%; background: #3b82f6;"></div></div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background: #f0fdf4; color: #10b981;"><i class="bi bi-memory"></i></div>
            <div class="stat-info">
                <span class="stat-label">RAM Usage</span>
                <span class="stat-value">45.2%</span>
            </div>
            <div class="stat-progress"><div style="width: 45.2%; background: #10b981;"></div></div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background: #fff7ed; color: #f59e0b;"><i class="bi bi-hdd"></i></div>
            <div class="stat-info">
                <span class="stat-label">Storage</span>
                <span class="stat-value">68.1%</span>
            </div>
            <div class="stat-progress"><div style="width: 68.1%; background: #f59e0b;"></div></div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background: #fef2f2; color: #ef4444;"><i class="bi bi-activity"></i></div>
            <div class="stat-info">
                <span class="stat-label">Active Users</span>
                <span class="stat-value">24</span>
            </div>
            <div style="font-size: 11px; color: #9ca3af; margin-top: 8px;">Real-time sessions</div>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 25px;">
        {{-- Main Actions --}}
        <div class="card" style="padding: 25px;">
            <h3 style="margin: 0 0 20px 0; font-size: 18px; color: #111827;">Quick Actions & Tools</h3>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">
                <a href="#" class="action-btn">
                    <div class="action-icon" style="background: #f3f4f6;"><i class="bi bi-terminal"></i></div>
                    <div class="action-text">
                        <strong>Log Viewer</strong>
                        <small>Check system logs</small>
                    </div>
                </a>
                <a href="#" class="action-btn">
                    <div class="action-icon" style="background: #f3f4f6;"><i class="bi bi-shield-check"></i></div>
                    <div class="action-text">
                        <strong>Access Control</strong>
                        <small>Manage permissions</small>
                    </div>
                </a>
                <a href="#" class="action-btn">
                    <div class="action-icon" style="background: #f3f4f6;"><i class="bi bi-database-up"></i></div>
                    <div class="action-text">
                        <strong>Backup DB</strong>
                        <small>Manual snapshot</small>
                    </div>
                </a>
                <a href="#" class="action-btn">
                    <div class="action-icon" style="background: #f3f4f6;"><i class="bi bi-gear-wide-connected"></i></div>
                    <div class="action-text">
                        <strong>Config Editor</strong>
                        <small>System parameters</small>
                    </div>
                </a>
            </div>
        </div>

        {{-- System Info itsupprot ya boy --}}
        <div class="card" style="padding: 25px;">
            <h3 style="margin: 0 0 20px 0; font-size: 18px; color: #111827;">System Info</h3>
            <ul style="list-style: none; padding: 0; margin: 0; display: flex; flex-direction: column; gap: 12px;">
                <li class="info-item">
                    <span class="info-label">App Version</span>
                    <span class="info-val">v2.1.0-stable</span>
                </li>
                <li class="info-item">
                    <span class="info-label">PHP Version</span>
                    <span class="info-val">{{ PHP_VERSION }}</span>
                </li>
                <li class="info-item">
                    <span class="info-label">Environment</span>
                    <span class="info-val" style="color: #3b82f6; font-weight: 600;">Production</span>
                </li>
                <li class="info-item">
                    <span class="info-label">Last Backup</span>
                    <span class="info-val">2 hours ago</span>
                </li>
            </ul>
        </div>
    </div>
</div>

<style>
    @keyframes slideIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .stat-card {
        background: white;
        border-radius: 16px;
        padding: 20px;
        border: 1px solid #e5e7eb;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
    }
    .stat-icon {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        margin-bottom: 15px;
    }
    .stat-info {
        display: flex;
        flex-direction: column;
    }
    .stat-label {
        font-size: 12px;
        color: #6b7280;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .stat-value {
        font-size: 24px;
        font-weight: 700;
        color: #111827;
        margin-top: 4px;
    }
    .stat-progress {
        height: 4px;
        background: #f3f4f6;
        border-radius: 2px;
        margin-top: 15px;
        overflow: hidden;
    }
    .stat-progress div {
        height: 100%;
        border-radius: 2px;
    }

    .action-btn {
        display: flex;
        align-items: center;
        gap: 15px;
        padding: 15px;
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        text-decoration: none;
        transition: all 0.2s ease;
    }
    .action-btn:hover {
        background: #f9fafb;
        border-color: #d1d5db;
        transform: scale(1.02);
    }
    .action-icon {
        width: 45px;
        height: 45px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
        color: #4b5563;
    }
    .action-text {
        display: flex;
        flex-direction: column;
    }
    .action-text strong {
        font-size: 14px;
        color: #111827;
    }
    .action-text small {
        font-size: 12px;
        color: #6b7280;
    }

    .info-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-bottom: 8px;
        border-bottom: 1px dashed #e5e7eb;
    }
    .info-item:last-child {
        border-bottom: none;
    }
    .info-label {
        font-size: 13px;
        color: #6b7280;
    }
    .info-val {
        font-size: 13px;
        color: #111827;
        font-weight: 500;
    }

    .card {
        border-radius: 20px !important;
    }
</style>
@endsection
