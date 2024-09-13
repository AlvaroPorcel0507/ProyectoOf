@extends('layouts.app')

@section('content')
@php
 use App\Models\User;
@endphp
<div class="container">
    <div class="d-flex justify-content-between align-items-center my-4">
        <h1 class="h3">Registrar Nueva Solicitud de Actividad</h1>
        <a href="{{ route('activities.index') }}" class="btn btn-secondary">Volver</a>
    </div>

    <div class="card">
        <div class="card-header">
            Añadir Solicitud
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

            <form id="activitiesForm" action="{{ route('activities.store') }}" method="POST">
                @csrf

                <div class="form-group">
                    <label for="name">Nombre de la Actividad</label>
                    <input type="text" name="name" id="name" class="form-control" value="{{ old('name') }}" required>
                </div>
                <div class="form-group">
                    <label for="description">Descripcion o Detalle</label>
                    <input type="text" name="description" id="description" class="form-control" value="{{ old('description') }}" required>
                </div>
                <div class="form-group">
                    <label for="scheduledDate">Fecha de Programacion</label>
                    <input type="date" name="scheduledDate" id="scheduledDate" class="form-control" value="{{ old('scheduledDate') }}" required>
                </div>
                <div class="form-group">
                    <label for="duration">Duracion</label>
                    <input type="date" name="duration" id="duration" class="form-control" value="{{ old('duration') }}" required>
                </div>
                <div class="form-group">
                    <label for="priority">Prioridad</label>
                    <select name="priority" id="priority" class="form-control" require>
                    <option value="" selected>SELECCIONE UN NIVEL DE PRIORIDAD</option>
                        <option value="1">Urgente</option>
                        <option value="2">Intermedia</option>
                        <option value="3 ">Básica</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="idUser">Solicitado</label>
                    <select name="idUser" id="idUser" class="form-control" require>
                    <option value="" selected>SELECCIONE UNA PRODUCTOR</option>
                    @foreach (App\Models\User::all() as $user)
                    @if((optional(user::find($user->id))->role)=='Productor')
                        <option value="{{ $user->id }}">{{ optional(user::find($user->id))->name . ' ' . optional(User::find($activity->idUser))->lastName }}</option>
                    @endif
                    @endforeach
                    </select>
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
                <p>Estás a punto de habilitar una nueva actividad con los siguientes datos:</p>
                <ul>
                    <li><strong>Nombre Actividad:</strong> <span id="modalName"></span></li>
                    <li><strong>Descripción:</strong> <span id="modalDescription"></span></li>
                    <li><strong>Fecha de Programación:</strong> <span id="modalScheduleDate"></span></li>
                    <li><strong>Duracion:</strong> <span id="modalDuration"></span></li>
                    <li><strong>Prioridad:</strong> <span id="modalPriority"></span></li>
                    <li><strong>Solicitado por:</strong> <span id="modalUserId"></span></li>
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
        const nameInput = document.getElementById('name');
        const descriptionInput = document.getElementById('description');
        const scheduleDateInput = document.getElementById('scheduleDate');
        const durationInput = document.getElementById('duration');
        const priorityInput = document.getElementById('priority');
        const idUserInput = document.getElementById('idUser');

        const modalName = document.getElementById('modalName');
        const modalDescription = document.getElementById('modalDescription');
        const modalScheduleDate = document.getElementById('modalScheduleDate');
        const modalDutation = document.getElementById('modalDutation');
        const modalPriority = document.getElementById('modalPriority');
        const modalIdUser = document.getElementById('modalIdUser');

        document.querySelector('[data-target="#confirmModal"]').addEventListener('click', function() {
            modalName.textContent = nameInput.value;
            modalDescription.textContent = descriptionInput.value;
            modalScheduleDate.textContent = scheduleDateInput.value;
            modalDutation.textContent = durationInput.value;
            modalPriority.textContent = priorityInput.value;
            modalIdUser.textContent = idUserInput.value;
        });

        // Enviar el formulario al confirmar
        document.getElementById('confirmButton').addEventListener('click', function() {
            document.getElementById('activitiesForm').submit();
        });
    });
</script>

@endsection
