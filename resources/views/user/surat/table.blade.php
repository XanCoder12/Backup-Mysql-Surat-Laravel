@extends('layouts.user')
@section('title', 'Tabel Surat Saya')

@section('content')

<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2 animate-in">
    <div>
        <h5 class="fw-bold mb-0" style="color:#1e3a5f;">📊 Tabel Surat Saya</h5>
        <small class="text-muted">Data surat dalam format tabel detail</small>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('user.surat.index') }}" class="btn btn-light d-flex align-items-center gap-2"
           style="border-radius:9px;font-size:13px;font-weight:600;">
            <i class="bi bi-grid-fill"></i> Tampilan Card
        </a>
        <a href="{{ route('user.surat.create') }}" class="btn btn-primary d-flex align-items-center gap-2"
           style="background:#1e3a5f;border-color:#1e3a5f;border-radius:9px;font-size:13px;font-weight:600;">
            <i class="bi bi-plus-circle-fill"></i> Ajukan Surat
        </a>
    </div>
</div>

{{-- FILTER --}}
<div class="card card-custom mb-4 animate-in" style="animation-delay: 0.1s;">
    <div class="card-body py-3 px-4">
        <form method="GET" action="{{ route('user.surat.table') }}"
              class="d-flex gap-3 align-items-end flex-wrap">
            <div>
                <label class="form-label mb-1" style="font-size:11px;color:#6b7280;font-weight:600;">STATUS</label>
                <select name="status" class="form-select form-select-sm" style="font-size:13px;border-radius:7px;width:130px;">
                    <option value="">Semua</option>
                    <option value="proses"  {{ request('status')==='proses'  ? 'selected':'' }}>Proses</option>
                    <option value="revisi"  {{ request('status')==='revisi'  ? 'selected':'' }}>Revisi</option>
                    <option value="selesai" {{ request('status')==='selesai' ? 'selected':'' }}>Selesai</option>
                    <option value="ditolak" {{ request('status')==='ditolak' ? 'selected':'' }}>Ditolak</option>
                    <option value="draft"   {{ request('status')==='draft'   ? 'selected':'' }}>Draf</option>
                </select>
            </div>
            <div>
                <label class="form-label mb-1" style="font-size:11px;color:#6b7280;font-weight:600;">JENIS</label>
                <select name="jenis" class="form-select form-select-sm" style="font-size:13px;border-radius:7px;width:160px;">
                    <option value="">Semua Jenis</option>
                    @foreach(\App\Models\Surat::JENIS_LABEL as $val => $label)
                        <option value="{{ $val }}" {{ request('jenis')===$val ? 'selected':'' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-sm btn-primary" style="background:#1e3a5f;border-color:#1e3a5f;border-radius:7px;font-size:12px;">
                    <i class="bi bi-search me-1"></i>Filter
                </button>
                <a href="{{ route('user.surat.table') }}" class="btn btn-sm btn-light" style="border-radius:7px;font-size:12px;">Reset</a>
            </div>
        </form>
    </div>
</div>

<div class="card card-custom animate-in" style="animation-delay: 0.2s;">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="font-size: 13px;">
                <thead class="bg-light text-muted" style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px;">
                    <tr>
                        <th class="ps-4 py-3">No</th>
                        <th class="py-3">Judul Surat</th>
                        <th class="py-3">Jenis</th>
                        <th class="py-3">Tgl Pengajuan</th>
                        <th class="py-3">Tujuan</th>
                        <th class="py-3">No. Surat</th>
                        <th class="py-3">SLA</th>
                        <th class="py-3">Tahap</th>
                        <th class="pe-4 py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($surats as $index => $surat)
                        <tr>
                            <td class="ps-4 text-muted">
                                {{ $surats->firstItem() + $index }}
                            </td>
                            <td>
                                <div class="fw-bold" style="color: #1e3a5f;">{{ $surat->judul }}</div>
                                <span class="badge badge-{{ $surat->sifat }}" style="font-size: 9px;">{{ ucfirst($surat->sifat) }}</span>
                            </td>
                            <td>
                                <span class="badge rounded-pill" style="background:#ede9fe; color:#6d28d9;">
                                    {{ $surat->jenis_label }}
                                </span>
                            </td>
                            <td class="text-muted">
                                {{ $surat->created_at->format('d/m/Y') }}<br>
                                <small style="font-size: 10px;">{{ $surat->created_at->format('H:i') }} WIB</small>
                            </td>
                            <td class="text-muted" style="max-width: 150px;">
                                <div class="text-truncate" title="{{ $surat->tujuan }}">
                                    {{ $surat->tujuan ?: '-' }}
                                </div>
                            </td>
                            <td>
                                @if($surat->nomor_surat)
                                    <code class="fw-bold" style="color: #2563eb;">{{ $surat->nomor_surat }}</code>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if($surat->status === 'selesai' || $surat->status === 'ditolak')
                                    <span class="text-muted">-</span>
                                @elseif($surat->sla_status === 'terlambat')
                                    <span class="badge bg-danger" style="font-size: 10px;">Terlambat</span>
                                @else
                                    <span class="text-primary fw-semibold" style="font-size: 11px;">
                                        <i class="bi bi-clock me-1"></i>{{ $surat->sisa_jam }}
                                    </span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="progress" style="width: 40px; height: 5px; border-radius: 99px; background: #e5e7eb;">
                                        <div class="progress-bar" style="width: {{ $surat->proses_persen }}%; background: #1e3a5f;"></div>
                                    </div>
                                    <span style="font-size: 11px; color: #64748b;">{{ $surat->tahap_sekarang }}/10</span>
                                </div>
                                <div style="font-size: 10px; color: #94a3b8; margin-top: 2px;">{{ $surat->nama_tahap }}</div>
                            </td>
                            <td class="pe-4 text-center">
                                <div class="d-flex justify-content-center gap-1">
                                    <a href="{{ route('user.surat.show', $surat) }}" class="btn btn-sm btn-light" style="font-size: 11px; border-radius: 6px;" title="Lihat Detail">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    @if($surat->status === 'draft')
                                        <a href="{{ route('user.surat.edit', $surat) }}" class="btn btn-sm btn-light" style="font-size: 11px; border-radius: 6px; color: #2563eb;" title="Edit Draf">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-5 text-muted">
                                <i class="bi bi-inbox mb-2" style="font-size: 32px; display: block;"></i>
                                Belum ada data surat.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($surats->hasPages())
        <div class="card-footer bg-white border-0 py-3 px-4">
            {{ $surats->links() }}
        </div>
    @endif
</div>

<style>
    .table thead th {
        font-weight: 700;
        color: #64748b;
    }
    .table tbody tr {
        transition: all 0.2s ease;
    }
    .table tbody tr:hover {
        background-color: #f8fafc;
    }
</style>

@endsection
