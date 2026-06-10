<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - ConsigueVentas</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#0f1b24] font-sans antialiased min-h-screen flex items-center justify-center p-4">

    <div class="w-full sm:max-w-md bg-[#1a2e3b] shadow-2xl rounded-2xl border border-slate-700/50 overflow-hidden px-8 py-8 transition-all">
        
        <div class="mb-8 text-center">
            <a href="/">
                <img src="{{ asset('images/LogoCV.png') }}" alt="Logo ConsigueVentas" class="h-16 w-auto object-contain mx-auto drop-shadow-md">
            </a>
            <h2 class="mt-4 text-xl font-bold text-white tracking-wide uppercase">
                Control de Actividades
            </h2>
            <p class="text-xs text-cyan-400 font-semibold tracking-wider mt-1">
                DEPARTAMENTO DE TECNOLOGÍA
            </p>
        </div>

        @if (session('status'))
            <div class="mb-4 font-medium text-sm text-green-400 bg-green-950/30 p-3 rounded-lg border border-green-800">
                {{ session('status') }}
            </div>
        @endif

       <form method="POST" action="{{ route('login') }}" class="space-y-6">
            @csrf

            <div>
                <label for="email" class="block font-medium text-xs text-slate-300 uppercase tracking-wider mb-1.5">
                    Correo Electrónico
                </label>
                <input id="email" 
                       class="block w-full bg-[#11222e] border border-slate-600 text-white focus:border-cyan-500 focus:ring-cyan-500 rounded-lg shadow-sm text-sm px-3 py-2.5 outline-none transition" 
                       type="email" 
                       name="email" 
                       value="{{ old('email') }}" 
                       required 
                       autofocus 
                       autocomplete="username" />
                
                @if ($errors->has('email'))
                    <p class="mt-1.5 text-red-400 text-xs font-medium">{{ $errors->first('email') }}</p>
                @endif
            </div>

<div class="mb-5">
    <label for="password" class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1.5">
        Contraseña
    </label>

    <div class="relative">
        <input id="password" 
               name="password" 
               type="password" 
               class="block w-full bg-[#11222e] border-slate-600 text-white focus:border-cyan-500 focus:ring-cyan-500 rounded-lg text-sm pl-3 pr-10 py-2.5 outline-none transition" 
               required 
               autocomplete="current-password" />
        
<button type="button" 
        id="togglePassword" 
        class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-cyan-400 transition-colors focus:outline-none"
        title="Mostrar/Ocultar contraseña">
    
    <svg id="eyeOpen" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
        <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
    </svg>

    <svg id="eyeClose" class="h-5 w-5 hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 11-4.243-4.243m4.242 4.242L9.88 9.88" />
    </svg>
</button>
    </div>
    
    @if($errors->get('password'))
        <p class="text-red-400 text-xs mt-1">{{ $errors->first('password') }}</p>
    @endif
</div>

            <div class="block mt-5 flex items-center justify-between text-xs">
                <label for="remember_me" class="inline-flex items-center cursor-pointer">
                    <input id="remember_me" type="checkbox" class="rounded bg-[#11222e] border-slate-600 text-cyan-600 shadow-sm focus:ring-cyan-500 cursor-pointer" name="remember">
                    <span class="ms-2 text-slate-300 select-none">Recordar sesión</span>
                </label>

                <a class="text-cyan-400 hover:text-cyan-300 transition font-medium cursor-pointer select-none" 
                   onclick="alert('Por cuestiones de seguridad, para restablecer su contraseña de acceso debe comunicarse con el Área de Soporte Técnico o con su Jefe Inmediato.')">
                    ¿Olvidó su contraseña?
                </a>
            </div>

            <div class="mt-6">
                <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-3 bg-cyan-600 hover:bg-cyan-500 active:bg-cyan-700 text-white font-bold text-xs uppercase tracking-widest rounded-xl focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:ring-offset-2 transition duration-150 shadow-md">
                    Ingresar al Sistema
                </button>
            </div>
        </form>

        <div class="mt-6 pt-4 border-t border-slate-700/60 text-center">
            <a href="/" class="text-xs text-slate-400 hover:text-slate-300 transition flex items-center justify-center space-x-1">
                <span>← Volver a la página de inicio</span>
            </a>
        </div>

    </div>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const passwordInput = document.getElementById('password');
        const togglePasswordButton = document.getElementById('togglePassword');
        const eyeOpenIcon = document.getElementById('eyeOpen');
        const eyeCloseIcon = document.getElementById('eyeClose');

        togglePasswordButton.addEventListener('click', function () {
            // Alternar el tipo de input
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                // Cambiar el icono visual al ojo tachado
                eyeOpenIcon.classList.add('hidden');
                eyeCloseIcon.classList.remove('hidden');
            } else {
                passwordInput.type = 'password';
                // Regresar al icono del ojo abierto
                eyeOpenIcon.classList.remove('hidden');
                eyeCloseIcon.classList.add('hidden');
            }
        });
    });
</script>
</body>
</html>