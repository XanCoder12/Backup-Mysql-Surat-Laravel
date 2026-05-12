@extends('layouts.admin')
@section('title', 'Monitoring SLA Per Divisi')

@section('content')
<div class="dashboard-header flex flex-col lg:flex-row lg:items-center justify-between gap-6">
    <div>
        <div class="flex items-center gap-3">
            <h1 class="text-3xl font-black text-slate-900 dark:text-white tracking-tight">Monitoring SLA</h1>
            <div class="flex items-center gap-2 px-3 py-1 bg-blue-500/10 text-blue-500 border border-blue-500/20 rounded-full text-[10px] font-black tracking-widest">
                <i class="bi bi-graph-up-arrow"></i> ANALYTICS
            </div>
        </div>
        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1 font-semibold opacity-80">Tren performa waktu pemrosesan per divisi (rata-rata jam).</p>
    </div>
</div>

<div class="space-y-6 mt-8">
    {{-- Main Chart Card --}}
    <div class="card shadow-sm border-slate-200 dark:border-slate-800 !p-6">
        <div class="flex items-center justify-between mb-8">
            <div>
                <h2 class="text-lg font-black text-slate-800 dark:text-white">Trend Kecepatan Respon</h2>
                <p class="text-xs text-slate-500 font-semibold">Rata-rata waktu penyelesaian tugas dalam satuan jam</p>
            </div>
            <div class="flex items-center gap-4">
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full" style="background-color: #4361ee;"></span>
                    <span class="text-[11px] font-bold text-slate-500">Arsiparis</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full" style="background-color: #10b981;"></span>
                    <span class="text-[11px] font-bold text-slate-500">Kasubbag TU</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full" style="background-color: #f59e0b;"></span>
                    <span class="text-[11px] font-bold text-slate-500">Kepala Balai</span>
                </div>
            </div>
        </div>

        <div class="relative w-full" style="height: 450px;">
            <canvas id="slaChart"></canvas>
        </div>
    </div>

    {{-- Info Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="card border-slate-200 dark:border-slate-800 hover:border-blue-500/30 transition-colors">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-2xl bg-blue-500/10 text-blue-500 flex items-center justify-center text-2xl">
                    <i class="bi bi-person-check"></i>
                </div>
                <div>
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Arsiparis</p>
                    <p class="text-xl font-black text-slate-800 dark:text-white">{{ end($chartData['Arsiparis']) }} <span class="text-xs font-bold text-slate-500">jam</span></p>
                    <p class="text-[10px] font-bold text-emerald-500 mt-0.5">Bulan Terakhir</p>
                </div>
            </div>
        </div>
        <div class="card border-slate-200 dark:border-slate-800 hover:border-emerald-500/30 transition-colors">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-2xl bg-emerald-500/10 text-emerald-500 flex items-center justify-center text-2xl">
                    <i class="bi bi-briefcase"></i>
                </div>
                <div>
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Kasubbag TU</p>
                    <p class="text-xl font-black text-slate-800 dark:text-white">{{ end($chartData['Kasubbag TU']) }} <span class="text-xs font-bold text-slate-500">jam</span></p>
                    <p class="text-[10px] font-bold text-emerald-500 mt-0.5">Bulan Terakhir</p>
                </div>
            </div>
        </div>
        <div class="card border-slate-200 dark:border-slate-800 hover:border-amber-500/30 transition-colors">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-2xl bg-amber-500/10 text-amber-500 flex items-center justify-center text-2xl">
                    <i class="bi bi-award"></i>
                </div>
                <div>
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Kepala Balai</p>
                    <p class="text-xl font-black text-slate-800 dark:text-white">{{ end($chartData['Kepala Balai']) }} <span class="text-xs font-bold text-slate-500">jam</span></p>
                    <p class="text-[10px] font-bold text-emerald-500 mt-0.5">Bulan Terakhir</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('slaChart').getContext('2d');
        const isDark = document.documentElement.classList.contains('dark');
        const textColor = isDark ? '#94a3b8' : '#64748b';
        const gridColor = isDark ? 'rgba(255,255,255,0.05)' : 'rgba(0,0,0,0.05)';

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: @json($monthLabels),
                datasets: [
                    {
                        label: 'Arsiparis',
                        data: @json($chartData['Arsiparis']),
                        borderColor: '#4361ee',
                        backgroundColor: 'rgba(67, 97, 238, 0.1)',
                        borderWidth: 4,
                        tension: 0.4,
                        fill: true,
                        pointRadius: 6,
                        pointBackgroundColor: '#4361ee',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 3,
                    },
                    {
                        label: 'Kasubbag TU',
                        data: @json($chartData['Kasubbag TU']),
                        borderColor: '#10b981',
                        backgroundColor: 'rgba(16, 185, 129, 0.1)',
                        borderWidth: 4,
                        tension: 0.4,
                        fill: true,
                        pointRadius: 6,
                        pointBackgroundColor: '#10b981',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 3,
                    },
                    {
                        label: 'Kepala Balai',
                        data: @json($chartData['Kepala Balai']),
                        borderColor: '#f59e0b',
                        backgroundColor: 'rgba(245, 158, 11, 0.1)',
                        borderWidth: 4,
                        tension: 0.4,
                        fill: true,
                        pointRadius: 6,
                        pointBackgroundColor: '#f59e0b',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 3,
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    intersect: false,
                    mode: 'index',
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: isDark ? '#1e293b' : '#ffffff',
                        titleColor: isDark ? '#f1f5f9' : '#1e293b',
                        bodyColor: isDark ? '#cbd5e1' : '#475569',
                        borderColor: gridColor,
                        borderWidth: 1,
                        padding: 12,
                        boxPadding: 6,
                        usePointStyle: true,
                        callbacks: {
                            label: function(context) {
                                return context.dataset.label + ': ' + context.parsed.y + ' jam';
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: gridColor, drawBorder: false },
                        ticks: { 
                            color: textColor, 
                            font: { size: 11, weight: '600' },
                            padding: 10,
                            callback: function(value) { return value + 'j'; }
                        },
                        title: {
                            display: true,
                            text: 'Rata-rata Waktu (Jam)',
                            color: textColor,
                            font: { size: 12, weight: '700' }
                        }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { 
                            color: textColor, 
                            font: { size: 11, weight: '600' },
                            padding: 10
                        }
                    }
                }
            }
        });
    });
</script>
@endpush
