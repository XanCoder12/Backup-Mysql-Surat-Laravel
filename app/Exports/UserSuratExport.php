<?php

namespace App\Exports;

use App\Models\Surat;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Support\Facades\Auth;

class UserSuratExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $filters;
    private $rowNumber = 0;

    public function __construct($filters = [])
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = Surat::where('user_id', Auth::id())
                      ->with('tahapans')
                      ->latest();

        if (!empty($this->filters['status'])) {
            $query->where('status', $this->filters['status']);
        } else {
            $query->where('status', '!=', 'draft');
        }

        if (!empty($this->filters['jenis'])) {
            $query->where('jenis', $this->filters['jenis']);
        }

        if (!empty($this->filters['tahun'])) {
            $query->whereYear('created_at', $this->filters['tahun']);
        }
        if (!empty($this->filters['bulan'])) {
            $query->whereMonth('created_at', $this->filters['bulan']);
        }

        if (!empty($this->filters['search'])) {
            $query->where('judul', 'like', '%' . $this->filters['search'] . '%');
        }

        return $query->get();
    }

    public function headings(): array
    {
        return [
            'No',
            'Judul Surat',
            'Jenis',
            'Sifat',
            'Tujuan',
            'Nomor Surat',
            'Tgl Pengajuan',
            'Status',
            'Tahap Sekarang',
            'Progress (%)',
        ];
    }

    public function map($surat): array
    {
        $this->rowNumber++;

        return [
            $this->rowNumber,
            $surat->judul,
            $surat->jenis_label,
            ucfirst($surat->sifat),
            $surat->tujuan,
            $surat->nomor_surat ?? '-',
            $surat->created_at->format('d/m/Y H:i'),
            ucfirst($surat->status),
            "Tahap {$surat->tahap_sekarang}/10 — {$surat->nama_tahap}",
            $surat->proses_persen . '%',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
