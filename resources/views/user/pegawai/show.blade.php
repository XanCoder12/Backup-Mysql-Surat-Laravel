@extends('layouts.user')
@section('title', 'Profil: ' . $user->name)

@section('content')
<style>
/* ===== PAGE STYLES ===== */
.pg-back-btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 8px 16px;
    border-radius: 10px;
    background: rgba(255,255,255,0.15);
    border: 1px solid rgba(255,255,255,0.25);
    color: white;
    font-size: 13px;
    font-weight: 600;
    text-decoration: none;
    transition: background 0.2s;
    margin-bottom: 24px;
    width: fit-content;
}
.pg-back-btn:hover { background: rgba(255,255,255,0.25); color: white; }

/* Profile Hero */
.profile-hero {
    background: white;
    border-radius: 20px;
    box-shadow: 0 4px 24px rgba(15,23,42,0.07);
    border: 1px solid #f1f5f9;
    overflow: hidden;
    margin-bottom: 24px;
}
.profile-hero-inner {
    display: flex;
    align-items: center;
    gap: 24px;
    padding: 28px 32px;
    flex-wrap: wrap;
}
.profile-avatar-box {
    width: 96px;
    height: 96px;
    border-radius: 50%;
    border: 4px solid #e0e7ff;
    overflow: hidden;
    background: linear-gradient(135deg, #4361ee, #6366f1);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 32px;
    font-weight: 800;
    color: white;
    flex-shrink: 0;
    box-shadow: 0 6px 20px rgba(67,97,238,0.25);
}
.profile-avatar-box img { width: 100%; height: 100%; object-fit: cover; }
.profile-meta { flex: 1; min-width: 0; }
.profile-name {
    font-size: 22px;
    font-weight: 800;
    color: #0f172a;
    margin-bottom: 4px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.profile-role-badge {
    display: inline-block;
    font-size: 11px;
    font-weight: 700;
    padding: 3px 10px;
    border-radius: 99px;
    background: rgba(67,97,238,0.08);
    color: #4361ee;
    border: 1px solid rgba(67,97,238,0.18);
    margin-left: 8px;
    vertical-align: middle;
}
.profile-detail-row {
    display: flex;
    flex-wrap: wrap;
    gap: 16px;
    margin-top: 8px;
}
.profile-detail-item {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 13px;
    color: #64748b;
    font-weight: 500;
}
.profile-detail-item i { font-size: 14px; color: #94a3b8; }

/* Stat Cards */
.pg-stat {
    border-radius: 18px;
    padding: 22px 20px;
    display: flex;
    align-items: center;
    gap: 16px;
    color: white;
    box-shadow: 0 8px 24px -4px rgba(0,0,0,0.18);
    position: relative;
    overflow: hidden;
    height: 100%;
}
.pg-stat.blue  { background: linear-gradient(135deg, #4361ee 0%, #3a0ca3 100%); }
.pg-stat.green { background: linear-gradient(135deg, #10b981 0%, #047857 100%); }
.pg-stat.amber { background: linear-gradient(135deg, #f59e0b 0%, #b45309 100%); }
.pg-stat-icon {
    width: 54px;
    height: 54px;
    border-radius: 16px;
    background: rgba(255,255,255,0.18);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    flex-shrink: 0;
}
.pg-stat-label {
    font-size: 10px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.1em;
    opacity: 0.8;
    margin-bottom: 4px;
}
.pg-stat-value { font-size: 32px; font-weight: 900; line-height: 1; }

/* Chart Cards */
.pg-chart-card {
    background: white;
    border-radius: 20px;
    box-shadow: 0 4px 20px rgba(15,23,42,0.06);
    border: 1px solid #f1f5f9;
    height: 100%;
    display: flex;
    flex-direction: column;
}
.pg-chart-head {
    padding: 18px 22px 14px;
    border-bottom: 1px solid #f8fafc;
}
.pg-chart-head h6 {
    font-size: 14px;
    font-weight: 700;
    color: #1e293b;
    margin: 0 0 2px;
}
.pg-chart-head small { font-size: 11px; color: #94a3b8; }
.pg-chart-body {
    flex: 1;
    padding: 16px 20px 20px;
    position: relative;
    min-height: 0;
}
</style>

<div class="container-fluid px-0">

    {{-- Back Button --}}
    <a href="{{ route('user.pegawai.index') }}" class="pg-back-btn">
        <i class="bi bi-arrow-left"></i> Kembali ke Pencarian
    </a>

    {{-- Profile Hero Card --}}
    <div class="profile-hero animate-in">
        <div style="height: 8px; background: linear-gradient(90deg, #4361ee 0%, #6366f1 50%, #0ea5e9 100%);"></div>
        <div class="profile-hero-inner">
            <div class="profile-avatar-box">
                @if($user->profile_photo)
                    <img src="{{ Storage::url($user->profile_photo) }}" alt="{{ $user->name }}">
                @else
                    {{ strtoupper(substr($user->name, 0, 2)) }}
                @endif
            </div>

            <div class="profile-meta">
                <div>
                    <span class="profile-name">{{ $user->name }}</span>
                    <span class="profile-role-badge">{{ $user->getRoleLabel() }}</span>
                </div>
                <div class="profile-detail-row">
                    <div class="profile-detail-item">
                        <i class="bi bi-credit-card-2-front"></i>
                        <span>NIP: <strong>{{ $user->nip ?: '—' }}</strong></span>
                    </div>
                    <div class="profile-detail-item">
                        <i class="bi bi-envelope"></i>
                        <span>{{ $user->email }}</span>
                    </div>
                    <div class="profile-detail-item">
                        <i class="bi bi-calendar-check"></i>
                        <span>Bergabung {{ $user->created_at->translatedFormat('M Y') }}</span>
                    </div>
                </div>
            </div>

            <div class="flex-shrink-0">
                <a href="mailto:{{ $user->email }}"
                   class="btn btn-primary fw-bold px-4"
                   style="border-radius: 12px; font-size: 13px; box-shadow: 0 4px 14px rgba(67,97,238,0.3); background: linear-gradient(135deg,#4361ee,#6366f1); border: none;">
                    <i class="bi bi-envelope-fill me-2"></i>Kirim Email
                </a>
            </div>
        </div>
    </div>

    {{-- Stat Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-12 col-sm-4 animate-in" style="animation-delay:0.05s">
            <div class="pg-stat blue">
                <div class="pg-stat-icon"><i class="bi bi-envelope-paper-fill"></i></div>
                <div>
                    <div class="pg-stat-label">Total Pengajuan</div>
                    <div class="pg-stat-value">{{ $stats['total'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-4 animate-in" style="animation-delay:0.1s">
            <div class="pg-stat green">
                <div class="pg-stat-icon"><i class="bi bi-check-circle-fill"></i></div>
                <div>
                    <div class="pg-stat-label">Surat Selesai</div>
                    <div class="pg-stat-value">{{ $stats['selesai'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-4 animate-in" style="animation-delay:0.15s">
            <div class="pg-stat amber">
                <div class="pg-stat-icon"><i class="bi bi-lightning-fill"></i></div>
                <div>
                    <div class="pg-stat-label">Rata-rata SLA</div>
                    <div class="pg-stat-value">{{ $stats['sla_avg'] }}%</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Charts --}}
    <div class="row g-4">
        <div class="col-12 col-lg-8 animate-in" style="animation-delay:0.2s">
            <div class="pg-chart-card">
                <div class="pg-chart-head">
                    <h6>Tren Aktivitas &amp; Performa</h6>
                    <small>6 bulan terakhir</small>
                </div>
                <div class="pg-chart-body">
                    <canvas id="pegawaiMixedChart" style="width:100%; height:270px;"></canvas>
                </div>
            </div>
        </div>
        <div class="col-12 col-lg-4 animate-in" style="animation-delay:0.25s">
            <div class="pg-chart-card">
                <div class="pg-chart-head">
                    <h6>Aktivitas Mingguan</h6>
                    <small>7 hari terakhir</small>
                </div>
                <div class="pg-chart-body">
                    <canvas id="pegawaiWeeklyChart" style="width:100%; height:270px;"></canvas>
                </div>
            </div>
        </div>
    </div>

</div>
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.2/dist/chart.umd.min.js"></script>
<script>
function initPegawaiCharts() {
    // Destroy existing chart instances to avoid duplicate canvas errors on Turbo re-visit
    ['pegawaiMixedChart', 'pegawaiWeeklyChart'].forEach(function(id) {
        var existing = Chart.getChart(id);
        if (existing) existing.destroy();
    });

    // Mixed Chart
    new Chart(document.getElementById('pegawaiMixedChart'), {
        type: 'bar',
        data: {
            labels: {!! json_encode($chartData['months']) !!},
            datasets: [
                {
                    label: 'Pengajuan',
                    data: {!! json_encode($chartData['submissions']) !!},
                    backgroundColor: 'rgba(67,97,238,0.75)',
                    borderRadius: 8,
                    borderSkipped: false,
                    yAxisID: 'y'
                },
                {
                    label: 'SLA (%)',
                    data: {!! json_encode($chartData['sla_rate']) !!},
                    type: 'line',
                    borderColor: '#10b981',
                    backgroundColor: 'rgba(16,185,129,0.06)',
                    borderWidth: 2.5,
                    pointBackgroundColor: '#10b981',
                    pointBorderColor: 'white',
                    pointBorderWidth: 2,
                    pointRadius: 5,
                    tension: 0.4,
                    fill: true,
                    yAxisID: 'y1'
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y:  { beginAtZero: true, position: 'left',  ticks: { stepSize: 1 }, grid: { display: false } },
                y1: { beginAtZero: true, max: 100, position: 'right', grid: { color: 'rgba(0,0,0,0.04)' }, ticks: { callback: v => v + '%' } }
            },
            plugins: {
                legend: { position: 'bottom', labels: { usePointStyle: true, padding: 16, font: { size: 11 } } },
                tooltip: { mode: 'index', intersect: false }
            }
        }
    });

    // Weekly Chart
    const ctxW = document.getElementById('pegawaiWeeklyChart').getContext('2d');
    const grad = ctxW.createLinearGradient(0, 0, 0, 250);
    grad.addColorStop(0, 'rgba(99,102,241,0.22)');
    grad.addColorStop(1, 'rgba(99,102,241,0)');

    new Chart(ctxW, {
        type: 'line',
        data: {
            labels: {!! json_encode($chartData['weekly']['labels']) !!},
            datasets: [{
                label: 'Aktivitas',
                data: {!! json_encode($chartData['weekly']['values']) !!},
                fill: true,
                backgroundColor: grad,
                borderColor: '#6366f1',
                borderWidth: 2.5,
                tension: 0.4,
                pointRadius: 4,
                pointBackgroundColor: '#6366f1',
                pointBorderColor: 'white',
                pointBorderWidth: 2,
                pointHitRadius: 12
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                x: { grid: { display: false } },
                y: { beginAtZero: true, ticks: { stepSize: 1 }, grid: { color: 'rgba(0,0,0,0.04)' } }
            },
            plugins: { legend: { display: false } }
        }
    });
}

// Support both standard page load and Hotwire Turbo navigation
document.addEventListener('turbo:load', initPegawaiCharts);
document.addEventListener('DOMContentLoaded', function() {
    // Only run if Turbo is not active (fallback for non-Turbo environments)
    if (typeof Turbo === 'undefined') initPegawaiCharts();
});
</script>
@endpush
@endsection
