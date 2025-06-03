<?php

namespace App\Http\Controllers;

use App\Models\Site;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class MonitoringController extends Controller
{
    public function index(Request $request)
    {
        $siteQuery = Site::query();
        $siteQuery->where('is_active', 1);
        $siteQuery->where('name', 'like', '%' . $request->get('q') . '%');
        $siteQuery->orderBy('name');
        $siteQuery->where('owner_id', auth()->id());

        if ($vendorId = $request->get('vendor_id')) {
            if ($vendorId == 'null') {
                $siteQuery->whereNull('vendor_id');
            } else {
                $siteQuery->where('vendor_id', $vendorId);
            }
        }

        $sites = $siteQuery->with('vendor')->get();

        $availableVendors = Vendor::orderBy('name')->pluck('name', 'id')->toArray();
        $availableVendors = ['null' => 'n/a'] + $availableVendors;

        return view('monitoring.index', compact('sites', 'availableVendors'));
    }

    public function index2(Request $request)
    {
        $siteQuery = Site::query();
        $siteQuery->where('is_active', 1);
        $siteQuery->where('name', 'like', '%' . $request->get('q') . '%');
        $siteQuery->orderBy('name');
        $siteQuery->where('owner_id', auth()->id());

        if ($vendorId = $request->get('vendor_id')) {
            if ($vendorId == 'null') {
                $siteQuery->whereNull('vendor_id');
            } else {
                $siteQuery->where('vendor_id', $vendorId);
            }
        }

        $sites = $siteQuery->with('vendor')->get();
        $availableVendors = Vendor::orderBy('name')->pluck('name', 'id')->toArray();
        $availableVendors = ['null' => 'n/a'] + $availableVendors;

        $vendors = Vendor::get();

        // Ambil data bank dari API v1
        $rekSummary = $this->fetchBankSummary();

        return view('monitoring.index2', compact('sites', 'availableVendors', 'vendors', 'rekSummary'));
    }

    public function getBankData()
    {
        $token = env('MOOTA_TOKEN_V1'); // Ganti ke v1

        $response = Http::withToken($token)
            ->acceptJson()
            ->get('https://app.moota.co/api/v1/bank');

        if ($response->successful()) {
            return response()->json($response->json());
        }

        return response()->json(['error' => 'Failed to fetch bank data'], 500);
    }

    public function getProfileData()
    {
        $token = env('MOOTA_TOKEN_V1');

        $response = Http::withToken($token)
            ->acceptJson()
            ->get('https://app.moota.co/api/v1/profile');

        if ($response->successful()) {
            return response()->json([
                'name' => $response->json()['name'],
                'email' => $response->json()['email'],
                'city' => $response->json()['city'],
            ]);
        }

        return response()->json(['error' => 'Failed to fetch profile data'], 500);
    }

    public function getBalanceData()
    {
        $token = env('MOOTA_TOKEN_V1');

        $response = Http::withToken($token)
            ->acceptJson()
            ->get('https://app.moota.co/api/v1/balance');

        if ($response->successful()) {
            return response()->json([
                'balance' => $response->json()['balance'],
                'bill' => $response->json()['bill'],
            ]);
        }

        return response()->json(['error' => 'Failed to fetch balance'], 500);
    }

    /**
     * Fetch Rekening summary from Moota API v1
     */
    private function fetchBankSummary()
    {
        $token = env('MOOTA_TOKEN_V1');

        $response = Http::withToken($token)
            ->acceptJson()
            ->get('https://app.moota.co/api/v1/bank');

        if (!$response->successful()) {
            return [
                'total_rek' => 0,
                'rek_aktif' => 0,
            ];
        }

        $banks = $response->json()['data'] ?? [];

        return [
            'total_rek' => count($banks),
            'rek_aktif' => collect($banks)->where('is_active', 1)->count(),
        ];
    }
}
