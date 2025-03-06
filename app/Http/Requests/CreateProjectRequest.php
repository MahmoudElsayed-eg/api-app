<?php

namespace App\Http\Requests;

use App\Enums\AttributeType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateProjectRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name'=>'required|string|max:255',
            'status' => 'required|string|max:255',
            'attributes' => 'array',
            'attributes.*.attr.name' => 'required|string|max:255',
            'attributes.*.attr.type' => ['required',Rule::enum(AttributeType::class)],
            'attributes.*.value.value' => 'required|string'
        ];
    }
}
