<?php

namespace App\Http\Controllers\Admin;

use App\Contracts\Interfaces\InstallmentsInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\installments\storeRequest;
use App\Http\Requests\installments\updateRequest;
use App\Models\PaymentInstallment;
use App\Traits\PaymentTrait;
use Illuminate\Http\Request;

class installmentsPaymentController extends Controller
{
    use PaymentTrait;
    private InstallmentsInterface $PaymentInstallment;

    public function __construct(InstallmentsInterface $PaymentInstallment)
    {
        $this->PaymentInstallment = $PaymentInstallment;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $installments = $this->PaymentInstallment->get();
        $tagihan = $this->getTagihanSiswa();
        return view('admin.installments_payment', compact('installments', 'tagihan'));
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
    public function api(storeRequest $request)
    {
        try {
            $this->PaymentInstallment->store($request->validated());
        } catch (\Throwable $th) {
            return response()->json(['message' => 'failed created'], 500);
        }
        return response()->json(['message' => 'success created'], 200);
    }

    public function store(storeRequest $request)
    {
        try {
            $this->PaymentInstallment->store($request->validated());
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
    public function update(updateRequest $request, PaymentInstallment $PaymentInstallment)
    {
        $this->PaymentInstallment->update($PaymentInstallment, $request->validated());
        try {
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'failed update');
        }
        return redirect()->back()->with('success', 'success update');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($PaymentInstallment)
    {
        try {
            $this->PaymentInstallment->delete($PaymentInstallment);
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'failed delete');
        }
        return redirect()->back()->with('success', 'success delete');
    }
}
