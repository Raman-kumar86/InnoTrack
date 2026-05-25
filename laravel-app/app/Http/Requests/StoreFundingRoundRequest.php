<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreFundingRoundRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'startup_id' => 'required|exists:startups,id',
            'round_type' => 'required|string|in:' . implode(',', config('funding.round_types', [])),
            'currency' => 'required|in:USD,INR,EUR,GBP',
            'amount_raised' => 'required|numeric|min:1',
            'exchange_rate_to_usd' => 'required_unless:currency,USD|numeric|min:0.01',
            'pre_money_valuation_usd' => 'nullable|numeric|min:0',
            'valuation_after_round_usd' => 'required|numeric|min:1',
            'equity_diluted_percent' => 'nullable|numeric|min:0|max:100',
            'investor_id' => 'nullable|exists:investors,id',
            'new_investor_name' => 'required_without:investor_id|nullable|string|max:255',
            'new_investor_type' => 'required_with:new_investor_name|nullable|string',
            'co_investor_ids' => 'nullable|array',
            'co_investor_ids.*' => 'exists:investors,id',
            'lead_investor' => 'required|in:Yes,No',
            'funding_date' => 'required|date|before_or_equal:today',
            'expected_close_date' => 'nullable|date|after_or_equal:funding_date',
            'round_status' => 'required|in:Completed,Pending,In Progress',
            'interest_rate' => 'required_if:round_type,Debt|nullable|numeric|min:0|max:100',
            'grant_authority' => 'required_if:round_type,Grant|nullable|string|max:255',
            'conversion_cap' => 'required_if:round_type,Convertible Note|nullable|numeric|min:0',
            'discount_rate' => 'nullable|numeric|min:0|max:100',
            'is_publicly_announced' => 'nullable|boolean',
            'notes' => 'nullable|string|max:2000',
            'term_sheet' => 'nullable|file|mimes:pdf|max:10240',
        ];
    }

    public function messages(): array
    {
        return [
            'startup_id.required' => 'Please select a startup.',
            'startup_id.exists' => 'Selected startup does not exist.',
            'round_type.required' => 'Round type is required.',
            'round_type.in' => 'Invalid round type selected.',
            'amount_raised.required' => 'Amount raised is required.',
            'amount_raised.numeric' => 'Amount must be a valid number.',
            'amount_raised.min' => 'Amount must be greater than zero.',
            'valuation_after_round_usd.required' => 'Post-money valuation is required.',
            'equity_diluted_percent.max' => 'Equity cannot exceed 100%.',
            'investor_id.exists' => 'Selected investor does not exist.',
            'new_investor_name.required_without' => 'Enter investor name or select existing.',
            'funding_date.required' => 'Funding date is required.',
            'funding_date.before_or_equal' => 'Funding date cannot be in the future.',
            'interest_rate.required_if' => 'Interest rate required for debt rounds.',
            'grant_authority.required_if' => 'Grant authority required for grant rounds.',
            'term_sheet.mimes' => 'Term sheet must be a PDF file.',
            'term_sheet.max' => 'Term sheet must be under 10MB.',
        ];
    }
}
