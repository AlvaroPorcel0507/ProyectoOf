@extends('layouts.app')



@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center my-4">
        <h1 class="h3 text-green-800">Categorias</h1>
        <a href="{{ route('categories.create') }}" class="btn btn-success">
            Registrar Nueva Categoria
        </a>
    </div>

    <div class="card border-success">
        <div class="card-header bg-success text-white">
            <i class="fas fa-users"></i> Categorias
        </div>
        <div class="card-body bg-light">
            <div class="table-responsive">
                <table class="table table-hover align-middle text-center">
                    <thead>
                        <tr>
                            <th scope="col">Nro.</th>
                            <th scope="col">Nombre Categoria</th>
                            <th scope="col">Modificado por</th>
                            <th scope="col">Estado</th>
                            <th scope="col">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($categories as $category)
                            <tr>
                                <th scope="row">{{ $category->id }}</th>
                                <td>
                                    @if ($category->name == 1)
                                        Lácteos
                                    @elseif ($category->name == 2)
                                        Hortalizas 
                                    @elseif ($category->name == 3)
                                        Legumbres
                                    @elseif ($category->name == 4)
                                        Frutas
                                    @endif
                                </td>
                                <td>{{ $category->userId }}</td>
                                <td>
                                    <span class="badge {{ $category->status == 1 ? 'bg-success' : 'bg-danger' }}">
                                        {{ $category->status == 1 ? 'Habilitado' : 'Deshabilitado' }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('categories.softDelete', $category->id) }}" class="btn btn-warning">
                                        <i class="fas fa-trash-alt"></i>
                                    </a>
                                    <button type="button" class="btn {{ $category->status ? 'btn-danger' : 'btn-success' }}" data-bs-toggle="modal" data-bs-target="#toggleStatusModal" data-category-id="{{ $category->id }}" data-category-name="{{ $category->name }}" data-category-status="{{ $category->status }}">
                                        <i class="fas {{ $category->status == 1 ? 'fa-toggle-off' : 'fa-toggle-on' }}"></i>
                                        {{ $category->status == 1 ? 'Deshabilitar' : 'Habilitar' }}
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Paginación -->
            <div class="d-flex justify-content-center mt-4">
                {{ $categories->appends(['sort_field' => $sortField, 'sort_direction' => $sortDirection])->links() }}
            </div>
        </div>
    </div>
</div>

<!-- Modal de Cambio de Estado -->
<div class="modal fade" id="toggleStatusModal" tabindex="-1" aria-labelledby="toggleStatusModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content border-success">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="toggleStatusModalLabel">Confirmar Cambio de Estado</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                ¿Estás seguro de que deseas <strong id="toggleStatusAction"></strong> al usuario <strong id="userName"></strong>? Esta acción cambiará el estado del usuario.
            </div>
            <div class="modal-footer">
                <form id="toggleStatusForm" action="" method="POST">
                    @csrf
                    @method('PATCH')
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
            var userId = button.getAttribute('data-user-id'); 
            var userName = button.getAttribute('data-user-name'); 
            var userStatus = button.getAttribute('data-user-status'); 
            var form = toggleStatusModal.querySelector('#toggleStatusForm');
            form.action = '/users/' + userId + '/toggleStatus';

            var actionText = userStatus == 1 ? 'deshabilitar' : 'habilitar';
            var toggleStatusActionElement = document.getElementById('toggleStatusAction');
            toggleStatusActionElement.textContent = actionText;

            var userNameElement = document.getElementById('userName');
            userNameElement.textContent = userName;
        });
    });
</script>
@endpush
@endsection
