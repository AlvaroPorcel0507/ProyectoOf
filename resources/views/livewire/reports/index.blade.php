@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center my-4">
        <h1 class="h3 text-green-800">Reportes</h1>
    </div>

    <div class="card border-success">
        <div class="card-header bg-success text-white">
            <i class="fas fa-file-text"></i> Reporte de Ventas
        </div>

        <div class="card-body">
            <form wire:submit.prevent="index" class="form-control">
                <div class="row mb-3">
                    <h2>Determine un rango de fechas</h2>
                    <div class="col-lg-6">
                        <label for="startDate">Fecha Inicio</label>
                        <input type="date" wire:model="startDate" id="startDate" class="form-control" />
                    </div>
                    <div class="col-lg-6">
                        <label for="endDate">Fecha Fin</label>
                        <input type="date" wire:model="endDate" id="endDate" class="form-control" />
                    </div>
                </div>
                <div class="row">
                    <button type="submit" class="btn btn-primary">Generar Reporte</button>
                </div>
            </form>
        </div>

        <div class="card-body bg-light">
            @if($sales->isNotEmpty())
                <div class="table-responsive">
                    <table class="table table-hover align-middle text-center">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Producto</th>
                                <th>Cantidad Vendida</th>
                                <th>Precio Unitario</th>
                                <th>Total</th>
                                <th>Fecha</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($sales as $sale)
                                @foreach($sale->saleDetails as $detail)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $detail->product->name }}</td>
                                        <td>{{ $detail->quantity }}</td>
                                        <td>{{ number_format($detail->unitPrice, 2) }}</td>
                                        <td>{{ number_format($detail->totalProduct, 2) }}</td>
                                        <td>{{ $sale->created_at->format('d-m-Y') }}</td>
                                    </tr>
                                @endforeach
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="alert alert-warning" role="alert">
                    No hay ventas en el rango de fechas seleccionado.
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
