<?php

namespace App\Http\Requests;

use App\Models\Transaction;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TransactionRequest extends FormRequest
{
    protected function prepareForValidation()
    {
        $this->merge([
            'type' => mb_strtolower($this->input('type'), 'UTF-8'),
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        $type = $this->input('type');

        return [
            'type'        => ['required', 'string', 'in:' . implode(',', Transaction::TYPES)],

            'destination' => [
                Rule::requiredIf(function () use ($type) {
                    return $type === Transaction::TYPES['deposit'] ||
                        $type === Transaction::TYPES['transfer'];
                }),
                'different:origin',
                'integer',
            ],

            'origin'      => [
                Rule::requiredIf(function () use ($type) {
                    return $type === Transaction::TYPES['withdraw'] ||
                        $type === Transaction::TYPES['transfer'];
                }),
                'different:destination',
                'integer',
            ],

            'amount'      => ['required', 'integer', 'min:1'],
        ];
    }
}
