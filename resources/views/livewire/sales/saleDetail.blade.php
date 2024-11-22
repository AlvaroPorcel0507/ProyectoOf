@extends('layouts.app')
@section('content')
@php
 use App\Models\SaleDetail;
 use App\Models\Product;
 use App\Models\Category;
@endphp

<div class="container">
    <div class="d-flex justify-content-between align-items-center my-4">
        <h1 class="h3 text-green-800">Registro de Compras</h1>
        <a href="{{ route('sales.index') }}" class="btn btn-secondary">Volver</a>
    </div>

    <div class="card border-success">
        <div class="card-header bg-success text-white">
            <i class="fas fa-cart-plus"></i> Detalle de Compra
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
                        @php $cont = 1; @endphp

                        @foreach ($saleDetail as $detail)
                            @if ($sale->customerId == Auth::id()) <!-- Validar cliente autenticado -->
                                <tr>
                                    <th scope="row">{{ $cont }}</th>
                                    <td>{{ $detail->product->name ?? 'Producto no encontrado' }}</td>
                                    <td>{{ $detail->quantity }} {{ $detail->description }}</td>
                                    <td>{{ number_format($detail->unitPrice, 2) }} Bs</td>
                                    <td>{{ number_format($detail->totalProduct, 2) }} Bs</td>
                                </tr>
                                @php $cont++; @endphp
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@push('scripts')
@endpush
@endsection