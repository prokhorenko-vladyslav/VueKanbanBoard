<?php

namespace Laurel\Kanban\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DeskUpdate extends FormRequest
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
     * @return array
     */
    public function rules()
    {
        return [
            'name' => [
                'required',
                'string:255',
                Rule::unique('desks', 'name')->ignore($this->desk),
            ],
            'description' => 'string:1000',
            'is_favorite' => 'boolean',
            'is_private' => 'boolean'
        ];
    }

    /**
     *  Filters to be applied to the input.
     *
     * @return array
     */
    public function filters()
    {
        return [
            'name' => 'trim|escape',
            'description' => 'trim|escape'
        ];
    }
}
