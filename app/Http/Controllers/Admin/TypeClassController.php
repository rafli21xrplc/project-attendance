<?php

namespace App\Http\Controllers\Admin;

use App\Contracts\Interfaces\TypeClassInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\TypeClass\StoreRequest;
use App\Http\Requests\TypeClass\UpdateRequest;
use App\Models\type_class;
use Illuminate\Http\Request;

class TypeClassController extends Controller
{
    private TypeClassInterface $type_class;

    /**
     * Display a listing of the resource.
     */
    public function __construct(TypeClassInterface $type_class)
    {
        $this->type_class = $type_class;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $type = $this->type_class->get();
        return view('admin.type_class', compact('type'));
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
    public function store(StoreRequest $request)
    {
        try {
            $this->type_class->store($request->validated());
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
    public function update(UpdateRequest $request, type_class $type_class)
    {
        try {
            $this->type_class->update($type_class->id, $request->validated());
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'failed update');
        }
        return redirect()->back()->with('success', 'success update');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(type_class $type_class)
    {
        try {
            $this->type_class->delete($type_class->id);
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'failed delete');
        }
        return redirect()->back()->with('success', 'success delete');
    }
}
