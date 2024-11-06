<?php
namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\SaleDetail;
use App\Models\Product;
use App\Models\Category;
use App\Models\TotalProduct;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class SalesController extends Controller
{
    public function index()
    {
        // Recuperar todas las ventas activas con detalles de venta paginados
        $sales = Sale::with(['saleDetails.product']) // Cargar detalles de venta y productos asociados
            ->paginate(8);

        return view('livewire.sales.index', compact('sales'));
    }

    // Método show
    public function show($saleId)
    {
        $sale = Sale::find($saleId);

        if (!$sale) {
            return redirect()->back()->with('error', 'Venta no encontrada');
        }
        $saleDetail = SaleDetail::with(['product'])->where('salesId', $saleId)->get();

        return view('livewire.sales.saleDetail', compact('sale', 'saleDetail'));
    }

    public function create()
    {
        $categories = Category::where('status', 1)->get();
        $products = Product::where('status', 1)->get(); // Asegurarse de que el status es el correcto

        return view('livewire.sales.create', compact('categories', 'products'));
    }


    public function getStockByProduct($productId)
    {
        // Realizamos la consulta a la base de datos con las adaptaciones necesarias para SQLite
        $stocks = DB::table('total_products')
            ->join('users as u', 'total_products.userId', '=', 'u.id')
            ->join('products as p', 'total_products.productId', '=', 'p.id')
            ->select(
                DB::raw("u.name || ' ' || u.lastName as producer_name"), // Concatenamos nombre y apellido
                'p.unitPrice as unit_price', // Precio unitario desde productos
                'total_products.stock'       // Stock disponible
            )
            ->where('total_products.productId', $productId)
            ->where('u.role', 'Productor') // Asegurarse de que 'productor' esté en comillas simples
            ->get();

        // Verificamos si se encontraron datos y devolvemos el resultado en JSON
        if ($stocks->isEmpty()) {
            return response()->json(['message' => 'No se encontraron registros para el producto especificado'], 404);
        }

        return response()->json($stocks);
    }



    public function store(Request $request)
    {
        // Validar los datos recibidos
        $request->validate([
            'userId' => 'required|integer',
            'cart' => 'required|json', // Asegúrate de que el carrito sea un JSON válido
        ]);
    
        // Obtener el cliente autenticado
        $customerId = auth()->id();
    
        // Decodificar el carrito JSON a un array
        $cart = json_decode($request->cart, true);
    
        // Iniciar la transacción
        DB::beginTransaction();
    
        try {
            // Calcular el total de la venta
            $total = 0;
    
            // Crear la venta
            $sale = Sale::create([
                'userId' => $request->userId,
                'customerId' => $customerId,
                'total' => 0, // Inicialmente 0, se actualizará más tarde
            ]);
    
            // Procesar cada producto en el carrito
            foreach ($cart as $item) {
                // Verificar que quantity y price son números
                if (!isset($item['quantity'], $item['price']) || !is_numeric($item['quantity']) || !is_numeric($item['price'])) {
                    return redirect()->back()->withErrors(['error' => 'Cantidad o precio no válidos.']);
                }
    
                $quantity = (int) $item['quantity'];
                $unitPrice = (float) $item['price'];
                $totalProduct = $quantity * $unitPrice; // Calcular el total del producto
    
                // Asegurarse de que totalProduct no sea nulo o negativo
                if ($totalProduct <= 0) {
                    return redirect()->back()->withErrors(['error' => 'El total del producto debe ser mayor que cero.']);
                }
    
                // Buscar el producto por su ID
                $product = Product::find($item['id']);
                if (!$product) {
                    return redirect()->back()->withErrors(['error' => 'Producto no encontrado.']);
                }
    
                // Verificar si el producto tiene suficiente stock
                if ($product->stock < $quantity) {
                    return redirect()->back()->withErrors(['error' => 'No hay suficiente stock para el producto: ' . $product->name]);
                }
    
                // Crear el detalle de la venta
                SaleDetail::create([
                    'quantity' => $quantity,
                    'unitPrice' => $unitPrice,
                    'totalProduct' => $totalProduct, // Asegúrate de incluir este valor
                    'salesId' => $sale->id, // Relación con la venta
                    'productsId' => $product->id, // ID del producto
                ]);
    
                // Actualizar el stock del producto (no lo elimina)
                $product->stock -= $quantity; // Resta la cantidad vendida
                $product->save(); // Guarda los cambios en el producto
    
                // Sumar el total del producto al total de la venta
                $total += $totalProduct;
            }
    
            // Actualizar el total de la venta
            $sale->total = $total;
            $sale->save(); // Guarda el total actualizado
    
            // Confirmar la transacción
            DB::commit();
    
            return redirect()->route('sales.index')->with('success', 'Venta creada exitosamente.');
    
        } catch (\Exception $e) {
            // Revertir la transacción en caso de error
            DB::rollBack();
            // Puedes registrar el error si es necesario
            \Log::error('Error al crear la venta: ' . $e->getMessage());
            return redirect()->back()->withErrors(['error' => 'Ocurrió un error al crear la venta.']);
        }
    }
    
}

