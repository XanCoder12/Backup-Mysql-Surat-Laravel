<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Surat;
use App\Models\SuratTahapan;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    public function sla(Request $request)
    {
        $months = [];
        for ($i = 5; $i >= 0; $i--) {
            $months[] = Carbon::now()->subMonths($i)->format('Y-m');
        }

        $divisions = [
            'Arsiparis' => [2, 5, 6, 7, 8, 9],
            'Kasubbag TU' => [3],
            'Kepala Balai' => [4],
        ];

        $chartData = [];
        foreach ($divisions as $name => $stages) {
            $dataPoints = [];
            foreach ($months as $month) {
                $start = Carbon::parse($month)->startOfMonth();
                $end = Carbon::parse($month)->endOfMonth();

                // Hitung rata-rata durasi pemrosesan untuk tahap-tahap ini di bulan ini
                // Durasi = selesai_pada - created_at (untuk tahap pertama)
                // Durasi = selesai_pada - selesai_pada tahap sebelumnya
                
                $averageTime = DB::table('surat_tahapans as current')
                    ->join('surat_tahapans as previous', function($join) {
                        $join->on('current.surat_id', '=', 'previous.surat_id')
                             ->on(DB::raw('current.tahap - 1'), '=', 'previous.tahap');
                    })
                    ->whereIn('current.tahap', $stages)
                    ->where('current.status', 'selesai')
                    ->whereBetween('current.selesai_pada', [$start, $end])
                    ->select(DB::raw('AVG(EXTRACT(EPOCH FROM (current.selesai_pada - previous.selesai_pada)) / 3600) as avg_hours'))
                    ->first();

                $dataPoints[] = round($averageTime->avg_hours ?? 0, 1);
            }
            $chartData[$name] = $dataPoints;
        }

        $monthLabels = array_map(function($m) {
            return Carbon::parse($m)->translatedFormat('M Y');
        }, $months);

        return view('admin.analytics.sla', compact('chartData', 'monthLabels'));
    }
}
