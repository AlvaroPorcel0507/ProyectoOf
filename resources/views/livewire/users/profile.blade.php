@extends('layouts.app')

@section('content')
@php
 use App\Models\User;
@endphp
<div class="container">
    <div class="d-flex justify-content-between align-items-center my-4">
        <h1 class="h3 text-green-800">Información de Perfil</h1>
    </div>

    <div class="card border-success mb-4">
        <div class="card-header bg-success text-white">
            <i class="fas fa-user"></i> Perfil de Usuario
        </div>
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
            <!-- Imagen de perfil e información -->
            <div class="d-flex align-items-center mb-4">
                <!-- Imagen de perfil -->
                <img src="https://via.placeholder.com/80" class="rounded-circle" alt="Foto de perfil" width="80">
                <div class="ms-3">
                    <h2 class="h4">{{ $user->name." ".$user->lastName." ".$user->secondLastName }}</h2>
                    <p class="text-muted mb-0">{{ $user->role }}</p>
                </div>
            </div>
            
            <!-- Botón al otro extremo -->
                <div>
                    <button type="button" id="cancel" class="btn btn-danger hidden" onclick="hideElement()">
                        Cancelar Edición
                    </button>
                </div>
            </div>


            <!-- Formulario de actualización de perfil -->
            <form action="#" method="POST">
                @csrf
                @method('PUT')
                
                <div class="form-group mb-3">
                    <label for="email">Correo Electrónico</label>
                    <input type="email" id="email" name="email" class="form-control" value="{{ $user->email }}" disabled required>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="name">Nombre</label>
                            <input type="text" id="name" name="name" class="form-control" value="{{ $user->name }}" disabled required>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="lastName">Primer Apellido</label>
                            <input type="text" id="lastName" name="lastName" class="form-control" value="{{ $user->lastName }}" disabled required>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="secondLastName2">Segundo Apellido</label>
                            <input type="text" id="secondLastName" name="secondLastName" class="form-control" value="{{ $user->secondLastName }}" disabled required>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group mb-3"> 
                            <label for="role">Rol</label>
                            <select name="role" id="role" class="form-control" disabled>
                                <option value="{{ $user->role }}">{{ $user->role }}</option>
                                <option value="Administador">Administador</option>
                                <option value="Productor">Productor</option>
                                <option value="Cliente">Cliente</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="form-group mb-3">
                    <label for="location">Dirección</label>
                    <input type="text" id="location" name="location" class="form-control" value="{{ $user->location }}" disabled>
                </div>

                <div class="form-group">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <button onclick="enableFields()" type="button" class="btn btn-success w-100">Editar Perfil</button>
                            </div>
                        </div>
                        <form action="{{ route('users.profileUpdate', $user->id) }}">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <button id="updateProfile" type="submit" class="btn btn-dark w-100" disabled>Actualizar Perfil</button>
                            </div>
                        </div>
                        </form>
                    </div>
                    
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function enableFields() {
        document.getElementById('email').disabled = false;
        document.getElementById('name').disabled = false;
        document.getElementById('lastName').disabled = false;
        document.getElementById('secondLastName').disabled = false;
        document.getElementById('role').disabled = false;
        document.getElementById('location').disabled = false;
        document.getElementById('updateProfile').disabled = false;

        var element = document.getElementById('cancel');
        element.classList.toggle('hidden'); 
    }

    function hideElement() {
        document.getElementById('email').disabled = true;
        document.getElementById('name').disabled = true;
        document.getElementById('lastName').disabled = true;
        document.getElementById('secondLastName').disabled = true;
        document.getElementById('role').disabled = true;
        document.getElementById('location').disabled = true;
        document.getElementById('updateProfile').disabled = true;
    
        var element = document.getElementById('cancel');
        if (!element.classList.contains('hidden')) {
            element.classList.add('hidden'); // Si no tiene la clase 'hidden', la añadimos
        }
}
</script>
@endpush


@endsection

