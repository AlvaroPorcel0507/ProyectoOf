<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Sale;
use App\Models\SaleDetail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use App\Models\Product;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportsController extends Controller
{
    public function index(Request $request)
    {
        // Obtener y formatear las fechas de inicio y fin
        $startDate = $request->input('start_date') ? Carbon::parse($request->input('start_date'))->startOfDay() : now()->startOfMonth();
        $endDate = $request->input('end_date') ? Carbon::parse($request->input('end_date'))->endOfDay() : now()->endOfDay();

        // Obtener los 5 productos más vendidos en el rango de fechas seleccionado
        $topProducts = DB::table('products')
            ->join('sale_details', 'products.id', '=', 'sale_details.productsId') // Asegurarse de que los campos coincidan
            ->join('sales', 'sales.id', '=', 'sale_details.id') // Asegurarse de que los campos coincidan
            ->select('products.name', DB::raw('SUM(sale_details.quantity) as total_sold'), DB::raw('SUM(sale_details.quantity * sale_details.unitPrice) as total_revenue'))
            ->whereBetween('sales.created_at', [$startDate, $endDate])
            ->groupBy('products.name')
            ->orderByDesc('total_sold')
            ->limit(5)
            ->get();

        // Renderizar la vista con los datos obtenidos
        return view('livewire.reports.index', compact('topProducts', 'startDate', 'endDate'));
    }

public function generatePDF(Request $request)
{
    // Obtener los productos más vendidos
    $startDate = $request->input('start_date') ? Carbon::parse($request->input('start_date'))->startOfDay() : now()->startOfMonth();
    $endDate = $request->input('end_date') ? Carbon::parse($request->input('end_date'))->endOfDay() : now()->endOfDay();

    $topProducts = DB::table('products')
        ->join('sale_details', 'products.id', '=', 'sale_details.productsId')
        ->join('sales', 'sales.id', '=', 'sale_details.id')
        ->select('products.name', DB::raw('SUM(sale_details.quantity) as total_sold'), DB::raw('SUM(sale_details.quantity * sale_details.unitPrice) as total_revenue'))
        ->whereBetween('sales.created_at', [$startDate, $endDate])
        ->groupBy('products.name')
        ->orderByDesc('total_sold')
        ->limit(5)
        ->get();

    // Generar el PDF
    $pdf = PDF::loadView('livewire/reports.report', compact('topProducts', 'startDate', 'endDate'));

    // Devolver el PDF al navegador o descargarlo
    return $pdf->download('productos_mas_vendidos.pdf');
}

    public function saleIndex(Request $request)
    {
        // Obtener y formatear las fechas de inicio y fin
        $startDate = $request->input('start_date') ? Carbon::parse($request->input('start_date'))->startOfDay() : now()->startOfMonth();
        $endDate = $request->input('end_date') ? Carbon::parse($request->input('end_date'))->endOfDay() : now()->endOfDay();
    
        // Obtener el detalle general de todos los productos vendidos en el rango de fechas
        $productSales = DB::table('products')
            ->leftJoin('sale_details', 'products.id', '=', 'sale_details.productsId')
            ->leftJoin('sales', 'sales.id', '=', 'sale_details.salesId')
            ->select(
                'products.name',
                DB::raw('COALESCE(SUM(sale_details.quantity), 0) as total_sold'),
                DB::raw('COALESCE(SUM(sale_details.quantity * sale_details.unitPrice), 0) as total_revenue')
            )
            ->where(function ($query) use ($startDate, $endDate) {
                $query->whereBetween('sales.created_at', [$startDate, $endDate])
                      ->orWhereNull('sales.created_at');
            })
            ->groupBy('products.name')
            ->orderBy('products.name')
            ->get();
    
        // Renderizar la vista con los datos obtenidos
        return view('livewire.reports.indexSale', compact('productSales', 'startDate', 'endDate'));
    }

    public function generateSalePDF(Request $request)
    {
        // Obtener el detalle general de todos los productos vendidos
        $startDate = $request->input('start_date') ? Carbon::parse($request->input('start_date'))->startOfDay() : now()->startOfMonth();
        $endDate = $request->input('end_date') ? Carbon::parse($request->input('end_date'))->endOfDay() : now()->endOfDay();
    
        $allProducts = DB::table('products')
            ->leftJoin('sale_details', 'products.id', '=', 'sale_details.productsId')
            ->leftJoin('sales', 'sales.id', '=', 'sale_details.salesId')
            ->select(
                'products.name',
                DB::raw('COALESCE(SUM(sale_details.quantity), 0) as total_sold'),
                DB::raw('COALESCE(SUM(sale_details.quantity * sale_details.unitPrice), 0) as total_revenue')
            )
            ->where(function ($query) use ($startDate, $endDate) {
                $query->whereBetween('sales.created_at', [$startDate, $endDate])
                      ->orWhereNull('sales.created_at');
            })
            ->groupBy('products.name')
            ->orderBy('products.name')
            ->get();
    
        // Generar el PDF
        $pdf = PDF::loadView('livewire/reports.reportSale', compact('allProducts', 'startDate', 'endDate'));
    
        // Devolver el PDF al navegador o descargarlo
        return $pdf->download('detalle_general_productos.pdf');
    }

    public function producerIndex(Request $request)
{
    // Obtener y formatear las fechas de inicio y fin
    $startDate = $request->input('start_date') 
        ? Carbon::parse($request->input('start_date'))->startOfDay() 
        : now()->startOfMonth();
    $endDate = $request->input('end_date') 
        ? Carbon::parse($request->input('end_date'))->endOfDay() 
        : now()->endOfDay();

    // Obtener productores para el filtro
    $producers = DB::table('users')
        ->where('role', 'Productor') // Filtrar productores
        ->select('id', 'name', 'lastName', 'secondLastName')
        ->get();

    // Obtener el productor seleccionado (si se selecciona)
    $producerId = $request->input('producer_id');

    // Consultar las ventas por producto
    $productSales = DB::table('products')
        ->join('sale_details', 'products.id', '=', 'sale_details.productsId')
        ->join('sales', 'sales.id', '=', 'sale_details.salesId')
        ->select(
            'products.name',
            DB::raw('COALESCE(SUM(sale_details.quantity), 0) as total_sold'),
            DB::raw('COALESCE(SUM(sale_details.quantity * sale_details.unitPrice), 0) as total_revenue')
        )
        ->when($producerId, function ($query) use ($producerId) {
            return $query->where('sale_details.producerId', $producerId);
        })
        ->whereBetween('sales.created_at', [$startDate, $endDate])
        ->groupBy('products.name')
        ->orderBy('products.name', 'asc')
        ->get();

    return view('livewire.reports.indexProducer', compact('productSales', 'producers', 'producerId', 'startDate', 'endDate'));
}

public function generateSaleProducerPDF(Request $request)
{
    // Fechas para el rango
    $startDate = $request->input('start_date') 
        ? Carbon::parse($request->input('start_date'))->startOfDay() 
        : now()->startOfMonth();
    $endDate = $request->input('end_date') 
        ? Carbon::parse($request->input('end_date'))->endOfDay() 
        : now()->endOfDay();

    // Filtro de productor (si aplica)
    $producerId = $request->input('producer_id');

    // Consultar las ventas por producto
    $productSales = DB::table('products')
        ->join('sale_details', 'products.id', '=', 'sale_details.productsId')
        ->join('sales', 'sales.id', '=', 'sale_details.salesId')
        ->select(
            'products.name',
            DB::raw('COALESCE(SUM(sale_details.quantity), 0) as total_sold'),
            DB::raw('COALESCE(SUM(sale_details.quantity * sale_details.unitPrice), 0) as total_revenue')
        )
        ->when($producerId, function ($query) use ($producerId) {
            return $query->where('sale_details.producerId', $producerId);
        })
        ->whereBetween('sales.created_at', [$startDate, $endDate])
        ->groupBy('products.name')
        ->orderBy('products.name', 'asc')
        ->get();

    $producerName = null;
    if ($producerId) {
        $producer = DB::table('users')
            ->where('id', $producerId)
            ->select('name', 'lastName', 'secondLastName')
            ->first();
        $producerName = $producer ? $producer->name.' '.$producer->lastName.' '.$producer->secondLastName : 'Productor no encontrado';
    }

    // Calcular el total global del monto vendido
    $totalRevenue = $productSales->sum('total_revenue');

    // Generar el PDF
    $pdf = PDF::loadView('livewire/reports.reportProducer', compact('productSales', 'producerName', 'startDate', 'endDate', 'totalRevenue'));

    // Devolver el PDF descargable
    return $pdf->download('detalle_general_productos_vendidos.pdf');
}

    public function generateforProducerPDF(Request $request)
    {
        // Fechas para el rango
        $startDate = $request->input('start_date') 
            ? Carbon::parse($request->input('start_date'))->startOfDay() 
            : now()->startOfMonth()->startOfDay(); // Inicio del mes si no hay fecha de inicio
        $endDate = $request->input('end_date') 
            ? Carbon::parse($request->input('end_date'))->endOfDay() 
            : now()->endOfDay(); // Fin del día actual si no hay fecha de fin

        // ID del productor autenticado
        $producerId = Auth::id();

        // Consultar ventas detalladas por producto del productor autenticado
        $detailedSales = DB::table('sales')
            ->join('sale_details', 'sales.id', '=', 'sale_details.salesId')
            ->join('products', 'sale_details.productsId', '=', 'products.id')
            ->select(
                'products.name as product_name',
                'sale_details.quantity',
                'sale_details.unitPrice',
                DB::raw('(sale_details.quantity * sale_details.unitPrice) as total_price'),
                'sales.created_at'
            )
            ->where('sale_details.producerId', $producerId) // Solo ventas del productor autenticado
            ->whereBetween('sales.created_at', [$startDate, $endDate]) // Rango de fechas
            ->orderBy('sales.created_at', 'desc')
            ->get();

        // Calcular el total global de las ventas
        $totalRevenue = $detailedSales->sum('total_price');

        // Generar el PDF
        $pdf = PDF::loadView('livewire/reports.reportForProducer', compact(
            'detailedSales', 'startDate', 'endDate', 'totalRevenue'
        ));

        // Devolver el PDF descargable
        return $pdf->download('mis_ventas.pdf');
    }

    public function generateReceiptPDF(Request $request)
    {
        // ID de la venta que se pasa como parámetro
        $saleId = $request->input('producer_id'); // Aquí recuperamos el ID de la venta

        // Consultar los detalles de la venta específica
        $saleDetails = DB::table('sale_details')
            ->join('sales', 'sale_details.salesId', '=', 'sales.id')
            ->join('products', 'sale_details.productsId', '=', 'products.id')
            ->select(
                'products.name as product_name',
                'sale_details.quantity',
                'sale_details.unitPrice',
                'sale_details.totalProduct',
                'sale_details.description',
                'sales.created_at as sale_date',
                'sale_details.salesId'
            )
            ->where('sale_details.salesId', $saleId) // Filtramos por la venta específica
            ->where('sales.customerId', Auth::id()) // Solo detalles de la venta del cliente autenticado
            ->get();

        // Verificar si existen detalles de la venta
        if ($saleDetails->isEmpty()) {
            return redirect()->route('sales.index')->with('error', 'No se encontraron detalles para esta venta.');
        }

        // Calcular el total global de la venta
        $totalSale = $saleDetails->sum('totalProduct');

        // Generar el PDF con los detalles de la venta
        $pdf = PDF::loadView('livewire/reports.reportReceipt', compact(
            'saleDetails', 'totalSale'
        ));

        // Devolver el PDF descargable
        return $pdf->download('recibo_compra_' . $saleId . '.pdf');
    }
}
