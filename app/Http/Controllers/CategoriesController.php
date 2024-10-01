<?php

namespace App\Http\Controllers;

use App\Models\Category;
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

        $categories = Category::where('status', 1)->orderBy($sortField, $sortDirection)->paginate(8);

        return view('livewire/categories.index', compact('categories', 'sortField', 'sortDirection'));
    }

    public function create()
    {
        return view('livewire/categories.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|max:50|regex:/^[a-zA-Z\s]+$/',
        ]);

        Category::create([
            'name' => $request->name,
        ]);

        return redirect()->route('categories.index')->with('success', 'Categoria creada exitosamente.');
    }

    public function delete(Category $category)
    {
        $category->update([
            'status' => 0
        ]);

        return redirect()->route('categories.index')->with('success', 'Categoria Eliminada con exito.');
    }

    public function edit(Category $category)
    {
        return view('livewire/categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name' => 'required|max:50|regex:/^[a-zA-Z\s]+$/',
        ]);

        $category->update([
            'name' => $request->name,
        ]);

        return redirect()->route('categories.index')->with('success', 'Categoria actualizada correctamente.');
    }
}
