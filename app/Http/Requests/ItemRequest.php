<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Inertia\Inertia;

class ItemRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'name' => 'required|string|max:30',
            'price' => 'required|integer|between:10,8388600',
            'possessioned' => 'required|boolean',
        ];
    }
}
