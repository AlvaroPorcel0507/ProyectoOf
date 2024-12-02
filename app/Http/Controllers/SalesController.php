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
    // Mostrar ventas
    public function index(Request $request)
    {
        // Obtener las fechas de inicio y fin del rango seleccionado o usar valores por defecto
        $startDate = $request->input('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', now()->toDateString());

        // Ajustar la fecha de fin para incluir todo el día
        $endDateWithTime = now()->parse($endDate)->endOfDay();

        // Filtrar los detalles de venta por el rango de fechas y el productor autenticado
        $saleDetails = SaleDetail::where('producerId', Auth::id())
            ->whereHas('sale', function ($query) use ($startDate, $endDateWithTime) {
                $query->whereBetween('created_at', [$startDate, $endDateWithTime]);
            })
            ->with(['sale.customer', 'product'])
            ->get();

        // Filtrar las ventas por el rango de fechas
        $sales = Sale::with(['saleDetails.product'])
            ->whereBetween('created_at', [$startDate, $endDateWithTime])
            ->paginate(8);

        // Pasar las fechas seleccionadas para mantenerlas en la vista
        return view('livewire.sales.index', compact('sales', 'saleDetails', 'startDate', 'endDate'));
    }

    // Mostrar detalles de venta
    public function show($saleId)
    {
        $sale = Sale::find($saleId);

        if (!$sale) {
            return redirect()->back()->with('error', 'Venta no encontrada');
        }

        // Obtén los detalles de la venta y sus relaciones
        $saleDetail = SaleDetail::where('salesId', $saleId)
            ->with(['product']) // Cargar relación del producto
            ->get();

        return view('livewire.sales.saleDetail', compact('sale', 'saleDetail'));
    }

    public function create(Request $request)
    {
        // Obtener todas las categorías activas (status = 1)
        $categories = Category::where('status', 1)->get();

        // Consultar productos activos filtrados por categoría si se envía un filtro
        $products = Product::where('status', 1); // Solo productos activos
        if ($request->filled('categoryId')) {
            $products = $products->where('categoryId', $request->categoryId);
        }
        $products = $products->get();

        // Inicializar inventario vacío
        $inventory = [];

        // Si se selecciona un producto, obtener detalles asociados
        if ($request->filled('productId')) {
            $inventory = TotalProduct::where('productId', $request->productId)
                ->with(['user', 'product']) // Cargar relaciones de usuario (productor) y producto
                ->get()
                ->map(function ($totalProduct) {
                    return [
                        'product_name' => $totalProduct->product->name ?? 'N/A',       // Nombre del producto
                        'producer_name' => $totalProduct->user 
                            ? $totalProduct->user->name . ' ' . $totalProduct->user->lastName 
                            : 'N/A',                                                // Nombre del productor
                        'stock' => $totalProduct->stock ?? 0,                        // Stock disponible
                        'unitPrice' => $totalProduct->unitPrice ?? 0.00,             // Precio unitario
                    ];
                });
        }

        return view('livewire.sales.create', [
            'categories' => $categories,  // Lista de categorías activas
            'products' => $products,      // Productos activos filtrados por categoría
            'inventory' => $inventory,    // Detalles del inventario asociados al producto seleccionado
        ]);
    }

    // Método para agregar un producto al carrito
    public function addToCart(Request $request)
    {
        // Validar los datos del producto
        $validated = $request->validate([
            'product_name' => 'required|string',
            'producer_name' => 'required|string',  // Nombre completo del productor
            'quantity' => 'required|numeric|min:1',
            'unit' => 'required|string',
            'unitPrice' => 'required|numeric|min:0',
        ]);

        // Obtener el carrito de la sesión
        $cart = session()->get('cart', []);

        // Separar el nombre completo del productor en sus partes
        $producerNameParts = explode(' ', $validated['producer_name']);
        $producerFirstName = $producerNameParts[0];
        $producerLastName = $producerNameParts[1] ?? ''; // El segundo apellido es opcional

        // Buscar el productor en la base de datos
        $producer = User::where('name', $producerFirstName)
                        ->where('lastName', $producerLastName)
                        ->first();

        if (!$producer) {
            return response()->json(['error' => 'Productor no encontrado.'], 404);
        }

        // Buscar el producto en la base de datos
        $product = Product::where('name', $validated['product_name'])->first();

        if (!$product) {
            return response()->json(['error' => 'Producto no encontrado.'], 404);
        }

        // Buscar la entrada del producto en total_products (para obtener el stock actual)
        $totalProduct = DB::table('total_products')
                        ->where('productId', $product->id)
                        ->where('userId', $producer->id) // Considerando que userId es el productor
                        ->first();

        if (!$totalProduct) {
            return response()->json(['error' => 'Producto no encontrado en el inventario del productor.'], 404);
        }

        // Convertir la cantidad a kg según la unidad de medida
        $convertedQuantity = $this->convertToKg($validated['quantity'], $validated['unit']);

        // Verificar si hay suficiente stock
        if ($totalProduct->stock < $convertedQuantity) {
            return response()->json(['error' => 'No hay suficiente stock disponible.'], 400);
        }

        // Restar la cantidad convertida del stock en la tabla total_products
        DB::table('total_products')
            ->where('productId', $product->id)
            ->where('userId', $producer->id)
            ->decrement('stock', $convertedQuantity);

        // Crear un nuevo ítem de carrito
        $cartItem = [
            'product_name' => $validated['product_name'],
            'producer_name' => $validated['producer_name'],
            'quantity' => $validated['quantity'],
            'unit' => $validated['unit'],
            'unitPrice' => $validated['unitPrice'],
            'totalPrice' => $validated['quantity'] * $validated['unitPrice'],
        ];

        // Verificar si el producto ya existe en el carrito
        $productExists = false;
        foreach ($cart as &$item) {
            if ($item['product_name'] == $validated['product_name'] && $item['unit'] == $validated['unit']) {
                // Actualizar el producto existente
                $item['quantity'] += $validated['quantity'];
                $item['totalPrice'] = $item['quantity'] * $item['unitPrice'];
                $productExists = true;
                break;
            }
        }

        // Si el producto no existe, agregarlo al carrito
        if (!$productExists) {
            $cart[] = $cartItem;
        }

        // Guardar el carrito actualizado en la sesión
        session()->put('cart', $cart);

        // Responder con el carrito actualizado y la señal de recarga de la vista
        return response()->json([
            'cart' => $cart,
            'reload' => true // Indica que la vista debe recargarse
        ]);
    }

private function convertToKg($quantity, $unit)
{
    switch (strtolower($unit)) {
        case 'lb':
            return $quantity * 0.45;  // 1 lb = 0.45 kg
        case 'kg':
            return $quantity;  // 1 kg = 1 kg
        case 'ar':
            return $quantity * 11.5;  // 1 arroba = 11.5 kg
        case 'cuartilla':
            return $quantity * 2.875;  // 1 cuartilla = 2.875 kg
        case 'quintal':
            return $quantity * 50;  // 1 quintal = 50 kg
        default:
            return 0;  // Si no es una unidad válida, retornar 0
    }
    
}


    // Método para ver el contenido del carrito
    
    public function showCart()
    {
        // Obtener el carrito de la sesión, si no existe, devuelve un array vacío
        $cart = session()->get('cart', []);
        dd($cart);
    
        // Verificar si el carrito no está vacío y tiene elementos
        if (!is_array($cart) || count($cart) == 0) {
            return redirect()->back()->with('error', 'El carrito está vacío');
        }
    
        // Calcular el total del carrito si es necesario
        $total = array_sum(array_column($cart, 'totalPrice'));
    
        // Pasar los datos a la vista
        return view('livewire.sales.create', compact('cart', 'total'));
    }
    public function removeFromCart(Request $request)
{
    // Obtener el índice del producto a eliminar
    $index = $request->input('index');

    // Obtener el carrito de la sesión
    $cart = session()->get('cart', []);

    // Verificar si el índice es válido
    if (isset($cart[$index])) {
        // Obtener el producto y la cantidad del carrito
        $cartItem = $cart[$index];
        $productName = $cartItem['product_name'];
        $producerName = $cartItem['producer_name'];
        $quantity = $cartItem['quantity'];
        $unit = $cartItem['unit'];

        // Separar el nombre completo del productor en sus partes
        $producerNameParts = explode(' ', $producerName);
        $producerFirstName = $producerNameParts[0];
        $producerLastName = $producerNameParts[1] ?? ''; // El segundo apellido es opcional

        // Buscar el productor en la base de datos
        $producer = User::where('name', $producerFirstName)
                        ->where('lastName', $producerLastName)
                        ->first();

        // Buscar el producto en la base de datos
        $product = Product::where('name', $productName)->first();

        // Buscar la entrada del producto en total_products (para obtener el stock actual)
        $totalProduct = DB::table('total_products')
                          ->where('productId', $product->id)
                          ->where('userId', $producer->id)
                          ->first();

        // Si no encontramos el producto en el inventario del productor, devolvemos error
        if (!$totalProduct) {
            return response()->json(['error' => 'Producto no encontrado en el inventario del productor.'], 404);
        }

        // Convertir la cantidad a kg según la unidad de medida
        $convertedQuantity = $this->convertToKg($quantity, $unit);

        // Sumar la cantidad eliminada al stock
        DB::table('total_products')
            ->where('productId', $product->id)
            ->where('userId', $producer->id)
            ->increment('stock', $convertedQuantity);

        // Eliminar el producto del carrito
        unset($cart[$index]);

        // Reindexar el carrito
        $cart = array_values($cart);

        // Guardar el carrito actualizado en la sesión
        session()->put('cart', $cart);
    }

    // Devolver el contenido HTML del carrito actualizado
    $cartHtml = view('partials.cart', ['cart' => $cart])->render();

    // Retornar el carrito actualizado y el HTML parcial
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

    // Calcular el total de la venta
    $total = array_reduce($cart, function ($carry, $item) {
        return $carry + $item['totalPrice'];
    }, 0);

    // Iniciar una transacción para asegurar la consistencia de los datos
    DB::beginTransaction();
    try {
        // Crear la venta en la tabla `sales`
        $sale = Sale::create([
            'customerId' => $customerId,
            'status' => 1, // Estado por defecto
            'total' => $total,
        ]);

        // Crear los detalles de la venta en `sale_details`
        foreach ($cart as $item) {
            // Separar el nombre completo del productor
            $producerNameParts = explode(' ', $item['producer_name']);
            $producerFirstName = $producerNameParts[0];
            $producerLastName = $producerNameParts[1] ?? ''; // Segundo apellido opcional

            // Buscar al productor
            $producer = User::where('name', $producerFirstName)
                            ->where('lastName', $producerLastName)
                            ->first();

            if (!$producer) {
                throw new \Exception('Productor no encontrado: ' . $item['producer_name']);
            }

            // Buscar el producto
            $product = Product::where('name', $item['product_name'])->first();
            if (!$product) {
                throw new \Exception('Producto no encontrado: ' . $item['product_name']);
            }

            // Crear el detalle de la venta
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

        // Limpiar el carrito
        session()->forget('cart');

        // Confirmar la transacción
        DB::commit();

        return response()->json(['message' => 'Compra finalizada con éxito.'], 200);
    } catch (\Exception $e) {
        // Revertir la transacción en caso de error
        DB::rollBack();
        return response()->json(['error' => $e->getMessage()], 500);
    }
}

}
