@extends('layouts.app')

@section('title', 'Administración de Usuarios')

@section('content')
<div class="space-y-4 max-w-7xl mx-auto pb-10 font-sans">

    <!-- ==========================================
         1. CABECERA: TÍTULO Y BOTÓN DE NUEVA CUENTA (CARD NOTORIA)
         ========================================== -->
    <div class="notoria-card px-5 py-4 shadow-sm flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        
        <div class="flex items-center gap-2.5">
            <div class="w-9 h-9 rounded-xl bg-[#F5B81C]/15 border border-[#F5B81C]/40 flex items-center justify-center text-[#F5B81C] shrink-0 shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                    <circle cx="9" cy="7" r="4"/>
                    <path d="M20 8v6M23 11h-6"/>
                </svg>
            </div>
            <div>
                <h1 class="text-lg sm:text-xl font-black tracking-tight theme-text-primary font-sans">
                    Administración de Usuarios
                </h1>
                <p class="text-xs theme-text-secondary mt-0.5">Control de cuentas y credenciales autorizadas para ingresar a la plataforma.</p>
            </div>
        </div>

        <div class="flex items-center gap-2 w-full sm:w-auto shrink-0">
            <button type="button" onclick="openCreateUserDrawer()" 
                    class="w-full sm:w-auto justify-center px-4 py-2.5 rounded-xl bg-[#F5B81C] hover:bg-[#e5ac18] text-black font-black text-xs transition-all flex items-center gap-1.5 shadow-sm cursor-pointer active:scale-95">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <line x1="12" y1="5" x2="12" y2="19"/>
                    <line x1="5" y1="12" x2="19" y2="12"/>
                </svg>
                <span>Nueva Cuenta</span>
            </button>
        </div>
    </div>

    <!-- ==========================================
         2. TARJETAS DE AUDITORÍA Y SEGURIDAD (CARDS NOTORIAS)
         ========================================== -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3.5 sm:gap-4">
        <!-- Total Cuentas -->
        <div class="notoria-card p-4 sm:p-5 shadow-sm">
            <span class="text-[11px] font-black theme-text-secondary uppercase tracking-wider block">Cuentas Habilitadas</span>
            <div class="mt-2 text-2xl sm:text-3xl font-black font-mono theme-text-primary tracking-tight">
                {{ $users->count() }} <span class="text-xs theme-text-muted font-sans font-normal">usuarios</span>
            </div>
            <p class="text-[10px] theme-text-muted mt-1 font-mono">Con acceso al sistema</p>
        </div>

        <!-- Administrador Principal -->
        <div class="notoria-card p-4 sm:p-5 shadow-sm">
            <span class="text-[11px] font-black theme-text-secondary uppercase tracking-wider block">Administrador Principal</span>
            <div class="mt-2 text-sm sm:text-base font-black theme-text-primary tracking-tight flex items-center gap-1.5">
                <span class="w-2 h-2 rounded-full bg-[#F5B81C]"></span>
                <span>Don Ludo</span>
            </div>
            <p class="text-[10px] theme-text-muted mt-1 truncate font-mono">DonLudo@gmail.com</p>
        </div>

        <!-- Política de Seguridad -->
        <div class="notoria-card p-4 sm:p-5 shadow-sm">
            <span class="text-[11px] font-black theme-text-secondary uppercase tracking-wider block">Seguridad de Acceso</span>
            <div class="mt-2 text-sm sm:text-base font-black theme-text-primary">
                Sesión por Apertura
            </div>
            <p class="text-[10px] theme-text-muted mt-1 font-mono">Requiere login al cerrar el navegador o puerto</p>
        </div>
    </div>

    <!-- Mensajes de Error de Borrado / Validaciones -->
    @if($errors->has('delete_error'))
        <div class="p-3 bg-rose-500/10 border border-rose-500/30 text-rose-300 rounded-xl text-xs font-semibold flex items-center gap-2">
            <svg class="w-4 h-4 text-rose-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
            <span>{{ $errors->first('delete_error') }}</span>
        </div>
    @endif

    <!-- ==========================================
         3. TARJETAS MÓVILES (md:hidden) (CARDS NOTORIAS)
         ========================================== -->
    <div class="md:hidden space-y-3">
        <div class="flex items-center justify-between px-1">
            <h2 class="text-xs font-bold uppercase tracking-wider theme-text-muted font-mono">Cuentas Registradas ({{ $users->count() }})</h2>
        </div>

        @forelse($users as $index => $u)
            @php
                $isCurrentUser = $u->id === Auth::id();
                $isMasterAdmin = (strcasecmp($u->email, 'DonLudo@gmail.com') === 0 || strcasecmp($u->email, 'DonLudo@gmail.chu') === 0);
            @endphp
            <div class="notoria-card p-3.5 shadow-sm space-y-3 {{ $isCurrentUser ? 'border-amber-500/50' : '' }}">
                <div class="flex items-center justify-between gap-2">
                    <div class="flex items-center gap-2.5 min-w-0">
                        @if($u->avatar_url)
                            <img src="{{ $u->avatar_url }}" alt="{{ $u->name }}" class="w-8 h-8 rounded-lg object-cover border notoria-border shrink-0">
                        @else
                            <div class="w-8 h-8 rounded-lg notoria-subtile flex items-center justify-center text-xs font-black theme-text-primary shrink-0">
                                {{ substr($u->name, 0, 1) }}
                            </div>
                        @endif
                        <div class="min-w-0">
                            <span class="font-black theme-text-primary text-xs block truncate">{{ $u->name }}</span>
                            <span class="text-[11px] theme-text-secondary font-mono block truncate">{{ $u->email }}</span>
                        </div>
                    </div>
                    <div class="shrink-0">
                        @if($isCurrentUser)
                            <span class="px-2 py-0.5 rounded-full text-[9px] font-black uppercase text-[#F5B81C] bg-[#F5B81C]/15 border border-[#F5B81C]/40 tracking-wider">Tu Sesión</span>
                        @elseif($isMasterAdmin)
                            <span class="px-2 py-0.5 rounded-full text-[9px] font-bold uppercase notoria-subtile theme-text-muted tracking-wider">Admin</span>
                        @endif
                    </div>
                </div>

                <div class="flex items-center justify-between text-[11px] theme-text-muted font-mono pt-2 border-t notoria-border">
                    <span>{{ $u->created_at ? $u->created_at->format('d/m/Y') : 'Inicial' }}</span>
                    <div class="flex items-center gap-1.5">
                        <button type="button" 
                                onclick="openEditUserDrawer({{ $u->id }}, '{{ addslashes($u->name) }}', '{{ addslashes($u->email) }}')"
                                class="px-3 py-1.5 rounded-lg notoria-subtile theme-text-secondary hover:theme-text-primary text-xs font-bold transition-all cursor-pointer">
                            Editar
                        </button>

                        @if($isCurrentUser || $isMasterAdmin)
                            <button type="button" disabled title="Cuenta protegida"
                                    class="px-2.5 py-1.5 rounded-lg notoria-subtile opacity-50 theme-text-muted text-xs cursor-not-allowed">
                                Bloqueado
                            </button>
                        @else
                            <form method="POST" action="{{ route('users.destroy', $u) }}" onsubmit="return confirm('¿Confirmas eliminar la cuenta de {{ addslashes($u->name) }}? Ya no podrá acceder al sistema.')" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="px-3 py-1.5 rounded-lg bg-rose-500/10 border border-rose-500/20 text-rose-500 hover:text-rose-400 text-xs font-bold transition-all cursor-pointer">
                                    Eliminar
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="p-6 text-center theme-text-muted rounded-xl notoria-card text-xs">
                No se encontraron cuentas registradas.
            </div>
        @endforelse
    </div>

    <!-- ==========================================
         4. TABLA LISTADO DE USUARIOS (DESKTOP: hidden md:block) - CARD NOTORIA
         ========================================== -->
    <div class="hidden md:block notoria-card overflow-hidden shadow-xl">
        
        <div class="notoria-card-header flex items-center justify-between">
            <h2 class="text-xs font-black uppercase tracking-wider text-white font-mono">Cuentas Registradas</h2>
            <span class="text-xs text-zinc-300 font-mono">({{ $users->count() }} registradas)</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="bg-slate-950 text-white uppercase tracking-wider font-mono font-bold text-[10px] border-b notoria-border">
                        <th class="px-4 py-3 w-12 text-center text-zinc-300">#</th>
                        <th class="px-4 py-3 text-zinc-200">Usuario</th>
                        <th class="px-4 py-3 text-zinc-200">Correo Electrónico (Login)</th>
                        <th class="px-4 py-3 text-zinc-200">Fecha de Registro</th>
                        <th class="px-4 py-3 text-right text-zinc-200">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y notoria-border font-sans">
                    @forelse($users as $index => $u)
                        @php
                            $isCurrentUser = $u->id === Auth::id();
                            $isMasterAdmin = (strcasecmp($u->email, 'DonLudo@gmail.com') === 0 || strcasecmp($u->email, 'DonLudo@gmail.chu') === 0);
                        @endphp
                        <tr class="hover:bg-slate-500/10 transition-colors {{ $isCurrentUser ? 'bg-[#F5B81C]/[0.05]' : '' }}">
                            
                            <td class="px-4 py-3 text-center theme-text-muted font-bold font-mono">
                                {{ $index + 1 }}
                            </td>

                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2.5">
                                    @if($u->avatar_url)
                                        <img src="{{ $u->avatar_url }}" alt="{{ $u->name }}" class="w-7 h-7 rounded-lg object-cover border notoria-border">
                                    @else
                                        <div class="w-7 h-7 rounded-lg notoria-subtile flex items-center justify-center text-xs font-black theme-text-primary">
                                            {{ substr($u->name, 0, 1) }}
                                        </div>
                                    @endif
                                    <div>
                                        <span class="font-black theme-text-primary text-xs block">{{ $u->name }}</span>
                                        @if($isCurrentUser)
                                            <span class="text-[9px] font-black uppercase text-[#F5B81C] tracking-wider">Tu Sesión Actual</span>
                                        @elseif($isMasterAdmin)
                                            <span class="text-[9px] font-bold uppercase theme-text-muted tracking-wider">Administrador Maestro</span>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            <td class="px-4 py-3">
                                <span class="theme-text-secondary font-mono text-xs">{{ $u->email }}</span>
                            </td>

                            <td class="px-4 py-3 theme-text-muted font-mono">
                                {{ $u->created_at ? $u->created_at->format('d/m/Y H:i') : 'Inicial' }}
                            </td>

                            <td class="px-4 py-3 text-right">
                                <div class="inline-flex items-center gap-1.5">
                                    <!-- Botón Editar -->
                                    <button type="button" 
                                            onclick="openEditUserDrawer({{ $u->id }}, '{{ addslashes($u->name) }}', '{{ addslashes($u->email) }}')"
                                            class="px-2.5 py-1 rounded-lg notoria-subtile theme-text-secondary hover:theme-text-primary text-xs font-bold transition-all cursor-pointer">
                                        Editar
                                    </button>

                                    <!-- Botón Eliminar -->
                                    @if($isCurrentUser || $isMasterAdmin)
                                        <button type="button" disabled title="Cuenta protegida"
                                                class="px-2.5 py-1 rounded-lg notoria-subtile opacity-50 theme-text-muted text-xs cursor-not-allowed">
                                            Bloqueado
                                        </button>
                                    @else
                                        <form method="POST" action="{{ route('users.destroy', $u) }}" onsubmit="return confirm('¿Confirmas eliminar la cuenta de {{ addslashes($u->name) }}? Ya no podrá acceder al sistema.')" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="px-2.5 py-1 rounded-lg bg-rose-500/10 border border-rose-500/20 text-rose-500 hover:text-rose-400 text-xs font-bold transition-all cursor-pointer">
                                                Eliminar
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center theme-text-muted text-xs font-sans">
                                No se encontraron cuentas registradas.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>

</div>

<!-- ==========================================
     4. DRAWER: CREAR NUEVA CUENTA
     ========================================== -->
<div id="drawer-create-user-overlay" class="drawer-overlay" onclick="closeCreateUserDrawer()"></div>

<div id="drawer-create-user" class="drawer-panel" style="z-index: 60;">
    <div class="drawer-header">
        <div>
            <h2 class="text-base font-black text-white tracking-tight">Nueva Cuenta de Usuario</h2>
            <p class="text-xs text-zinc-300 mt-0.5">Crea una cuenta autorizada para acceder al sistema Tío Chu.</p>
        </div>
        <button type="button" onclick="closeCreateUserDrawer()" class="w-8 h-8 flex items-center justify-center rounded-lg notoria-subtile theme-text-secondary hover:theme-text-primary transition-all cursor-pointer">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>

    <form method="POST" action="{{ route('users.store') }}" class="drawer-body space-y-4">
        @csrf

        <!-- Nombre -->
        <div>
            <label for="create_name" class="block text-xs font-bold theme-text-secondary uppercase tracking-wider mb-1.5">
                Nombre Completo / Encargado *
            </label>
            <input type="text" name="name" id="create_name" required placeholder="Ej: Ariel Subterráneo"
                   class="w-full px-3.5 py-2.5 rounded-xl text-xs notoria-input theme-text-primary placeholder-zinc-400 focus:outline-none focus:border-[#F5B81C] transition-all font-sans">
        </div>

        <!-- Correo Electrónico -->
        <div>
            <label for="create_email" class="block text-xs font-bold theme-text-secondary uppercase tracking-wider mb-1.5">
                Correo Electrónico (Login) *
            </label>
            <input type="email" name="email" id="create_email" required placeholder="ariel@tiochu.com"
                   class="w-full px-3.5 py-2.5 rounded-xl text-xs notoria-input theme-text-primary placeholder-zinc-400 focus:outline-none focus:border-[#F5B81C] transition-all font-mono">
            <p class="text-[10px] theme-text-muted mt-1">Este correo se usará para iniciar sesión en la pantalla de bienvenida.</p>
        </div>

        <!-- Contraseña -->
        <div>
            <label for="create_password" class="block text-xs font-bold theme-text-secondary uppercase tracking-wider mb-1.5">
                Contraseña de Acceso *
            </label>
            <div class="relative">
                <input type="password" name="password" id="create_password" required minlength="6" placeholder="Mínimo 6 caracteres"
                       class="w-full px-3.5 py-2.5 rounded-xl text-xs notoria-input theme-text-primary placeholder-zinc-400 focus:outline-none focus:border-[#F5B81C] transition-all pr-10">
                <button type="button" onclick="togglePasswordVisibility('create_password')" 
                        class="absolute right-3 top-1/2 -translate-y-1/2 theme-text-muted hover:theme-text-primary cursor-pointer text-xs font-bold">
                    Ver
                </button>
            </div>
        </div>

        <div class="pt-4 border-t notoria-border flex flex-col-reverse sm:flex-row sm:items-center sm:justify-end gap-2">
            <button type="button" onclick="closeCreateUserDrawer()" 
                    class="w-full sm:w-auto px-4 py-2.5 rounded-xl notoria-subtile theme-text-secondary hover:theme-text-primary text-xs font-bold transition-all cursor-pointer">
                Cancelar
            </button>
            <button type="submit" 
                    class="w-full sm:w-auto px-5 py-2.5 rounded-xl bg-[#F5B81C] hover:bg-[#e5ac18] text-black font-black text-xs transition-all shadow-md cursor-pointer active:scale-95">
                Guardar Cuenta
            </button>
        </div>
    </form>
</div>

<!-- ==========================================
     5. DRAWER: EDITAR CUENTA
     ========================================== -->
<div id="drawer-edit-user-overlay" class="drawer-overlay" onclick="closeEditUserDrawer()"></div>

<div id="drawer-edit-user" class="drawer-panel" style="z-index: 60;">
    <div class="drawer-header">
        <div>
            <h2 class="text-base font-black text-white tracking-tight">Editar Cuenta de Usuario</h2>
            <p class="text-xs text-zinc-300 mt-0.5">Modifica los datos de acceso o restablece la contraseña.</p>
        </div>
        <button type="button" onclick="closeEditUserDrawer()" class="w-8 h-8 flex items-center justify-center rounded-lg notoria-subtile theme-text-secondary hover:theme-text-primary transition-all cursor-pointer">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>

    <form method="POST" id="edit-user-form" action="" class="drawer-body space-y-4">
        @csrf
        @method('PUT')

        <!-- Nombre -->
        <div>
            <label for="edit_name" class="block text-xs font-bold theme-text-secondary uppercase tracking-wider mb-1.5">
                Nombre Completo *
            </label>
            <input type="text" name="name" id="edit_name" required
                   class="w-full px-3.5 py-2.5 rounded-xl text-xs notoria-input theme-text-primary focus:outline-none focus:border-[#F5B81C] transition-all font-sans">
        </div>

        <!-- Correo Electrónico -->
        <div>
            <label for="edit_email" class="block text-xs font-bold theme-text-secondary uppercase tracking-wider mb-1.5">
                Correo Electrónico (Login) *
            </label>
            <input type="email" name="email" id="edit_email" required
                   class="w-full px-3.5 py-2.5 rounded-xl text-xs notoria-input theme-text-primary focus:outline-none focus:border-[#F5B81C] transition-all font-mono">
        </div>

        <!-- Nueva Contraseña (Opcional) -->
        <div>
            <label for="edit_password" class="block text-xs font-bold theme-text-secondary uppercase tracking-wider mb-1.5">
                Nueva Contraseña <span class="theme-text-muted font-normal">(Opcional)</span>
            </label>
            <div class="relative">
                <input type="password" name="password" id="edit_password" minlength="6" placeholder="Dejar en blanco para conservar la actual"
                       class="w-full px-3.5 py-2.5 rounded-xl text-xs notoria-input theme-text-primary placeholder-zinc-400 focus:outline-none focus:border-[#F5B81C] transition-all pr-10">
                <button type="button" onclick="togglePasswordVisibility('edit_password')" 
                        class="absolute right-3 top-1/2 -translate-y-1/2 theme-text-muted hover:theme-text-primary cursor-pointer text-xs font-bold">
                    Ver
                </button>
            </div>
            <p class="text-[10px] theme-text-muted mt-1">Escribe solo si deseas asignarle una nueva contraseña.</p>
        </div>

        <div class="pt-4 border-t notoria-border flex flex-col-reverse sm:flex-row sm:items-center sm:justify-end gap-2">
            <button type="button" onclick="closeEditUserDrawer()" 
                    class="w-full sm:w-auto px-4 py-2.5 rounded-xl notoria-subtile theme-text-secondary hover:theme-text-primary text-xs font-bold transition-all cursor-pointer">
                Cancelar
            </button>
            <button type="submit" 
                    class="w-full sm:w-auto px-5 py-2.5 rounded-xl bg-[#F5B81C] hover:bg-[#e5ac18] text-black font-black text-xs transition-all shadow-md cursor-pointer active:scale-95">
                Actualizar Cuenta
            </button>
        </div>
    </form>
</div>

<script>
    function openCreateUserDrawer() {
        const overlay = document.getElementById('drawer-create-user-overlay');
        const drawer = document.getElementById('drawer-create-user');
        if (overlay && drawer) {
            overlay.classList.add('is-open');
            drawer.classList.add('is-open');
            document.body.classList.add('drawer-open');
            document.body.style.overflow = 'hidden';
            setTimeout(() => document.getElementById('create_name')?.focus(), 250);
        }
    }

    function closeCreateUserDrawer() {
        const overlay = document.getElementById('drawer-create-user-overlay');
        const drawer = document.getElementById('drawer-create-user');
        if (overlay && drawer) {
            overlay.classList.remove('is-open');
            drawer.classList.remove('is-open');
            document.body.classList.remove('drawer-open');
            document.body.style.overflow = '';
        }
    }

    function openEditUserDrawer(id, name, email) {
        const overlay = document.getElementById('drawer-edit-user-overlay');
        const drawer = document.getElementById('drawer-edit-user');
        const form = document.getElementById('edit-user-form');
        if (overlay && drawer && form) {
            form.action = `/users/${id}`;
            document.getElementById('edit_name').value = name;
            document.getElementById('edit_email').value = email;
            document.getElementById('edit_password').value = '';

            overlay.classList.add('is-open');
            drawer.classList.add('is-open');
            document.body.classList.add('drawer-open');
            document.body.style.overflow = 'hidden';
        }
    }

    function closeEditUserDrawer() {
        const overlay = document.getElementById('drawer-edit-user-overlay');
        const drawer = document.getElementById('drawer-edit-user');
        if (overlay && drawer) {
            overlay.classList.remove('is-open');
            drawer.classList.remove('is-open');
            document.body.classList.remove('drawer-open');
            document.body.style.overflow = '';
        }
    }

    function togglePasswordVisibility(inputId) {
        const input = document.getElementById(inputId);
        if (input) {
            input.type = input.type === 'password' ? 'text' : 'password';
        }
    }

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeCreateUserDrawer();
            closeEditUserDrawer();
        }
    });
</script>
@endsection
