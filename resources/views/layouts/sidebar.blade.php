<!-- Sidebar de Tío Chu: 100% Estático, tipografía visible y legible, fondo negro puro -->
<aside class="w-64 h-screen max-h-screen sticky top-0 flex flex-col justify-between shrink-0 overflow-hidden z-30 select-none border-r border-zinc-850" style="background-color: #000000; border-color: #1e1f24;">
    
    <!-- Cabecera de Marca Oficial -->
    <div class="h-16 flex items-center px-4 justify-between border-b shrink-0" style="border-color: #1a1a1f;">
        <div class="flex items-center gap-3 min-w-0">
            <img src="{{ asset('images/LogoTioChu.png') }}" 
                 alt="Logo Tío Chu" 
                 class="w-10 h-10 object-contain shrink-0">
            <div class="min-w-0">
                <span class="text-base font-bold tracking-tight text-white block truncate uppercase">TÍO CHU</span>
                <span class="text-xs text-zinc-500 block truncate">Gestión Operativa</span>
            </div>
        </div>
        @if(isset($currentSession) && $currentSession)
            @if($currentSession->isOpen())
                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 shrink-0">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                    Abierta
                </span>
            @else
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-zinc-900 text-zinc-500 border border-zinc-800 shrink-0">
                    Cerrada
                </span>
            @endif
        @endif
    </div>

    <!-- Navegación Natural y Compacta (Sin espacios vacíos artificiales) -->
    <nav class="flex-1 px-3 py-4 space-y-4 overflow-y-auto">
        
        <!-- 0. Resumen General -->
        <div>
            <a href="{{ route('dashboard') }}" 
               class="flex items-center gap-3 px-3 py-2 text-sm rounded-xl transition-colors {{ request()->routeIs('dashboard') ? 'bg-zinc-900 text-[#F5B81C] font-semibold border-l-2 border-[#F5B81C]' : 'text-zinc-400 hover:text-white hover:bg-zinc-900/50' }}">
                <svg class="w-5 h-5 shrink-0 {{ request()->routeIs('dashboard') ? 'text-[#F5B81C]' : 'text-zinc-500' }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <rect width="7" height="9" x="3" y="3" rx="1"/>
                    <rect width="7" height="5" x="14" y="3" rx="1"/>
                    <rect width="7" height="9" x="14" y="12" rx="1"/>
                    <rect width="7" height="5" x="3" y="16" rx="1"/>
                </svg>
                <span class="truncate">Resumen General</span>
            </a>
        </div>

        <!-- 1. Bodega & Licores -->
        <div class="space-y-1">
            <p class="px-3 text-xs font-semibold uppercase tracking-wider text-zinc-500">Bodega & Licores</p>
            
            <a href="{{ route('products.index') }}" 
               class="flex items-center gap-3 px-3 py-2 text-sm rounded-xl transition-colors {{ request()->routeIs('products.*') ? 'bg-zinc-900 text-[#F5B81C] font-semibold border-l-2 border-[#F5B81C]' : 'text-zinc-400 hover:text-white hover:bg-zinc-900/50' }}">
                <svg class="w-5 h-5 shrink-0 {{ request()->routeIs('products.*') ? 'text-[#F5B81C]' : 'text-zinc-500' }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="m7.5 4.27 9 5.15"/>
                    <path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"/>
                    <path d="m3.3 7 8.7 5 8.7-5"/>
                    <path d="M12 22V12"/>
                </svg>
                <span class="truncate">Inventario Bebidas</span>
            </a>

            <a href="{{ route('barInventory.index') }}" 
               class="flex items-center gap-3 px-3 py-2 text-sm rounded-xl transition-colors {{ request()->routeIs('barInventory.*') ? 'bg-zinc-900 text-[#F5B81C] font-semibold border-l-2 border-[#F5B81C]' : 'text-zinc-400 hover:text-white hover:bg-zinc-900/50' }}">
                <svg class="w-5 h-5 shrink-0 {{ request()->routeIs('barInventory.*') ? 'text-[#F5B81C]' : 'text-zinc-500' }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M4 6h16M4 12h16m-7 6h7"/>
                    <path d="M4 18h4"/>
                </svg>
                <span class="truncate">Inventario Barras</span>
            </a>

            <a href="{{ route('sales.index') }}" 
               class="flex items-center gap-3 px-3 py-2 text-sm rounded-xl transition-colors {{ request()->routeIs('sales.*') ? 'bg-zinc-900 text-[#F5B81C] font-semibold border-l-2 border-[#F5B81C]' : 'text-zinc-400 hover:text-white hover:bg-zinc-900/50' }}">
                <svg class="w-5 h-5 shrink-0 {{ request()->routeIs('sales.*') ? 'text-[#F5B81C]' : 'text-zinc-500' }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M3 3v18h18"/>
                    <path d="m19 9-5 5-4-4-3 3"/>
                </svg>
                <span class="truncate">Ventas por Barra</span>
            </a>
        </div>

        <!-- 2. Caja & Cobros -->
        <div class="space-y-1">
            <p class="px-3 text-xs font-semibold uppercase tracking-wider text-zinc-500">Recaudación</p>
            
            <a href="{{ route('invoices.index') }}" 
               class="flex items-center gap-3 px-3 py-2 text-sm rounded-xl transition-colors {{ request()->routeIs('invoices.*') ? 'bg-zinc-900 text-[#F5B81C] font-semibold border-l-2 border-[#F5B81C]' : 'text-zinc-400 hover:text-white hover:bg-zinc-900/50' }}">
                <svg class="w-5 h-5 shrink-0 {{ request()->routeIs('invoices.*') ? 'text-[#F5B81C]' : 'text-zinc-500' }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <rect width="20" height="14" x="2" y="5" rx="2"/>
                    <line x1="2" x2="22" y1="10" y2="10"/>
                </svg>
                <span class="truncate">Facturas & POS</span>
            </a>

            <a href="{{ route('qrs.index') }}" 
               class="flex items-center gap-3 px-3 py-2 text-sm rounded-xl transition-colors {{ request()->routeIs('qrs.*') ? 'bg-zinc-900 text-[#F5B81C] font-semibold border-l-2 border-[#F5B81C]' : 'text-zinc-400 hover:text-white hover:bg-zinc-900/50' }}">
                <svg class="w-5 h-5 shrink-0 {{ request()->routeIs('qrs.*') ? 'text-[#F5B81C]' : 'text-zinc-500' }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <rect width="5" height="5" x="3" y="3" rx="1"/>
                    <rect width="5" height="5" x="16" y="3" rx="1"/>
                    <rect width="5" height="5" x="3" y="16" rx="1"/>
                    <path d="M21 16h-3a2 2 0 0 0-2 2v3"/>
                    <path d="M21 21v.01"/>
                    <path d="M12 7v3a2 2 0 0 1-2 2H7"/>
                </svg>
                <span class="truncate">Cobros QR</span>
            </a>

            <a href="{{ route('closing.index') }}" 
               class="flex items-center gap-3 px-3 py-2 text-sm rounded-xl transition-colors {{ request()->routeIs('closing.*') ? 'bg-zinc-900 text-[#F5B81C] font-semibold border-l-2 border-[#F5B81C]' : 'text-zinc-400 hover:text-white hover:bg-zinc-900/50' }}">
                <svg class="w-5 h-5 shrink-0 {{ request()->routeIs('closing.*') ? 'text-[#F5B81C]' : 'text-zinc-500' }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M14 2v4a2 2 0 0 0 2 2h4"/>
                    <path d="M16 22h2a2 2 0 0 0 2-2V7l-5-5H6a2 2 0 0 0-2 2v18"/>
                    <path d="M8 12h8"/>
                    <path d="M8 16h6"/>
                </svg>
                <span class="truncate">Cierre de Caja</span>
            </a>
        </div>

        <!-- 3. Personal & Turnos -->
        <div class="space-y-1">
            <p class="px-3 text-xs font-semibold uppercase tracking-wider text-zinc-500">Equipo</p>
            
            <a href="{{ route('staff.index') }}" 
               class="flex items-center gap-3 px-3 py-2 text-sm rounded-xl transition-colors {{ request()->routeIs('staff.*') ? 'bg-zinc-900 text-[#F5B81C] font-semibold border-l-2 border-[#F5B81C]' : 'text-zinc-400 hover:text-white hover:bg-zinc-900/50' }}">
                <svg class="w-5 h-5 shrink-0 {{ request()->routeIs('staff.*') ? 'text-[#F5B81C]' : 'text-zinc-500' }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                    <circle cx="9" cy="7" r="4"/>
                    <path d="M22 21v-2a4 4 0 0 0-3-3.87"/>
                    <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                </svg>
                <span class="truncate">Personal y Turnos</span>
            </a>

            <a href="{{ route('staffPayments.index') }}" 
               class="flex items-center gap-3 px-3 py-2 text-sm rounded-xl transition-colors {{ request()->routeIs('staffPayments.*') ? 'bg-zinc-900 text-[#F5B81C] font-semibold border-l-2 border-[#F5B81C]' : 'text-zinc-400 hover:text-white hover:bg-zinc-900/50' }}">
                <svg class="w-5 h-5 shrink-0 {{ request()->routeIs('staffPayments.*') ? 'text-[#F5B81C]' : 'text-zinc-500' }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M11 15h2"/>
                    <path d="M12 12v3"/>
                    <path d="M2 17a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2v10Z"/>
                </svg>
                <span class="truncate">Pagos Personal</span>
            </a>
        </div>

        <!-- 4. Auditoría -->
        <div>
            <a href="{{ route('sessions.index') }}" 
               class="flex items-center gap-3 px-3 py-2 text-sm rounded-xl transition-colors {{ request()->routeIs('sessions.*') ? 'bg-zinc-900 text-[#F5B81C] font-semibold border-l-2 border-[#F5B81C]' : 'text-zinc-400 hover:text-white hover:bg-zinc-900/50' }}">
                <svg class="w-5 h-5 shrink-0 {{ request()->routeIs('sessions.*') ? 'text-[#F5B81C]' : 'text-zinc-500' }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <rect width="18" height="18" x="3" y="4" rx="2" ry="2"/>
                    <line x1="16" y1="2" x2="16" y2="6"/>
                    <line x1="8" y1="2" x2="8" y2="6"/>
                    <line x1="3" y1="10" x2="21" y2="10"/>
                </svg>
                <span class="truncate">Historial de Noches</span>
            </a>
        </div>

        <!-- 5. Multimedia -->
        <div>
            <a href="{{ route('media.index') }}" 
               class="flex items-center gap-3 px-3 py-2 text-sm rounded-xl transition-colors {{ request()->routeIs('media.*') ? 'bg-zinc-900 text-[#F5B81C] font-semibold border-l-2 border-[#F5B81C]' : 'text-zinc-400 hover:text-white hover:bg-zinc-900/50' }}">
                <svg class="w-5 h-5 shrink-0 {{ request()->routeIs('media.*') ? 'text-[#F5B81C]' : 'text-zinc-500' }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <rect width="18" height="18" x="3" y="3" rx="2" ry="2"/>
                    <circle cx="9" cy="9" r="2"/>
                    <path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"/>
                </svg>
                <span class="truncate">Multimedia</span>
            </a>
        </div>

    </nav>

    <!-- Pie del Sidebar: Usuario & Salida -->
    <div class="p-3.5 border-t shrink-0" style="border-color: #1a1a1f;">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-2.5 min-w-0">
                @if(Auth::user()?->avatar_url)
                    <img src="{{ Auth::user()->avatar_url }}" 
                         alt="{{ Auth::user()->name }}" 
                         class="w-8 h-8 rounded-xl object-cover shrink-0 border border-zinc-850">
                @else
                    <div class="w-8 h-8 rounded-xl bg-zinc-900 text-zinc-200 font-semibold text-xs flex items-center justify-center shrink-0 border border-zinc-800">
                        {{ strtoupper(substr(Auth::user()->name ?? 'D', 0, 1)) }}
                    </div>
                @endif

                <div class="truncate">
                    <p class="text-xs font-semibold text-white truncate">{{ Auth::user()->name ?? 'Don Ludo' }}</p>
                    <p class="text-[11px] text-zinc-500 truncate">{{ Auth::user()->email ?? 'admin@tiochu.com' }}</p>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" title="Cerrar sesión" 
                        class="p-2 text-zinc-500 hover:text-white hover:bg-zinc-900 rounded-xl transition-colors cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                        <polyline points="16 17 21 12 16 7"/>
                        <line x1="21" y1="12" x2="9" y2="12"/>
                    </svg>
                </button>
            </form>
        </div>
    </div>
</aside>

