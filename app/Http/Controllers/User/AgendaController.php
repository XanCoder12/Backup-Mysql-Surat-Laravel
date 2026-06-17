<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Surat;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AgendaController extends Controller
{
    /**
     * Tampilkan halaman Agenda / Kalender.
     * Menampilkan kalender bulanan + daftar agenda surat.
     */
    public function index(Request $request)
    {
        $userId = Auth::id();

        // Bulan & tahun yang dipilih (default: sekarang)
        $bulan = (int) $request->input('bulan', now()->month);
        $tahun = (int) $request->input('tahun', now()->year);

        // Filter jenis agenda
        $filterJenis = $request->input('jenis', 'semua'); // semua, pengajuan, deadline, selesai

        $startOfMonth = Carbon::create($tahun, $bulan, 1)->startOfMonth();
        $endOfMonth = Carbon::create($tahun, $bulan, 1)->endOfMonth();

        // ─── Ambil semua surat user di bulan ini ───
        $surats = Surat::where('user_id', $userId)
            ->where(function ($q) use ($startOfMonth, $endOfMonth) {
                $q->whereBetween('created_at', [$startOfMonth, $endOfMonth])
                  ->orWhereBetween('deadline_sla', [$startOfMonth, $endOfMonth])
                  ->orWhereBetween('disetujui_pada', [$startOfMonth, $endOfMonth])
                  ->orWhereBetween('tanggal_surat', [$startOfMonth, $endOfMonth]);
            })
            ->orderBy('created_at', 'desc')
            ->get();

        // ─── Kelompokkan surat berdasarkan tanggal + jenis event ───
        $events = [];

        foreach ($surats as $surat) {
            // 1. Event: Pengajuan (created_at)
            if ($surat->created_at && $surat->created_at->between($startOfMonth, $endOfMonth)) {
                $dateKey = $surat->created_at->format('Y-m-d');
                $events[$dateKey][] = [
                    'type' => 'pengajuan',
                    'label' => 'Pengajuan',
                    'color' => '#3b82f6',
                    'icon' => 'bi-send',
                    'surat' => $surat,
                    'time' => $surat->created_at->format('H:i'),
                ];
            }

            // 2. Event: Deadline SLA
            if ($surat->deadline_sla && $surat->deadline_sla->between($startOfMonth, $endOfMonth)) {
                $dateKey = $surat->deadline_sla->format('Y-m-d');
                $isOverdue = $surat->status !== 'selesai' && now()->gt($surat->deadline_sla);
                $events[$dateKey][] = [
                    'type' => 'deadline',
                    'label' => $isOverdue ? 'Deadline Terlewat' : 'Deadline SLA',
                    'color' => $isOverdue ? '#ef4444' : '#f59e0b',
                    'icon' => $isOverdue ? 'bi-exclamation-triangle-fill' : 'bi-clock-history',
                    'surat' => $surat,
                    'time' => $surat->deadline_sla->format('H:i'),
                ];
            }

            // 3. Event: Selesai / Disetujui
            if ($surat->disetujui_pada && $surat->disetujui_pada->between($startOfMonth, $endOfMonth)) {
                $dateKey = $surat->disetujui_pada->format('Y-m-d');
                $events[$dateKey][] = [
                    'type' => 'selesai',
                    'label' => 'Selesai',
                    'color' => '#22c55e',
                    'icon' => 'bi-check-circle-fill',
                    'surat' => $surat,
                    'time' => $surat->disetujui_pada->format('H:i'),
                ];
            }

            // 4. Event: Tanggal Surat
            if ($surat->tanggal_surat && $surat->tanggal_surat->between($startOfMonth, $endOfMonth)) {
                $dateKey = $surat->tanggal_surat->format('Y-m-d');
                $events[$dateKey][] = [
                    'type' => 'tanggal_surat',
                    'label' => 'Tanggal Surat',
                    'color' => '#a855f7',
                    'icon' => 'bi-calendar-event',
                    'surat' => $surat,
                    'time' => '',
                ];
            }
        }

        // Urutkan event per tanggal
        ksort($events);

        // Filter berdasarkan jenis jika dipilih
        if ($filterJenis !== 'semua') {
            foreach ($events as $dateKey => &$dayEvents) {
                $dayEvents = array_filter($dayEvents, fn($e) => $e['type'] === $filterJenis);
            }
            // Hapus tanggal kosong
            $events = array_filter($events, fn($dayEvents) => count($dayEvents) > 0);
        }

        // ─── Statistik ringkas bulan ini ───
        $stats = [
            'total_pengajuan' => collect($events)->flatten(1)->where('type', 'pengajuan')->count(),
            'total_deadline' => collect($events)->flatten(1)->where('type', 'deadline')->count(),
            'total_selesai' => collect($events)->flatten(1)->where('type', 'selesai')->count(),
            'total_surat' => $surats->count(),
        ];

        // ─── Agenda hari ini ───
        $todayKey = now()->format('Y-m-d');
        $todayEvents = $events[$todayKey] ?? [];

        // ─── Surat mendatang (upcoming deadlines) ───
        $upcomingSurat = Surat::where('user_id', $userId)
            ->whereIn('status', ['proses', 'revisi', 'revisi_admin'])
            ->whereNotNull('deadline_sla')
            ->where('deadline_sla', '>=', now())
            ->orderBy('deadline_sla', 'asc')
            ->limit(5)
            ->get();

        // ─── Build calendar grid data ───
        $calendarDays = $this->buildCalendarGrid($tahun, $bulan, $events);

        // ─── Daftar bulan untuk dropdown ───
        $namaBulan = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];

        return view('user.agenda.index', [
            'title' => 'Agenda / Kalender',
            'bulan' => $bulan,
            'tahun' => $tahun,
            'namaBulan' => $namaBulan,
            'calendarDays' => $calendarDays,
            'events' => $events,
            'stats' => $stats,
            'todayEvents' => $todayEvents,
            'upcomingSurat' => $upcomingSurat,
            'filterJenis' => $filterJenis,
        ]);
    }

    /**
     * AJAX: Ambil event untuk bulan tertentu (untuk navigasi kalender tanpa reload)
     */
    public function events(Request $request)
    {
        $userId = Auth::id();
        $bulan = (int) $request->input('bulan', now()->month);
        $tahun = (int) $request->input('tahun', now()->year);
        $filterJenis = $request->input('jenis', 'semua');

        $startOfMonth = Carbon::create($tahun, $bulan, 1)->startOfMonth();
        $endOfMonth = Carbon::create($tahun, $bulan, 1)->endOfMonth();

        $surats = Surat::where('user_id', $userId)
            ->where(function ($q) use ($startOfMonth, $endOfMonth) {
                $q->whereBetween('created_at', [$startOfMonth, $endOfMonth])
                  ->orWhereBetween('deadline_sla', [$startOfMonth, $endOfMonth])
                  ->orWhereBetween('disetujui_pada', [$startOfMonth, $endOfMonth])
                  ->orWhereBetween('tanggal_surat', [$startOfMonth, $endOfMonth]);
            })
            ->orderBy('created_at', 'desc')
            ->get();

        $events = [];
        foreach ($surats as $surat) {
            if ($surat->created_at && $surat->created_at->between($startOfMonth, $endOfMonth)) {
                $dateKey = $surat->created_at->format('Y-m-d');
                $events[$dateKey][] = [
                    'type' => 'pengajuan',
                    'label' => 'Pengajuan',
                    'color' => '#3b82f6',
                    'icon' => 'bi-send',
                    'surat_uuid' => $surat->uuid,
                    'surat_judul' => $surat->judul,
                    'surat_jenis' => $surat->jenis_label,
                    'surat_status' => $surat->status,
                    'surat_url' => route('user.surat.show', $surat),
                    'time' => $surat->created_at->format('H:i'),
                ];
            }

            if ($surat->deadline_sla && $surat->deadline_sla->between($startOfMonth, $endOfMonth)) {
                $dateKey = $surat->deadline_sla->format('Y-m-d');
                $isOverdue = $surat->status !== 'selesai' && now()->gt($surat->deadline_sla);
                $events[$dateKey][] = [
                    'type' => 'deadline',
                    'label' => $isOverdue ? 'Deadline Terlewat' : 'Deadline SLA',
                    'color' => $isOverdue ? '#ef4444' : '#f59e0b',
                    'icon' => $isOverdue ? 'bi-exclamation-triangle-fill' : 'bi-clock-history',
                    'surat_uuid' => $surat->uuid,
                    'surat_judul' => $surat->judul,
                    'surat_jenis' => $surat->jenis_label,
                    'surat_status' => $surat->status,
                    'surat_url' => route('user.surat.show', $surat),
                    'time' => $surat->deadline_sla->format('H:i'),
                ];
            }

            if ($surat->disetujui_pada && $surat->disetujui_pada->between($startOfMonth, $endOfMonth)) {
                $dateKey = $surat->disetujui_pada->format('Y-m-d');
                $events[$dateKey][] = [
                    'type' => 'selesai',
                    'label' => 'Selesai',
                    'color' => '#22c55e',
                    'icon' => 'bi-check-circle-fill',
                    'surat_uuid' => $surat->uuid,
                    'surat_judul' => $surat->judul,
                    'surat_jenis' => $surat->jenis_label,
                    'surat_status' => $surat->status,
                    'surat_url' => route('user.surat.show', $surat),
                    'time' => $surat->disetujui_pada->format('H:i'),
                ];
            }

            if ($surat->tanggal_surat && $surat->tanggal_surat->between($startOfMonth, $endOfMonth)) {
                $dateKey = $surat->tanggal_surat->format('Y-m-d');
                $events[$dateKey][] = [
                    'type' => 'tanggal_surat',
                    'label' => 'Tanggal Surat',
                    'color' => '#a855f7',
                    'icon' => 'bi-calendar-event',
                    'surat_uuid' => $surat->uuid,
                    'surat_judul' => $surat->judul,
                    'surat_jenis' => $surat->jenis_label,
                    'surat_status' => $surat->status,
                    'surat_url' => route('user.surat.show', $surat),
                    'time' => '',
                ];
            }
        }

        // Filter
        if ($filterJenis !== 'semua') {
            foreach ($events as $dateKey => &$dayEvents) {
                $dayEvents = array_values(array_filter($dayEvents, fn($e) => $e['type'] === $filterJenis));
            }
            $events = array_filter($events, fn($dayEvents) => count($dayEvents) > 0);
        }

        ksort($events);

        $calendarDays = $this->buildCalendarGridAjax($tahun, $bulan, $events);

        return response()->json([
            'events' => $events,
            'calendarDays' => $calendarDays,
            'bulan' => $bulan,
            'tahun' => $tahun,
        ]);
    }

    /**
     * Build data array untuk grid kalender (server-rendered)
     */
    private function buildCalendarGrid(int $tahun, int $bulan, array $events): array
    {
        $firstDay = Carbon::create($tahun, $bulan, 1);
        $daysInMonth = $firstDay->daysInMonth;
        // Hari pertama: 0=Minggu, 1=Senin, ... 6=Sabtu → Geser ke Senin-first: (dayOfWeek + 6) % 7
        $startDow = ($firstDay->dayOfWeek + 6) % 7; // 0=Sen, 6=Min

        $today = now()->format('Y-m-d');
        $days = [];

        // Empty cells sebelum hari ke-1
        for ($i = 0; $i < $startDow; $i++) {
            $days[] = ['day' => null, 'dateKey' => null, 'isToday' => false, 'events' => []];
        }

        // Isi hari
        for ($d = 1; $d <= $daysInMonth; $d++) {
            $dateKey = sprintf('%04d-%02d-%02d', $tahun, $bulan, $d);
            $days[] = [
                'day' => $d,
                'dateKey' => $dateKey,
                'isToday' => $dateKey === $today,
                'events' => $events[$dateKey] ?? [],
            ];
        }

        return $days;
    }

    /**
     * Build data array untuk grid kalender (AJAX response)
     */
    private function buildCalendarGridAjax(int $tahun, int $bulan, array $events): array
    {
        $firstDay = Carbon::create($tahun, $bulan, 1);
        $daysInMonth = $firstDay->daysInMonth;
        $startDow = ($firstDay->dayOfWeek + 6) % 7;

        $today = now()->format('Y-m-d');
        $days = [];

        for ($i = 0; $i < $startDow; $i++) {
            $days[] = ['day' => null, 'dateKey' => null, 'isToday' => false, 'events' => []];
        }

        for ($d = 1; $d <= $daysInMonth; $d++) {
            $dateKey = sprintf('%04d-%02d-%02d', $tahun, $bulan, $d);
            $days[] = [
                'day' => $d,
                'dateKey' => $dateKey,
                'isToday' => $dateKey === $today,
                'events' => $events[$dateKey] ?? [],
            ];
        }

        return $days;
    }
}
