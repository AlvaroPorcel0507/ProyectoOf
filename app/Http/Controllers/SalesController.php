<?php
namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\SaleDetail;
use App\Models\Products;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SalesController extends Controller
{
    public function index()
{
    // Recuperar todas las ventas activas con detalles de venta paginados
    $sales = Sale::with(['saleDetails.product']) // Cargar detalles de venta y productos asociados
        ->paginate(8);

    // Devolver a la vista 'sales.index' los datos de las ventas
// Suggested code may be subject to a license. Learn more: ~LicenseLog:830732890.
    return view('livewire/sales.index', compact('sales'));
}
public function create()
{
    // Obtener todos los clientes
    $customers = User::where('role', 'Cliente')->get(); // Ajusta esto según cómo determines a un cliente

    // Obtener todos los productos
    $products = Products::all();

    // Devolver la vista de creación de ventas con los datos necesarios
    return view('livewire.sales.create', compact('customers', 'products'));
}

public function store(Request $request)
{
    $request->validate([
        'customerId' => 'required|exists:users,id',
        'products' => 'required|array',
        'products.*.id' => 'required|exists:products,id',
        'products.*.quantity' => 'required|integer|min:1',
    ]);

    // Crear la venta
    $sale = new Sale();
    $sale->userId = Auth::id(); // Suponiendo que el usuario autenticado es quien realiza la venta
    $sale->customerId = $request->customerId;
    $sale->status = 1; // Estado activo
    $sale->total = 0; // Inicialmente el total es cero
    $sale->save();

    // Procesar los detalles de la venta
    foreach ($request->products as $product) {
        // Recuperar el producto
        $productModel = Products::find($product['id']);
        $totalProduct = $productModel->price * $product['quantity']; // Calcular el total por producto

        // Crear el detalle de la venta
        $saleDetail = new SaleDetail();
        $saleDetail->salesId = $sale->id;
        $saleDetail->productsId = $product['id'];
        $saleDetail->quantity = $product['quantity'];
        $saleDetail->unitPrice = $productModel->price; // Asegúrate de que este campo no sea nulo
        $saleDetail->totalProduct = $totalProduct;
        $saleDetail->save();

        // Actualizar el total de la venta
        $sale->total += $totalProduct;
    }

    // Guardar el total en la venta
    $sale->save();

    return redirect()->route('sales.index')->with('success', 'Venta registrada exitosamente.');
}

}

