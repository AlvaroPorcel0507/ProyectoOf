@extends('layouts.app')



@section('content')
<div class="container">
    
    <div class="d-flex justify-content-between align-items-center my-4">
        <h1 class="h3">Editar Datos de Cliente</h1>
        <a href="{{ route('customers.index') }}" class="btn btn-secondary">Volver</a>
    </div>
    
    <div class="card border-success">
        <div class="card-header bg-success text-white">
            Formulario de Edición de Cliente
        </div>
        <div class="card-body">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form id="customerForm" action="{{ route('customers.update', $customer->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label for="name">Ci/Nit</label>
                    <input type="text" name="ciNit" id="ciNit" value="{{ old('ciNit', $customer->ciNit) }}" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="name">Rázon Social</label>
                    <input type="text" name="companyName" id="companyName" value="{{ old('companyName', $customer->companyName) }}" class="form-control" required>
                </div>

               <button type="button" class="btn btn-success mt-4" data-toggle="modal" data-target="#confirmModal">
                    Actualizar
                </button>
            </form>
        </div>
    </div>
</div>

<!-- Modal de Confirmación -->
<div class="modal fade" id="confirmModal" tabindex="-1" role="dialog" aria-labelledby="confirmModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content border-success">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="confirmModalLabel">Confirmar Actualización</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Estás a punto de actualizar los datos del Cliente con el siguiente detalle:</p>
                <ul>
                    <li><strong>CI/NIT:</strong> <span id="modalCiNit"></span></li>
                    <li><strong>Rázon Social:</strong> <span id="modalCompanyName"></span></li>
                </ul>
                <p>¿Estás seguro de que deseas continuar?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="confirmButton">Confirmar</button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ciNitInput = document.getElementById('ciNit');
        const companyNameInput = document.getElementById('companyName');

        const modalCiNit = document.getElementById('modalCiNit');
        const modalCompanyName = document.getElementById('modalCompanyName');

        document.querySelector('[data-target="#confirmModal"]').addEventListener('click', function() {
            modalName.textContent = nameInput.value;
        });

        document.getElementById('confirmButton').addEventListener('click', function() {
            document.getElementById('customerForm').submit();
        });
    });
</script>
@endsection