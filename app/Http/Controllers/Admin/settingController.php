<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\setting\settingRequest;
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
        return view('admin.setting', compact('setting'));
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

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
