<?php

namespace App\Http\Controllers;

use App\Models\Categories;
use Illuminate\Http\Request;

class CategoriesController extends Controller
{
    public function index(Request $request)
    {
        $sortField = $request->input('sort_field', 'id');
        $sortDirection = $request->input('sort_direction', 'asc');

        // Asegúrate de que el campo de ordenación sea uno de los campos permitidos
        $validSortFields = ['id', 'name', 'status', 'userId'];
        if (!in_array($sortField, $validSortFields)) {
            $sortField = 'id';
        }

        $categories = Categories::where('status',1)->orderBy($sortField, $sortDirection)->paginate(8);

        return view('livewire/categories.index', compact('categories', 'sortField', 'sortDirection'));
    }

    public function create()
    {
        return view('livewire/categories.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        Categories::create([
            'name' => $request->name,
        ]);

        return redirect()->route('categories.index')->with('success', 'Categoria creada exitosamente.');
    }

    public function delete(Categories $category)
    {
        $category->update([
            'status' => 0
        ]);

        return redirect()->route('categories.index')->with('success', 'Categoria Eliminada con exito.');
    }
}
