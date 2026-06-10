<x-app-layout>
    <div class="py-12 bg-[#0f1b24] min-h-screen text-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
            
            <div class="border-b border-slate-800 pb-4">
                <h1 class="text-xl font-bold text-white tracking-wide uppercase flex items-center gap-2">
                     Gestión de Usuarios 
                </h1>
                <p class="text-xs text-slate-400 mt-1">Listado del personal corporativo junto a la creación y asignación de cuentas para el acceso al sistema.</p>
            </div>

            {{-- Alertas de Éxito --}}
            @if (session('status') === 'user-created')
                <div class="p-4 bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-xs rounded-lg font-semibold">
                    ✓ El usuario ha sido registrado en el sistema correctamente.
                </div>
            @endif
            @if (session('status') === 'user-updated')
                <div class="p-4 bg-cyan-500/10 border border-cyan-500/20 text-cyan-400 text-xs rounded-lg font-semibold">
                    ✓ Los datos del usuario se han actualizado con éxito.
                </div>
            @endif
            @if (session('status') === 'user-deleted')
                <div class="p-4 bg-rose-500/10 border border-rose-500/20 text-rose-400 text-xs rounded-lg font-semibold">
                    ✓ El colaborador ha sido eliminado de la base de datos de manera definitiva.
                </div>
            @endif
            {{-- Nueva alerta para el cambio de contraseña --}}
            @if (session('success'))
                <div class="p-4 bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-xs rounded-lg font-semibold">
                    ✓ {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="p-4 bg-rose-500/10 border border-rose-500/20 text-rose-400 text-xs rounded-lg font-semibold space-y-1">
                    @foreach ($errors->all() as $error)
                        <p>⚠ {{ $error }}</p>
                    @endforeach
                </div>
            @endif

            {{-- Formulario de Registro --}}
            <div class="bg-[#1a2e3b] border border-slate-700/60 rounded-xl p-6 shadow-xl">
                <div class="border-b border-slate-700/60 pb-3 mb-5">
                    <h2 class="text-xs font-bold uppercase tracking-wider text-white">Registrar Nuevo Usuario</h2>
                </div>

                <form method="POST" action="{{ route('admin.users.store') }}" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                    @csrf

                    <div>
                        <label class="block text-[11px] font-bold text-slate-300 uppercase mb-1.5 tracking-wide">Nombres</label>
                        <input type="text" name="name" value="{{ old('name') }}" class="block w-full bg-[#11222e] border border-slate-600 focus:border-cyan-500 text-white rounded-lg text-xs px-3 py-2.5 outline-none" required />
                        <x-input-error :messages="$errors->get('name')" class="mt-1" />
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold text-slate-300 uppercase mb-1.5 tracking-wide">Apellidos</label>
                        <input type="text" name="apellidos" value="{{ old('apellidos') }}" class="block w-full bg-[#11222e] border border-slate-600 focus:border-cyan-500 text-white rounded-lg text-xs px-3 py-2.5 outline-none" required />
                        <x-input-error :messages="$errors->get('apellidos')" class="mt-1" />
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold text-slate-300 uppercase mb-1.5 tracking-wide">Correo Corporativo</label>
                        <input type="email" name="email" value="{{ old('email') }}" class="block w-full bg-[#11222e] border border-slate-600 focus:border-cyan-500 text-white rounded-lg text-xs px-3 py-2.5 outline-none" required />
                        <x-input-error :messages="$errors->get('email')" class="mt-1" />
                    </div>

                    <div class="space-y-1">
                        <label class="block text-[11px] font-bold tracking-wide text-slate-300 uppercase mb-1.5">Rol Asignado</label>
                        <select name="role_id" class="block w-full bg-[#11222e] border border-slate-600 text-white rounded-lg text-xs px-3 py-2.5 outline-none focus:border-cyan-500 transition-colors" required>
                            <option value="" disabled selected>Seleccione un rol...</option>
                            @foreach($roles as $role)
                                @if(auth()->user()->role && auth()->user()->role->name === 'Responsable' && $role->name === 'Administrador')
                                    @continue
                                @endif
                                <option value="{{ $role->id }}">{{ $role->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold text-slate-300 uppercase mb-1.5 tracking-wide">Contraseña</label>
                        <input type="password" name="password" class="block w-full bg-[#11222e] border border-slate-600 focus:border-cyan-500 text-white rounded-lg text-xs px-3 py-2.5 outline-none" required />
                        <x-input-error :messages="$errors->get('password')" class="mt-1" />
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold text-slate-300 uppercase mb-1.5 tracking-wide">Confirmar Contraseña</label>
                        <input type="password" name="password_confirmation" class="block w-full bg-[#11222e] border border-slate-600 focus:border-cyan-500 text-white rounded-lg text-xs px-3 py-2.5 outline-none" required />
                    </div>

                    <div class="md:col-span-2 lg:col-span-3 pt-2 flex justify-end">
                        <button type="submit" class="px-6 py-2.5 bg-cyan-600 hover:bg-cyan-500 text-white font-bold text-xs uppercase tracking-wider rounded-lg transition-colors shadow-md">
                            Registrar
                        </button>
                    </div>
                </form>
            </div>

            {{-- Sección de Tabla y Modales coordinados por Alpine --}}
            <div x-data="{ editModal: false, selectedUser: { id: '', name: '', apellidos: '', email: '', role_id: '' } }">
                
                <div class="bg-[#1a2e3b] border border-slate-700/60 rounded-xl p-6 shadow-xl mb-8">
                    <div class="border-b border-slate-700/60 pb-3 mb-5">
                        <h2 class="text-xs font-bold uppercase tracking-wider text-white">Lista de Personal Registrado</h2>
                    </div>

                    <div class="overflow-x-auto rounded-lg border border-slate-700/60 bg-[#11222e]">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-[#0b161f] border-b border-slate-700/80 text-[10px] font-bold uppercase tracking-wider text-slate-300">
                                    <th class="p-4">Colaborador</th>
                                    <th class="p-4">Correo Corporativo</th>
                                    <th class="p-4 text-center">Rol Asignado</th>
                                    <th class="p-4 text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="text-xs text-slate-300 divide-y divide-slate-700/40">
                                @foreach($users as $u)
                                    <tr class="hover:bg-slate-800/30 transition-colors">
                                        <td class="p-4 font-semibold text-white">
                                            {{ $u->name }} {{ $u->apellidos }}
                                        </td>
                                        <td class="p-4 text-slate-400 font-mono text-[11px]">
                                            {{ $u->email }}
                                        </td>
                                        <td class="p-4 text-center">
                                            <span class="inline-block px-3 py-1 rounded-full text-[10px] font-bold tracking-wide {{ $u->role?->name === 'Administrador' ? 'bg-amber-500/10 text-amber-400 border border-amber-500/20' : 'bg-cyan-500/10 text-cyan-400 border border-cyan-500/20' }}">
                                                {{ $u->role ? $u->role->name : 'Sin Rol' }}
                                            </span>
                                        </td>
                                        <td class="p-4 text-center">
                                            <div class="flex items-center justify-center gap-2">
                                                <button 
                                                    type="button"
                                                    @click="selectedUser = { id: '{{ $u->id }}', name: '{{ addslashes($u->name) }}', apellidos: '{{ addslashes($u->apellidos) }}', email: '{{ $u->email }}', role_id: '{{ $u->role_id }}' }; editModal = true;"
                                                    class="px-3 py-1.5 bg-slate-700 hover:bg-cyan-600 text-white rounded font-bold text-[11px] transition-colors"
                                                >
                                                    Gestionar
                                                </button>

                                                <form action="{{ route('admin.users.destroy', $u->id) }}" method="POST" onsubmit="return confirm('¿Está completamente seguro de eliminar de forma permanente a este colaborador del sistema de MySQL?');" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button 
                                                        type="submit" 
                                                        class="px-3 py-1.5 bg-rose-950/40 hover:bg-rose-600 border border-rose-500/30 text-rose-400 hover:text-white rounded font-bold text-[11px] transition-colors"
                                                    >
                                                        Eliminar
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Modal Único de Gestión (Alpine.js) --}}
                <div 
                    x-show="editModal" 
                    class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm"
                    style="display: none;"
                    x-transition
                >
                    <div class="bg-[#1a2e3b] border border-slate-700 rounded-xl max-w-md w-full p-6 space-y-6 shadow-2xl" @click.away="editModal = false">
                        
                        <div class="flex items-center justify-between border-b border-slate-700/60 pb-2">
                            <h3 class="text-xs font-bold uppercase tracking-wider text-white">Gestionar Colaborador</h3>
                            <button type="button" @click="editModal = false" class="text-slate-400 hover:text-white text-xl">&times;</button>
                        </div>

                        {{-- SECCIÓN A: MODIFICAR DATOS BÁSICOS --}}
                        <form :action="'/admin/usuarios/' + selectedUser.id" method="POST" class="space-y-4">
                            @csrf
                            @method('PUT')

                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-[10px] font-bold uppercase text-slate-400 mb-1">Nombres</label>
                                    <input type="text" name="name" x-model="selectedUser.name" class="block w-full bg-[#11222e] border border-slate-600 text-white rounded-lg text-xs px-3 py-2 outline-none focus:border-amber-500" required />
                                </div>
                                <div>
                                    <label class="block text-[10px] font-bold uppercase text-slate-400 mb-1">Apellidos</label>
                                    <input type="text" name="apellidos" x-model="selectedUser.apellidos" class="block w-full bg-[#11222e] border border-slate-600 text-white rounded-lg text-xs px-3 py-2 outline-none focus:border-amber-500" required />
                                </div>
                            </div>

                            <div>
                                <label class="block text-[10px] font-bold uppercase text-slate-400 mb-1">Correo Corporativo</label>
                                <input type="email" name="email" x-model="selectedUser.email" class="block w-full bg-[#11222e] border border-slate-600 text-white rounded-lg text-xs px-3 py-2 outline-none focus:border-amber-500" required />
                            </div>

                            <div>
                                <label class="block text-[10px] font-bold uppercase text-slate-400 mb-1">Rol en el Sistema</label>
                                <select name="role_id" x-model="selectedUser.role_id" class="block w-full bg-[#11222e] border border-slate-600 text-slate-200 rounded-lg text-xs px-3 py-2 outline-none focus:border-amber-500" required>
                                    @foreach($roles as $role)
                                        <option value="{{ $role->id }}">{{ $role->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="flex justify-end gap-2 pt-2">
                                <button type="submit" class="w-full px-4 py-2 bg-amber-600 text-white text-xs font-bold uppercase rounded-lg hover:bg-amber-500 transition-colors">
                                    Guardar Cambios de Perfil
                                </button>
                            </div>
                        </form>

                        {{-- SECCIÓN B: FORMULARIO INDEPENDIENTE PARA CAMBIAR CONTRASEÑA --}}
                        <div class="border-t border-slate-700/60 pt-4">
                            <div class="mb-3">
                                <h4 class="text-[11px] font-bold uppercase tracking-wider text-cyan-400 flex items-center gap-1">
                                    🔑 Restablecer Contraseña Físicamente
                                </h4>
                                <p class="text-[10px] text-slate-400 mt-0.5">Esto modificará de manera inmediata las credenciales en el servidor de MySQL.</p>
                            </div>

                            <form :action="'/admin/usuarios/' + selectedUser.id + '/reset-password'" method="POST" class="space-y-3">
                                @csrf
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-[10px] font-bold uppercase text-slate-400 mb-1">Nueva Clave</label>
                                        <input type="password" name="password" placeholder="Mín. 6 caracteres" class="block w-full bg-[#11222e] border border-slate-600 text-white rounded-lg text-xs px-3 py-2 outline-none focus:border-cyan-500" required />
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-bold uppercase text-slate-400 mb-1">Confirmar Clave</label>
                                        <input type="password" name="password_confirmation" placeholder="Repita clave" class="block w-full bg-[#11222e] border border-slate-600 text-white rounded-lg text-xs px-3 py-2 outline-none focus:border-cyan-500" required />
                                    </div>
                                </div>

                                <div class="flex justify-end pt-1">
                                    <button type="submit" class="w-full px-4 py-2 bg-cyan-700 hover:bg-cyan-600 text-white text-xs font-bold uppercase rounded-lg transition-colors shadow-md">
                                        Actualizar Credenciales cloud
                                    </button>
                                </div>
                            </form>
                        </div>

                    </div>
                </div>

            </div> 
        </div>
    </div>
</x-app-layout>