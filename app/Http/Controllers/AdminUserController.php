<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class AdminUserController extends Controller
{
    /**
     * Muestra el formulario de creación y la lista de usuarios actuales.
     */
    public function index()
    {
        $roles = Role::all();

        // Ordenamos los usuarios por una lista personalizada en MySQL: Administrador -> Responsable -> Trabajador
        $users = User::with('role')
            ->join('roles', 'users.role_id', '=', 'roles.id')
            ->select('users.*')
            ->orderByRaw("FIELD(roles.name, 'Administrador', 'Responsable', 'Trabajador')")
            ->get();

        return view('admin.index', compact('users', 'roles'));
    }

    /**
     * Procesa y guarda el nuevo usuario.
     */
    public function store(Request $request)
    {
        /** @var \App\Models\User $currentUser */
        $currentUser = auth()->user();

        // Validaciones estrictas del sistema corporativo
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'apellidos' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'role_id' => ['required', 'exists:roles,id'], 
            'password' => ['required', 'confirmed', Rules\Password::defaults()], 
        ]);

        // REGLA DE SEGURIDAD: Un Responsable no puede registrar cuentas con rol de Administrador
        $targetRole = Role::findOrFail($request->role_id);
        if ($currentUser->role && $currentUser->role->name === 'Responsable' && $targetRole->name === 'Administrador') {
            return redirect()->back()->withErrors(['auth' => 'Acceso denegado: Un Responsable no tiene permitido registrar cuentas con privilegios de Administrador.']);
        }

        User::create([
            'name' => $request->name,
            'apellidos' => $request->apellidos,
            'email' => $request->email,
            'role_id' => $request->role_id,
            'password' => Hash::make($request->password), 
            'dni' => null,      
            'telefono' => null, 
            'avatar' => null,   
        ]);

        return redirect()->route('admin.users.index')->with('status', 'user-created');
    }
   
    /**
     * Actualiza el perfil y rol del usuario seleccionado.
     */
    public function update(Request $request, $id)
    {
        $userToEdit = User::findOrFail($id);
        
        /** @var \App\Models\User $currentUser */
        $currentUser = auth()->user();

        // REGLA: Si el usuario activo es Responsable, restringimos alteraciones críticas
        if ($currentUser->role && $currentUser->role->name === 'Responsable') {
            
            //  Un Responsable no puede tocar el perfil de un Administrador
            if ($userToEdit->role && $userToEdit->role->name === 'Administrador') {
                return redirect()->back()->withErrors(['auth' => 'Acceso denegado: Un Responsable no puede modificar a un Administrador.']);
            }

            //  Un Responsable no puede cambiar el rol de nadie (debe mantenerse igual)
            if ((int)$request->role_id !== (int)$userToEdit->role_id) {
                return redirect()->back()->withErrors(['auth' => 'Acceso denegado: Los Responsables no tienen autorización para alterar los roles del personal.']);
            }
        }

        // Validación de datos para la actualización
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'apellidos' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,'.$userToEdit->id],
            'role_id' => ['required', 'exists:roles,id'],
        ]);

        // Actualizamos los campos usando la variable correcta ($userToEdit)
        $userToEdit->update([
            'name' => $request->name,
            'apellidos' => $request->apellidos,
            'email' => $request->email,
            'role_id' => $request->role_id,
        ]);

        return redirect()->route('admin.users.index')->with('status', 'user-updated');
    }

    public function create()
    {
        $roles = Role::all();
        return view('admin.create', compact('roles'));
    }

    /**
     * Elimina de forma lógica o permanente al usuario.
     */
    public function destroy($id)
    {
        $userToDelete = User::findOrFail($id);
        
        /** @var \App\Models\User $currentUser */
        $currentUser = auth()->user();

        // Evitar auto-eliminación por seguridad elemental
        if ($userToDelete->id === $currentUser->id) {
            return redirect()->back()->withErrors(['auth' => 'No puedes eliminar tu propia cuenta del sistema en la sesión actual.']);
        }

        // REGLA: Un Responsable NO puede borrar a un Administrador
        if ($currentUser->role && $currentUser->role->name === 'Responsable') {
            if ($userToDelete->role && $userToDelete->role->name === 'Administrador') {
                return redirect()->back()->withErrors(['auth' => 'Acceso denegado: Los Responsables no disponen de privilegios para borrar a un Administrador.']);
            }
        }

        $userToDelete->delete();

        return redirect()->back()->with('status', 'user-deleted');
    }
    public function resetPassword(Request $request, User $user)
{
    // 1. Validar la estructura de la nueva contraseña
    $request->validate([
        'password' => 'required|string|min:6|confirmed', 
    ], [
        'password.required' => 'La contraseña es obligatoria.',
        'password.min' => 'La nueva contraseña debe tener al menos 6 caracteres.',
        'password.confirmed' => 'La confirmación de la contraseña no coincide.',
    ]);

    // 2. Encriptar y actualizar físicamente en la base de datos cloud
    $user->update([
        'password' => Hash::make($request->password)
    ]);

    // 3. Retornar a la vista con una alerta de éxito
    return redirect()->back()->with('success', "La contraseña de {$user->name} se restableció con éxito.");
}
}