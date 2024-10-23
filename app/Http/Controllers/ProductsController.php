<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;


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
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048', // Validación de imagen
        ]);

        // Calcular el stock basado en la unidad de medida
        $conversionFactor = $request->measurementUnit === 'Caja' ? 25 : 60; // Caja: 25, Carga: 60
        $stock = $request->quantity * $conversionFactor;

        // Manejar la imagen
        $image = $request->file('image')->store('image', 'public'); // Guardar imagen en public/storage/images

        Product::create([
            'name' => $request->name,
            'description' => $request->description,
            'measurementUnit' => $request->measurementUnit,
            'unitPrice' => $request->unitPrice,
            'stock' => $stock,
            'image' => $image, // Almacenar la ruta de la imagen
            'categoryId' => $request->categoryId,
            'userId' => auth()->id(), // Asumiendo que estás guardando el ID del usuario autenticado
        ]);

        return redirect()->route('products.index')->with('success', 'Producto creado exitosamente.');
    }


    public function edit(Product $product)
    {
        return view('livewire/products.edit', compact('product'));
    }

    public function update(Request $request, $id)
    {
        // Validar los datos de entrada
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Asegúrate de ajustar el tamaño máximo según tus necesidades
            'description' => 'required|string|max:1000',
            'stock' => 'required|integer|min:0',
            'unitPrice' => 'required|numeric|min:0',
            'categoryId' => 'required|exists:categories,id',
        ]);

        // Encontrar el producto por ID
        $product = Product::findOrFail($id);

        // Actualizar el nombre y la descripción
        $product->name = $validatedData['name'];
        $product->description = $validatedData['description'];
        $product->stock = $validatedData['stock'];
        $product->unitPrice = $validatedData['unitPrice'];
        $product->categoryId = $validatedData['categoryId'];

        // Manejar la imagen si se proporciona
        if ($request->hasFile('image')) {
            // Eliminar la imagen anterior si existe
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }

            // Guardar la nueva imagen y obtener su ruta
            $path = $request->file('image')->store('products', 'public');
            $product->image = $path; // Actualizar el campo de imagen en el producto
        }

        // Guardar los cambios en la base de datos
        $product->save();

        // Redireccionar a la lista de productos con un mensaje de éxito
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
        'surtirQuantity' => 'required|numeric', // Permitimos cantidades negativas para reducción de stock
    ]);

    // Obtener el producto por su ID
    $product = Product::findOrFail($id);

    // Obtener el stock actual del producto
    $oldStock = $product->stock;

    // Obtener la cantidad ingresada en el formulario
    $modifyQuantity = $request->input('surtirQuantity');

    // Calcular el factor de conversión basado en la unidad de medida
    $conversionFactor = $product->measurementUnit === 'Caja' ? 25 : 60; // Caja: 25 Kgs, Carga: 60 Kgs

    // Calcular la cantidad a modificar en kilogramos (permite incrementos y disminuciones)
    $convertedQuantity = $modifyQuantity * $conversionFactor;

    // Calcular el nuevo stock
    $newStock = $oldStock + $convertedQuantity;

    // Validar que el nuevo stock no sea negativo
    if ($newStock < 0) {
        return redirect()->back()->with('error', 'No puedes reducir el stock por debajo de 0 Kgs.');
    }

    // Actualizar el stock en la base de datos
    $product->stock = $newStock;
    $product->save();

    // Retornar una respuesta con un mensaje de éxito
    return redirect()->back()->with('success', 'Stock actualizado correctamente. El stock anterior era ' . $oldStock . ' Kgs, y ahora es ' . $newStock . ' Kgs.');
}


}
