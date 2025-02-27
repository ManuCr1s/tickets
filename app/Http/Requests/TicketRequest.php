<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TicketRequest extends FormRequest
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
    public function messages()
    {
        return [
            'ticket.required'=>__('Ingrese un numero de Ticket'),
            'ticket.size'=>__('Ingrese un numero de Ticket de 2555 numeros')
        ];
    }
    public function rules()
    {
        return [
            'ticket'=>'required|size:31'
        ];
    }
}
