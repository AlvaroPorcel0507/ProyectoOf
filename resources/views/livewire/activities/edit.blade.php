@extends('layouts.app')

@section('content')
@php
 use App\Models\User;
@endphp
<div class="container">
    
    <div class="d-flex justify-content-between align-items-center my-4">
        <h1 class="h3">Editar Solicitud</h1>
        <a href="{{ route('activities.index') }}" class="btn btn-secondary">Volver</a>
    </div>
    
    <div class="card border-success">
        <div class="card-header bg-success text-white">
            Formulario de Edición de Solicitud
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

            <form id="activitiesForm" action="{{ route('activities.update', $activity->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label for="name">Nombre</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $activity->name) }}" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="description">Descripcion o Detalle</label>
                    <input type="text" name="description" id="description" class="form-control" value="{{ old('description', $activity->description) }}" required>
                </div>
                <div class="form-group">
                    <label for="scheduledDate">Fecha de Programacion</label>
                    <input type="date" name="scheduledDate" id="scheduledDate" class="form-control" value="{{ old('scheduledDate', date('Y-m-d', strtotime($activity->scheduledDate))) }}" required>
                </div>
                <div class="form-group">
                    <label for="duration">Duracion</label>
                    <input type="date" name="duration" id="duration" class="form-control" value="{{ old('duration', date('Y-m-d', strtotime($activity->duration))) }}" required>
                </div>
                <div class="form-group">
                    <label for="priority">Prioridad</label>
                    <select name="priority" id="priority" class="form-control" require>
                    <option value="" selected>
                        @if(($activity->priority)==1)
                            Basica
                        @elseif(($activity->priority)==2)
                            Intermedia
                        @else
                            Urgente
                        @endif
                    </option>
                        <option value="1">Básica</option>
                        <option value="2">Intermedia</option>
                        <option value="3 ">Urgente</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="idUser">Solicitado</label>
                    <select name="idUser" id="idUser" class="form-control" require>
                    <option value="{{ optional(User::find($activity->idUser))->id }}" selected>{{ optional(User::find($activity->idUser))->name . ' ' . optional(User::find($activity->idUser))->lastName }}</option>
                    @foreach (App\Models\User::all() as $user)
                    @if((optional(user::find($user->id))->role)=='Productor')
                        <option value="{{ $user->id }}">{{ optional(user::find($user->id))->name . ' ' . optional(User::find($activity->idUser))->lastName }}</option>
                    @endif
                    @endforeach
                    </select>
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
                <p>Estás a punto de actualizar la categoria con el siguiente detalle:</p>
                <ul>
                    <li><strong>Nombre Actividad:</strong> <span id="modalName"></span></li>
                    <li><strong>Descripción:</strong> <span id="modalDescription"></span></li>
                    <li><strong>Fecha de Programación:</strong> <span id="modalScheduledDate"></span></li>
                    <li><strong>Duracion:</strong> <span id="modalDuration"></span></li>
                    <li><strong>Prioridad:</strong> <span id="modalPriority"></span></li>
                    <!--<li><strong>Solicitado por:</strong> <span id="modalUserId"></span></li>-->
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
        const descriptionInput = document.getElementById('description');
        const scheduledDateInput = document.getElementById('scheduledDate');
        const durationInput = document.getElementById('duration');
        const priorityInput = document.getElementById('priority');
        const idUserInput = document.getElementById('idUser');

        const modalName = document.getElementById('modalName');
        const modalDescription = document.getElementById('modalDescription');
        const modalScheduledDate = document.getElementById('modalScheduledDate');
        const modalDuration = document.getElementById('modalDuration');
        const modalPriority = document.getElementById('modalPriority');
        const modalUserId = document.getElementById('modalUserId');

        document.querySelector('[data-target="#confirmModal"]').addEventListener('click', function() {
            modalName.textContent = nameInput.value;
            modalDescription.textContent = descriptionInput.value;
            modalScheduledDate.textContent = scheduledDateInput.value;
            modalDuration.textContent = durationInput.value;
            modalPriority.textContent = priorityInput.options[priorityInput.selectedIndex].text;
            modalUserId.textContent = idUserInput.options[idUserInput.selectedIndex].text;
        });

        document.getElementById('confirmButton').addEventListener('click', function() {
            document.getElementById('activitiesForm').submit();
        });
    });
</script>
@endsection