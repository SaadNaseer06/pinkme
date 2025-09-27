<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreApplicationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    // public function rules(): array
    // {
    //     return [
    //         'first_name' => 'required|string|max:100',
    //         'last_name' => 'required|string|max:100',
    //         'gender' => 'required|in:male,female,other',
    //         'blood_group' => 'required',
    //         'email' => 'required|email',
    //         'contact_number' => 'required',
    //         'documents.*' => 'nullable|file|max:5120|mimes:pdf,jpg,jpeg,png,docx',
    //         'program_id' => 'required|exists:sponsorship_programs,id',
    //         'description' => 'nullable|string',
    //     ];
    // }


    public function rules()
    {
        return [
            'title' => 'required|string|max:255',
            'blood_group' => 'required|string|in:A+,A-,B+,B-,AB+,AB-,O+,O-',
            'assistance_type' => 'required|string',
            'program_id' => 'required|exists:sponsorship_programs,id',
            'description' => 'required|string|min:20',
            'documents.*' => 'nullable|file|max:5120|mimes:jpg,jpeg,png,pdf,doc,docx',
        ];
    }
}
