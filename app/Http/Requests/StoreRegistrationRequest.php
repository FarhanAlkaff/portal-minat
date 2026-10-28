<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRegistrationRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'academic_year_id' => ['required', 'exists:academic_years,id'],
            'grade_id' => ['required', 'exists:grades,id'],
            'campus_id' => ['required', 'exists:campuses,id'],
            'student_category_id' => ['required', 'exists:student_categories,id'],

            'full_name' => ['required', 'string', 'max:191'],
            'nickname' => ['required', 'string', 'max:100'],
            'gender' => ['required', Rule::in(['L', 'P'])],
            'nisn' => ['nullable', 'string', 'max:20'],
            'class' => ['nullable', 'string', 'max:30'],
            'birth_place' => ['required', 'string', 'max:100'],
            'birth_date' => ['required', 'date', 'before_or_equal:today'],
            'family_status' => ['required', 'string', 'max:30'],
            'child_order' => ['required', 'integer', 'min:1', 'max:20'],
            'address' => ['required', 'string', 'max:1000'],
            'phone' => ['required', 'string', 'max:25'],
            'previous_school' => ['nullable', 'string', 'max:191'],

            'father_name' => ['required', 'string', 'max:191'],
            'mother_name' => ['required', 'string', 'max:191'],
            'grandfather_name' => ['required', 'string', 'max:191'],
            'father_job' => ['nullable', 'string', 'max:100'],
            'mother_job' => ['nullable', 'string', 'max:100'],
            'father_phone' => ['nullable', 'string', 'max:25'],
            'mother_phone' => ['nullable', 'string', 'max:25'],

            'agreement' => ['accepted'],
        ];
    }

    public function attributes(): array
    {
        return [
            'full_name' => 'nama lengkap',
            'nickname' => 'nama panggilan',
            'birth_date' => 'tanggal lahir',
            'birth_place' => 'tempat lahir',
            'family_status' => 'status keluarga',
            'child_order' => 'anak ke-',
            'father_name' => 'nama ayah',
            'mother_name' => 'nama ibu',
            'grandfather_name' => 'nama kakek dari ayah',
        ];
    }
}
