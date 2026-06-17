@extends('layouts.user')

@section('content')
<div class="container-fluid py-3">

    {{-- Header --}}
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <h1 class="h4 fw-bold mb-1" style="color: var(--text-primary);">
                <i class="bi bi-calendar2-week text-primary me-2"></i>{{ $title }}
            </h1>
            <p class="text-muted small mb-0">Pantau timeline pengajuan, deadline, dan penyelesaian surat Anda.</p>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            {{-- Filter Jenis --}}
            <select id="filterJenis" class="form-select form-select-sm rounded-pill" style="width:auto;font-size:.8rem;">
                <option value="semua" {{ $filterJenis === 'semua' ? 'selected' : '' }}>Semua Event</option>
                <option value="pengajuan" {{ $filterJenis === 'pengajuan' ? 'selected' : '' }}>Pengajuan</option>
                <option value="deadline" {{ $filterJenis === 'deadline' ? 'selected' : '' }}>Deadline SLA</option>
                <option value="selesai" {{ $filterJenis === 'selesai' ? 'selected' : '' }}>Selesai</option>
                <option value="tanggal_surat" {{ $filterJenis === 'tanggal_surat' ? 'selected' : '' }}>Tanggal Surat</option>
            </select>
        </div>
    </div>

    {{-- Stats Row --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card-custom p-3">
                <div class="d-flex align-items-center gap-2">
                    <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0"
                        style="width:40px;height:40px;background:rgba(59,130,246,0.12);">
                        <i class="bi bi-send text-primary"></i>
                    </div>
                    <div>
                        <div class="text-muted" style="font-size:10px;font-weight:700;letter-spacing:.08em;text-transform:uppercase;">Pengajuan</div>
                        <div class="fs-5 fw-bold">{{ $stats['total_pengajuan'] }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card-custom p-3">
                <div class="d-flex align-items-center gap-2">
                    <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0"
                        style="width:40px;height:40px;background:rgba(245,158,11,0.12);">
                        <i class="bi bi-clock-history" style="color:#f59e0b;"></i>
                    </div>
                    <div>
                        <div class="text-muted" style="font-size:10px;font-weight:700;letter-spacing:.08em;text-transform:uppercase;">Deadline</div>
                        <div class="fs-5 fw-bold">{{ $stats['total_deadline'] }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card-custom p-3">
                <div class="d-flex align-items-center gap-2">
                    <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0"
                        style="width:40px;height:40px;background:rgba(34,197,94,0.12);">
                        <i class="bi bi-check-circle-fill text-success"></i>
                    </div>
                    <div>
                        <div class="text-muted" style="font-size:10px;font-weight:700;letter-spacing:.08em;text-transform:uppercase;">Selesai</div>
                        <div class="fs-5 fw-bold">{{ $stats['total_selesai'] }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card-custom p-3">
                <div class="d-flex align-items-center gap-2">
                    <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0"
                        style="width:40px;height:40px;background:rgba(168,85,247,0.12);">
                        <i class="bi bi-envelope-paper" style="color:#a855f7;"></i>
                    </div>
                    <div>
                        <div class="text-muted" style="font-size:10px;font-weight:700;letter-spacing:.08em;text-transform:uppercase;">Total Surat</div>
                        <div class="fs-5 fw-bold">{{ $stats['total_surat'] }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Main: Calendar + Agenda Sidebar --}}
    <div class="row g-4">

        {{-- Calendar --}}
        <div class="col-lg-8">
            <div class="card-custom p-4">
                {{-- Month Navigation --}}
                <div class="d-flex align-items-center justify-content-between mb-4">
                    <button class="btn btn-sm btn-outline-secondary rounded-pill" id="btnPrevMonth">
                        <i class="bi bi-chevron-left"></i>
                    </button>
                    <h2 class="h5 fw-bold mb-0" id="calendarTitle">
                        {{ $namaBulan[$bulan] }} {{ $tahun }}
                    </h2>
                    <button class="btn btn-sm btn-outline-secondary rounded-pill" id="btnNextMonth">
                        <i class="bi bi-chevron-right"></i>
                    </button>
                </div>

                {{-- Day Headers (Sen - Min) --}}
                <div class="agenda-calendar-grid" id="calendarGrid">
                    <div class="agenda-day-header">Sen</div>
                    <div class="agenda-day-header">Sel</div>
                    <div class="agenda-day-header">Rab</div>
                    <div class="agenda-day-header">Kam</div>
                    <div class="agenda-day-header">Jum</div>
                    <div class="agenda-day-header">Sab</div>
                    <div class="agenda-day-header">Min</div>

                    @foreach($calendarDays as $cell)
                        @if(is_null($cell['day']))
                            <div class="agenda-day-cell is-empty"></div>
                        @else
                            <div class="agenda-day-cell {{ $cell['isToday'] ? 'is-today' : '' }}"
                                 data-date="{{ $cell['dateKey'] }}">
                                <span class="agenda-day-num">{{ $cell['day'] }}</span>
                                @if(count($cell['events']) > 0)
                                    <div class="agenda-dots">
                                        @foreach(array_slice($cell['events'], 0, 3) as $evt)
                                            <span class="agenda-dot" style="background:{{ $evt['color'] }};" title="{{ $evt['label'] }}: {{ $evt['surat']->judul }}"></span>
                                        @endforeach
                                        @if(count($cell['events']) > 3)
                                            <span class="agenda-dot-more">+{{ count($cell['events']) - 3 }}</span>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        @endif
                    @endforeach
                </div>

                {{-- Legend --}}
                <div class="d-flex flex-wrap gap-3 mt-4 pt-3 border-top small text-muted">
                    <span class="d-inline-flex align-items-center gap-1">
                        <span class="rounded-circle d-inline-block" style="width:10px;height:10px;background:#3b82f6;"></span> Pengajuan
                    </span>
                    <span class="d-inline-flex align-items-center gap-1">
                        <span class="rounded-circle d-inline-block" style="width:10px;height:10px;background:#f59e0b;"></span> Deadline
                    </span>
                    <span class="d-inline-flex align-items-center gap-1">
                        <span class="rounded-circle d-inline-block" style="width:10px;height:10px;background:#22c55e;"></span> Selesai
                    </span>
                    <span class="d-inline-flex align-items-center gap-1">
                        <span class="rounded-circle d-inline-block" style="width:10px;height:10px;background:#a855f7;"></span> Tanggal Surat
                    </span>
                </div>
            </div>
        </div>

        {{-- Agenda Sidebar --}}
        <div class="col-lg-4">

            {{-- Today's Agenda --}}
            <div class="card-custom p-4 mb-4">
                <h3 class="h6 fw-bold mb-3 d-flex align-items-center gap-2">
                    <i class="bi bi-calendar2-day text-primary"></i>
                    Agenda Hari Ini
                    <span class="badge bg-primary rounded-pill ms-auto">{{ count($todayEvents) }}</span>
                </h3>

                @if(count($todayEvents) > 0)
                    <div class="agenda-timeline">
                        @foreach($todayEvents as $evt)
                            <div class="agenda-timeline-item">
                                <div class="agenda-timeline-dot" style="background:{{ $evt['color'] }};"></div>
                                <div class="agenda-timeline-content">
                                    <div class="d-flex align-items-center gap-2 mb-1">
                                        <i class="bi {{ $evt['icon'] }} small" style="color:{{ $evt['color'] }};"></i>
                                        <span class="badge rounded-pill" style="background:{{ $evt['color'] }}20;color:{{ $evt['color'] }};font-size:10px;font-weight:700;">
                                            {{ $evt['label'] }}
                                        </span>
                                        @if($evt['time'])
                                            <span class="text-muted ms-auto" style="font-size:11px;">{{ $evt['time'] }}</span>
                                        @endif
                                    </div>
                                    <a href="{{ route('user.surat.show', $evt['surat']) }}" class="text-decoration-none">
                                        <div class="fw-semibold small text-truncate" style="max-width:250px;">{{ $evt['surat']->judul }}</div>
                                    </a>
                                    <div class="text-muted" style="font-size:11px;">
                                        {{ $evt['surat']->jenis_label }} · {{ ucfirst($evt['surat']->status) }}
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-4 text-muted">
                        <i class="bi bi-calendar-x fs-1 d-block mb-2 opacity-25"></i>
                        <p class="small mb-0">Tidak ada agenda hari ini</p>
                    </div>
                @endif
            </div>

            {{-- Upcoming Deadlines --}}
            <div class="card-custom p-4 mb-4">
                <h3 class="h6 fw-bold mb-3 d-flex align-items-center gap-2">
                    <i class="bi bi-hourglass-split text-warning"></i>
                    Deadline Mendatang
                </h3>

                @if($upcomingSurat->count() > 0)
                    @foreach($upcomingSurat as $surat)
                        <a href="{{ route('user.surat.show', $surat) }}" class="text-decoration-none">
                            <div class="d-flex align-items-start gap-3 py-2 {{ !$loop->last ? 'border-bottom' : '' }}">
                                <div class="flex-shrink-0 text-center" style="min-width:44px;">
                                    <div class="fw-bold text-danger" style="font-size:14px;">
                                        {{ $surat->deadline_sla->format('d') }}
                                    </div>
                                    <div class="text-muted" style="font-size:10px;text-transform:uppercase;">
                                        {{ $surat->deadline_sla->translatedFormat('M') }}
                                    </div>
                                </div>
                                <div class="flex-grow-1 overflow-hidden">
                                    <div class="fw-semibold small text-truncate text-dark">{{ $surat->judul }}</div>
                                    <div class="d-flex align-items-center gap-2 mt-1">
                                        <span class="badge rounded-pill" style="font-size:9px;background:{{ $surat->sla_color }}20;color:{{ $surat->sla_color }};">
                                            {{ $surat->sla_icon }} {{ $surat->sisa_jam }}
                                        </span>
                                        <span class="text-muted" style="font-size:10px;">{{ $surat->nama_tahap }}</span>
                                    </div>
                                </div>
                            </div>
                        </a>
                    @endforeach
                @else
                    <div class="text-center py-3 text-muted">
                        <i class="bi bi-check-all fs-3 d-block mb-1 opacity-25"></i>
                        <p class="small mb-0">Tidak ada deadline mendatang</p>
                    </div>
                @endif
            </div>

            {{-- Selected Day Events (hidden by default, shown on click) --}}
            <div class="card-custom p-4" id="selectedDayPanel" style="display:none;">
                <h3 class="h6 fw-bold mb-3 d-flex align-items-center gap-2">
                    <i class="bi bi-calendar-check text-info"></i>
                    <span id="selectedDayTitle">-</span>
                </h3>
                <div id="selectedDayEvents"></div>
            </div>

        </div>
    </div>

</div>

{{-- Selected Day Event Template --}}
<template id="eventItemTpl">
    <div class="d-flex align-items-start gap-2 py-2 border-bottom">
        <div class="flex-shrink-0 mt-1">
            <span class="agenda-timeline-dot" style="width:10px;height:10px;"></span>
        </div>
        <div class="flex-grow-1">
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="badge rounded-pill evt-label" style="font-size:10px;font-weight:700;"></span>
                <span class="text-muted evt-time" style="font-size:11px;"></span>
            </div>
            <a href="#" class="text-decoration-none evt-link">
                <div class="fw-semibold small evt-title text-truncate" style="max-width:220px;"></div>
            </a>
            <div class="text-muted evt-meta" style="font-size:11px;"></div>
        </div>
    </div>
</template>
@endsection

@push('scripts')
<script>
(function() {
    let currentBulan = {{ $bulan }};
    let currentTahun = {{ $tahun }};
    let currentFilter = '{{ $filterJenis }}';
    const namaBulan = @json($namaBulan);

    const calendarGrid = document.getElementById('calendarGrid');
    const calendarTitle = document.getElementById('calendarTitle');
    const btnPrev = document.getElementById('btnPrevMonth');
    const btnNext = document.getElementById('btnNextMonth');
    const filterJenis = document.getElementById('filterJenis');
    const selectedDayPanel = document.getElementById('selectedDayPanel');
    const selectedDayTitle = document.getElementById('selectedDayTitle');
    const selectedDayEvents = document.getElementById('selectedDayEvents');
    const eventItemTpl = document.getElementById('eventItemTpl');

    // Navigate months
    btnPrev.addEventListener('click', () => {
        currentBulan--;
        if (currentBulan < 1) { currentBulan = 12; currentTahun--; }
        loadMonth();
    });

    btnNext.addEventListener('click', () => {
        currentBulan++;
        if (currentBulan > 12) { currentBulan = 1; currentTahun++; }
        loadMonth();
    });

    // Filter
    filterJenis.addEventListener('change', () => {
        currentFilter = filterJenis.value;
        loadMonth();
    });

    // Click on day cell
    calendarGrid.addEventListener('click', (e) => {
        const cell = e.target.closest('.agenda-day-cell:not(.is-empty)');
        if (!cell) return;

        // Remove active from all cells
        calendarGrid.querySelectorAll('.agenda-day-cell').forEach(c => c.classList.remove('is-selected'));
        cell.classList.add('is-selected');

        const dateKey = cell.dataset.date;
        if (!dateKey) return;

        // Show events for this day from current data
        showDayEvents(dateKey, cell);
    });

    async function loadMonth() {
        // Show loading state
        calendarTitle.textContent = 'Memuat...';
        calendarGrid.style.opacity = '0.5';
        selectedDayPanel.style.display = 'none';

        try {
            const params = new URLSearchParams({
                bulan: currentBulan,
                tahun: currentTahun,
                jenis: currentFilter
            });

            const resp = await fetch(`/agenda/events?${params}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });

            if (!resp.ok) throw new Error('Failed to load');

            const data = await resp.json();
            renderCalendar(data.calendarDays, data.events);
            calendarTitle.textContent = `${namaBulan[currentBulan]} ${currentTahun}`;

            // Update URL without reload
            const url = new URL(window.location);
            url.searchParams.set('bulan', currentBulan);
            url.searchParams.set('tahun', currentTahun);
            url.searchParams.set('jenis', currentFilter);
            window.history.replaceState({}, '', url);

        } catch (err) {
            console.error('Agenda load error:', err);
            calendarTitle.textContent = `${namaBulan[currentBulan]} ${currentTahun} (Gagal memuat)`;
        } finally {
            calendarGrid.style.opacity = '1';
        }
    }

    function renderCalendar(days, events) {
        // Remove all cells except headers (first 7 children)
        while (calendarGrid.children.length > 7) {
            calendarGrid.removeChild(calendarGrid.lastChild);
        }

        const today = new Date().toISOString().split('T')[0];

        days.forEach(cell => {
            const div = document.createElement('div');

            if (cell.day === null) {
                div.className = 'agenda-day-cell is-empty';
            } else {
                div.className = `agenda-day-cell ${cell.isToday ? 'is-today' : ''}`;
                div.dataset.date = cell.dateKey;

                const numSpan = document.createElement('span');
                numSpan.className = 'agenda-day-num';
                numSpan.textContent = cell.day;
                div.appendChild(numSpan);

                if (cell.events && cell.events.length > 0) {
                    const dotsDiv = document.createElement('div');
                    dotsDiv.className = 'agenda-dots';
                    cell.events.slice(0, 3).forEach(evt => {
                        const dot = document.createElement('span');
                        dot.className = 'agenda-dot';
                        dot.style.background = evt.color;
                        dot.title = `${evt.label}: ${evt.surat_judul || ''}`;
                        dotsDiv.appendChild(dot);
                    });
                    if (cell.events.length > 3) {
                        const more = document.createElement('span');
                        more.className = 'agenda-dot-more';
                        more.textContent = `+${cell.events.length - 3}`;
                        dotsDiv.appendChild(more);
                    }
                    div.appendChild(dotsDiv);
                }
            }

            calendarGrid.appendChild(div);
        });

        // Store events for day click
        calendarGrid._events = events;
    }

    function showDayEvents(dateKey, cellEl) {
        const events = (calendarGrid._events && calendarGrid._events[dateKey]) || [];

        // Format date
        const dateObj = new Date(dateKey + 'T00:00:00');
        const dayNames = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];
        const monthNames = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
        const formatted = `${dayNames[dateObj.getDay()]}, ${dateObj.getDate()} ${monthNames[dateObj.getMonth()]} ${dateObj.getFullYear()}`;

        selectedDayTitle.textContent = formatted;
        selectedDayEvents.innerHTML = '';

        if (events.length === 0) {
            selectedDayEvents.innerHTML = `
                <div class="text-center py-3 text-muted">
                    <i class="bi bi-calendar-x fs-4 d-block mb-1 opacity-25"></i>
                    <p class="small mb-0">Tidak ada agenda</p>
                </div>`;
        } else {
            events.forEach(evt => {
                const tpl = eventItemTpl.content.cloneNode(true);
                tpl.querySelector('.agenda-timeline-dot').style.background = evt.color;
                const label = tpl.querySelector('.evt-label');
                label.textContent = evt.label;
                label.style.background = evt.color + '20';
                label.style.color = evt.color;
                if (evt.time) {
                    tpl.querySelector('.evt-time').textContent = evt.time;
                }
                tpl.querySelector('.evt-title').textContent = evt.surat_judul || '-';
                tpl.querySelector('.evt-link').href = evt.surat_url || '#';
                tpl.querySelector('.evt-meta').textContent = `${evt.surat_jenis || ''} · ${evt.surat_status || ''}`;
                selectedDayEvents.appendChild(tpl);
            });
        }

        selectedDayPanel.style.display = 'block';
        selectedDayPanel.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }
})();
</script>
@endpush

@push('styles')
<style>
/* ═══ Agenda Calendar Grid ═══ */
.agenda-calendar-grid {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    gap: 4px;
}

.agenda-day-header {
    text-align: center;
    font-size: 11px;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: .06em;
    color: rgba(100, 116, 139, 0.7);
    padding: 8px 0;
}

.agenda-day-cell {
    aspect-ratio: 1;
    border-radius: 12px;
    background: rgba(241, 245, 249, 0.5);
    border: 1px solid rgba(226, 232, 240, 0.5);
    padding: 6px;
    display: flex;
    flex-direction: column;
    cursor: pointer;
    transition: all 0.2s ease;
    position: relative;
    min-height: 60px;
}

.agenda-day-cell:hover:not(.is-empty) {
    background: rgba(6, 182, 212, 0.06);
    border-color: rgba(6, 182, 212, 0.25);
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(6, 182, 212, 0.08);
}

.agenda-day-cell.is-empty {
    background: transparent;
    border-color: transparent;
    cursor: default;
}

.agenda-day-cell.is-today {
    background: linear-gradient(135deg, rgba(6, 182, 212, 0.1), rgba(37, 99, 235, 0.08));
    border-color: rgba(6, 182, 212, 0.4);
    box-shadow: 0 0 0 2px rgba(6, 182, 212, 0.12), 0 4px 16px rgba(6, 182, 212, 0.1);
}

.agenda-day-cell.is-selected {
    background: rgba(6, 182, 212, 0.12) !important;
    border-color: var(--accent) !important;
    box-shadow: 0 0 0 2px var(--accent-soft), 0 4px 16px rgba(6, 182, 212, 0.15) !important;
}

.agenda-day-num {
    font-size: 13px;
    font-weight: 700;
    color: var(--text-primary);
    line-height: 1;
}

.is-today .agenda-day-num {
    color: var(--accent);
    font-weight: 800;
}

.agenda-dots {
    display: flex;
    flex-wrap: wrap;
    gap: 3px;
    margin-top: auto;
    align-items: center;
}

.agenda-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    display: inline-block;
    box-shadow: 0 0 6px currentColor;
}

.agenda-dot-more {
    font-size: 9px;
    font-weight: 800;
    color: var(--text-secondary);
    opacity: 0.7;
}

/* ═══ Agenda Timeline ═══ */
.agenda-timeline {
    position: relative;
    padding-left: 0;
}

.agenda-timeline-item {
    display: flex;
    gap: 12px;
    padding: 10px 0;
    border-bottom: 1px solid rgba(226, 232, 240, 0.5);
}

.agenda-timeline-item:last-child {
    border-bottom: none;
    padding-bottom: 0;
}

.agenda-timeline-dot {
    width: 12px;
    height: 12px;
    border-radius: 50%;
    flex-shrink: 0;
    margin-top: 4px;
    box-shadow: 0 0 8px currentColor;
}

.agenda-timeline-content {
    flex: 1;
    min-width: 0;
}

/* ═══ Responsive ═══ */
@media (max-width: 768px) {
    .agenda-calendar-grid {
        gap: 2px;
    }

    .agenda-day-cell {
        min-height: 44px;
        padding: 4px;
        border-radius: 8px;
    }

    .agenda-day-num {
        font-size: 11px;
    }

    .agenda-dot {
        width: 6px;
        height: 6px;
    }

    .agenda-day-header {
        font-size: 9px;
    }

    .agenda-dot-more {
        font-size: 8px;
    }
}
</style>
@endpush
