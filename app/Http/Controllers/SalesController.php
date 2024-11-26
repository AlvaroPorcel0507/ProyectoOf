<?php
namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\SaleDetail;
use App\Models\Product;
use App\Models\Category;
use App\Models\Inventory;
use App\Models\TotalProduct;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class SalesController extends Controller
{
    public function index()
    {
        $sales = Sale::with(['saleDetails.product'])->paginate(8);
        return view('livewire.sales.index', compact('sales'));
    }

    public function show($saleId)
    {
        $sale = Sale::find($saleId);

        if (!$sale) {
            return redirect()->back()->with('error', 'Venta no encontrada');
        }

        $saleDetail = SaleDetail::where('salesId', $saleId)
            ->with(['product']) 
            ->get();

        return view('livewire.sales.saleDetail', compact('sale', 'saleDetail'));
    }

    public function create(Request $request)
    {
        $categories = Category::where('status', 1)->get();

        $products = Product::where('status', 1); 
        if ($request->filled('categoryId')) {
            $products = $products->where('categoryId', $request->categoryId);
        }
        $products = $products->get();

        $inventory = [];

        if ($request->filled('productId')) {
            $inventory = TotalProduct::where('productId', $request->productId)
                ->with(['user', 'product']) 
                ->get()
                ->map(function ($totalProduct) {
                    return [
                        'product_name' => $totalProduct->product->name ?? 'N/A',       
                        'producer_name' => $totalProduct->user 
                            ? $totalProduct->user->name . ' ' . $totalProduct->user->lastName 
                            : 'N/A',                                                
                        'stock' => $totalProduct->stock ?? 0,  
                        'unitPrice' => $totalProduct->unitPrice ?? 0.00,  
                    ];
                });
        }

        return view('livewire.sales.create', [
            'categories' => $categories,  
            'products' => $products,      
            'inventory' => $inventory,    
        ]);
    }

    public function addToCart(Request $request)
{
    $validated = $request->validate([
        'product_name' => 'required|string',
        'quantity' => 'required|numeric|min:1',
        'unit' => 'required|string',
        'unitPrice' => 'required|numeric|min:0',
    ]);

    $cart = session()->get('cart', []);

    $producerNameParts = explode(' ', $validated['producer_name']);
    $producerFirstName = $producerNameParts[0];
    $producerLastName = $producerNameParts[1] ?? ''; 

    $producer = User::where('name', $producerFirstName)
                    ->where('lastName', $producerLastName)
                    ->first();

    if (!$producer) {
        return response()->json(['error' => 'Productor no encontrado.'], 404);
    }

    $product = Product::where('name', $validated['product_name'])->first();

    if (!$product) {
        return response()->json(['error' => 'Producto no encontrado.'], 404);
    }

    $totalProduct = DB::table('total_products')
                      ->where('productId', $product->id)
                      ->where('userId', $producer->id) 
                      ->first();

    if (!$totalProduct) {
        return response()->json(['error' => 'Producto no encontrado en el inventario del productor.'], 404);
    }

    $convertedQuantity = $this->convertToKg($validated['quantity'], $validated['unit']);

    if ($totalProduct->stock < $convertedQuantity) {
        return response()->json(['error' => 'No hay suficiente stock disponible.'], 400);
    }

    DB::table('total_products')
        ->where('productId', $product->id)
        ->where('userId', $producer->id)
        ->decrement('stock', $convertedQuantity);

    $cartItem = [
        'product_name' => $validated['product_name'],
        'producer_name' => $validated['producer_name'],
        'quantity' => $validated['quantity'],
        'unit' => $validated['unit'],
        'unitPrice' => $validated['unitPrice'],
        'totalPrice' => $validated['quantity'] * $validated['unitPrice'],
    ];

    $productExists = false;
    foreach ($cart as &$item) {
        if ($item['product_name'] == $validated['product_name'] && $item['unit'] == $validated['unit']) {
            $item['quantity'] += $validated['quantity'];
            $item['totalPrice'] = $item['quantity'] * $item['unitPrice'];
            $productExists = true;
            break;
        }
    }

    if (!$productExists) {
        $cart[] = $cartItem;
    }

    session()->put('cart', $cart);

    return response()->json([
        'cart' => $cart,
        'reload' => true 
    ]);
}

private function convertToKg($quantity, $unit)
{
    switch (strtolower($unit)) {
        case 'lb':
            return $quantity * 0.45;  
        case 'kg':
            return $quantity;  
        case 'ar':
            return $quantity * 11.5;  
        case 'cuartilla':
            return $quantity * 2.875;  
        case 'quintal':
            return $quantity * 50;  
        default:
            return 0;  
    }
}

    public function showCart()
    {
        $cart = session()->get('cart', []);
        dd($cart);
    
        if (!is_array($cart) || count($cart) == 0) {
            return redirect()->back()->with('error', 'El carrito está vacío');
        }
    
        $total = array_sum(array_column($cart, 'totalPrice'));
    
        return view('livewire.sales.create', compact('cart', 'total'));
    }
    public function removeFromCart(Request $request)
{
    $index = $request->input('index');

    $cart = session()->get('cart', []);

    if (isset($cart[$index])) {
        $cartItem = $cart[$index];
        $productName = $cartItem['product_name'];
        $producerName = $cartItem['producer_name'];
        $quantity = $cartItem['quantity'];
        $unit = $cartItem['unit'];

        $producerNameParts = explode(' ', $producerName);
        $producerFirstName = $producerNameParts[0];
        $producerLastName = $producerNameParts[1] ?? ''; 

        $producer = User::where('name', $producerFirstName)
                        ->where('lastName', $producerLastName)
                        ->first();

        $product = Product::where('name', $productName)->first();

        $totalProduct = DB::table('total_products')
                          ->where('productId', $product->id)
                          ->where('userId', $producer->id)
                          ->first();

        if (!$totalProduct) {
            return response()->json(['error' => 'Producto no encontrado en el inventario del productor.'], 404);
        }

        $convertedQuantity = $this->convertToKg($quantity, $unit);

        DB::table('total_products')
            ->where('productId', $product->id)
            ->where('userId', $producer->id)
            ->increment('stock', $convertedQuantity);

        unset($cart[$index]);

        $cart = array_values($cart);

        session()->put('cart', $cart);
    }

    $cartHtml = view('partials.cart', ['cart' => $cart])->render();

    return response()->json([
        'cartHtml' => $cartHtml,
    ]);
}
public function finalizePurchase(Request $request)
{

    $cart = session()->get('cart', []);

    if (empty($cart)) {
        return response()->json(['error' => 'El carrito está vacío.'], 400);
    }

    $customerId = auth()->id();
    if (!$customerId) {
        return response()->json(['error' => 'Usuario no autenticado.'], 403);
    }

    $total = array_reduce($cart, function ($carry, $item) {
        return $carry + $item['totalPrice'];
    }, 0);

    DB::beginTransaction();
    try {
        $sale = Sale::create([
            'customerId' => $customerId,
            'status' => 1, 
            'total' => $total,
        ]);

        foreach ($cart as $item) {
            $producerNameParts = explode(' ', $item['producer_name']);
            $producerFirstName = $producerNameParts[0];
            $producerLastName = $producerNameParts[1] ?? ''; 

            $producer = User::where('name', $producerFirstName)
                            ->where('lastName', $producerLastName)
                            ->first();

            if (!$producer) {
                throw new \Exception('Productor no encontrado: ' . $item['producer_name']);
            }

            $product = Product::where('name', $item['product_name'])->first();
            if (!$product) {
                throw new \Exception('Producto no encontrado: ' . $item['product_name']);
            }

            SaleDetail::create([
                'quantity' => $item['quantity'],
                'unitPrice' => $item['unitPrice'],
                'totalProduct' => $item['totalPrice'],
                'description' => $item['unit'], 
                'producerId' => $producer->id,
                'salesId' => $sale->id,
                'productsId' => $product->id,
            ]);
        }

        session()->forget('cart');

        DB::commit();

        return response()->json(['message' => 'Compra finalizada con éxito.'], 200);
    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json(['error' => $e->getMessage()], 500);
    }
}

}
