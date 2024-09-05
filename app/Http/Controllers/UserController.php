<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $sortField = $request->input('sort_field', 'id');
        $sortDirection = $request->input('sort_direction', 'asc');

        // Asegúrate de que el campo de ordenación sea uno de los campos permitidos
        $validSortFields = ['id', 'name', 'lastName', 'secondLastName', 'role', 'location', 'created_at'];
        if (!in_array($sortField, $validSortFields)) {
            $sortField = 'id';
        }

        $users = User::where('status', 1)->orderBy($sortField, $sortDirection)->paginate(8);

        return view('livewire/users.index', compact('users', 'sortField', 'sortDirection'));
    }

    public function create()
    {
        return view('livewire/users.create');
    }

    public function edit(User $user)
    {
        return view('livewire/users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'lastName' => 'required|string|max:255',
            'secondLastName' => 'nullable|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'location' => 'nullable|string|max:255',
            'role' => 'required|string|max:255',
        ]);

        $user->update([
            'name' => $request->name,
            'lastName' => $request->lastName,
            'secondLastName' => $request->secondLastName,
            'email' => $request->email,
            'location' => $request->location,
            'role' => $request->role,
        ]);

        return redirect()->route('users.index')->with('success', 'Usuario actualizado correctamente.');
    }

    public function destroy(User $user)
    {
        $user->status = 0;
        $user->save();

        return redirect()->route('users.index')->with('success', 'Usuario deshabilitado correctamente.');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|max:50|regex:/^[a-zA-Z\s]+$/',
            'lastName' => 'required|max:40|regex:/^[a-zA-Z]+$/',
            'secondLastName' => 'nullable|max:40|regex:/^[a-zA-Z]*$/',
            'email' => 'required|string|email|max:255|unique:users',
            'location' => 'nullable|max:80|regex:/^[a-zA-Z\s]*$/',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|string|max:255',
        ]);

        User::create([
            'name' => $request->name,
            'lastName' => $request->lastName,
            'secondLastName' => $request->secondLastName,
            'email' => $request->email,
            'location' => $request->location,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        return redirect()->route('users.index')->with('success', 'Usuario creado exitosamente.');
    }

    public function delete(User $user)
    {
        $user->update([
            'status' => 0
        ]);

        return redirect()->route('users.index')->with('success', 'Usuario Eliminado con exito.');
    }
}
