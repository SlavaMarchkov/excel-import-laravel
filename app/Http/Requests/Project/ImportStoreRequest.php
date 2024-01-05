<?php

namespace App\Http\Requests\Project;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

/**
 * @property mixed $file
 */
class ImportStoreRequest extends FormRequest
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
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        if ($this->file->getClientOriginalExtension() != 'xlsx') {
            throw ValidationException::withMessages(['Incorrect file type']);
        }
        return [
            'file' => 'required|file',
            'type' => 'required|integer|in:1,2',
        ];
    }
}
