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
                            <th>Jumlah Rekening</th>
                            <th>Aktif</th>
                            <th>Tidak Aktif</th>
                            <th>Saldo</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="border-t">
                          <td class="px-2 py-2 font-bold" id="nama-akun">Loading...</td>
                          <td class="text-center" id="jumlah-rekening">-</td>
                          <td class="text-center" id="rekening-aktif">-</td>
                          <td class="text-center" id="rekening-tidak-aktif">-</td>
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
                <table class="table w-full text-sm border" id="notifikasi-monitoring">
                    <thead class="bg-blue-100">
                        <tr>
                            <th class="px-2 py-2 text-left">Nama</th>
                            <th>Nomor</th>
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
    // Ambil data rekening
    fetch('/api/moota/bank', {
        method: 'GET',
        headers: {
            'Accept': 'application/json'
        }
    })
    .then(res => res.ok ? res.json() : Promise.reject(res))
    .then(data => {
        const akun = data.data ?? [];
        const aktif = akun.filter(x => x.is_active).length;
        const tidakAktif = akun.length - aktif;

        document.getElementById('jumlah-rekening').textContent = akun.length;
        document.getElementById('rekening-aktif').textContent = aktif;
        document.getElementById('rekening-tidak-aktif').textContent = tidakAktif;
    })
    .catch(error => {
        console.error('Error fetching bank data:', error);
        document.getElementById('jumlah-rekening').textContent = '-';
        document.getElementById('rekening-aktif').textContent = '-';
        document.getElementById('rekening-tidak-aktif').textContent = '-';
    });

    // Ambil nama user
    fetch('/api/moota/profile', {
        method: 'GET',
        headers: {
            'Accept': 'application/json'
        }
    })
    .then(res => res.ok ? res.json() : Promise.reject(res))
    .then(data => {
        document.getElementById('nama-akun').textContent = data.name ?? 'Tidak diketahui';
    })
    .catch(error => {
        console.error('Error fetching profile name:', error);
        document.getElementById('nama-akun').textContent = '-';
    });

    // Ambil saldo poin Moota
    fetch('/api/moota/balance', {
        method: 'GET',
        headers: {
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
        console.error('Error fetching balance:', error);
        document.getElementById('saldo-moota').textContent = 'Gagal';
    });

    // Ambil data notifikasi dari Woowa
    fetch('/proxy/woowa', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({})
    })
    .then(res => {
        console.log('Response status:', res.status);
        console.log('Response headers:', res.headers);
        return res.ok ? res.json() : Promise.reject(res);
    })
    .then(data => {
        console.log('Data fetched from proxy Woowa:', data);
        const tbody = document.querySelector('#notifikasi-monitoring tbody');
        tbody.innerHTML = ''; // Kosongkan tabel sebelum diisi

        data.forEach(item => {
            const row = document.createElement('tr');
            row.classList.add('border-t');

            row.innerHTML = `
                <td class="px-2 py-2 font-bold">${item.account}</td>
                <td class="text-center">${item.Number}</td>
                <td class="text-center">Woowa</td>
                <td class="text-center">Rahmat</td>
                <td><span class="bg-green-100 text-green-700 px-2 py-1 rounded text-xs">${item.status}</span></td>
            `;

            tbody.appendChild(row);
        });
    })
    .catch(error => {
        console.error('Error fetching data from proxy Woowa:', error);
    });
});
</script>
@endpush
