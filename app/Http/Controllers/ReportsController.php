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

        return view('livewire.reports.index', compact('topProducts', 'startDate', 'endDate'));
    }

public function generatePDF(Request $request)
{
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

    $pdf = PDF::loadView('livewire/reports.report', compact('topProducts', 'startDate', 'endDate'));

    return $pdf->download('productos_mas_vendidos.pdf');
}

    public function saleIndex(Request $request)
    {
        $startDate = $request->input('start_date') ? Carbon::parse($request->input('start_date'))->startOfDay() : now()->startOfMonth();
        $endDate = $request->input('end_date') ? Carbon::parse($request->input('end_date'))->endOfDay() : now()->endOfDay();
    
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
    
        return view('livewire.reports.indexSale', compact('productSales', 'startDate', 'endDate'));
    }

    public function generateSalePDF(Request $request)
    {
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
    
        $pdf = PDF::loadView('livewire/reports.reportSale', compact('allProducts', 'startDate', 'endDate'));
    
        return $pdf->download('detalle_general_productos.pdf');
    }

    public function producerIndex(Request $request)
{
    $startDate = $request->input('start_date') 
        ? Carbon::parse($request->input('start_date'))->startOfDay() 
        : now()->startOfMonth();
    $endDate = $request->input('end_date') 
        ? Carbon::parse($request->input('end_date'))->endOfDay() 
        : now()->endOfDay();

    $producers = DB::table('users')
        ->where('role', 'Productor') 
        ->select('id', 'name', 'lastName', 'secondLastName')
        ->get();

    $producerId = $request->input('producer_id');

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
    $startDate = $request->input('start_date') 
        ? Carbon::parse($request->input('start_date'))->startOfDay() 
        : now()->startOfMonth();
    $endDate = $request->input('end_date') 
        ? Carbon::parse($request->input('end_date'))->endOfDay() 
        : now()->endOfDay();

    $producerId = $request->input('producer_id');

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

    $totalRevenue = $productSales->sum('total_revenue');

    $pdf = PDF::loadView('livewire/reports.reportProducer', compact('productSales', 'producerName', 'startDate', 'endDate', 'totalRevenue'));

    return $pdf->download('detalle_general_productos_vendidos.pdf');
}
}
