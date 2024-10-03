<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductsController extends Controller
{
    public function index(Request $request)
    {
        $sortField = $request->input('sort_field', 'id');
        $sortDirection = $request->input('sort_direction', 'asc');

        // Asegúrate de que el campo de ordenación sea uno de los campos permitidos
        $validSortFields = ['id', 'name', 'description', 'stock', 'categoryId'];
        if (!in_array($sortField, $validSortFields)) {
            $sortField = 'id';
        }
        
        $products = Product::where('status', 1)->orderBy($sortField, $sortDirection)->paginate(8);

        return view('livewire/products.index', compact('products', 'sortField', 'sortDirection'));
    }

    public function create()
    {
        return view('livewire/products.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|max:100|regex:/^[a-zA-Z\s]+$/',
            'description' => 'required|max:500|regex:/^[a-zA-Z\s]+$/',
            'quantity' => 'required|numeric|min:0',
            'measurementUnit' => 'required',
            'unitPrice' => 'required|max:50|regex:/^\d{1,5}(\.\d{0,2})?$/',
            'categoryId' => 'required|numeric|min:1|max:20',
        ]);
    
        // Calcular el stock basado en la unidad de medida
        $conversionFactor = $request->measurementUnit === 'Caja' ? 25 : 60; // Caja: 25, Carga: 60
        $stock = $request->quantity * $conversionFactor;
    
        Product::create([
            'name' => $request->name,
            'description' => $request->description,
            'measurementUnit' => $request->measurementUnit, // Corrige aquí la propiedad
            'unitPrice' => $request->unitPrice,
            'stock' => $stock, // Guarda el stock calculado
            'categoryId' => $request->categoryId,
        ]);
    
        return redirect()->route('products.index')->with('success', 'Producto creado exitosamente.');
    }
    

    public function edit(Product $product)
    {
        return view('livewire/products.edit', compact('product'));
    }

    public function update(Request $request, Product $product)
    {

        $request->validate([
            'name' => 'required|max:50|regex:/^[a-zA-Z]+$/',
            'description' => 'required|max:500|regex:/^[a-zA-Z\s]+$/',
            'stock' => 'required|numeric|min:1|max:999',
            'unitPrice' => 'required|max:50|regex:/^\d{1,5}(\.\d{0,2})?$/',
            'categoryId' => 'required|numeric|min:1|max:20',
        ]);

        $product->update([
            'name' => $request->name,
            'description' => $request->description,
            'stock' => $request->stock,
            'unitPrice' => $request->unitPrice,
            'categoryId' => $request->categoryId,
        ]);

        

        return redirect()->route('products.index')->with('success', 'Producto actualizado correctamente.');
    }

    public function delete(Product $product)
    {
        $product->update([
            'status' => 0
        ]);

        return redirect()->route('products.index')->with('success', 'Producto Eliminado con exito.');
    }

    public function surtir(Request $request, $id)
    {
        $request->validate([
            'surtirQuantity' => 'required|numeric',
        ]);

        // Obtener el producto por su ID
        $product = Product::findOrFail($id);

        // Obtener el stock actual del producto
        $oldStock = $product->stock;

        // Obtener la cantidad ingresada en el formulario
        $modifyQuantity = $request->input('surtirQuantity');

        // Calcular el nuevo stock
        $newStock = $oldStock + $modifyQuantity;

        // Validar que el nuevo stock no sea negativo
        if ($newStock < 0) {
            return redirect()->back()->with('error', 'No puedes reducir el stock por debajo de 0.');
        }

        // Actualizar el stock en la base de datos
        $product->stock = $newStock;
        $product->save();

        // Retornar una respuesta con un mensaje de éxito
        return redirect()->back()->with('success', 'Stock actualizado correctamente. El stock anterior era ' . $oldStock . ' Kgs, y ahora es ' . $newStock . ' Kgs.');
    }

}
