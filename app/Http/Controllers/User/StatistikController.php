<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Surat;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class StatistikController extends Controller
{
    public function index(Request $request)
    {
        $userId = auth()->id();
        $tahun = $request->input('tahun', date('Y'));

        // 1. Get statistics for status cards (filtered by year)
        $baseQuery = Surat::where('user_id', $userId)->whereYear('created_at', $tahun);
        
        $totalSurat = (clone $baseQuery)->count();
        $totalDisetujui = (clone $baseQuery)->where('status', 'selesai')->count();
        $totalDitolak = (clone $baseQuery)->where('status', 'ditolak')->count();
        $totalProses = (clone $baseQuery)->whereIn('status', ['proses', 'revisi'])->count();

        // 2. Data for Chart: Group by Month (All months in the selected year)
        $monthlyStats = [];
        $labels = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        for ($m = 1; $m <= 12; $m++) {
            $count = Surat::where('user_id', $userId)
                ->whereYear('created_at', $tahun)
                ->whereMonth('created_at', $m)
                ->count();
            $monthlyStats[] = $count;
        }

        // 3. Detailed Monthly Data for Table with Status Breakdown and Sparkline
        $monthlyDetails = [];
        for ($m = 1; $m <= 12; $m++) {
            $monthlyQuery = Surat::where('user_id', $userId)
                ->whereYear('created_at', $tahun)
                ->whereMonth('created_at', $m);
            
            $mTotal = (clone $monthlyQuery)->count();
            $mDisetujui = (clone $monthlyQuery)->where('status', 'selesai')->count();
            $mProses = (clone $monthlyQuery)->whereIn('status', ['proses', 'revisi'])->count();
            $mDitolak = (clone $monthlyQuery)->where('status', 'ditolak')->count();

            // Daily activity in that month for inline sparkline
            $dailyData = Surat::where('user_id', $userId)
                ->whereYear('created_at', $tahun)
                ->whereMonth('created_at', $m)
                ->selectRaw('EXTRACT(DAY FROM created_at)::int as day, count(*) as count')
                ->groupByRaw('EXTRACT(DAY FROM created_at)')
                ->pluck('count', 'day')
                ->toArray();

            $daysInMonth = Carbon::create($tahun, $m, 1)->daysInMonth;
            $sparklineData = [];
            for ($d = 1; $d <= $daysInMonth; $d++) {
                $sparklineData[] = $dailyData[$d] ?? 0;
            }

            $monthlyDetails[] = [
                'name' => $labels[$m - 1],
                'total' => $mTotal,
                'disetujui' => $mDisetujui,
                'proses' => $mProses,
                'ditolak' => $mDitolak,
                'sparkline' => $sparklineData,
            ];
        }

        // 4. Data for Chart: Group by Status
        $statusLabels = ['Disetujui', 'Ditolak', 'Diproses'];
        $statusStats = [$totalDisetujui, $totalDitolak, $totalProses];

        // 5. Data for Chart: Distribusi Jenis Surat & Detailed Jenis Table
        $jenisSurat = (clone $baseQuery)
                          ->select('jenis', DB::raw('count(*) as total'))
                          ->groupBy('jenis')
                          ->pluck('total', 'jenis')
                          ->toArray();

        $jenisDetails = [];
        foreach (Surat::JENIS_LABEL as $key => $label) {
            $jenisQuery = Surat::where('user_id', $userId)
                ->whereYear('created_at', $tahun)
                ->where('jenis', $key);

            $jTotal = (clone $jenisQuery)->count();
            $jDisetujui = (clone $jenisQuery)->where('status', 'selesai')->count();
            $jProses = (clone $jenisQuery)->whereIn('status', ['proses', 'revisi'])->count();
            $jDitolak = (clone $jenisQuery)->where('status', 'ditolak')->count();

            // Monthly trend for this specific jenis
            $jenisMonthlyTrend = [];
            for ($m = 1; $m <= 12; $m++) {
                $jenisMonthlyTrend[] = (clone $jenisQuery)->whereMonth('created_at', $m)->count();
            }

            if ($jTotal > 0) {
                $jenisDetails[] = [
                    'label' => $label,
                    'total' => $jTotal,
                    'disetujui' => $jDisetujui,
                    'proses' => $jProses,
                    'ditolak' => $jDitolak,
                    'trend' => $jenisMonthlyTrend,
                ];
            }
        }

        // Sort jenisDetails descending by total
        usort($jenisDetails, function($a, $b) {
            return $b['total'] <=> $a['total'];
        });

        // 6. Data Heatmap Kontribusi (GitHub Stats)
        $heatmapYear = (int) $request->input('heatmap_year', date('Y'));
        $heatmapData = auth()->user()->getActivityHeatmapData($heatmapYear);

        return view('user.statistik.index', [
            'title' => 'Statistik & Chart',
            'tahun' => $tahun,
            'totalSurat' => $totalSurat,
            'totalDisetujui' => $totalDisetujui,
            'totalDitolak' => $totalDitolak,
            'totalProses' => $totalProses,
            'chartLabels' => $labels,
            'chartData' => $monthlyStats,
            'statusLabels' => $statusLabels,
            'statusData' => $statusStats,
            'jenisSurat' => $jenisSurat,
            'heatmapData' => $heatmapData,
            'heatmapYear' => $heatmapYear,
            'monthlyDetails' => $monthlyDetails,
            'jenisDetails' => $jenisDetails,
        ]);
    }
}
