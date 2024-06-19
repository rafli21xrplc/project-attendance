<?php

namespace App\Http\Controllers\Admin;

use App\Contracts\Interfaces\AbsencePointInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\absence_point\storeRequest;
use App\Http\Requests\absence_point\updateRequest;
use App\Models\absence_point;
use Illuminate\Http\Request;

class absencePointController extends Controller
{
    private AbsencePointInterface $AbsencePointInterface;

    public function __construct(AbsencePointInterface $AbsencePointInterface)
    {
        $this->AbsencePointInterface = $AbsencePointInterface;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $absence_point = $this->AbsencePointInterface->get();
        return view('admin.absence_point', compact('absence_point'));
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
            $this->AbsencePointInterface->store($request->validated());
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
    public function update(updateRequest $request, absence_point $absence_point)
    {
        try {
            $this->AbsencePointInterface->update($absence_point->id, $request->validated());
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'failed update');
        }
        return redirect()->back()->with('success', 'success update');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(absence_point $absence_point)
    {
        try {
            $this->AbsencePointInterface->delete($absence_point->id);
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'failed delete');
        }
        return redirect()->back()->with('success', 'success delete');
    }
}
