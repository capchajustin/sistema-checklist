<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Cloudinary\Configuration\Configuration; 
use Cloudinary\Api\Upload\UploadApi;       

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        
        // Llenar los datos de texto (Nombres, Apellidos, DNI, Teléfono, Email)
        $user->fill($request->validated());

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        // PROCESAR LA FOTO DE PERFIL (AVATAR) CON CLOUDINARY NATIVO
        if ($request->hasFile('avatar')) {
            // Inicializar el SDK usando la variable de entorno de Render
            Configuration::instance(env('CLOUDINARY_URL'));

            $uploadApi = new UploadApi();
            $response = $uploadApi->upload($request->file('avatar')->getRealPath(), [
                'folder' => 'avatars' // Lo guarda organizado en la carpeta 'avatars' dentro de Cloudinary
            ]);

            // Asignar la URL HTTPS directa y limpia devuelta por la API al campo avatar
            $user->avatar = $response['secure_url'];
        }

        // Guardar todos los cambios en la base de datos de Aiven
        $user->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}