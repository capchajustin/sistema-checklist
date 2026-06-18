<x-app-layout>
    <div class="py-8 bg-[#0f1b24] min-h-screen text-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            
            <div class="border-b border-slate-800 pb-4 flex justify-between items-center">
                <div>
                    <h1 class="text-lg font-bold text-white tracking-wide uppercase">🛡️ Panel de Supervisión Horaria</h1>
                    <p class="text-xs text-slate-400 mt-0.5">Auditoría, aprobación de actividades y control de horas del personal.</p>
                </div>
            </div>

            @if (session('status_updated'))
                <div class="p-3 bg-cyan-500/10 border border-cyan-500/20 text-cyan-400 text-xs rounded-lg font-semibold">
                    ✓ {{ session('status_updated') }}
                </div>
            @endif

            <div class="bg-[#1a2e3b] border border-slate-700/60 rounded-xl p-5 shadow-xl">
                <form method="GET" action="{{ route('admin.dashboard') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-4 items-end">
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wide mb-1.5">Seleccionar Colaborador:</label>
                        <select name="user_id" class="w-full bg-[#11222e] border border-slate-600 focus:border-cyan-500 text-white rounded-lg text-xs p-2.5 outline-none transition-all" required>
                            <option value="">-- Seleccione Trabajador --</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ $userId == $user->id ? 'selected' : '' }}>{{ $user->name }} ({{ $user->email }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wide mb-1.5">Fecha de Tareas (Día):</label>
                        <input type="date" name="date" value="{{ $dateFilter }}" class="w-full bg-[#11222e] border border-slate-600 focus:border-cyan-500 text-white rounded-lg text-xs p-2.5 outline-none">
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wide mb-1.5">Mes de Control Horario:</label>
                        <input type="month" name="month" value="{{ $monthFilter }}" class="w-full bg-[#11222e] border border-slate-600 focus:border-cyan-500 text-white rounded-lg text-xs p-2.5 outline-none">
                    </div>

                    <button type="submit" class="w-full py-2.5 bg-cyan-600 hover:bg-cyan-500 text-white font-bold text-xs uppercase tracking-wider rounded-lg transition-colors shadow-md">
                        🔍 Buscar Reportes
                    </button>
                </form>
            </div>

            @if($userId)
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="bg-[#1a2e3b] border border-emerald-500/20 p-5 rounded-xl flex justify-between items-center shadow-lg">
                        <div>
                            <span class="text-[10px] uppercase font-bold text-emerald-400 font-mono tracking-wider">Horas Válidas del Día Seleccionado</span>
                            <h3 class="text-3xl font-extrabold text-white mt-1 font-mono">{{ $dayHours }} <span class="text-sm font-normal text-slate-500">Horas Aptas</span></h3>
                        </div>
                        <div class="text-2xl bg-emerald-500/10 p-2.5 rounded-lg text-emerald-400">⏱️</div>
                    </div>

                    <div class="bg-[#1a2e3b] border border-cyan-500/20 p-5 rounded-xl flex justify-between items-center shadow-lg">
                        <div>
                            <span class="text-[10px] uppercase font-bold text-cyan-400 font-mono tracking-wider">Total Acumulado Apto del Mes</span>
                            <h3 class="text-3xl font-extrabold text-white mt-1 font-mono">{{ $monthHours }} <span class="text-sm font-normal text-slate-500">Horas Acumuladas</span></h3>
                        </div>
                        <div class="text-2xl bg-cyan-500/10 p-2.5 rounded-lg text-cyan-400">📊</div>
                    </div>
                </div>

                <div class="space-y-4">
                    <h2 class="text-xs font-bold uppercase text-slate-400 tracking-wider px-1">Actividades Registradas</h2>
                    
                    @if($submissions->isEmpty())
                        <div class="p-10 text-center bg-[#1a2e3b] border border-dashed border-slate-700 rounded-xl">
                            <p class="text-xs text-slate-400">El colaborador no ha subido ninguna evidencia ni reporte en la fecha consultada.</p>
                        </div>
                    @else
                        <div class="space-y-4">
                            @foreach($submissions as $sub)
                                <div class="bg-[#1a2e3b] border rounded-xl p-5 shadow-md transition-all
                                    {{ $sub->status === 'approved' ? 'border-emerald-500/30 bg-[#122834]' : ($sub->status === 'rejected' ? 'border-rose-500/30 bg-[#24161b]' : 'border-slate-700/60') }}">
                                    
                                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center border-b border-slate-700/40 pb-3 mb-4 gap-3">
                                        <div class="flex items-center gap-3">
                                            <span class="px-3 py-1.5 bg-slate-900 border border-slate-700 text-cyan-400 font-mono rounded-lg text-xs font-bold">
                                                ⏱️ Bloque: {{ $sub->time_block }}
                                            </span>
                                            
                                            @if($sub->status === 'approved')
                                                <span class="px-2.5 py-1 bg-emerald-500/20 text-emerald-400 rounded text-[10px] font-bold uppercase tracking-wide">✓ APTO</span>
                                            @elseif($sub->status === 'rejected')
                                                <span class="px-2.5 py-1 bg-rose-500/20 text-rose-400 rounded text-[10px] font-bold uppercase tracking-wide">❌ NO APTO</span>
                                            @else
                                                <span class="px-2.5 py-1 bg-amber-500/20 text-amber-400 rounded text-[10px] font-bold uppercase tracking-wide">○ POR EVALUAR</span>
                                            @endif
                                        </div>
                                        
                                        <form action="{{ route('admin.submission.status', $sub->id) }}" method="POST">
                                            @csrf
                                            <select name="status" onchange="this.form.submit()" class="bg-[#11222e] border border-slate-600 focus:border-cyan-500 text-slate-200 rounded-lg text-xs p-2 outline-none font-semibold cursor-pointer">
                                                <option value="pending" {{ $sub->status === 'pending' ? 'selected' : '' }}>⚖️ Pendiente de Revisión</option>
                                                <option value="approved" {{ $sub->status === 'approved' ? 'selected' : '' }}>👍 Clasificar como APTO</option>
                                                <option value="rejected" {{ $sub->status === 'rejected' ? 'selected' : '' }}>👎 Clasificar como NO APTO</option>
                                            </select>
                                        </form>
                                    </div>

                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 text-xs">
                                        <div class="md:col-span-2 space-y-3">
                                            <div>
                                                <span class="text-slate-500 font-bold block uppercase text-[10px]">Proyecto o Área Destino:</span>
                                                <p class="text-white font-medium text-xs mt-0.5">{{ $sub->project_name }}</p>
                                            </div>
                                            <div>
                                                <span class="text-slate-500 font-bold block uppercase text-[10px]">Título del Avance:</span>
                                                <p class="text-white font-medium text-xs mt-0.5">{{ $sub->activity_title }}</p>
                                            </div>
                                            <div>
                                                <span class="text-slate-500 font-bold block uppercase text-[10px]">Descripción Técnica del Trabajo:</span>
                                                <p class="text-slate-300 leading-relaxed mt-1 whitespace-pre-line bg-[#11222e] p-3 rounded-lg border border-slate-800 font-mono text-[11px]">
                                                    {{ $sub->description }}
                                                </p>
                                            </div>
                                        </div>
                                        
                                        <div class="flex flex-col bg-[#11222e] p-3 rounded-lg border border-slate-800 justify-between">
                                            <div>
                                                <span class="text-slate-500 font-bold block uppercase text-[10px] mb-2">Captura del Sistema Adjunta:</span>
                                                <div class="relative rounded overflow-hidden bg-slate-900 border border-slate-700 h-36 flex items-center justify-center">
                                                    @if($sub->evidence_photo)
                                                        @if(\Illuminate\Support\Str::startsWith($sub->evidence_photo, 'http'))
                                                            <img src="{{ $sub->evidence_photo }}" class="object-cover w-full h-full opacity-80 hover:opacity-100 transition-opacity" alt="Evidencia de trabajo">
                                                        @else
                                                            <img src="{{ asset('storage/' . $sub->evidence_photo) }}" class="object-cover w-full h-full opacity-80 hover:opacity-100 transition-opacity" alt="Evidencia de trabajo">
                                                        @endif
                                                    @else
                                                        <span class="text-slate-600 text-[10px]">Sin archivo visual</span>
                                                    @endif
                                                </div>
                                            </div>
                                            
                                            @if($sub->evidence_photo)
                                                <a href="{{ \Illuminate\Support\Str::startsWith($sub->evidence_photo, 'http') ? $sub->evidence_photo : asset('storage/' . $sub->evidence_photo) }}" 
                                                   target="_blank" 
                                                   class="mt-3 text-center py-2 bg-slate-800 hover:bg-slate-700 text-cyan-400 font-bold rounded-md text-[11px] block transition-colors">
                                                    🔍 Ver Evidencia en Pantalla Completa
                                                </a>
                                            @else
                                                <button type="button" disabled class="mt-3 text-center py-2 bg-slate-800/40 text-slate-600 font-bold rounded-md text-[11px] block cursor-not-allowed">
                                                    🚫 Sin Archivo Adjunto
                                                </button>
                                            @endif
                                        </div>
                                    </div>

                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            @else
                <div class="p-16 text-center bg-[#1a2e3b] border border-slate-700/60 rounded-xl">
                    <div class="text-5xl mb-3">📋</div>
                    <h3 class="text-sm font-bold text-cyan-400 uppercase tracking-wider">Esperando Parámetros de Auditoría</h3>
                    <p class="text-xs text-slate-400 max-w-sm mx-auto leading-relaxed mt-1.5">
                        Selecciona un trabajador de la lista desplegable y ajusta los rangos de fecha para auditar sus entregables y computar sus horas de trabajo válidas.
                    </p>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>