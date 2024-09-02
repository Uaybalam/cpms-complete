<div class="app-sidebar colored">
    <div class="sidebar-header">
        <a class="header-brand">
            <div class="logo-img">
                <img width="40" height="40" src="{{ asset('img/logo.png') }}" class="header-brand-img" alt="lavalite">
            </div>
            <span class="text">parking</span>
        </a>
        <!-- <button type="button" class="nav-toggle"><i data-toggle="expanded" class="ik ik-toggle-right toggle-icon"></i></button> -->
        <button id="sidebarClose" class="nav-close"><i class="ik ik-x"></i></button>
    </div>

    <div class="sidebar-content">
        <div class="nav-container">
            <nav id="main-menu-navigation" class="navigation-main">
                <div class="nav-lavel" >Navegacion</div>
                <div class="nav-item active">
                    <a href="{{ route('home') }}" ><i class="ik ik-bar-chart-2"></i><span style="font-size:1rem;">Dashboard</span></a>
                </div>

                @if (auth()->check() && auth()->user()->role == 'Administrador')
                    <div class="nav-item has-sub {{ request()->routeIs('user*') ? 'open' : '' }}">
                        <a href="javascript:void(0)"><i class="ik ik-user"></i><span style="font-size:1rem;">Administrar Usuarios</span> </a>
                        <div class="submenu-content">
                            <a href="{{ route('user.create') }}"
                                class="menu-item  {{ request()->routeIs('user.create') ? 'active' : '' }}"style="font-size:16px;">Crear</a>
                            <a href="{{ route('user.index') }}"
                                class="menu-item  {{ request()->routeIs('user.index') ? 'active' : '' }}" style="font-size:16px;">Lista</a>
                        </div>
                    </div>
                    <div class="nav-item has-sub {{ request()->routeIs('customers*') ? 'open' : '' }}">
                        <a href="{{ route('customers.index') }}"><i class="ik ik-users"></i><span style="font-size:1rem;">Administrar Clientes
                            </span> </a>
                    </div>
                    <div class="nav-lavel">Vehiculos</div>
                    <div class="nav-item has-sub {{ request()->routeIs('categories*') ? 'open' : '' }}">
                        <a href="#"><i class="ik ik-box"></i><span style="font-size:1rem;">Administrar Tarifas</span></a>
                        <div class="submenu-content">
                            <a href="{{ route('categories.create') }}"
                                class="menu-item  {{ request()->routeIs('categories.create') ? 'active' : '' }}" style="font-size:16px;">Crear</a>
                                <a href="{{ route('categoria.import') }}"
                                class="menu-item  {{ request()->routeIs('categoria.import') ? 'active' : '' }}" style="font-size:16px;">Importar Tabla</a>
                            <a href="{{ route('categories.index') }}"
                                class="menu-item  {{ request()->routeIs('categories.index') ? 'active' : '' }}" style="font-size:16px;">Lista</a>
                        </div>
                    </div>
                    <div class="nav-item has-sub {{ request()->routeIs('vehicles*') ? 'open' : '' }}">
                        <a href="#"><i class="ik ik-truck"></i><span style="font-size:1rem;">Registrar Vehiculo</span> </a>
                        <div class="submenu-content">
                            <a href="{{ route('vehicles.create') }}"
                                class="menu-item  {{ request()->routeIs('vehicles.create') ? 'active' : '' }}" style="font-size:17px;">Crear</a>
                            <a href="{{ route('vehicles.index') }}"
                                class="menu-item  {{ request()->routeIs('vehicles.index') ? 'active' : '' }}" style="font-size:17px;">Lista</a>
                        </div>
                    </div>

                    <div
                        class="nav-item has-sub {{ request()->routeIs('vehiclesIn*') || request()->routeIs('vehiclesOut*') ? 'open' : '' }}">
                        <a href="#"><i class="ik ik-gitlab" ></i><span style="font-size:1rem;">Administrar Vehiculos</span> </a>
                        <div class="submenu-content">
                            <a href="{{ route('vehiclesIn.index') }}"
                                class="menu-item  {{ request()->routeIs('vehiclesIn*') ? 'active' : '' }}" style="font-size:17px;">Entrantes</a>
                            <a href="{{ route('vehiclesOut.index') }}"
                                class="menu-item  {{ request()->routeIs('vehiclesOut*') ? 'active' : '' }}" style="font-size:17px;">Salientes</a>
                        </div>
                    </div>
                    <div class="nav-lavel">Caja</div>
                    <div class="nav-item ">
                        <a href="{{ route('abrir.caja') }}"><i class="x-fas-history"></i><span style="font-size:1rem;">Abrir Nueva
                                Caja</span></a>
                    </div>
                    <div class="nav-item ">
                        <a href="{{ route('caja.venta') }}"><i class="ik ik-wal"></i><span style="font-size:1rem;">Cobrar</span></a>
                    </div>
                    <div class="nav-item ">
                        <a href="{{ route('cierreCaja') }}"><i class="x-fas-history"></i><span style="font-size:1rem;">Cierre de Caja</span></a>
                    </div>
                    <div class="nav-item has-sub">
                        <a href="#"><i class="x-fas-history"></i><span style="font-size:1rem;">Pensiones</span></a>
                        <div class="submenu-content">
                            <a href="{{ route('pensionados.index') }}" class="menu-item" style="font-size:17px;">Crear</a>
                            <a href="{{ route('pensionados.pensionados') }}" class="menu-item" style="font-size:17px;">Lista</a>
                        </div>
                    </div>
                    <div class="nav-item ">
                        <a href="{{ route('historial') }}"><i class="x-fas-history"></i><span style="font-size:1rem;">Historial</span></a>
                    </div>
                @endif
                @if (auth()->check() && auth()->user()->role == 'Cajero')
                    <div class="nav-item has-sub {{ request()->routeIs('vehicles*') ? 'open' : '' }}">
                        <a href="#"><i class="ik ik-truck"></i><span style="font-size:1rem;">Registrar Vehiculo</span> </a>
                        <div class="submenu-content">
                            <a href="{{ route('vehicles.create') }}"
                                class="menu-item  {{ request()->routeIs('vehicles.create') ? 'active' : '' }}"style="font-size:17px;">Crear</a>
                            <a href="{{ route('vehicles.index') }}"
                                class="menu-item  {{ request()->routeIs('vehicles.index') ? 'active' : '' }}" style="font-size:17px;">Lista</a>
                        </div>
                    </div>
                    <div class="nav-lavel">Caja</div>
                    <div class="nav-item ">
                        <a href="{{ route('abrir.caja') }}"><i class="x-fas-history"></i><span style="font-size:1rem;">Abrir Nueva
                                Caja</span></a>
                    </div>
                    <div class="nav-item ">
                        <a href="{{ route('caja.venta') }}"><i class="ik ik-wal"></i><span style="font-size:1rem;">Cobrar</span></a>
                    </div>
                    <div class="nav-item ">
                        <a href="{{ route('cierreCaja') }}"><i class="x-fas-history"></i><span style="font-size:1rem;">Cierre caja</span></a>
                    </div>
                    <div class="nav-item has-sub">
                        <a href="#"><i class="x-fas-history"></i><span style="font-size:1rem;">Pensiones</span></a>
                        <div class="submenu-content">
                            <a href="{{ route('pensionados.index') }}" class="menu-item" style="font-size:17px;">Crear</a>
                            <a href="{{ route('pensionados.pensionados') }}" class="menu-item" style="font-size:17px;">Lista</a>
                        </div>
                    </div>
                    <div class="nav-item ">
                        <a href="{{ route('historial') }}"><i class="x-fas-history"></i><span style="font-size:1rem;">Historial</span></a>
                    </div>
                @endif
            </nav>
        </div>
    </div>
</div>
