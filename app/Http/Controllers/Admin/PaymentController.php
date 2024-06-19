<?php

namespace App\Http\Controllers\Admin;

use App\Contracts\Interfaces\PaymentInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\Payment\StoreRequest;
use App\Http\Requests\Payment\UpdateRequest;
use App\Models\payment;
use Illuminate\Http\Request;

class PaymentController extends Controller
{

    private PaymentInterface $payment;

    public function __construct(PaymentInterface $payment)
    {
        $this->payment = $payment;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $payment = $this->payment->get();
        return view('admin.payment', compact('payment'));
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
    // public function store(StoreRequest $request)
    public function store(Request $request)
    {
        try {
            $this->payment->store($request->validated());
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
    public function update(UpdateRequest $request, payment $payment)
    {
        try {
            $this->payment->update($payment->id, $request->validated());
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'failed update');
        }
        return redirect()->back()->with('success', 'success update');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(payment $payment)
    {
        try {
            $this->payment->delete($payment->id);
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'failed delete');
        }
        return redirect()->back()->with('success', 'success delete');
    }
}
