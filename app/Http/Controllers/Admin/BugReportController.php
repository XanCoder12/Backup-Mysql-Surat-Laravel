<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Aspirasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BugReportController extends Controller
{
    public function index(Request $request)
    {
        $query = Aspirasi::with('user')->latest();

        if ($request->filled('tahun')) {
            $query->whereYear('created_at', $request->tahun);
        }

        $reports = $query->paginate(15)->withQueryString();
        return view('admin.bug-report.index', compact('reports'), ['title' => 'Bantuan IT Support']);
    }

    public function store(Request $request)
    {
        $request->validate([
            'subjek' => 'required|string|max:255',
            'kategori' => 'required|in:bug,error,fitur,lainnya',
            'isi' => 'required|string',
        ]);

        Aspirasi::create([
            'user_id' => Auth::id(),
            'judul' => $request->subjek,
            'isi' => $request->isi,
            'kategori' => $request->kategori,
            'status' => 'pending',
            'tujuan' => 'it_support',
        ]);

        return back()->with('success', 'Laporan berhasil dikirim ke IT Support!');
    }

    public function destroy(Aspirasi $aspirasi)
    {
        $aspirasi->delete();
        return back()->with('success', 'Laporan berhasil dihapus.');
    }
}