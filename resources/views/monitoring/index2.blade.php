@extends('layouts.app')

@section('content')
<main class="p-8 space-y-6">
    <!-- 3 Kolom Website Status -->
    <div class="row grid-cols-1 lg:grid-cols-3 gap-2">
        <!-- Card -->
        @foreach($vendors as $vendor)
        <div class="bg-white rounded shadow-sm p-4 col">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-green-600 h4">{{ $vendor->name }}</h2>        
            </div>
            <div class="space-y-2">  
                @foreach ($sites->where('vendor_id', $vendor->id) as $site)
                <a href="{{ route('sites.show', [$site]) }}" class="d-flex justify-content-between border-b pb-1 text-decoration-none site">
                    <span class="text-sm font-bold">{{ $loop->iteration }}. {{ $site->name }}</span>            
                    @livewire('uptime-badge', [
                        'site' => $site,
                        'uptimePoll' => request('uptime_poll', 0)
                    ])
                </a>
                @endforeach
            </div>
        </div>
        @endforeach
    </div>

    <!-- Monitoring Section -->
    <div class="row gap-2 mt-4">
        <!-- Moota Monitoring -->
        <div class="col bg-white rounded shadow-sm p-4">
            <div class="d-flex justify-content-between items-center mb-2">
                <h2 class="font-semibold text-lg h4">Moota Monitoring</h2>
                <div>
                    <select class="form-control text-sm border border-gray-300 rounded-lg px-2 py-1">
                        <option>Semua Kategori</option>
                    </select>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="table w-full text-sm border">
                    <thead class="bg-blue-100">
                        <tr>
                            <th class="px-2 py-2 text-left">Nama</th>
                            <th>Status</th>
                            <th>Rek</th>
                            <th>Rek Aktif</th>
                            <th>Saldo</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="border-t">
                          <td class="px-2 py-2 font-bold" id="nama-akun">Loading...</td>
                          <td><span class="bg-green-100 text-green-700 px-2 py-1 rounded text-xs">Aktif</span></td>
                          <td class="text-center" id="jumlah-rekening">-</td>
                          <td class="text-center" id="rekening-aktif">-</td>
                          <td class="font-bold text-right pr-2" id="saldo-moota">Loading...</td>                          
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Notifikasi Monitoring -->
        <div class="col bg-white rounded shadow-sm p-4">
            <div class="d-flex justify-content-between items-center mb-2">
                <h2 class="font-semibold text-lg h4">Notifikasi Monitoring</h2>
                <div>
                    <select class="form-control text-sm border border-gray-300 rounded-lg px-2 py-1">
                        <option>Semua Kategori</option>
                    </select>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="table w-full text-sm border">
                    <thead class="bg-blue-100">
                        <tr>
                            <th class="px-2 py-2 text-left">Nama</th>
                            <th>Number</th>
                            <th>Vendor</th>
                            <th>CS</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="border-t">
                            <td class="px-2 py-2 font-bold">-</td>
                            <td class="text-center">628452xxx</td>
                            <td class="text-center">woowa</td>
                            <td class="text-center">Rahmat</td>
                            <td><span class="bg-green-100 text-green-700 px-2 py-1 rounded text-xs">Aktif</span></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>
@endsection

@push('styles')
<style>
    .log_indicator {
        padding: 4px 1px;
        cursor: pointer;
        margin-left: -0.4px;
    }
    .site:hover {
        transform: scale(1.02);
        background-color: #f0f0f0;
    }
</style>
@endpush   

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', async function () {
        const response = await fetch('/api/moota-tokens');
        const tokens = await response.json();
        const tokenV1 = tokens.tokenV1;
        const tokenV2 = tokens.tokenV2;

        // Ambil data rekening
        fetch('https://app.moota.co/api/v2/bank', {
            method: 'GET',
            headers: {
                'Authorization': tokenV2,
                'Accept': 'application/json'
            }
        })
        .then(res => res.ok ? res.json() : Promise.reject(res))
        .then(data => {
            const akun = data.data ?? [];
            const aktif = akun.filter(x => x.is_active).length;
            console.log(data.data)

            document.getElementById('jumlah-rekening').textContent = akun.length;
            document.getElementById('rekening-aktif').textContent = aktif;
        })
        .catch(error => {
            console.error('Error fetching bank data:', error);
            document.getElementById('jumlah-rekening').textContent = '-';
            document.getElementById('rekening-aktif').textContent = '-';
        });

        // Ambil nama user
        fetch('https://app.moota.co/api/v1/profile', {
            method: 'GET',
            headers: {
                'Authorization': tokenV1,
                'Accept': 'application/json'
            }
        })
        .then(res => res.ok ? res.json() : Promise.reject(res))
        .then(data => {
            document.getElementById('nama-akun').textContent = data.name ?? 'Tidak diketahui';
            console.log(data)

            const formatted = new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR'
            }).format(data.point ?? 0);

            document.getElementById('saldo-moota').textContent = formatted;

        })
        .catch(error => {
            console.error('Error fetching user data:', error);
            document.getElementById('nama-akun').textContent = '-';
            document.getElementById('saldo-moota').textContent = 'Gagal';
        });

        // Ambil saldo poin Moota
        fetch('https://app.moota.co/api/v1/balance', {
            method: 'GET',
            headers: {
                'Authorization': tokenV1,
                'Accept': 'application/json'
            }
        })
        .then(res => res.ok ? res.json() : Promise.reject(res))
        .then(data => {
            const formatted = new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR'
            }).format(data.balance ?? 0);

            document.getElementById('saldo-moota').textContent = formatted;
        })
        .catch(error => {
            console.error('Error fetching balance data:', error);
            document.getElementById('saldo-moota').textContent = 'Gagal';
        });
    });
</script>
@endpush
