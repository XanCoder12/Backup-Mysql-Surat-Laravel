@extends('layouts.admin')
@section('title', 'Antrian Surat')

@section('content')

{{-- FILTER BAR --}}
<div class="card" style="margin-bottom:16px;">
    <form method="GET" action="{{ route('admin.surat.index') }}"
          style="display:flex; gap:10px; align-items:flex-end; flex-wrap:wrap;">

        <div style="flex:2; min-width:180px;">
            <label style="font-size:11px; color:#6b7280; display:block; margin-bottom:4px;">Cari Judul</label>
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Cari judul surat..."
                   style="width:100%; padding:7px 10px; border:1px solid #e5e7eb; border-radius:7px; font-size:13px;">
        </div>

        <div style="flex:1; min-width:140px;">
            <label style="font-size:11px; color:#6b7280; display:block; margin-bottom:4px;">Jenis Surat</label>
            <select name="jenis" style="width:100%; padding:7px 10px; border:1px solid #e5e7eb; border-radius:7px; font-size:13px;">
                <option value="">Semua Jenis</option>
                @foreach(\App\Models\Surat::JENIS_LABEL as $val => $label)
                    <option value="{{ $val }}" {{ request('jenis') === $val ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
        </div>

        <div style="flex:1; min-width:120px;">
            <label style="font-size:11px; color:#6b7280; display:block; margin-bottom:4px;">Status</label>
            <select name="status" style="width:100%; padding:7px 10px; border:1px solid #e5e7eb; border-radius:7px; font-size:13px;">
                <option value="">Semua</option>
                <option value="proses"  {{ request('status') === 'proses'  ? 'selected' : '' }}>Proses</option>
                <option value="selesai" {{ request('status') === 'selesai' ? 'selected' : '' }}>Selesai</option>
                <option value="ditolak" {{ request('status') === 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                <option value="revisi"  {{ request('status') === 'revisi'  ? 'selected' : '' }}>Revisi</option>
            </select>
        </div>

        <div style="flex:1; min-width:120px;">
            <label style="font-size:11px; color:#6b7280; display:block; margin-bottom:4px;">Tahap</label>
            <select name="tahap" style="width:100%; padding:7px 10px; border:1px solid #e5e7eb; border-radius:7px; font-size:13px;">
                <option value="">Semua Tahap</option>
                @foreach(\App\Models\Surat::NAMA_TAHAP as $no => $nama)
                    <option value="{{ $no }}" {{ request('tahap') == $no ? 'selected' : '' }}>{{ $no }}. {{ $nama }}</option>
                @endforeach
            </select>
        </div>

        <div style="display:flex; gap:6px;">
            <button type="submit" class="btn btn-primary">🔍 Filter</button>
            <a href="{{ route('admin.surat.index') }}" class="btn">Reset</a>
        </div>
    </form>
</div>

{{-- TABEL --}}
<div class="card">
    <div class="section-header">
        <div>
            <h2>📬 Semua Antrian Surat</h2>
            <small>Total {{ $surats->total() }} surat ditemukan</small>
        </div>
    </div>

    @if($surats->isEmpty())
        <div style="text-align:center; padding:40px; color:#9ca3af; font-size:13px;">
            📭 Tidak ada surat yang ditemukan
        </div>
    @else
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Judul Surat</th>
                        <th>Pengusul</th>
                        <th>Jenis</th>
                        <th>Sifat</th>
                        <th>Tahap</th>
                        <th>Status</th>
                        <th>SLA</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($surats as $surat)
                    <tr>
                        <td style="color:#9ca3af; font-size:12px;">{{ $loop->iteration }}</td>
                        <td>
                            <div style="font-weight:500; color:#111827; max-width:200px;">
                                {{ \Illuminate\Support\Str::limit($surat->judul, 40) }}
                            </div>
                            <div style="font-size:11px; color:#9ca3af; margin-top:2px;">
                                {{ $surat->created_at?->format('d M Y') ?? '—' }}
                            </div>
                        </td>
                        <td style="font-size:13px;">{{ $surat->user?->name ?? '—' }}</td>
                        <td><span class="badge badge-purple">{{ $surat->jenis_label }}</span></td>
                        <td>
                            @if($surat->sifat === 'segera')
                                <span class="badge badge-red">Segera</span>
                            @elseif($surat->sifat === 'rahasia')
                                <span class="badge badge-amber">Rahasia</span>
                            @else
                                <span class="badge badge-gray">Biasa</span>
                            @endif
                        </td>
                        <td>
                            <div style="font-size:12px; font-weight:500; color:#1d4ed8;">
                                Tahap {{ $surat->tahap_sekarang }}/10
                            </div>
                            <div style="font-size:11px; color:#6b7280;">{{ $surat->nama_tahap }}</div>
                            <div class="progress-bar" style="margin-top:4px; width:90px;">
                                <div
                                    class="progress-fill"
                                    @style(['width' => min(100, max(0, (int) $surat->proses_persen)).'%'])
                                ></div>
                            </div>
                        </td>
                        <td>
                            @if($surat->status === 'selesai')
                                <span class="badge badge-green">Selesai</span>
                            @elseif($surat->status === 'ditolak')
                                <span class="badge badge-red">Ditolak</span>
                            @elseif($surat->status === 'revisi')
                                <span class="badge badge-amber">📝 Revisi ({{ $surat->revisi_count }}x)</span>
                            @else
                                <span class="badge badge-amber">Proses</span>
                            @endif
                        </td>
                        <td>
                            @if($surat->status === 'selesai')
                                <span class="badge badge-green">✓ OK</span>
                            @elseif($surat->sla_status === 'terlambat')
                                <span class="badge badge-red">⚠ {{ $surat->sisa_jam }}</span>
                            @else
                                <span class="badge badge-blue">⏱ {{ $surat->sisa_jam }}</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('admin.surat.show', $surat) }}"
                               class="btn btn-sm btn-primary">Detail →</a>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        {{-- PAGINATION --}}
        <div style="margin-top:16px;">
            {{ $surats->links() }}
        </div>
    @endif
</div>

@endsection