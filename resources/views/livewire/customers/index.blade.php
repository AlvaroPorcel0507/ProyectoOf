@extends('layouts.app')

@section('content')
@php
 use App\Models\Customers;
@endphp

<div class="container">
    <div class="d-flex justify-content-between align-items-center my-4">
        <h1 class="h3 text-green-800">Clientes</h1>
        <a href="{{ route('customers.create') }}" class="btn btn-success">
            Registrar Nuevo Cliente
        </a>
    </div>

    <div class="card border-success">
        <div class="card-header bg-success text-white">
            <i class="fas fa-users"></i> Clientes
        </div>
        <div class="card-body bg-light">
            <div class="table-responsive">
                <table class="table table-hover align-middle text-center">
                    <thead>
                        <tr>
                            <th scope="col">Nro.</th>
                            <th scope="col">CI/NIT</th>
                            <th scope="col">Razón Social</th>
                            <th scope="col">Editar</th>
                            <th scope="col">Eliminar</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                        $cont=1;
                        @endphp
                        @foreach ($customers as $customer)
                        
                            <tr>
                                <th scope="row">{{ $cont }}</th>
                                <td>{{ $customer->ciNit }}</td>
                                <td>{{ $customer->companyName }}</td>
                                <td>
                                    <a href="{{ route('customers.edit', $customer->id) }}" class="btn btn-info">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#toggleStatusModal" data-customer-id="{{ $customer->id }}" data-customer-ciNit="{{ $customer->ciNit }}" data-customer-companyName="{{ $customer->companyName }}" data-customer-status="{{ $customer->status }}">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            @php $cont++; @endphp
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Paginación -->
            <div class="d-flex justify-content-center mt-4">
                {{ $customers->appends(['sort_field' => $sortField, 'sort_direction' => $sortDirection])->links() }}
            </div>
        </div>
    </div>
</div>

<!-- Modal de Cambio de Estado -->
<div class="modal fade" id="toggleStatusModal" tabindex="-1" aria-labelledby="toggleStatusModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content border-success">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="toggleStatusModalLabel">Confirmar</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                ¿Estás seguro de que deseas eliminar al cliente <strong id="companyName"></strong>? Esta acción elimiara al cliente.
            </div>
            <div class="modal-footer">
                <form id="toggleStatusFormCustomers" action="" method="POST">
                    @csrf
                    @method('PUT')
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-warning">Confirmar</button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var toggleStatusModal = document.getElementById('toggleStatusModal');
        toggleStatusModal.addEventListener('show.bs.modal', function (event) {
            var button = event.relatedTarget; 
            var customerId = button.getAttribute('data-customer-id'); 
            var customerCiNit = button.getAttribute('data-customer-ciNit'); 
            var customerCompanyName = button.getAttribute('data-customer-companyName');
            var customerStatus = button.getAttribute('data-customer-status'); 
            var form = toggleStatusModal.querySelector('#toggleStatusFormCustomers');
            form.action = '/customers/' + customerId + '/softDelete';

            var actionText = categoryStatus == 1 ? 'deshabilitar' : 'habilitar';
            var toggleStatusActionElement = document.getElementById('toggleStatusAction');
            toggleStatusActionElement.textContent = actionText;

            var userNameElement = document.getElementById('companyName');
            userNameElement.textContent = userName;
        });
    });
</script>
@endpush
@endsection