<!-- Sidebar de Tío Chu: Fondo translúcido con borde reflectivo y acentos oro -->
<aside class="w-64 backdrop-blur-2xl bg-[#040507]/65 border-r border-white/10 shadow-[4px_0_32px_0_rgba(0,0,0,0.5)] flex flex-col shrink-0 min-h-screen relative z-20 transition-all">
    
    <!-- Cabecera de Marca con Escudo Dorado Oficial TC -->
    <div class="h-20 flex items-center px-6 border-b border-white/10 justify-between bg-white/[0.01]">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-amber-400 via-amber-500 to-amber-600 text-zinc-950 font-black text-sm flex items-center justify-center tracking-tight shadow-lg shadow-amber-500/25 border border-amber-300/40 shrink-0">
                TC
            </div>
            <div class="min-w-0">
                <div class="flex items-center gap-1.5">
                    <h1 class="text-xs font-black tracking-widest text-white uppercase truncate">TÍO CHU</h1>
                    <span class="w-1.5 h-1.5 rounded-full bg-amber-400 shadow-[0_0_8px_rgba(251,191,36,0.9)] animate-pulse"></span>
                </div>
                <p class="text-[10px] text-amber-400/90 font-mono tracking-wider uppercase truncate">Club Privado &bull; Caja</p>
            </div>
        </div>
    </div>

    <!-- Noche Activa en vivo con pulso esmeralda -->
    <div class="px-5 py-3 border-b border-white/5 bg-white/[0.01]">
        <div class="flex items-center justify-between text-[11px]">
            <span class="text-zinc-400 font-medium">Noche Activa:</span>
            @if(isset($currentSession) && $currentSession)
                @if($currentSession->isOpen())
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/30">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                        ABIERTA
                    </span>
                @else
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-zinc-800 text-zinc-400 border border-white/5">
                        CERRADA
                    </span>
                @endif
            @else
                <span class="text-[10px] text-zinc-500 italic">Sin jornada</span>
            @endif
        </div>
        @if(isset($currentSession) && $currentSession)
            <p class="text-xs font-bold text-white mt-1 truncate font-mono">
                {{ $currentSession->day_name }} {{ \Carbon\Carbon::parse($currentSession->session_date)->format('d/m/Y') }}
            </p>
        @endif
    </div>

    <!-- Navegación con Acentos en Amarillo Oro -->
    <nav class="flex-1 px-3 py-4 space-y-4 overflow-y-auto">
        
        <!-- 0. Resumen General -->
        <div>
            <a href="{{ route('dashboard') }}" 
               class="flex items-center gap-3 px-3 py-2.5 text-xs rounded-xl transition-all {{ request()->routeIs('dashboard') ? 'bg-amber-400/10 text-amber-400 font-bold border-r-2 border-amber-400 shadow-[inset_0_0_12px_rgba(251,191,36,0.06)]' : 'text-zinc-400 hover:text-zinc-100 hover:bg-white/[0.04]' }}">
                <svg class="w-4 h-4 {{ request()->routeIs('dashboard') ? 'text-amber-400' : 'text-zinc-500' }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <rect width="7" height="9" x="3" y="3" rx="1"/>
                    <rect width="7" height="5" x="14" y="3" rx="1"/>
                    <rect width="7" height="9" x="14" y="12" rx="1"/>
                    <rect width="7" height="5" x="3" y="16" rx="1"/>
                </svg>
                <span>Resumen General</span>
            </a>
        </div>

        <!-- 1. Catálogo e Inventario -->
        <div>
            <p class="px-3 pb-1.5 text-[10px] font-bold uppercase tracking-wider text-zinc-500 font-mono">Bodega & Licores</p>
            <div class="space-y-1">
                <a href="{{ route('products.index') }}" 
                   class="flex items-center gap-3 px-3 py-2 text-xs rounded-xl transition-all {{ request()->routeIs('products.*') ? 'bg-amber-400/10 text-amber-400 font-bold border-r-2 border-amber-400 shadow-[inset_0_0_12px_rgba(251,191,36,0.06)]' : 'text-zinc-400 hover:text-zinc-100 hover:bg-white/[0.04]' }}">
                    <svg class="w-4 h-4 {{ request()->routeIs('products.*') ? 'text-amber-400' : 'text-zinc-500' }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="m7.5 4.27 9 5.15"/>
                        <path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"/>
                        <path d="m3.3 7 8.7 5 8.7-5"/>
                        <path d="M12 22V12"/>
                    </svg>
                    <span>Inventario Bebidas</span>
                </a>

                <a href="{{ route('sales.index') }}" 
                   class="flex items-center gap-3 px-3 py-2 text-xs rounded-xl transition-all {{ request()->routeIs('sales.*') ? 'bg-amber-400/10 text-amber-400 font-bold border-r-2 border-amber-400 shadow-[inset_0_0_12px_rgba(251,191,36,0.06)]' : 'text-zinc-400 hover:text-zinc-100 hover:bg-white/[0.04]' }}">
                    <svg class="w-4 h-4 {{ request()->routeIs('sales.*') ? 'text-amber-400' : 'text-zinc-500' }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M3 3v18h18"/>
                        <path d="m19 9-5 5-4-4-3 3"/>
                    </svg>
                    <span>Ventas por Barra</span>
                </a>
            </div>
        </div>

        <!-- 2. Caja & Cobros -->
        <div>
            <p class="px-3 pb-1.5 text-[10px] font-bold uppercase tracking-wider text-zinc-500 font-mono">Recaudación de Caja</p>
            <div class="space-y-1">
                <a href="{{ route('invoices.index') }}" 
                   class="flex items-center gap-3 px-3 py-2 text-xs rounded-xl transition-all {{ request()->routeIs('invoices.*') ? 'bg-amber-400/10 text-amber-400 font-bold border-r-2 border-amber-400 shadow-[inset_0_0_12px_rgba(251,191,36,0.06)]' : 'text-zinc-400 hover:text-zinc-100 hover:bg-white/[0.04]' }}">
                    <svg class="w-4 h-4 {{ request()->routeIs('invoices.*') ? 'text-amber-400' : 'text-zinc-500' }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <rect width="20" height="14" x="2" y="5" rx="2"/>
                        <line x1="2" x2="22" y1="10" y2="10"/>
                    </svg>
                    <span>Facturas & POS</span>
                </a>

                <a href="{{ route('qrs.index') }}" 
                   class="flex items-center gap-3 px-3 py-2 text-xs rounded-xl transition-all {{ request()->routeIs('qrs.*') ? 'bg-amber-400/10 text-amber-400 font-bold border-r-2 border-amber-400 shadow-[inset_0_0_12px_rgba(251,191,36,0.06)]' : 'text-zinc-400 hover:text-zinc-100 hover:bg-white/[0.04]' }}">
                    <svg class="w-4 h-4 {{ request()->routeIs('qrs.*') ? 'text-amber-400' : 'text-zinc-500' }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <rect width="5" height="5" x="3" y="3" rx="1"/>
                        <rect width="5" height="5" x="16" y="3" rx="1"/>
                        <rect width="5" height="5" x="3" y="16" rx="1"/>
                        <path d="M21 16h-3a2 2 0 0 0-2 2v3"/>
                        <path d="M21 21v.01"/>
                        <path d="M12 7v3a2 2 0 0 1-2 2H7"/>
                        <path d="M3 12h.01"/>
                        <path d="M12 3h.01"/>
                        <path d="M12 16v.01"/>
                        <path d="M16 12h1"/>
                        <path d="M21 12v.01"/>
                        <path d="M12 21v-1"/>
                    </svg>
                    <span>Cobros QR</span>
                </a>

                <a href="{{ route('closing.index') }}" 
                   class="flex items-center gap-3 px-3 py-2 text-xs rounded-xl transition-all {{ request()->routeIs('closing.*') ? 'bg-amber-400/10 text-amber-400 font-bold border-r-2 border-amber-400 shadow-[inset_0_0_12px_rgba(251,191,36,0.06)]' : 'text-zinc-400 hover:text-zinc-100 hover:bg-white/[0.04]' }}">
                    <svg class="w-4 h-4 {{ request()->routeIs('closing.*') ? 'text-amber-400' : 'text-zinc-500' }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M14 2v4a2 2 0 0 0 2 2h4"/>
                        <path d="M16 22h2a2 2 0 0 0 2-2V7l-5-5H6a2 2 0 0 0-2 2v18"/>
                        <path d="M8 12h8"/>
                        <path d="M8 16h6"/>
                    </svg>
                    <span>Cierre de Caja & Gastos</span>
                </a>
            </div>
        </div>

        <!-- 3. Personal & Turnos -->
        <div>
            <p class="px-3 pb-1.5 text-[10px] font-bold uppercase tracking-wider text-zinc-500 font-mono">Equipo de Turno</p>
            <div class="space-y-1">
                <a href="{{ route('staff.index') }}" 
                   class="flex items-center gap-3 px-3 py-2 text-xs rounded-xl transition-all {{ request()->routeIs('staff.*') ? 'bg-amber-400/10 text-amber-400 font-bold border-r-2 border-amber-400 shadow-[inset_0_0_12px_rgba(251,191,36,0.06)]' : 'text-zinc-400 hover:text-zinc-100 hover:bg-white/[0.04]' }}">
                    <svg class="w-4 h-4 {{ request()->routeIs('staff.*') ? 'text-amber-400' : 'text-zinc-500' }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                        <circle cx="9" cy="7" r="4"/>
                        <path d="M22 21v-2a4 4 0 0 0-3-3.87"/>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                    </svg>
                    <span>Personal y Turnos</span>
                </a>

                <a href="{{ route('staffPayments.index') }}" 
                   class="flex items-center gap-3 px-3 py-2 text-xs rounded-xl transition-all {{ request()->routeIs('staffPayments.*') ? 'bg-amber-400/10 text-amber-400 font-bold border-r-2 border-amber-400 shadow-[inset_0_0_12px_rgba(251,191,36,0.06)]' : 'text-zinc-400 hover:text-zinc-100 hover:bg-white/[0.04]' }}">
                    <svg class="w-4 h-4 {{ request()->routeIs('staffPayments.*') ? 'text-amber-400' : 'text-zinc-500' }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M11 15h2"/>
                        <path d="M12 12v3"/>
                        <path d="M2 17a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2v10Z"/>
                    </svg>
                    <span>Pagos al Personal</span>
                </a>
            </div>
        </div>

        <!-- 4. Auditoría -->
        <div>
            <p class="px-3 pb-1.5 text-[10px] font-bold uppercase tracking-wider text-zinc-500 font-mono">Auditoría</p>
            <div class="space-y-1">
                <a href="{{ route('sessions.index') }}" 
                   class="flex items-center gap-3 px-3 py-2 text-xs rounded-xl transition-all {{ request()->routeIs('sessions.*') ? 'bg-amber-400/10 text-amber-400 font-bold border-r-2 border-amber-400 shadow-[inset_0_0_12px_rgba(251,191,36,0.06)]' : 'text-zinc-400 hover:text-zinc-100 hover:bg-white/[0.04]' }}">
                    <svg class="w-4 h-4 {{ request()->routeIs('sessions.*') ? 'text-amber-400' : 'text-zinc-500' }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <rect width="18" height="18" x="3" y="4" rx="2" ry="2"/>
                        <line x1="16" y1="2" x2="16" y2="6"/>
                        <line x1="8" y1="2" x2="8" y2="6"/>
                        <line x1="3" y1="10" x2="21" y2="10"/>
                    </svg>
                    <span>Historial de Noches</span>
                </a>
            </div>
        </div>

    </nav>

    <!-- Pie del Sidebar: Usuario & Cierre de Sesión -->
    <div class="p-4 border-t border-white/10 bg-white/[0.01]">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-2.5 min-w-0">
                <div class="w-8 h-8 rounded-xl bg-amber-500/10 text-amber-400 font-bold text-xs flex items-center justify-center shrink-0 border border-amber-400/30">
                    {{ strtoupper(substr(Auth::user()->name ?? 'D', 0, 1)) }}
                </div>
                <div class="truncate">
                    <p class="text-xs font-bold text-white truncate">{{ Auth::user()->name ?? 'Don Ludo' }}</p>
                    <p class="text-[10px] text-zinc-400 truncate font-mono">{{ Auth::user()->email ?? 'admin@tiochu.com' }}</p>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" title="Cerrar sesión" 
                        class="p-2 text-zinc-400 hover:text-amber-400 hover:bg-white/5 rounded-xl transition-colors cursor-pointer">
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
