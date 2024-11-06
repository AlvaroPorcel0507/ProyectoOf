@extends('layouts.app')

@section('content')
@php
 use App\Models\Activity;
 use App\Models\User;
@endphp

@if(Auth::User()->role=='Administrador')
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
                            <th scope="col">Estado</th>
                            <th scope="col">Solicitado Por:</th>
                            <th scope="col">Acciones</th>
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
                                <td>
                                    @if($activity->status == 2)
                                        <span class="badge bg-success">
                                            Completado
                                        </span>
                                    @else
                                        <span class="badge bg-danger">
                                            En Proceso
                                        </span>
                                    @endif
                                </td>
                                <td>{{ $activity->duration }}</td>
                                <td>
                                    {{ optional(User::find($activity->idUser))->name . ' ' . optional(User::find($activity->idUser))->lastName }}
                                </td>
                                <td>
                                @if($activity->status == 1)
                                    <a href="#" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#confirmModal">
                                        <i class="fas fa-check-square"></i>
                                    </a>
                                    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#toggleStatusModal" data-activity-id="{{ $activity->id }}" data-activity-name="{{ $activity->name }}" data-activity-description="{{ $activity->description }}" data-activity-scheduledDate="{{ $activity->scheduledDate }}" data-activity-duration="{{ $activity->duration }}" data-activity-idUser="{{ $activity->idUser }}" data-activity-status="{{ $activity->status }}">
                                        <i class="fas fa-times-rectangle"></i>
                                    </button>
                                    <a href="{{ route('activities.edit', $activity->id) }}" class="btn btn-warning">
                                            <i class="fas fa-edit"></i>
                                    </a>
                                @else
                                    <p style="font-weight: bold;">Servicio Completado</p>
                                @endif
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

<!-- Modal de Confirmación -->
<div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmModalLabel">Confirmar</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="conclusionForm" method="POST" action="{{ route('activities.updateConclusion', $activity->id) }}">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="conclusion" class="form-label">¿ Esta seguro de marcar como completado el servicio ?</label>
                    </div>

                    <div class="mb-3">
                        <button type="submit" class="btn btn-success">Confirmar</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    </div>
                </form>
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
                ¿Estás seguro de que deseas eliminar la actividad? Esta acción elimiara la actividad.
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
@else
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
                            <th scope="col">Estado</th>
                            <th scope="col">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                        $cont=1;
                        @endphp
                        @foreach ($activities as $activity)
                        @if ($activity->idUser == Auth::id())
                            <tr>
                                <th scope="row">{{ $cont }}</th>
                                <td>{{ $activity->name }}</td>
                                <td>{{ $activity->description }}</td>
                                <td>{{ $activity->scheduledDate }}</td>
                                <td>
                                    @if($activity->status == 2)
                                        <span class="badge bg-success">
                                            Completado
                                        </span>
                                    @else
                                        <span class="badge bg-danger">
                                            En Proceso
                                        </span>
                                    @endif
                                </td>
                                <td>
                                @if($activity->status == 1)
                                <a href="{{ route('activities.edit', $activity->id) }}" class="btn btn-info">
                                        <i class="fas fa-edit"></i>
                                </a>
                                <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#toggleStatusModal" data-activity-id="{{ $activity->id }}" data-activity-name="{{ $activity->name }}" data-activity-description="{{ $activity->description }}" data-activity-scheduledDate="{{ $activity->scheduledDate }}" data-activity-duration="{{ $activity->duration }}" data-activity-idUser="{{ $activity->idUser }}" data-activity-status="{{ $activity->status }}">
                                    <i class="fas fa-trash"></i>
                                </button>
                                @else
                                    <p style="font-weight: bold;">Servicio Completado</p>
                                @endif
                                    
                                </td>
                            </tr>
                            @php $cont++; @endphp
                        @endif
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
                ¿Estás seguro de que deseas eliminar la actividad ? Esta acción elimiara la actividad.
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
@endif
@endsection
