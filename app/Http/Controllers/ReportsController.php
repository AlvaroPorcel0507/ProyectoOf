<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Sale;
use App\Models\SaleDetail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
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

}
