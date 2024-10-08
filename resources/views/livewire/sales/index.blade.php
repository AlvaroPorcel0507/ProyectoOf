@extends('layouts.app')

@section('content')
<div class="container">
<div class="d-flex justify-content-between align-items-center my-4">
        <h1 class="h3 text-green-800">Lista de Compras</h1>
        <a href="{{ route('sales.create') }}" class="btn btn-success">
            Registrar Nueva Compra
        </a>
    </div>

    <div class="card border-success">
        <div class="card-header bg-success text-white">
            <i class="fas fa-cart-plus"></i> Compras
        </div>
        <div class="card-body bg-light">
            <div class="table-responsive">
                <table class="table table-hover align-middle text-center">
                    <thead>
                        <tr>
                            <th scope="col">Nro.</th>
                            <th scope="col">Productor</th>
                            <th scope="col">Total</th>
                            <th scope="col">Estado</th>
                            <th scope="col">Detalles</th>
                            <th scope="col">Editar</th>
                            <th scope="col">Eliminar</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $cont = 1;
                        @endphp
                        @foreach ($sales as $sale)
                            <tr>
                                <th scope="row">{{ $cont }}</th>
                                <td>{{ $sale->customer->name }}</td>
                                <td>{{ $sale->total }}</td>
                                <td>{{ $sale->status == 1 ? 'Activo' : 'Inactivo' }}</td>
                                <td>
                                    <ul>
                                        @foreach ($sale->saleDetails as $detail)
                                            <li>
                                                {{ $detail->product->name }} - 
                                                {{ $detail->quantity }} unidades 
                                                ({{ $detail->unitPrice }} cada uno)
                                            </li>
                                        @endforeach
                                    </ul>
                                </td>
                                <td>
                                  
                                </td>
                                <td>
                                  
                                </td>
                            </tr>
                            @php $cont++; @endphp
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Paginación -->
            <div class="d-flex justify-content-center">
                {{ $sales->links() }} <!-- Enlace a la paginación -->
            </div>
        </div>
    </div>
</div>
@endsection
