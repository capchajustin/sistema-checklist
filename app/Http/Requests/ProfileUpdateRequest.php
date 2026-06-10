<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
public function rules(): array
{
    return [
        'name' => ['required', 'string', 'max:255'],
        'apellidos' => ['required', 'string', 'max:255'],
        'dni' => ['required', 'string', 'size:8', 'regex:/^[0-9]+$/', 'unique:users,dni,'.$this->user()->id],
        'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email,'.$this->user()->id],
        'telefono' => ['required', 'string', 'min:9', 'max:15'],
        'avatar' => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:2048'], 
    ];
}
}
