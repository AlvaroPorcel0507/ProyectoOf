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
    // Validar los datos del formulario
    $request->validate([
        'quantity' => 'required|numeric',
        'measurementUnit' => 'required|in:Caja,Carga',
        'unitPrice' => 'required|numeric',
        'categoryId' => 'required|exists:categories,id',
    ]);

    // Obtener el producto por su ID
    $product = Product::findOrFail($id);
    $measurementUnit = $request->input('measurementUnit');
    
    // Convertir la cantidad de acuerdo a la unidad de medida seleccionada
    $quantity = $request->input('quantity');
    $convertedQuantity = ($measurementUnit === 'Caja') ? ($quantity * 25) : ($quantity * 60);
    
    // Verificar si la cantidad es negativa para decrementar el stock
    if ($quantity < 0) {
        // Validar que la reducción no baje el stock por debajo del mínimo permitido
        if (($product->stock + $convertedQuantity) < (($measurementUnit === 'Caja') ? 25 : 60)) {
            return redirect()->back()->withErrors(['error' => 'El stock no puede reducirse por debajo de la cantidad mínima de una ' . $measurementUnit . '.']);
        }
    }

    // Iniciar transacción
    DB::beginTransaction();
    try {
        // Actualizar el stock del producto
        $product->stock += $convertedQuantity;
        $product->save();

        // Buscar el inventario existente
        $inventory = Inventory::where('productId', $product->id)->firstOrFail();
        
        // Actualizar el registro existente en la tabla 'inventories'
        $inventory->quantity += $quantity; // Aumentar o disminuir según la cantidad ingresada
        $inventory->measurementUnit = $measurementUnit; // Mantener la unidad de medida
        $inventory->unitPrice = $request->input('unitPrice'); // Actualizar el precio unitario
        $inventory->userId = auth()->id(); // Actualizar el ID del usuario
        $inventory->save();

        // Actualizar la tabla 'total_products'
        $totalProduct = TotalProduct::where('userId', auth()->id())
            ->where('productId', $product->id)
            ->first();

        if ($totalProduct) {
            $totalProduct->stock += $convertedQuantity;
            $totalProduct->save();
        } else {
            TotalProduct::create([
                'stock' => $convertedQuantity,
                'userId' => auth()->id(),
                'productId' => $product->id,
            ]);
        }

        // Confirmar la transacción
        DB::commit();
        return redirect()->route('products.index')->with('success', 'Producto actualizado exitosamente.');

    } catch (\Exception $e) {
        // Revertir la transacción en caso de error
        DB::rollBack();
        return redirect()->back()->withErrors(['error' => 'Ocurrió un error durante la actualización: ' . $e->getMessage()]);
    }
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
