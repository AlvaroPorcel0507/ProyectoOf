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
        $lastInventory = Inventory::where('userId', auth()->id())->orderBy('created_at', 'desc')->first();

        return view('livewire/products.create', [
            'lastInventory' => $lastInventory,
        ]);
    }


    public function store(Request $request)
    {
        $request->validate([
            'productSelect' => 'nullable|exists:products,id',
            'newProductName' => 'nullable|string|regex:/^[a-zA-Z]+$/|max:255',
            'measurementUnit' => 'required|string',
            'quantity' => 'required|numeric',
            'unitPrice' => 'required|numeric',
            'categoryId' => 'required|exists:categories,id',
        ]);

        DB::beginTransaction(); 

        try {
            $quantityInKg = $request->quantity; 

            if ($request->measurementUnit === 'Caja') {
                $quantityInKg *= 25; 
            } elseif ($request->measurementUnit === 'Carga') {
                $quantityInKg *= 60; 
            }

            if ($request->filled('productSelect')) {
                $product = Product::findOrFail($request->productSelect);

                Inventory::create([
                    'quantity' => $quantityInKg,
                    'measurementUnit' => $request->measurementUnit,
                    'unitPrice' => $request->unitPrice,
                    'userId' => auth()->id(),
                    'productId' => $product->id,
                ]);

                $product->stock += $quantityInKg;
                $product->save();

                $totalProduct = TotalProduct::where('productId', $product->id)
                    ->where('userId', auth()->id())
                    ->first();

                if ($totalProduct) {
                    $totalProduct->stock = $quantityInKg;
                    $totalProduct->unitPrice = $request->unitPrice; 
                    $totalProduct->save();
                } else {
                    TotalProduct::create([
                        'stock' => $quantityInKg,
                        'unitPrice' => $request->unitPrice,
                        'userId' => auth()->id(),
                        'productId' => $product->id,
                    ]);
                }
            } else {
                $newProduct = Product::create([
                    'name' => $request->newProductName,
                    'description' => $request->description,
                    'stock' => $quantityInKg,
                    'userId' => auth()->id(),
                    'categoryId' => $request->categoryId,
                ]);

                Inventory::create([
                    'quantity' => $quantityInKg,
                    'measurementUnit' => $request->measurementUnit,
                    'unitPrice' => $request->unitPrice,
                    'userId' => auth()->id(),
                    'productId' => $newProduct->id,
                ]);

                TotalProduct::create([
                    'stock' => $quantityInKg,
                    'unitPrice' => $request->unitPrice,
                    'userId' => auth()->id(),
                    'productId' => $newProduct->id,
                ]);
            }

            DB::commit(); 
            return redirect()->route('products.index')->with('success', 'Producto registrado correctamente.');
        } catch (\Exception $e) {
            DB::rollBack(); 
            return redirect()->back()->withErrors(['error' => 'Ocurrió un error al registrar el producto.']);
        }
    }

    public function edit(Product $product)
    {
        return view('livewire/products.edit', compact('product'));
    }

    public function update(Request $request, $id)
{
    $request->validate([
        'quantity' => 'required|numeric',
        'measurementUnit' => 'required|in:Caja,Carga',
        'unitPrice' => 'required|numeric',
        'categoryId' => 'required|exists:categories,id',
    ]);

    $product = Product::findOrFail($id);
    $measurementUnit = $request->input('measurementUnit');
    
    $quantity = $request->input('quantity');
    $convertedQuantity = ($measurementUnit === 'Caja') ? ($quantity * 25) : ($quantity * 60);
    
    if ($quantity < 0) {
        if (($product->stock + $convertedQuantity) < (($measurementUnit === 'Caja') ? 25 : 60)) {
            return redirect()->back()->withErrors(['error' => 'El stock no puede reducirse por debajo de la cantidad mínima de una ' . $measurementUnit . '.']);
        }
    }

    DB::beginTransaction();
    try {
        $product->stock += $convertedQuantity;
        $product->save();

        $inventory = Inventory::where('productId', $product->id)->firstOrFail();
        
        $inventory->quantity += $quantity; 
        $inventory->measurementUnit = $measurementUnit; 
        $inventory->unitPrice = $request->input('unitPrice');
        $inventory->userId = auth()->id(); 
        $inventory->save();

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

        DB::commit();
        return redirect()->route('products.index')->with('success', 'Producto actualizado exitosamente.');

    } catch (\Exception $e) {
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
        'surtirQuantity' => 'required|numeric', 
    ]);

    $product = Product::findOrFail($id);

    $oldStock = $product->stock;

    $modifyQuantity = $request->input('surtirQuantity');

    $conversionFactor = $product->measurementUnit === 'Caja' ? 25 : 60;

    $convertedQuantity = $modifyQuantity * $conversionFactor;

    $newStock = $oldStock + $convertedQuantity;

    if ($newStock < 0) {
        return redirect()->back()->with('error', 'No puedes reducir el stock por debajo de 0 Kgs.');
    }

    $product->stock = $newStock;
    $product->save();

    return redirect()->back()->with('success', 'Stock actualizado correctamente. El stock anterior era ' . $oldStock . ' Kgs, y ahora es ' . $newStock . ' Kgs.');
}


}
