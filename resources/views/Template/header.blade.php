<nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
    <div class="container-fluid d-flex justify-content-between align-items-center">
        <h5 class="ml-3 mt-2 mb-0">Halaman @yield('title')</h5>

        <form method="POST" action="{{ route('logout') }}" class="mb-0 mr-3">
            @csrf
            <button type="submit" class="btn btn-sm btn-light border border-danger text-danger d-flex align-items-center px-3 py-1 rounded-pill shadow-sm">
                <i class="fas fa-sign-out-alt mr-2"></i> Logout
            </button>
        </form>
    </div>
</nav>
