<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TaskRequest extends FormRequest
{
    public function rules()
    {
        return [
            'title' => ['required'],
            'description' => ['nullable'],
            'status' => ['boolean'],
            'due' => ['required', 'date'],
        ];
    }

    public function authorize()
    {
        return true;
    }
}
