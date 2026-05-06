@php
    $totalSurat = Auth::user()->surats()->count();
    $suratSelesai = Auth::user()->surats()->where('status', 'selesai')->count();
    $suratProses = Auth::user()->surats()->where('status', 'proses')->count();
@endphp

<div class="mb-8">
    <div class="relative overflow-hidden bg-white rounded-2xl border border-gray-100 shadow-xl shadow-gray-200/50">
        <!-- Background Gradient/Pattern -->
        <div class="absolute top-0 left-0 w-full h-32 bg-gradient-to-r from-indigo-600 to-blue-500 opacity-90"></div>
        <div class="absolute top-0 right-0 p-6 text-white/20">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-32 h-32" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
            </svg>
        </div>
<br>
<br>
<br>
        <div class="relative px-8 pt-16 pb-8">
            <div class="flex flex-col md:flex-row items-end gap-6">
                <!-- Avatar -->
                <div class="relative group">
                    <div class="w-32 h-32 rounded-2xl overflow-hidden border-4 border-white shadow-lg bg-indigo-50">
                        <img id="avatar-preview-stats"
                            src="{{ Auth::user()->profile_photo ? Storage::url(Auth::user()->profile_photo) : 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->name) . '&background=4f46e5&color=fff&size=128' }}"
                            alt="{{ Auth::user()->name }}"
                            class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">
                    </div>
                </div>

                <!-- Info -->
                <div class="flex-1 mb-2">
                    <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">{{ Auth::user()->name }}</h1>
                    <div class="flex flex-wrap items-center gap-4 mt-2">
                        <span
                            class="inline-flex items-center gap-1.5 py-1 px-3 rounded-full text-xs font-bold bg-indigo-50 text-indigo-700 border border-indigo-100">
                            <i class="bi bi-person-badge-fill"></i>
                            {{ Auth::user()->getRoleLabel() }}
                        </span>
                        <span class="text-gray-500 text-sm flex items-center gap-1.5">
                            <i class="bi bi-envelope"></i>
                            {{ Auth::user()->email }}
                        </span>
                        @if(Auth::user()->nip)
                            <span class="text-gray-500 text-sm flex items-center gap-1.5">
                                <i class="bi bi-card-text"></i>
                                NIP: {{ Auth::user()->nip }}
                            </span>
                        @endif
                    </div>
                </div>

                <!-- Quick Stats -->
                <div class="grid grid-cols-3 gap-4 md:gap-8 bg-gray-50 p-4 rounded-xl border border-gray-100">
                    <div class="text-center">
                        <div class="text-2xl font-black text-indigo-600">{{ $totalSurat }}</div>
                        <div class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Total Surat</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-black text-green-600">{{ $suratSelesai }}</div>
                        <div class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Selesai</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-black text-blue-600">{{ $suratProses }}</div>
                        <div class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Proses</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>