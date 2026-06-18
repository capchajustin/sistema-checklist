<x-app-layout>
    <div class="py-8 bg-[#0f1b24] min-h-screen text-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            
            <div class="border-b border-slate-800 pb-4 flex justify-between items-center">
                <div>
                    <h1 class="text-lg font-bold text-white tracking-wide uppercase">📋 Control de Entregables Diarios</h1>
                    <p class="text-xs text-slate-400 mt-0.5">Checklist por bloques obligatorios segun el turno del trabajador.</p>
                </div>
                <div class="flex flex-col items-end">
                    <span class="text-xs font-mono bg-slate-800 border border-slate-700 px-3 py-1.5 rounded-lg text-cyan-400 font-bold shadow-sm flex items-center gap-1.5">
                        📅 <span id="realtime-date">Cargando fecha...</span> | ⏱ <span id="realtime-clock">00:00:00 AM</span>
                    </span>
                </div>
            </div>

            @if (session('status') === 'success')
                <div class="p-3 bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-xs rounded-lg font-semibold">
                    ✓ Evidencia y datos del bloque guardados correctamente en el servidor.
                </div>
            @endif

            @if ($errors->any())
                <div class="p-3 bg-rose-500/10 border border-rose-500/20 text-rose-400 text-xs rounded-lg font-semibold">
                    ⚠️ {{ $errors->first() }}
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-12 gap-6 items-start">
                
                <div class="md:col-span-4 space-y-2 max-h-[75vh] overflow-y-auto pr-1 scrollbar-thin scrollbar-thumb-slate-800">
                    <h2 class="text-xs font-bold uppercase text-slate-400 tracking-wider px-1 mb-3">Bloques de la Jornada</h2>
                    
                    <div class="flex flex-col gap-2">
                        @foreach($timeBlocks as $block)
                            @php
                                $isCompleted = in_array($block, $completedBlocks);
                                $isSelected = ($block === $selectedBlock);
                                
                                // Determinar estados temporales básicos para pintar la barra lateral
                                list($bStart, $bEnd) = explode(' - ', $block);
                                $currentL = \Carbon\Carbon::now('America/Lima')->format('H:i');
                                $isCurrentLoop = ($currentL >= $bStart && $currentL < $bEnd);
                            @endphp
                            
                            <a href="{{ route('dashboard', ['block' => $block]) }}" 
                               class="w-full text-left p-3.5 rounded-xl border text-xs font-semibold flex items-center justify-between transition-all duration-150
                               {{ $isSelected ? 'bg-cyan-600 border-cyan-500 text-white shadow-lg shadow-cyan-600/10' : 'bg-[#1a2e3b] hover:bg-[#203646]' }}
                               {{ !$isSelected && $isCompleted ? 'border-emerald-500/30 text-emerald-400 bg-[#122834]' : 'border-slate-700/60' }}
                               {{ $isCurrentLoop && !$isSelected && !$isCompleted ? 'ring-1 ring-amber-500/50' : '' }}">
                                
                                <div class="flex items-center gap-3">
                                    <span class="font-mono tracking-wider flex items-center gap-1.5">
                                        ⏱ {{ $block }}
                                        @if($isCurrentLoop)
                                            <span class="w-2 h-2 rounded-full bg-amber-400 inline-block animate-ping" title="Bloque Actual"></span>
                                        @endif
                                    </span>
                                </div>

                                <div>
                                    @if($isCompleted)
                                        <span class="px-2 py-0.5 bg-emerald-500/20 text-emerald-300 rounded text-[10px] font-bold uppercase">✓ Listo</span>
                                    @else
                                        <span class="px-2 py-0.5 bg-slate-800 text-slate-400 rounded text-[10px] font-bold uppercase">○ Pendiente</span>
                                    @endif
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>

                <div class="md:col-span-8 bg-[#1a2e3b] border border-slate-700/60 rounded-xl p-6 shadow-xl">
                    <div class="border-b border-slate-700/50 pb-3 mb-4 flex justify-between items-center">
                        <div>
                            <span class="text-[10px] font-bold text-amber-400 uppercase tracking-widest font-mono">Formulario de Reporte</span>
                            <h2 class="text-base font-bold text-white mt-0.5">Bloque seleccionado: {{ $selectedBlock }}</h2>
                        </div>
                    </div>

                    @if(!$currentSubmission)
                        @if($isTimeValid)
                            <form action="{{ route('checklist.submit') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                                @csrf
                                <input type="hidden" name="time_block" value="{{ $selectedBlock }}">

                                <div>
                                    <label class="block text-xs font-bold text-slate-300 uppercase tracking-wide mb-1.5">Nombre del Proyecto o Área:</label>
                                    <input type="text" name="project_name" placeholder="Ej: Sistema Control Actividades / Mantenimiento TI" 
                                           class="block w-full bg-[#11222e] border border-slate-600 focus:border-cyan-500 text-white rounded-lg text-xs p-2.5 outline-none transition-colors" required>
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-slate-300 uppercase tracking-wide mb-1.5">Nombre de la Actividad realizada:</label>
                                    <input type="text" name="activity_title" placeholder="Ej: Pruebas unitarias de login / Diseño de Base de Datos" 
                                           class="block w-full bg-[#11222e] border border-slate-600 focus:border-cyan-500 text-white rounded-lg text-xs p-2.5 outline-none transition-colors" required>
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-slate-300 uppercase tracking-wide mb-1.5">Descripción del Trabajo desarrollado:</label>
                                    <textarea name="description" placeholder="Detalla los avances, actividades ejecutadas o incidencias..." rows="4" 
                                              class="block w-full bg-[#11222e] border border-slate-600 focus:border-cyan-500 text-white rounded-lg text-xs p-2.5 outline-none resize-none transition-colors" required></textarea>
                                </div>

                                <div class="bg-[#11222e] border border-slate-600 border-dashed rounded-lg p-4">
                                    <label class="block text-xs font-bold text-slate-300 uppercase tracking-wide mb-2">Captura de Pantalla / Foto de Evidencia:</label>
                                    <input type="file" name="evidence_photo" accept="image/*" 
                                           class="block w-full text-xs text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-slate-700 file:text-white hover:file:bg-slate-600 cursor-pointer" required />
                                </div>

                                <button type="submit" class="w-full py-3 bg-cyan-600 hover:bg-cyan-500 text-white font-bold text-xs uppercase tracking-wider rounded-lg transition-colors shadow-md">
                                    Guardar Registro de Bloque Horario
                                </button>
                            </form>
                        @else
                            <div class="p-8 text-center bg-[#0b161f]/40 border border-dashed border-slate-700 rounded-xl space-y-3">
                                <div class="text-3xl">🔒</div>
                                <h3 class="text-sm font-bold text-amber-400">Bloque Inactivo por Horario</h3>
                                <p class="text-xs text-slate-400 max-w-md mx-auto leading-relaxed">
                                    Aún no es momento o ya expiró el rango de entrega para este tramo. De acuerdo a las políticas del **Grupo ConsigueVentas**, solo puedes reportar tus tareas en tiempo real mientras transcurre el bloque de hora correspondiente.
                                </p>
                                <div class="inline-block mt-2 px-3 py-1 bg-slate-800 rounded-md text-[11px] font-mono text-slate-400">
                                    Rango del bloque: <span class="text-white font-bold">{{ $selectedBlock }}</span>
                                </div>
                            </div>
                        @endif
                    @else
                        <div class="bg-[#0b161f]/60 border border-emerald-500/20 p-5 rounded-xl space-y-4">
                            <div class="flex justify-between items-center border-b border-slate-800 pb-2">
                                <span class="text-[10px] bg-emerald-500/20 text-emerald-400 font-bold px-2 py-0.5 rounded uppercase">Entregable Guardado</span>
                                <span class="text-[10px] text-slate-500 font-mono">Enviado hoy a las {{ $currentSubmission->created_at->setTimezone('America/Lima')->format('h:i A') }}</span>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                                <div>
                                    <span class="text-slate-500 font-bold block uppercase text-[10px]">Proyecto / Área:</span>
                                    <p class="text-white font-medium mt-0.5">{{ $currentSubmission->project_name }}</p>
                                </div>
                                <div>
                                    <span class="text-slate-500 font-bold block uppercase text-[10px]">Actividad:</span>
                                    <p class="text-white font-medium mt-0.5">{{ $currentSubmission->activity_title }}</p>
                                </div>
                            </div>

                            <div class="text-xs">
                                <span class="text-slate-500 font-bold block uppercase text-[10px]">Descripción del Avance:</span>
                                <p class="text-slate-300 leading-relaxed mt-1 whitespace-pre-line bg-[#11222e] p-3 rounded-lg border border-slate-800">
                                    {{ $currentSubmission->description }}
                                </p>
                            </div>

                           <div class="pt-2">
    <span class="text-slate-500 font-bold block uppercase text-[10px] mb-2">Archivo de Evidencia:</span>
    
    @if(Str::startsWith($currentSubmission->evidence_photo, 'http'))
        <a href="{{ $currentSubmission->evidence_photo }}" target="_blank" 
           class="inline-flex items-center gap-2 px-4 py-2.5 bg-slate-800 border border-slate-700 hover:bg-slate-750 text-cyan-400 rounded-lg font-semibold text-xs transition-colors shadow-sm">
            📸 Ver captura enviada (Cloudinary)
        </a>
    @else
        <a href="{{ asset('storage/' . $currentSubmission->evidence_photo) }}" target="_blank" 
           class="inline-flex items-center gap-2 px-4 py-2.5 bg-slate-800 border border-slate-700 hover:bg-slate-750 text-cyan-400 rounded-lg font-semibold text-xs transition-colors shadow-sm">
            📸 Ver captura enviada (Local)
        </a>
    @endif
</div>
                        </div>
                    @endif
                </div>

            </div>
        </div>
    </div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const selectedBlock = "{{ $selectedBlock }}";
    const clockElement  = document.getElementById('realtime-clock');
    const dateElement   = document.getElementById('realtime-date');
    const allBlocks     = @json($timeBlocks);
    const isBlockAlreadyCompleted = {{ in_array($selectedBlock, $completedBlocks) ? 'true' : 'false' }};

    let alert10Triggered = false;
    let alert5Triggered  = false;
    let blockExpiredNotified = false;

    function speak(msg) {
        if (!('speechSynthesis' in window)) return;
        window.speechSynthesis.cancel();
        const u = new SpeechSynthesisUtterance(msg);
        u.lang = 'es-PE'; u.rate = 0.95; u.pitch = 1.0;
        const trySpeak = () => {
            const voices = window.speechSynthesis.getVoices();
            const v = voices.find(v => /es/.test(v.lang));
            if (v) u.voice = v;
            window.speechSynthesis.speak(u);
        };
        window.speechSynthesis.getVoices().length ? trySpeak()
            : (window.speechSynthesis.onvoiceschanged = trySpeak);
    }

    function toMins(timeStr) {
        const [h, m] = timeStr.split(':').map(Number);
        return h * 60 + m;
    }

    // Muestra banner de bloque expirado y bloquea el formulario
    function handleBlockExpired() {
        if (blockExpiredNotified) return;
        blockExpiredNotified = true;

        // Deshabilitar formulario si existe
        const form = document.querySelector('form[action*="submit"]');
        if (form) {
            form.querySelectorAll('input, textarea, button, select').forEach(el => {
                el.disabled = true;
            });
        }

        // Crear banner de aviso 
        if (!document.getElementById('block-expired-banner')) {
            // Encontrar bloque activo actual para ofrecer el enlace
            const now = new Date();
            const nowMins = now.getHours() * 60 + now.getMinutes();
            let nextBlock = null;
            for (const block of allBlocks) {
                const [s, e] = block.split(' - ');
                if (nowMins >= toMins(s) && nowMins < toMins(e)) {
                    nextBlock = block; break;
                }
            }

            const banner = document.createElement('div');
            banner.id = 'block-expired-banner';
            
            banner.style.cssText = `
                display: flex;
                align-items: center;
                justify-content: space-between;
                width: 100%;
                margin-top: 1rem;
                margin-bottom: 1.5rem;
                background: rgba(239, 68, 68, 0.1);
                border: 1px solid rgba(239, 68, 68, 0.25);
                color: #fca5a5;
                padding: 12px 16px;
                font-size: 12px;
                font-weight: 600;
                border-radius: 0.75rem;
                gap: 16px;
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            `;

            const msg = document.createElement('span');
            msg.innerHTML = `🔒 El bloque horario <span class="text-white font-bold">${selectedBlock}</span> ha expirado de forma definitiva. El formulario para este tramo ha sido cerrado por el sistema.`;

            banner.appendChild(msg);

            if (nextBlock) {
                const btn = document.createElement('a');
                btn.href = "{{ route('dashboard') }}?block=" + encodeURIComponent(nextBlock);
                btn.textContent = `Ir al bloque activo: ${nextBlock} →`;
                btn.style.cssText = `
                    background: rgba(239, 68, 68, 0.2);
                    border: 1px solid rgba(239, 68, 68, 0.4);
                    color: #ffffff;
                    padding: 6px 14px;
                    border-radius: 0.5rem;
                    text-decoration: none;
                    white-space: nowrap;
                    font-size: 11px;
                    transition: all 0.15s ease-in-out;
                `;
                
                btn.onmouseenter = () => btn.style.background = 'rgba(239, 68, 68, 0.3)';
                btn.onmouseleave = () => btn.style.background = 'rgba(239, 68, 68, 0.2)';
                
                banner.appendChild(btn);
            }

            const reportForm = document.querySelector('form[action*="submit"]') || document.querySelector('.grid') || document.getElementById('realtime-clock')?.parentElement;
            
            if (reportForm) {
                reportForm.parentNode.insertBefore(banner, reportForm);
            } else {
                const mainContent = document.querySelector('.max-w-7xl');
                if (mainContent) mainContent.prepend(banner);
            }
        }
    }

    function tick() {
        const now = new Date();

        // Fecha
        if (dateElement) {
            const d  = String(now.getDate()).padStart(2,'0');
            const mo = String(now.getMonth()+1).padStart(2,'0');
            dateElement.textContent = `${d}/${mo}/${now.getFullYear()}`;
        }

        // Reloj
        if (clockElement) {
            const h24 = now.getHours();
            const mm  = String(now.getMinutes()).padStart(2,'0');
            const ss  = String(now.getSeconds()).padStart(2,'0');
            const ap  = h24 >= 12 ? 'PM' : 'AM';
            const h12 = String(h24 % 12 || 12).padStart(2,'0');
            clockElement.textContent = `${h12}:${mm}:${ss} ${ap}`;
        }

        if (!selectedBlock.includes(' - ')) return;

        const [, endStr] = selectedBlock.split(' - ');
        const nowMins    = now.getHours() * 60 + now.getMinutes();
        const endMins    = toMins(endStr);
        const totalSecs  = (endMins * 60) - (now.getHours()*3600 + now.getMinutes()*60 + now.getSeconds());
        const minsLeft   = Math.ceil(totalSecs / 60);

        // Alertas de voz 
        if (!isBlockAlreadyCompleted) {
            if (minsLeft > 11 || minsLeft < 0) {
                alert10Triggered = false;
                alert5Triggered  = false;
            }
            if (minsLeft <= 10 && minsLeft > 9 && !alert10Triggered) {
                speak('Atención. Quedan 10 minutos. Realiza tu Checklist.');
                alert10Triggered = true;
            }
            if (minsLeft <= 5 && minsLeft > 4 && !alert5Triggered) {
                speak('Atención urgente. Quedan solo 5 minutos. Realiza tu Checklist antes del bloqueo.');
                alert5Triggered = true;
            }
        }

        if (nowMins >= endMins) {
            handleBlockExpired();
        }
    }

    tick();
    setInterval(tick, 1000);
});
</script>
</x-app-layout>