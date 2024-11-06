<?php
namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\SaleDetail;
use App\Models\Product;
use App\Models\Category;
use App\Models\Inventory;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class SalesController extends Controller
{
    // Mostrar ventas
    public function index()
    {
        $sales = Sale::with(['saleDetails.product'])->paginate(8);
        return view('livewire.sales.index', compact('sales'));
    }

    // Mostrar detalles de venta
    public function show($saleId)
    {
        $sale = Sale::find($saleId);

        if (!$sale) {
            return redirect()->back()->with('error', 'Venta no encontrada');
        }
        $saleDetail = SaleDetail::with(['product'])->where('salesId', $saleId)->get();

        return view('livewire.sales.saleDetail', compact('sale', 'saleDetail'));
    }

    public function create(Request $request)
    {
        // Obtener categorías activas (status = 1)
        $categories = Category::where('status', 1)->get();
    
        // Filtrar productos activos (status = 1) y por categoría si se pasa un filtro
        $products = Product::where('status', 1); // Solo productos activos
        if ($request->has('categoryId') && $request->categoryId != '') {
            // Si hay una categoría seleccionada, filtrar productos por esa categoría
            $products = $products->where('categoryId', $request->categoryId);
        }
        $products = $products->get();
    
        // Obtener inventarios agrupados por producto y productor, solo si se pasa un producto seleccionado
        $inventory = [];
        if ($request->has('productId') && $request->productId != '') {
            $inventory = Inventory::where('productId', $request->productId)
                ->with('user', 'product')  // Obtener el productor y el producto relacionado
                ->get()
                ->groupBy(function($item) {
                    // Agrupar por combinación de productId y userId para obtener datos por productor
                    return $item->productId . '-' . $item->userId;
                });
    
            // Agrupar las cantidades y obtener el último precio unitario para cada grupo
            $inventory = $inventory->map(function($group) {
                $totalQuantity = $group->sum('quantity'); // Sumar las cantidades
                $unitPrice = $group->last()->unitPrice;  // Obtener el último precio unitario del grupo
    
                // Incluir todos los detalles requeridos en el resultado
                return [
                    'product' => $group->first()->product,  // Producto completo
                    'user' => $group->first()->user,       // Productor (usuario)
                    'quantity' => $totalQuantity,          // Cantidad total disponible por productor
                    'unitPrice' => $unitPrice              // Precio unitario
                ];
            });
        }
    
        return view('livewire.sales.create', [
            'categories' => $categories,  // Categorías activas
            'products' => $products,      // Productos activos filtrados
            'inventory' => $inventory,    // Inventarios agrupados por producto y productor
        ]);
    }
    


    public function addToCart(Request $request)
{
    $productId = $request->input('productId');
    $quantity = $request->input('quantity');
    $unitPrice = $request->input('unitPrice');
    $productName = $request->input('productName');  // Nombre del producto
    $producerName = $request->input('producerName');  // Nombre del productor
    $producerId = $request->input('producerId');  // ID del productor

    // Asegúrate de que el carrito exista en la sesión
    $cart = session()->get('cart', []);

    // Crea una clave única para el producto basado en productId y producerId
    $cartKey = $productId . '-' . $producerId;

    // Verifica si el producto ya está en el carrito
    if (isset($cart[$cartKey])) {
        // Si el producto ya está en el carrito, actualiza la cantidad
        $cart[$cartKey]['quantity'] += $quantity;
        $cart[$cartKey]['totalProduct'] = $cart[$cartKey]['unitPrice'] * $cart[$cartKey]['quantity'];  // Actualizar el total del producto
    } else {
        // Si el producto no está en el carrito, agrégalo
        $cart[$cartKey] = [
            'name' => $productName,
            'quantity' => $quantity,
            'unitPrice' => $unitPrice,
            'totalProduct' => $unitPrice * $quantity,
            'producer_name' => $producerName,  // Nombre del productor
            'producer_id' => $producerId,  // ID del productor
        ];
    }

    // Guarda el carrito actualizado en la sesión
    session()->put('cart', $cart);

    return redirect()->route('sales.create')->with('success', 'Producto agregado al carrito');
}

    




    


   // Método para eliminar un producto del carrito
public function removeFromCart(Request $request)
{
    $productId = $request->input('productId');

    // Obtén el carrito de la sesión
    $cart = session()->get('cart', []);

    // Elimina el producto del carrito si existe
    if (isset($cart[$productId])) {
        unset($cart[$productId]);
    }

    // Guarda el carrito actualizado en la sesión
    session()->put('cart', $cart);

    return redirect()->route('sales.create')->with('success', 'Producto eliminado del carrito');
}
// Método para actualizar la cantidad de un producto en el carrito
public function updateQuantity(Request $request)
{
    $productId = $request->input('productId');
    $newQuantity = $request->input('quantity');

    // Obtén el carrito de la sesión
    $cart = session()->get('cart', []);

    // Verifica si el producto está en el carrito
    if (isset($cart[$productId])) {
        // Actualiza la cantidad del producto
        $cart[$productId]['quantity'] = $newQuantity;
        $cart[$productId]['totalProduct'] = $cart[$productId]['unitPrice'] * $newQuantity;  // Recalcular el total
    }

    // Guarda el carrito actualizado en la sesión
    session()->put('cart', $cart);

    return redirect()->route('sales.create')->with('success', 'Cantidad actualizada');
}
public function processSale(Request $request)
{
    $cart = $request->input('cart'); // Obtener los productos del carrito

    // Si el carrito está vacío, muestra el error y termina la ejecución
    if (empty($cart)) {
        return redirect()->route('sales.create')->with('error', 'No hay productos en el carrito para procesar la venta.');
    }

    // Obtener el cliente que está realizando la compra (puedes obtenerlo desde el usuario autenticado)
    $customerId = auth()->user()->id;

    // Calcular el total de la venta
    $totalVenta = 0;
    foreach ($cart as $item) {
        $totalVenta += $item['totalProduct']; // Sumar los totales de cada producto
    }

    // Iniciar la transacción para asegurar la integridad
    DB::beginTransaction();

    try {
        // Crear el registro de la venta
        $sale = Sale::create([
            'customerId' => $customerId,  // El cliente que hace la compra
            'status' => 1,  // Estado de la venta (1 = pendiente)
            'total' => $totalVenta,
        ]);

        // Procesar cada producto del carrito y agregarlo a sale_details
        foreach ($cart as $item) {
            // Tomar solo la primera parte del productId (antes del '-')
            $productId = explode('-', $item['productId'])[0]; // Tomamos la primera parte
            $producerId = $item['producerId']; // Asigna el producerId

            // Verificar que el producto exista
            $product = Product::find($productId);
            $producer = User::find($producerId);

            if ($product && $producer) {
                // Crear el detalle de la venta sin afectar el stock por ahora
                SaleDetail::create([
                    'salesId' => $sale->id,
                    'productsId' => $productId,
                    'producerId' => $producerId,
                    'quantity' => $item['quantity'],
                    'unitPrice' => $item['unitPrice'],
                    'totalProduct' => $item['totalProduct'],
                    'description' => $item['productName'], // Usar el nombre del producto o una descripción
                ]);
            } else {
                return redirect()->route('sales.create')->with('error', 'Uno o más productos no fueron encontrados.');
            }
        }

        // Commit de la transacción
        DB::commit();

        // Limpiar el carrito después de la venta
        session()->forget('cart');

        return redirect()->route('sales.create')->with('success', 'Venta procesada con éxito.');
    } catch (\Exception $e) {
        // Rollback en caso de error
        DB::rollback();

        return redirect()->route('sales.create')->with('error', 'Hubo un problema al procesar la venta. Intenta nuevamente.');
    }
}

}
