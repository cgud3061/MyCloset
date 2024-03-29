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
            'type_id' => 'required|integer',
            'categorie_id' => 'required|integer',
            'brand_id' => 'required|integer',
            'price' => 'required|integer|between:10,8388600',
            'state' => 'required|boolean',
            'description' => 'required|string|max:255',
            'file' => 'required|file',
        ];
    }
}