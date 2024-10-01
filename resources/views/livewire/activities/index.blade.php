@extends('layouts.app')

@section('content')
@php
 use App\Models\Activity;
 use App\Models\User;
@endphp

<div class="container">
    <div class="d-flex justify-content-between align-items-center my-4">
        <h1 class="h3 text-green-800">Programacion de Actividades</h1>
        <a href="{{ route('activities.create') }}" class="btn btn-success">
            Registrar Nueva Actividad
        </a>
    </div>

    <div class="card border-success">
        <div class="card-header bg-success text-white">
            <i class="fas fa-users"></i> Actividades Agricolas
        </div>
        <div class="card-body bg-light">
            <div class="table-responsive">
                <table class="table table-hover align-middle text-center">
                    <thead>
                        <tr>
                            <th scope="col">Nro.</th>
                            <th scope="col">Nombre</th>
                            <th scope="col">Descripcion</th>
                            <th scope="col">Fecha Programada</th>
                            <th scope="col">Duracion</th>
                            <th scope="col">Solicitado Por:</th>
                            <th scope="col">Editar</th>
                            <th scope="col">Eliminar</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                        $cont=1;
                        @endphp
                        @foreach ($activities as $activity)
                        
                            <tr>
                                <th scope="row">{{ $cont }}</th>
                                <td>{{ $activity->name }}</td>
                                <td>{{ $activity->description }}</td>
                                <td>{{ $activity->scheduledDate }}</td>
                                <td>{{ $activity->duration }}</td>
                                <td>
                                    {{ optional(User::find($activity->idUser))->name . ' ' . optional(User::find($activity->idUser))->lastName }}
                                </td>
                                <td>
                                    <a href="{{ route('activities.edit', $activity->id) }}" class="btn btn-info">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#toggleStatusModal" data-activity-id="{{ $activity->id }}" data-activity-name="{{ $activity->name }}" data-activity-description="{{ $activity->description }}" data-activity-scheduledDate="{{ $activity->scheduledDate }}" data-activity-duration="{{ $activity->duration }}" data-activity-idUser="{{ $activity->idUser }}" data-activity-status="{{ $activity->status }}">
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
                {{ $activities->appends(['sort_field' => $sortField, 'sort_direction' => $sortDirection])->links() }}
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
                ¿Estás seguro de que deseas eliminar la categoria <strong id="categoriesName"></strong>? Esta acción elimiara la categoria.
            </div>
            <div class="modal-footer">
                <form id="toggleStatusFormCategories" action="" method="POST">
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
            var activityId = button.getAttribute('data-activity-id'); 
            var activityName = button.getAttribute('data-activity-name');
            var activityDescription = button.getAttribute('data-activity-description');
            var activityScheduledDate = button.getAttribute('data-activity-scheduledDate');
            var activityDuration = button.getAttribute('data-activity-duration');
            var activityIdUser = button.getAttribute('data-activity-idUser');
            var activityStatus = button.getAttribute('data-activity-status'); 
            var form = toggleStatusModal.querySelector('#toggleStatusFormCategories');
            form.action = '/activities/' + activityId + '/softDelete';

            var actionText = categoryStatus == 1 ? 'deshabilitar' : 'habilitar';
            var toggleStatusActionElement = document.getElementById('toggleStatusAction');
            toggleStatusActionElement.textContent = actionText;

            var activityNameElement = document.getElementById('name');
            activityNameElement.textContent = activityName;
        });
    });
</script>
@endpush
@endsection
