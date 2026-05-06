<section class="space-y-6">
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Manage Switchable Accounts') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __('Manage the accounts you have previously logged into on this device for instant switching.') }}
        </p>
    </header>

    <div id="switch-accounts-profile-list" class="mt-6 space-y-4">
        <!-- Will be populated by JS -->
        <div class="animate-pulse flex space-x-4">
            <div class="rounded-full bg-gray-200 h-10 w-10"></div>
            <div class="flex-1 space-y-6 py-1">
                <div class="h-2 bg-gray-200 rounded"></div>
                <div class="space-y-3">
                    <div class="grid grid-cols-3 gap-4">
                        <div class="h-2 bg-gray-200 rounded col-span-2"></div>
                        <div class="h-2 bg-gray-200 rounded col-span-1"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const STORAGE_KEY = 'bpsuml_saved_accounts';
            const currentUserId = {{ Auth::id() }};
            const SWITCH_URL = '{{ route("auth.switch") }}';
            const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            function getAccounts() {
                try { return JSON.parse(localStorage.getItem(STORAGE_KEY) || '[]'); } catch(e) { return []; }
            }

            function removeAccount(id) {
                if(!confirm('Are you sure you want to remove this account from the switch list?')) return;
                let accounts = getAccounts().filter(a => a.id !== id);
                localStorage.setItem(STORAGE_KEY, JSON.stringify(accounts));
                renderAccounts();
            }

            function doSwitch(e, userId, token) {
                e.preventDefault();
                if (!token) {
                    alert('Session expired for this account. Please login manually.');
                    return;
                }

                const btn = e.currentTarget;
                const originalContent = btn.innerHTML;
                btn.disabled = true;
                btn.innerHTML = '<svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Switching...';

                fetch(SWITCH_URL, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': CSRF_TOKEN,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ user_id: userId, switch_token: token }),
                })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        let accounts = getAccounts();
                        const idx = accounts.findIndex(a => a.id === data.user_id);
                        if (idx >= 0) {
                            accounts[idx].switch_token = data.new_token;
                            accounts[idx].photo = data.photo;
                        }
                        localStorage.setItem(STORAGE_KEY, JSON.stringify(accounts));
                        window.location.href = data.redirect;
                    } else {
                        alert('Failed to switch: ' + data.message);
                        btn.disabled = false;
                        btn.innerHTML = originalContent;
                    }
                })
                .catch(() => {
                    alert('Network error. Please try again.');
                    btn.disabled = false;
                    btn.innerHTML = originalContent;
                });
            }

            function renderAccounts() {
                const accounts = getAccounts();
                const container = document.getElementById('switch-accounts-profile-list');
                
                if (accounts.length === 0 || (accounts.length === 1 && accounts[0].id === currentUserId)) {
                    container.innerHTML = `
                        <div class="p-6 bg-gray-50 rounded-xl border border-dashed border-gray-300 text-center">
                            <p class="text-gray-500 text-sm">No other accounts saved on this device.</p>
                        </div>
                    `;
                    return;
                }

                container.innerHTML = accounts.map(acc => {
                    const isCurrent = acc.id === currentUserId;
                    return `
                        <div class="flex items-center justify-between p-4 bg-white border border-gray-200 rounded-xl shadow-sm hover:border-indigo-300 transition-colors ${isCurrent ? 'opacity-60 bg-gray-50' : ''}">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-full overflow-hidden bg-indigo-100 flex items-center justify-center text-indigo-600 font-bold border-2 border-white shadow-sm">
                                    ${acc.photo ? `<img src="${acc.photo}" class="w-full h-full object-cover">` : acc.initials}
                                </div>
                                <div>
                                    <h4 class="text-sm font-semibold text-gray-900">${acc.name} ${isCurrent ? '<span class="ml-2 text-[10px] bg-indigo-100 text-indigo-600 px-2 py-0.5 rounded-full uppercase tracking-wider">Current</span>' : ''}</h4>
                                    <p class="text-xs text-gray-500">${acc.email} • <span class="capitalize text-indigo-500 font-medium">${acc.role || 'user'}</span></p>
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                ${!isCurrent ? `
                                    <button onclick="window.switchAccProfile(${acc.id}, '${acc.switch_token}', event)" 
                                            class="inline-flex items-center px-3 py-1.5 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
                                        Switch
                                    </button>
                                    <button onclick="window.removeAccProfile(${acc.id})" 
                                            class="inline-flex items-center px-3 py-1.5 bg-white border border-red-300 rounded-md font-semibold text-xs text-red-700 uppercase tracking-widest shadow-sm hover:text-red-500 focus:outline-none focus:border-blue-300 focus:ring ring-blue-200 active:text-red-800 active:bg-gray-50 disabled:opacity-25 transition ease-in-out duration-150">
                                        Remove
                                    </button>
                                ` : ''}
                            </div>
                        </div>
                    `;
                }).join('');
            }

            // Expose to window for inline onclick
            window.removeAccProfile = removeAccount;
            window.switchAccProfile = (id, token, e) => doSwitch(e, id, token);

            renderAccounts();
        });
    </script>
</section>
