@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center my-4">
        <h1 class="h3">Registrar Nuevo Cliente</h1>
        <a href="{{ route('customers.index') }}" class="btn btn-secondary">Volver</a>
    </div>

    <div class="card">
        <div class="card-header">
            Añadir Cliente
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

            <form id="customersForm" action="{{ route('customers.store') }}" method="POST">
                @csrf

                <div class="form-group">
                    <label for="name">CI/NIT</label>
                    <input type="text" name="ciNit" id="ciNit" class="form-control" value="{{ old('ciNit') }}" required>
                </div>
                <div class="form-group">
                    <label for="name">Rázon Social</label>
                    <input type="text" name="companyName" id="companyName" class="form-control" value="{{ old('commpanyName') }}" required>
                </div>

                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#confirmModal">
                    Registrar
                </button>
            </form>
        </div>
    </div>
</div>

<!-- Modal de Confirmación -->
<div class="modal fade" id="confirmModal" tabindex="-1" role="dialog" aria-labelledby="confirmModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmModalLabel">Confirmar Registro</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Estás a punto de habilitar un nuevo cliente con los siguientes datos:</p>
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
        // Cargar datos en el modal cuando se hace clic en "Registrar"
        const ciNitInput = document.getElementById('ciNit');
        const companyNameInput = document.getElementById('companyName');

        const modalCiNit = document.getElementById('modalCiNit');
        const modalCompanyName = document.getElementById('modalCompanyName');

        document.querySelector('[data-target="#confirmModal"]').addEventListener('click', function() {
            modalCiNit.textContent = ciNitInput.value;
            modalCompanyName.textContent = companyNameInput.value;
        });

        // Enviar el formulario al confirmar
        document.getElementById('confirmButton').addEventListener('click', function() {
            document.getElementById('customersForm').submit();
        });
    });
</script>

@endsection