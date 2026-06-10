<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Perfil - ConsigueVentas</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#0f1b24] font-sans antialiased min-h-screen text-slate-200 pb-12">

    <nav class="bg-[#1a2e3b] border-b border-slate-700/50 px-6 py-4 shadow-md mb-8">
        <div class="max-w-6xl mx-auto flex justify-between items-center">
            <div class="flex items-center space-x-3">
                <img src="{{ asset('images/LogoCV.png') }}" alt="Logo" class="h-8 w-auto object-contain">
                <span class="text-white font-bold tracking-wider text-sm hidden sm:inline">Apartado de Perfil del Trabajador</span>
            </div>
            <div class="flex items-center space-x-4">
                <a href="{{ route('dashboard') }}" class="text-xs bg-slate-800 hover:bg-slate-700 text-cyan-400 font-bold uppercase tracking-wider px-4 py-2 rounded-lg border border-slate-600 transition">
                    ← Volver al Checklist
                </a>
            </div>
        </div>
    </nav>

    <main class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-white tracking-wide uppercase">Configuración del Perfil</h1>
            <p class="text-xs text-slate-400 mt-1">Mantenga sus datos de contacto y fotografía actualizados para el correcto monitoreo de actividades.</p>
        </div>

        <form method="post" action="{{ route('profile.update') }}" enctype="multipart/form-data">
            @csrf
            @method('patch')

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
                
                <div class="lg:col-span-1 bg-[#1a2e3b] border border-slate-700/50 rounded-2xl p-6 shadow-xl text-center">
                    
                    <div class="relative w-28 h-28 mx-auto mb-4">
    @if(Auth::user()->avatar)
        <img src="{{ asset('storage/' . Auth::user()->avatar) }}" 
             alt="Foto de Perfil" 
             class="w-full h-full object-cover rounded-full border-2 border-cyan-500 shadow-lg">
    @else
        <div class="w-full h-full bg-cyan-600/20 text-cyan-400 border-2 border-cyan-500 rounded-full flex items-center justify-center text-3xl font-bold uppercase shadow-inner">
            {{ substr(Auth::user()->name, 0, 2) }}
        </div>
    @endif
</div>
                    
                    <h3 class="text-lg font-bold text-white tracking-wide whitespace-normal break-words px-2">
                        {{ Auth::user()->name }} {{ Auth::user()->apellidos }}
                    </h3>
                    
                    <span class="inline-block mt-3 px-4 py-1.5 bg-cyan-950/60 text-cyan-400 border border-cyan-800/80 rounded-full text-xs font-bold uppercase tracking-widest shadow-sm">
                        Cargo: {{ Auth::user()->role ? Auth::user()->role->name : 'DEVELOPER' }}
                    </span>

                    <hr class="my-5 border-slate-700/50">

                    <div class="space-y-4 text-left text-xs">
                        <div>
                            <span class="text-slate-400 block uppercase font-medium tracking-wider">Departamento:</span>
                            <p class="text-slate-200 font-semibold text-sm mt-0.5">Tecnología de la Información</p>
                        </div>
                        <div>
                            <span class="text-slate-400 block uppercase font-medium tracking-wider">Área:</span>
                            <p class="text-emerald-400 font-bold text-sm flex items-center mt-0.5">
                                <span class="w-2 h-2 rounded-full bg-emerald-400 inline-block mr-1.5 animate-pulse"></span> Diseño de Software
                            </p>
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-2">
                    <div class="bg-[#1a2e3b] border border-slate-700/50 rounded-2xl p-6 shadow-xl space-y-5">
                        
                        <div class="flex items-center space-x-2 pb-3 border-b border-slate-700/50">
                            <svg class="h-5 w-5 text-cyan-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                            <h2 class="text-sm font-bold uppercase tracking-wider text-white">Actualizar Información Personal</h2>
                        </div>

                        <div>
                            <label for="avatar" class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1.5">Fotografía de Perfil</label>
                            <input id="avatar" name="avatar" type="file" accept="image/*" class="block w-full text-xs text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-slate-800 file:text-cyan-400 hover:file:bg-slate-700 cursor-pointer bg-[#11222e] border border-slate-600 rounded-lg p-1 outline-none focus:border-cyan-500" />
                            <p class="text-[10px] text-slate-400 mt-1">Formatos permitidos: JPG, PNG. Máximo 2MB.</p>
                            @if($errors->get('avatar'))
                                <p class="text-red-400 text-xs mt-1">{{ $errors->first('avatar') }}</p>
                            @endif
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="name" class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1.5">Nombres</label>
                                <input id="name" name="name" type="text" class="block w-full bg-[#11222e] border-slate-600 text-white focus:border-cyan-500 focus:ring-cyan-500 rounded-lg text-sm px-3 py-2.5 outline-none transition" value="{{ old('name', $user->name) }}" required autocomplete="name" />
                                @if($errors->get('name'))
                                    <p class="text-red-400 text-xs mt-1">{{ $errors->first('name') }}</p>
                                @endif
                            </div>
                            <div>
                                <label for="apellidos" class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1.5">Apellidos</label>
                                <input id="apellidos" name="apellidos" type="text" class="block w-full bg-[#11222e] border-slate-600 text-white focus:border-cyan-500 focus:ring-cyan-500 rounded-lg text-sm px-3 py-2.5 outline-none transition" value="{{ old('apellidos', $user->apellidos) }}" required />
                                @if($errors->get('apellidos'))
                                    <p class="text-red-400 text-xs mt-1">{{ $errors->first('apellidos') }}</p>
                                @endif
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="dni" class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1.5">DNI (Documento de Identidad)</label>
                                <input id="dni" name="dni" type="text" maxlength="8" class="block w-full bg-[#11222e] border-slate-600 text-white focus:border-cyan-500 focus:ring-cyan-500 rounded-lg text-sm px-3 py-2.5 outline-none transition" value="{{ old('dni', $user->dni) }}" required />
                                @if($errors->get('dni'))
                                    <p class="text-red-400 text-xs mt-1">{{ $errors->first('dni') }}</p>
                                @endif
                            </div>
                            <div>
                                <label for="telefono" class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1.5">Teléfono / Celular</label>
                                <input id="telefono" name="telefono" type="text" class="block w-full bg-[#11222e] border-slate-600 text-white focus:border-cyan-500 focus:ring-cyan-500 rounded-lg text-sm px-3 py-2.5 outline-none transition" value="{{ old('telefono', $user->telefono) }}" required />
                                @if($errors->get('telefono'))
                                    <p class="text-red-400 text-xs mt-1">{{ $errors->first('telefono') }}</p>
                                @endif
                            </div>
                        </div>

                        <div>
                            <label for="email" class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1.5">Correo Electrónico Corporativo</label>
                            <input id="email" name="email" type="email" class="block w-full bg-[#11222e] border-slate-600 text-white focus:border-cyan-500 focus:ring-cyan-500 rounded-lg text-sm px-3 py-2.5 outline-none transition" value="{{ old('email', $user->email) }}" required autocomplete="username" />
                            @if($errors->get('email'))
                                <p class="text-red-400 text-xs mt-1">{{ $errors->first('email') }}</p>
                            @endif
                        </div>

                        <div class="flex items-center gap-4 pt-2">
                            <button type="submit" class="px-5 py-2.5 bg-cyan-600 hover:bg-cyan-500 text-white font-bold text-xs uppercase tracking-widest rounded-lg shadow-md transition-colors">
                                Guardar Cambios
                            </button>

                            @if (session('status') === 'profile-updated')
                                <p class="text-xs text-emerald-400 font-medium">
                                    ✓ Información actualizada correctamente.
                                </p>
                            @endif
                        </div>

                    </div>
                </div>

            </div>
        </form>
    </main>

</body>
</html>