<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GetRatesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'from' => ['nullable', 'string', 'size:3'],
            'to'   => ['required', 'string', 'size:3'],
            'days' => ['nullable', 'integer', 'in:0,3,5,7,10,14'],
        ];
    }

    public function fromCurrency(): string
    {
        return strtoupper($this->input('from', 'USD'));
    }

    public function toCurrency(): string
    {
        return strtoupper($this->input('to'));
    }

    public function periodDays(): int
    {
        return (int) $this->input('days', 3);
    }
}
