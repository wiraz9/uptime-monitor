<?php

namespace App\Http\Controllers;

use App\Models\Site;
use App\Models\Vendor;
use Illuminate\Http\Request;

class MonitoringController extends Controller
{
    private $tokenV1 = 'Bearer X8O3lDacHJ6IiYO1Xr0oLtL2vat1OusSW1hmBsXipOaZjzp5SM';
    private $tokenV2 = 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiJucWllNHN3OGxsdyIsImp0aSI6ImFlODdjNWYzZWEzMTM2MGJjMTIyMWVjM2FkODNlYjgxOGZmMzAyMmYwMWFiNjY0NTdjMDVjYzkwNGZjMmY5NGY0ZGNkMzdlZDA2YWIzNWI3IiwiaWF0IjoxNzQ4MzE3NTA0LjcwNTk0NCwibmJmIjoxNzQ4MzE3NTA0LjcwNTk0NywiZXhwIjoxNzc5ODUzNTA0LjcwMzgsInN1YiI6IjE2NjI2Iiwic2NvcGVzIjpbImFwaSIsImJhbmtfcmVhZCIsIm11dGF0aW9uX3JlYWQiXX0.DD2OlE48q7-awVYSYnJa26i1_V9VW54pfkQXMduhlJnyvxeZz84oM6qLel7Axr7OwK7TSsmhyRZrO5Coiz7V439bAjnkrYXrmfAmB4S6m4j0y4AsYaCHeotd76JHvfWLBiBE0kPk1KPvXUaWCgdWBZ66ydX9uBcVVOwzDgxlw-QX_CvrCXcHLdNn4q1iMI4YCyEaQTAVVwM3H6z55Oyb_X57xY7UoCWdoNamo2SSAuvsXhPhgDxKt8_I6JadV3fKnVh8R5WSanPDL0Wa57900P3dANjvR3qkoLCh5WD6_xIH_7tPzNgFCR6VbbI54oxpjaGjHI1AQvxPHsaZZnPUKdrkpQQ4F-__lj2KNgO7UkAQR7yvu-rNJGDN3XbxSImazZrYD1M4vwABQbsfws3oPfj0i5-V9QfKIDhTzYIKgHJVu9z0wfaK7o1JClRuecqW1JM4KDdU9f-czuFk4m9IpIvVSCtSrgY_siKaEx_ccQq7ai4nB5YZYutGo0gPM1cukzyIjymUGrw0YLTsANixqP76-byHp_ZZ7dGd3IcmbE6EwFGA7nIT7e2zOkWHI5kyaWDF4sLW5znSrD_ga5TZ5H72tmXJDPm_dJdCeQzVfRSF13Ioe3f9rg2VevQZa1sTqrjjVsCBj71lFy-K8o-tCpm1pDloiODOoGwhyEgGIM4';

    public function getTokens()
    {
        return [
            'tokenV1' => $this->tokenV1,
            'tokenV2' => $this->tokenV2,
        ];
    }

    public function index(Request $request)
    {
        $siteQuery = Site::query();
        $siteQuery->where('is_active', 1);
        $siteQuery->where('name', 'like', '%'.$request->get('q').'%');
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
        $siteQuery->where('name', 'like', '%'.$request->get('q').'%');
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

        return view('monitoring.index2', compact('sites', 'availableVendors' , 'vendors'));
    }
}