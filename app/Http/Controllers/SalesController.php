<?php

namespace App\Http\Controllers;

use App\Models\Sales;
use App\Models\User;
use Illuminate\Http\Request;

class SalesController extends Controller
{
    public function index(Request $request)
    {
        $sortField = $request->input('sort_field', 'id');
        $sortDirection = $request->input('sort_direction', 'asc');

        // Asegúrate de que el campo de ordenación sea uno de los campos permitidos
        $validSortFields = ['id', 'date', 'status', 'userId'];
        if (!in_array($sortField, $validSortFields)) {
            $sortField = 'id';
        }

        $sales = Sales::where('status', '>', 0)->orderBy($sortField, $sortDirection)->paginate(8);
        $customers = User::where('status',1);
        dd(['customers' => $customers]);

        return view('livewire/sales.index', compact('sales', 'customers', 'sortField', 'sortDirection'));
    }
}
