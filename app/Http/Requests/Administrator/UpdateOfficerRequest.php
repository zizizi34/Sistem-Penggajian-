<?php

namespace App\Http\Requests\Administrator;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateOfficerRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        $officer = $this->route('officer');

        return [
            'id_departemen' => 'nullable|exists:departemen,id_departemen',
            'name' => 'required|string|min:3|max:255',
            'email' => [
                'required',
                'email',
                'min:3',
                'max:255',
                Rule::unique('officers', 'email')->ignore($officer->id),
            ],
            'password' => 'nullable|confirmed|min:3|max:255',
            'phone_number' => [
                'required',
                'numeric',
                'digits_between:3,255',
                Rule::unique('officers', 'phone_number')->ignore($officer->id),
            ],
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Kolom nama wajib diisi!',
            'name.string' => 'Kolom nama harus berupa karakter!',
            'name.min' => 'Kolom nama minimal :min karakter!',
            'name.max' => 'Kolom nama maksimal :max karakter!',

            'email.required' => 'Kolom alamat email wajib diisi!',
            'email.email' => 'Kolom alamat email wajib email yang valid!',
            'email.unique' => 'Alamat email sudah terdaftar!',
            'email.min' => 'Kolom alamat email minimal :min karakter!',
            'email.max' => 'Kolom alamat email maksimal :max karakter!',

            'password.confirmed' => 'Kolom konfirmasi password tidak sesuai!',
            'password.min' => 'Kolom password minimal :min karakter!',
            'password.max' => 'Kolom password maksimal :max karakter!',

            'phone_number.required' => 'Kolom nomor handphone wajib diisi!',
            'phone_number.numeric' => 'Kolom nomor handphone harus berupa angka!',
            'phone_number.unique' => 'Nomor handphone sudah terdaftar!',
            'phone_number.digits_between' => 'Kolom nomor handphone minimal :min karakter dan maksimal :max karakter!',
        ];
    }
}
