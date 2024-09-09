<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\permission\searchExportRequest;
use App\Models\permission;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Traits\PermissionTrait;
use Illuminate\Http\Request;

class permissionController extends Controller
{
    use PermissionTrait;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $permissions = $this->getPermission();
        return view('admin.permission', compact('permissions'));
    }

    public function exportPdf(searchExportRequest $request)
    {

        $request->validated();

        $permissions = permission::whereBetween('created_at', [$request->start_date, $request->end_date])->get();

        $pdf = Pdf::loadView('exports.permissions', compact('permissions'));

        return $pdf->download('permission_report.pdf');
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
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
