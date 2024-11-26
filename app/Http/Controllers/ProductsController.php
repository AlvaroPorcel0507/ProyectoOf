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

    public function getProductDetails($id)
    {
        $product = Product::with(['inventory' => function ($query) {
            $query->where('userId', auth()->id());
        }])->find($id);

        if (!$product) {
            return response()->json(['error' => 'Producto no encontrado'], 404);
        }

        return response()->json([
            'product' => [
                'name' => $product->name,
            ],
            'inventory' => $product->inventory->map(function ($item) {
                return [
                    'quantity' => $item->quantity,
                    'measurementUnit' => $item->measurementUnit,
                    'unitPrice' => $item->unitPrice,
                ];
            }),
        ]);
    }

    public function create()
    {
        // Obtener el último registro de inventario del usuario autenticado
        $lastInventory = Inventory::where('userId', auth()->id())->orderBy('created_at', 'desc')->first();

        return view('livewire/products.create', [
            'lastInventory' => $lastInventory,
        ]);
    }


    public function store(Request $request)
    {
        // Validar el request
        $request->validate([
            'productSelect' => 'nullable|exists:products,id',
            'newProductName' => 'nullable|string|regex:/^[a-zA-Z]+$/|max:255',
            'measurementUnit' => 'required|string',
            'quantity' => 'required|numeric',
            'unitPrice' => 'required|numeric',
            'categoryId' => 'required|exists:categories,id',
        ]);

        DB::beginTransaction(); // Iniciar la transacción

        try {
            // Obtener la cantidad según la unidad seleccionada
            $quantityInKg = $request->quantity; // Cantidad ingresada

            // Realizar la conversión de acuerdo a la unidad
            if ($request->measurementUnit === 'Caja') {
                $quantityInKg *= 25; // Convertir a kilogramos
            } elseif ($request->measurementUnit === 'Carga') {
                $quantityInKg *= 60; // Convertir a kilogramos
            }

            // Verificar si se ha seleccionado un producto existente o si se va a crear uno nuevo
            if ($request->filled('productSelect')) {
                // Producto existente
                $product = Product::findOrFail($request->productSelect);

                // Registrar el nuevo inventario
                Inventory::create([
                    'quantity' => $quantityInKg,
                    'measurementUnit' => $request->measurementUnit,
                    'unitPrice' => $request->unitPrice,
                    'userId' => auth()->id(),
                    'productId' => $product->id,
                ]);

                // Actualizar el stock en la tabla de productos globalmente
                $product->stock += $quantityInKg;
                $product->save();

                // Actualizar el stock y el unitPrice en total_products para el usuario autenticado y el producto
                $totalProduct = TotalProduct::where('productId', $product->id)
                    ->where('userId', auth()->id())
                    ->first();

                if ($totalProduct) {
                    // Solo actualizar el stock y el unitPrice
                    $totalProduct->stock = $quantityInKg;
                    $totalProduct->unitPrice = $request->unitPrice; // Actualizar el unitPrice
                    $totalProduct->save();
                } else {
                    // Crear un nuevo registro si no existe en total_products
                    TotalProduct::create([
                        'stock' => $quantityInKg,
                        'unitPrice' => $request->unitPrice,
                        'userId' => auth()->id(),
                        'productId' => $product->id,
                    ]);
                }
            } else {
                // Crear un nuevo producto si no existe
                $newProduct = Product::create([
                    'name' => $request->newProductName,
                    'description' => $request->description,
                    'stock' => $quantityInKg,
                    'userId' => auth()->id(),
                    'categoryId' => $request->categoryId,
                ]);

                // Registrar el nuevo inventario para el nuevo producto
                Inventory::create([
                    'quantity' => $quantityInKg,
                    'measurementUnit' => $request->measurementUnit,
                    'unitPrice' => $request->unitPrice,
                    'userId' => auth()->id(),
                    'productId' => $newProduct->id,
                ]);

                // Crear el registro en total_products para el nuevo producto y usuario
                TotalProduct::create([
                    'stock' => $quantityInKg,
                    'unitPrice' => $request->unitPrice,
                    'userId' => auth()->id(),
                    'productId' => $newProduct->id,
                ]);
            }

            DB::commit(); // Confirmar la transacción
            return redirect()->route('products.index')->with('success', 'Producto registrado correctamente.');
        } catch (\Exception $e) {
            DB::rollBack(); // Revertir la transacción en caso de error
            return redirect()->back()->withErrors(['error' => 'Ocurrió un error al registrar el producto.']);
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
