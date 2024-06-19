<?php

namespace App\Http\Controllers\Admin;

use App\Contracts\Interfaces\KbmPeriodInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\kbm_period\storeRequest;
use App\Http\Requests\kbm_period\updateRequest;
use App\Models\kbm_period;
use Illuminate\Http\Request;

class kbmPeriodController extends Controller
{

    private KbmPeriodInterface $kbmPeriodInterface;

    public function __construct(KbmPeriodInterface $kbmPeriodInterface)
    {
        $this->kbmPeriodInterface = $kbmPeriodInterface;
    }

    
    public function index()
    {
        $kbm_period = $this->kbmPeriodInterface->get();
        return view('admin.kbm_period', compact('kbm_period'));
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
    public function store(storeRequest $request)
    {
        try {
            $this->kbmPeriodInterface->store($request->validated());
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'failed created');
        }
        return redirect()->back()->with('success', 'success created');
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
    public function update(updateRequest $request, kbm_period $kbm_period)
    {
        try {
            $this->kbmPeriodInterface->update($kbm_period->id, $request->validated());
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'failed update');
        }
        return redirect()->back()->with('success', 'success update');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy( kbm_period $kbm_period)
    {
        try {
            $this->kbmPeriodInterface->delete($kbm_period->id);
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'failed delete');
        }
        return redirect()->back()->with('success', 'success delete');
    }
}
