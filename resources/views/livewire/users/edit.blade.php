@extends('layouts.app')



@section('content')
<div class="container">
    
    <div class="d-flex justify-content-between align-items-center my-4">
        <h1 class="h3">Editar Usuario</h1>
        <a href="{{ route('users.index') }}" class="btn btn-secondary">Volver</a>
    </div>
    
    <div class="card border-success">
        <div class="card-header bg-success text-white">
            Formulario de Edición de Usuario
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

            <form id="userForm" action="{{ route('users.update', $user->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label for="name">Nombre</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="lastName">Primer Apellido</label>
                    <input type="text" name="lastName" id="lastName" value="{{ old('lastName', $user->lastName) }}" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="secondLastName">Segundo Apellido</label>
                    <input type="text" name="secondLastName" id="secondLastName" value="{{ old('secondLastName', $user->secondLastName) }}" class="form-control">
                </div>

                <div class="form-group">
                    <label for="email">Correo Electrónico</label>
                    <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="role">Rol</label>
                    <select name="role" id="role" class="form-control" required style="height: 45px;">
                        <option value="">{{ $user->role }}</option>
                        <option value="Administrador">Administrador</option>
                        <option value="Productor">Productor</option>
                        <option value="Cliente">Cliente</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="location">Ubicación</label>
                    <input type="text" name="location" id="location" value="{{ old('location', $user->location) }}" class="form-control">
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
                <p>Estás a punto de actualizar los datos del usuario con los siguientes detalles:</p>
                <ul>
                    <li><strong>Nombre:</strong> <span id="modalName"></span></li>
                    <li><strong>Primer Apellido:</strong> <span id="modalLastName"></span></li>
                    <li><strong>Segundo Apellido:</strong> <span id="modalSecondLastName"></span></li>
                    <li><strong>Correo Electrónico:</strong> <span id="modalEmail"></span></li>
                    <li><strong>Teléfono:</strong> <span id="modalPhone"></span></li>
                    <li><strong>Rol:</strong> <span id="modalRole"></span></li>
                    <li><strong>Ubicación:</strong> <span id="modalLocation"></span></li>
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
        const nameInput = document.getElementById('name');
        const lastNameInput = document.getElementById('lastName');
        const secondLastNameInput = document.getElementById('secondLastName');
        const emailInput = document.getElementById('email');
        const phoneInput = document.getElementById('phone');
        const roleInput = document.getElementById('role');
        const locationInput = document.getElementById('location');

        const modalName = document.getElementById('modalName');
        const modalLastName = document.getElementById('modalLastName');
        const modalSecondLastName = document.getElementById('modalSecondLastName');
        const modalEmail = document.getElementById('modalEmail');
        const modalPhone = document.getElementById('modalPhone');
        const modalRole = document.getElementById('modalRole');
        const modalLocation = document.getElementById('modalLocation');

        document.querySelector('[data-target="#confirmModal"]').addEventListener('click', function() {
            modalName.textContent = nameInput.value;
            modalLastName.textContent = lastNameInput.value;
            modalSecondLastName.textContent = secondLastNameInput.value;
            modalEmail.textContent = emailInput.value;
            modalPhone.textContent = phoneInput.value;
            modalRole.textContent = roleInput.options[roleInput.selectedIndex].text;
            modalLocation.textContent = locationInput.value;
        });

        document.getElementById('confirmButton').addEventListener('click', function() {
            document.getElementById('userForm').submit();
        });
    });
</script>
@endsection
