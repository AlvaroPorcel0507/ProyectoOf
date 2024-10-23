@extends('layouts.app')
@section('content')
@php
 use App\Models\SaleDetail;
 use App\Models\Product;
 use App\Models\Category;
@endphp
<!-----------------------------VISTA ADMINSITRADOR --------------------------------- -->

<div class="container">
    <div class="d-flex justify-content-between align-items-center my-4">
        <h1 class="h3 text-green-800">Ventas</h1>
        <a href="{{ route('sales.index') }}" class="btn btn-secondary">Volver</a>
    </div>

    <div class="card border-success">
        <div class="card-header bg-success text-white">
            <i class="fas fa-cart-plus"></i> Ventas
        </div>
        <div class="card-body bg-light">
            <div class="table-responsive">
                <table class="table table-hover align-middle text-center">
                    <thead>
                        <tr>
                            <th scope="col">Nro.</th>
                            <th scope="col">Nombre Producto</th>
                            <th scope="col">Cantidad Kgs.</th>
                            <th scope="col">Precio Unitario</th>
                            <th scope="col">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $cont=1; @endphp
                            <tr>
                                <th scope="row">{{ $cont }}</th>
                                <td>{{ optional(SaleDetail::find($sale->id))->product->name }}</td>
                                <td>{{ optional(SaleDetail::find($sale->id))->quantity }}</td>
                                <td>{{ optional(SaleDetail::find($sale->id))->unitPrice }}</td>
                                <td>{{ optional(SaleDetail::find($sale->id))->totalProduct }}</td>
                            </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@push('scripts')
@endpush
@endsection