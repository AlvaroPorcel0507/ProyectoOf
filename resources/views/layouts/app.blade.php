<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="{{ asset('leaflet/leaflet.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    @livewireStyles
    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.2.1/dist/chart.umd.min.js"></script>
</head>
<body class="font-sans antialiased bg-gray-200">
    <div class="min-h-screen flex">

        <!-- Sidebar -->
        <aside class="w-64 bg-green-600 text-white flex flex-col">
            <div class="flex items-center justify-center h-16 bg-green-700">
                <span class="text-xl font-semibold">Material Dashboard 2</span>
            </div>
            <nav class="flex-1 px-4 py-2 space-y-2">

            @if(Auth::User()->role=='Administrador')
                <a href="{{ route('users.profile') }}" class="flex items-center px-4 py-2 rounded-lg hover:bg-orange-600 focus:bg-orange-600 focus:ring focus:ring-orange-500">
                    <i class="fas fa-user-circle mr-2"></i> 
                    <span>Perfil de Usuario</span>
                </a>
                <a href="{{ route('users.index') }}" class="flex items-center px-4 py-2 rounded-lg hover:bg-orange-600 focus:bg-orange-600 focus:ring focus:ring-orange-500">
                    <i class="fas fa-user-plus mr-2"></i> 
                    <span>Administracion de Usuarios</span>
                </a>
                <a href="{{ route('categories.index') }}" class="flex items-center px-4 py-2 rounded-lg hover:bg-orange-600 focus:bg-orange-600 focus:ring focus:ring-orange-500">
                    <i class="fas fa-th-list mr-2"></i> 
                    <span>Categorias</span>
                </a>
                <a href="{{ route('products.index') }}" class="flex items-center px-4 py-2 rounded-lg hover:bg-orange-600 focus:bg-orange-600 focus:ring focus:ring-orange-500">
                    <i class="fas fa-shopping-basket mr-2"></i> 
                    <span>Productos</span>
                </a>
                <a href="{{ route('customers.index') }}" class="flex items-center px-4 py-2 rounded-lg hover:bg-orange-600 focus:bg-orange-600 focus:ring focus:ring-orange-500">
                    <i class="fas fa-users mr-2"></i> 
                    <span>Clientes</span>
                </a>
                <a href="{{ route('activities.index') }}" class="flex items-center px-4 py-2 rounded-lg hover:bg-orange-600 focus:bg-orange-600 focus:ring focus:ring-orange-500">
                    <i class="fas fa-calendar mr-2"></i> 
                    <span>Programacion de Actividades</span>
                </a>
                <a href="{{ route('sales.index') }}" class="flex items-center px-4 py-2 rounded-lg hover:bg-orange-600 focus:bg-orange-600 focus:ring focus:ring-orange-500">
                    <i class="fas fa-cart-plus mr-2"></i> 
                    <span>Ventas</span>
                </a>
            @elseif(Auth::User()->role=='Productor')
                <a href="{{ route('users.profile') }}" class="flex items-center px-4 py-2 rounded-lg hover:bg-orange-600 focus:bg-orange-600 focus:ring focus:ring-orange-500">
                    <i class="fas fa-user-circle mr-2"></i> 
                    <span>Perfil de Usuario</span>
                </a>
                <a href="{{ route('products.index') }}" class="flex items-center px-4 py-2 rounded-lg hover:bg-orange-600 focus:bg-orange-600 focus:ring focus:ring-orange-500">
                    <i class="fas fa-shopping-basket mr-2"></i> 
                    <span>Mis Productos</span>
                </a>
            @else
                <a href="{{ route('users.profile') }}" class="flex items-center px-4 py-2 rounded-lg hover:bg-orange-600 focus:bg-orange-600 focus:ring focus:ring-orange-500">
                    <i class="fas fa-user-circle mr-2"></i> 
                    <span>Perfil de Usuario</span>
                </a>
                <a href="{{ route('sales.index') }}" class="flex items-center px-4 py-2 rounded-lg hover:bg-orange-600 focus:bg-orange-600 focus:ring focus:ring-orange-500">
                    <i class="fas fa-cart-plus mr-2"></i> 
                    <span>Compras</span>
                </a>
            @endif    
            </nav>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col p-6">
            <!-- Header -->
            <header class="bg-white shadow rounded-lg mb-6 flex justify-between items-center px-6 py-4">
                <h1 class="text-lg font-semibold">
                    
                </h1>
                <div class="flex items-center space-x-4">
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="block px-4 py-2 text-sm hover:bg-gray-200 w-full text-left text-black">
                                Cerrar Sesión
                            </button>
                        </form>
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 bg-white rounded-lg shadow p-6">
                 @if (session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif
                @yield('content')
            </main>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="{{ asset('leaflet/leaflet.js') }}"></script>
    @livewireScripts
    @stack('scripts')
</body>
</html>
