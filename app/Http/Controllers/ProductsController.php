<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Product;
use App\Models\Inventory;
use App\Models\TotalProduct;


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
        // Validación de los datos de entrada
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'quantity' => 'required|numeric',
            'measurementUnit' => 'required|in:Caja,Carga',
            'unitPrice' => 'required|numeric',
            'categoryId' => 'required|exists:categories,id',
        ]);

        // Convertir la cantidad de acuerdo a la unidad de medida seleccionada
        $quantity = $request->input('quantity');
        $measurementUnit = $request->input('measurementUnit');
        $convertedQuantity = ($measurementUnit === 'Caja') ? ($quantity * 25) : ($quantity * 60);

        // Iniciar transacción
        DB::beginTransaction();

        try {
            // Verificar si el producto ya existe
            $product = Product::where('name', $request->input('name'))->first();

            if ($product) {
                // Si el producto ya existe, actualizar el stock
                $product->stock += $convertedQuantity;
                $product->save();
            } else {
                // Si el producto no existe, crear un nuevo producto
                $product = Product::create([
                    'name' => $request->input('name'),
                    'description' => $request->input('description'),
                    'stock' => $convertedQuantity,
                    'status' => 1,
                    'categoryId' => $request->input('categoryId'),
                ]);
            }

            // Registrar en la tabla 'inventories'
            Inventory::create([
                'quantity' => $request->input('quantity'),
                'measurementUnit' => $measurementUnit,
                'unitPrice' => $request->input('unitPrice'),
                'userId' => auth()->id(), // ID del usuario productor autenticado
                'productId' => $product->id,
            ]);

            // Actualizar o insertar en la tabla 'total_products'
            $totalProduct = TotalProduct::where('userId', auth()->id())
                ->where('productId', $product->id)
                ->first();

            if ($totalProduct) {
                // Si ya existe un registro, actualizar el stock
                $totalProduct->stock += $convertedQuantity;
                $totalProduct->save();
            } else {
                // Si no existe, crear un nuevo registro
                TotalProduct::create([
                    'stock' => $convertedQuantity,
                    'userId' => auth()->id(),
                    'productId' => $product->id,
                ]);
            }

            // Confirmar la transacción
            DB::commit();
            return redirect()->route('products.index')->with('success', 'Producto registrado exitosamente.');

        } catch (\Exception $e) {
            // Revertir la transacción en caso de error
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => 'Ocurrió un error durante el registro: ' . $e->getMessage()]);
        }
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
