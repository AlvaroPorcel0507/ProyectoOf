<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Sale;
use App\Models\SaleDetail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use App\Models\Product;

class ReportsController extends Controller
{
    public function index(Request $request)
    {
        // Obtener las fechas de inicio y fin
        $startDate = $request->input('startDate');
        $endDate = $request->input('endDate');

        // Asegúrate de que las fechas estén en formato adecuado (puedes hacer validaciones adicionales si es necesario)
        $startDate = Carbon::parse($startDate)->startOfDay();
        $endDate = Carbon::parse($endDate)->endOfDay();

        // Consulta para obtener los tres productos más vendidos en el rango de fechas
        $topProducts = SaleDetail::whereHas('sale', function ($query) use ($startDate, $endDate) {
            // Filtrar las ventas dentro del rango de fechas
            $query->whereBetween('created_at', [$startDate, $endDate]);
        })
        ->select('productsId', DB::raw('SUM(quantity) as total_sold'))
        ->groupBy('productsId')
        ->orderByDesc('total_sold')
        ->limit(3)
        ->with('product') // Relacionar con el modelo Product para obtener los detalles del producto
        ->get();

        // Retornar los resultados a la vista
        return view('livewire.reports.index', compact('topProducts'));
    }

}
