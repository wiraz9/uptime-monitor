@extends('layouts.app')

@section('content')

{{-- <script src="https://cdn.tailwindcss.com"></script> --}}

<main class="p-8 space-y-6">
    <!-- 3 Kolom Website Status -->
    <div class="row grid-cols-1 lg:grid-cols-3 gap-2">
      
        <!-- Card -->
        @foreach($vendors as $vendor)

    
        <div class="bg-white rounded shadow-sm p-4 col ">
            <div class="flex justify-between items-center mb-6">
            <h2 class="text-green-600 h4">{{ $vendor->name }}</h2>        
            </div>
            <div class="space-y-2">  
          

            @foreach ($sites->where('vendor_id' , $vendor->id) as $site)

            <a href="{{ route('sites.show', [$site]) }}" class="d-flex justify-content-between border-b pb-1 text-decoration-none site">
                <span class="text-sm font-bold"> {{ $loop->iteration }}. {{ $site->name }}</span>            
                
                @livewire('uptime-badge', [
                                    'site' => $site,
                                    'uptimePoll' => request('uptime_poll', 0)
                                ])

            </a>
            

            @endforeach


            
            <!-- Repeat row as needed -->
            </div>
        </div>

        @endforeach

    
    </div>

    <!-- Monitoring Section -->
    <div class="row gap-2 mt-4">
      <!-- Moota Monitoring -->
      <div class="col bg-white rounded shadow-sm p-4 rounded">
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
                <td class="px-2 py-2 font-bold">-</td>
                <td><span class="bg-green-100 text-green-700 px-2 py-1 rounded text-xs">Aktif</span></td>
                <td class="text-center">18</td>
                <td class="text-center">4</td>
                <td class="font-bold text-right pr-2">0</td>
              </tr>
              <!-- Repeat rows as needed -->
            </tbody>
          </table>
        </div>
      </div>

      <!-- Notifikasi Monitoring -->
      <div class="col bg-white rounded shadow-sm p-4 rounded">
        <div class="d-flex justify-content-between items-center mb-2">
          <h2 class="font-semibold text-lg h4">Notifikasi Monitoring</h2>
          
          <div>
              <select class="form-control text-sm border border-gray-300 rounded-lg px-2 py-1">
                <option>Semua Kategori</option>
              </select>
          </div>

        </div>
        <div class="overflow-x-auto">
          <table class="table w-full text-sm border ">
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
              <!-- Repeat rows as needed -->
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </main>

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

@endsection