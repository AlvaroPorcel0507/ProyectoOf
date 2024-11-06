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
            ->with('user')  // Obtener el productor relacionado
            ->get()
            ->groupBy(function($item) {
                // Agrupar por combinación de productId y userId para obtener datos por productor
                return $item->productId . '-' . $item->userId;
            });

        // Agrupar las cantidades y obtener el último precio unitario para cada grupo
        $inventory = $inventory->map(function($group) {
            $totalQuantity = $group->sum('quantity'); // Sumar las cantidades
            $unitPrice = $group->last()->unitPrice;  // Obtener el último precio unitario del grupo

            return [
                'user' => $group->first()->user,   // Obtener el productor del primer elemento del grupo
                'quantity' => $totalQuantity,      // Cantidad total por grupo
                'unitPrice' => $unitPrice          // Precio unitario
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
    
    // Asegúrate de que el carrito exista en la sesión
    $cart = session()->get('cart', []);
    
    // Verifica si el producto ya está en el carrito
    if (isset($cart[$productId])) {
        // Si el producto ya está en el carrito, actualiza la cantidad
        $cart[$productId]['quantity'] += $quantity;
        $cart[$productId]['totalProduct'] = $cart[$productId]['unitPrice'] * $cart[$productId]['quantity'];  // Actualizar el total del producto
    } else {
        // Si el producto no está en el carrito, agrégalo
        $cart[$productId] = [
            'name' => $productName,
            'quantity' => $quantity,
            'unitPrice' => $unitPrice,
            'totalProduct' => $unitPrice * $quantity,
            'producer_name' => $producerName,  // Agregar el nombre del productor
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

}
