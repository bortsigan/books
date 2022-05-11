<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PostBookRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'isbn' => 'required|numeric|digits_between:1,13|unique:books',
            'title' => 'required',
            'description' => 'required',
            'published_year' => 'required|numeric|between:1900,2020',
            'authors' => 'required|array',
            'authors.*' => 'required|exists:authors,id' 
        ];
    }
}
