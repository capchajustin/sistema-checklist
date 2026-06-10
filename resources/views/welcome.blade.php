<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio - Checklist ConsigueVentas</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#0f1b24] font-sans antialiased min-h-screen flex flex-col justify-between text-slate-200">

    <header class="bg-[#1a2e3b] shadow-md py-3 border-b border-slate-700/30">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex justify-between items-center">
            
            <div class="flex items-center space-x-3">
                <img src="{{ asset('images/LogoCV.png') }}" alt="Logo ConsigueVentas" class="h-12 w-auto object-contain">
                <span class="text-xs font-semibold text-cyan-400 uppercase tracking-widest hidden sm:inline">
                    | Dept. Tecnología
                </span>
            </div>
            
            <div>
                @auth
                    <a href="{{ url('/dashboard') }}" class="text-sm font-medium text-cyan-400 hover:text-cyan-300 transition">
                        Ir al Panel →
                    </a>
                @else
                    <a href="{{ route('login') }}" class="inline-flex items-center px-4 py-2 bg-cyan-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-cyan-500 active:bg-cyan-700 focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        Iniciar Sesión
                    </a>
                @endauth
            </div>
        </div>
    </header>

    <main class="flex-grow flex items-center justify-center px-4 py-12">
        <div class="max-w-2xl w-full text-center bg-[#1a2e3b] p-8 sm:p-12 rounded-2xl shadow-2xl border border-slate-700/40">
            
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-cyan-950/60 text-cyan-400 border border-cyan-800/60 uppercase tracking-wider mb-6">
                Departamento de Tecnología
            </span>

            <h1 class="text-3xl sm:text-4xl font-extrabold text-white tracking-tight mb-4 uppercase">
                Bienvenido al Checklist <br class="hidden sm:inline">
                <span class="text-cyan-400">de Desarrollo de Software</span>
            </h1>

            <p class="text-sm text-slate-400 mb-8 max-w-lg mx-auto leading-relaxed">
                Este es un aplicativo web propuesto para el registro, seguimiento de actividades diarias y carga de evidencias técnicas, optimizando el flujo de control interno de los trabajadores.
            </p>

            <div class="space-y-4 sm:space-y-0 sm:space-x-4 flex flex-col sm:flex-row justify-center items-center">
                @auth
                    <a href="{{ url('/dashboard') }}" class="w-full sm:w-auto text-center px-8 py-3 bg-cyan-600 text-white font-bold rounded-xl shadow-md hover:bg-cyan-500 transition uppercase text-sm tracking-wider">
                        Ingresar al Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}" class="w-full sm:w-auto text-center px-8 py-3 bg-cyan-600 text-white font-bold rounded-xl shadow-md hover:bg-cyan-500 transition uppercase text-sm tracking-wider">
                        Loguearse en el Sistema
                    </a>
                    
                    <a href="#soporte" onclick="alert('Por favor, solicite sus accesos de prueba al Jefe Inmediato: Jhoel Fernandez Alvarado.')" class="w-full sm:w-auto text-center px-6 py-3 bg-[#11222e] text-slate-300 font-medium rounded-xl border border-slate-600 hover:bg-slate-700 transition text-sm">
                        Solicitar Credenciales
                    </a>
                @endauth
            </div>

            <div class="mt-10 p-4 bg-amber-500/10 border-l-4 border-amber-500 text-amber-400 text-xs text-left rounded-r-lg">
                <strong class="font-semibold block mb-1">Aviso:</strong>
                <span class="text-slate-300">Este aplicativo web sustituye formalmente los reportes basados en las anteriores hojas de cálculo compartidas de Excel, debe utilizar esta herramienta para garantizar la aprobación de sus actividades.</span>
            </div>

        </div>
    </main>

    <footer class="bg-[#11222e] border-t border-slate-800/80 py-4 text-center text-xs text-slate-500 uppercase tracking-wider">
        <div class="max-w-7xl mx-auto px-4">
            &copy; {{ date('Y') }} Grupo ConsigueVentas Inversiones E.I.R.L. - Todos los derechos reservados.
        </div>
    </footer>

</body>
</html>