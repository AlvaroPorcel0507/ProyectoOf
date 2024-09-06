<?php

namespace App\Http\Controllers;

use App\Models\Products;
use Illuminate\Http\Request;

class ProductsController extends Controller
{
    public function index(Request $request)
    {
        $sortField = $request->input('sort_field', 'id');
        $sortDirection = $request->input('sort_direction', 'asc');

        // Asegúrate de que el campo de ordenación sea uno de los campos permitidos
        $validSortFields = ['id', 'name', 'description', 'stock', 'unitPrice', 'categoryId'];
        if (!in_array($sortField, $validSortFields)) {
            $sortField = 'id';
        }

        $products = Products::where('status', 1)->orderBy($sortField, $sortDirection)->paginate(8);

        return view('livewire/products.index', compact('products', 'sortField', 'sortDirection'));
    }

    public function create()
    {
        return view('livewire/products.create');
    }
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|max:50|regex:/^[a-zA-Z]+$/',
            'description' => 'required|max:500|regex:/^[a-zA-Z\s]+$/',
            'stock' => 'required|numeric|min:1|max:999',
            'unitPrice' => 'required|max:50|regex:/^\d{1,5}(\.\d{0,2})?$/',
            'categoryId' => 'required|numeric|min:1|max:20',
        ]);

        Products::create([
            'name' => $request->name,
            'description' => $request->description,
            'stock' => $request->stock,
            'unitPrice' => $request->unitPrice,
            'categoryId' => $request->categoryId,
        ]);

        return redirect()->route('products.index')->with('success', 'Producto creado exitosamente.');
    }
}
