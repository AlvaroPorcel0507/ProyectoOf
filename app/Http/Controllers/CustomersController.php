<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class CustomersController extends Controller
{
    public function index(Request $request)
    {
        $sortField = $request->input('sort_field', 'id');
        $sortDirection = $request->input('sort_direction', 'asc');

        // Asegúrate de que el campo de ordenación sea uno de los campos permitidos
        $validSortFields = ['id', 'ciNit', 'companyName'];
        if (!in_array($sortField, $validSortFields)) {
            $sortField = 'id';
        }

        $customers = User::where('status', 1)->where('role','=','Cliente')->orderBy($sortField, $sortDirection)->paginate(8);

        return view('livewire/customers.index', compact('customers', 'sortField', 'sortDirection'));
    }

    public function create()
    {
        return view('livewire/customers.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'ciNit' => 'required|max:50|regex:/^[a-zA-Z0-9\s\-]+$/',
            'companyName' => 'required|max:50|regex:/^[a-zA-Z\s]+$/',
        ]);

        User::create([
            'ciNit' => $request->ciNit,
            'companyName' => $request->companyName,
        ]);

        return redirect()->route('customers.index')->with('success', 'Cliente creado exitosamente.');
    }

    public function edit(User $customer)
    {
        return view('livewire/customers.edit', compact('customer'));
    }

    public function update(Request $request, User $customer)
    {
        $request->validate([
            'ciNit' => 'required|max:50|regex:/^[a-zA-Z0-9\s\-]+$/',
            'companyName' => 'required|max:50|regex:/^[a-zA-Z\s]+$/',
        ]);

        $customer->update([
            'ciNit' => $request->ciNit,
            'companyName' => $request->companyName,
        ]);

        return redirect()->route('customers.index')->with('success', 'Cliente actualizado correctamente.');
    }

    public function delete(User $customer)
    {
        $customer->update([
            'status' => 0
        ]);

        return redirect()->route('customers.index')->with('success', 'Cliente Eliminado con exito.');
    }
}
