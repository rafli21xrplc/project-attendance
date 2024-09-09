<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\setting\settingRequest;
use App\Http\Requests\setting\settingSpesialDayRequest;
use App\Traits\SettingTrait;
use Illuminate\Http\Request;

class settingController extends Controller
{

    use SettingTrait;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $setting = $this->getSetting()->pluck('value', 'key')->toArray();
        $startDate = null;
        $endDate = null;

        $specialDays = isset($setting['spesial_day']) ? json_decode($setting['spesial_day'], true) : [];

        if (!empty($specialDays)) {
            $startDate = $specialDays[0]['start_date'] ?? null;
            $endDate = $specialDays[0]['end_date'] ?? null;
        }

        $SubtractionTime = $this->getSubtractionTime();
        return view('admin.setting', compact('setting', 'SubtractionTime', 'startDate', 'endDate'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(settingRequest $request)
    {
        try {
            $this->storeOrUpdateSetting($request->validated());
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'save data failed.');
        }
        return redirect()->back()->with('success', 'save data successfully.');
    }

    public function spesialDay(settingSpesialDayRequest $request)
    {
        try {
            $this->settingSpesialDay($request->validated());
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'save data failed.');
        }
        return redirect()->back()->with('success', 'save data successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
