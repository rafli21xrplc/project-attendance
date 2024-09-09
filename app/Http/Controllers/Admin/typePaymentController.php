<?php

namespace App\Http\Controllers\admin;

use App\Contracts\Interfaces\TypePaymentInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\typePayment\storeRequest;
use Illuminate\Http\Request;

class typePaymentController extends Controller
{

    private TypePaymentInterface $paymentType;

    public function __construct(TypePaymentInterface $paymentType)
    {
        $this->paymentType = $paymentType;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(storeRequest $request)
    {
        try {
            $this->paymentType->store($request->validated());
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
