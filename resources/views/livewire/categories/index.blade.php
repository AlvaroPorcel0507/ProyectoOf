@extends('layouts.app')



@section('content')
@php
 use App\Models\Categories;
@endphp

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
                        @php
                        $cont=1;
                        @endphp
                        @foreach ($categories as $category)
                        
                            <tr>
                                <th scope="row">{{ $cont }}</th>
                                <td>
                                    {{ optional(Categories::find($category->id))->name }}
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
                            @php $cont++; @endphp
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
                ¿Estás seguro de que deseas <strong id="toggleStatusAction"></strong> la categoria <strong id="categoryName"></strong>? Esta acción cambiará el estado del categoria.
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
            var categoryId = button.getAttribute('data-category-id'); 
            var categoryName = button.getAttribute('data-category-name'); 
            var categoryStatus = button.getAttribute('data-category-status'); 
            var form = toggleStatusModal.querySelector('#toggleStatusForm');
            form.action = '/categories/' + categoryId + '/toggleStatus';

            var actionText = userStatus == 1 ? 'deshabilitar' : 'habilitar';
            var toggleStatusActionElement = document.getElementById('toggleStatusAction');
            toggleStatusActionElement.textContent = actionText;

            var categoryElement = document.getElementById('categoryName');
            categoryNameElement.textContent = categoryName;
        });
    });
</script>
@endpush
@endsection
