<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFundingRoundRequest;
use App\Models\FundingRound;
use App\Models\Investor;
use App\Models\Notification;
use App\Models\Startup;
use App\Models\StartupDocument;
use App\Models\StartupInvestor;
use App\Models\StartupUpdate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FundingRoundController extends Controller
{
    public function create(Request $request, ?Startup $startup = null)
    {
        $preselectedStartup = $startup;

        $investors = Investor::query()
            ->orderBy('investor_name')
            ->get(['id', 'investor_name', 'investor_type']);

        $roundTypes = config('funding.round_types', []);
        $currencies = config('funding.currencies', []);
        $investorTypes = config('funding.investor_types', []);
        $roundStatuses = config('funding.round_statuses', []);
        $exchangeRates = config('funding.exchange_rates', []);

        $breadcrumbs = [
            ['label' => 'Home', 'url' => route('dashboard')],
            ['label' => 'Startups', 'url' => route('startups.index')],
            ['label' => 'Add Funding Round', 'url' => '#'],
        ];

        return view('funding.create', compact(
            'preselectedStartup',
            'investors',
            'roundTypes',
            'currencies',
            'investorTypes',
            'roundStatuses',
            'exchangeRates',
            'breadcrumbs'
        ));
    }

    public function store(StoreFundingRoundRequest $request)
    {
        DB::transaction(function () use ($request): void {
            $investorId = $request->input('investor_id');

            if (!$investorId && $request->filled('new_investor_name')) {
                $newInvestor = Investor::create([
                    'investor_name' => $request->input('new_investor_name'),
                    'investor_type' => $request->input('new_investor_type'),
                    'portfolio_count' => 1,
                ]);
                $investorId = $newInvestor->id;
            }

            $currency = (string) $request->input('currency');
            $rawAmount = (float) $request->input('amount_raised');
            $exchangeRate = $currency === 'USD' ? 1.0 : (float) $request->input('exchange_rate_to_usd');

            $amountUsd = $currency === 'USD'
                ? (int) $rawAmount
                : (int) round($rawAmount / max($exchangeRate, 0.0001));

            $preMoney = (int) ($request->input('pre_money_valuation_usd') ?? 0);
            $postMoney = $request->filled('valuation_after_round_usd')
                ? (int) $request->input('valuation_after_round_usd')
                : $preMoney + $amountUsd;

            $round = FundingRound::create([
                'startup_id' => $request->input('startup_id'),
                'round_type' => $request->input('round_type'),
                'amount_raised_usd' => $amountUsd,
                'investor_id' => $investorId,
                'funding_date' => $request->input('funding_date'),
                'expected_close_date' => $request->input('expected_close_date'),
                'round_status' => $request->input('round_status'),
                'equity_diluted_percent' => $request->input('equity_diluted_percent'),
                'valuation_after_round_usd' => $postMoney,
                'pre_money_valuation_usd' => $preMoney,
                'lead_investor' => $request->input('lead_investor', 'Yes'),
                'currency' => $currency,
                'exchange_rate_to_usd' => $exchangeRate,
                'interest_rate' => $request->input('interest_rate'),
                'grant_authority' => $request->input('grant_authority'),
                'conversion_cap' => $request->input('conversion_cap'),
                'discount_rate' => $request->input('discount_rate'),
                'is_publicly_announced' => $request->boolean('is_publicly_announced'),
                'notes' => $request->input('notes'),
                'created_by' => auth()->id(),
            ]);

            $coInvestorIds = array_values(array_filter((array) $request->input('co_investor_ids', [])));
            if ($coInvestorIds !== []) {
                $round->coInvestors()->syncWithoutDetaching($coInvestorIds);

                foreach ($coInvestorIds as $cid) {
                    StartupInvestor::create([
                        'startup_id' => $request->input('startup_id'),
                        'investor_id' => $cid,
                        'investment_amount_usd' => 0,
                        'investment_date' => $request->input('funding_date'),
                        'investment_stage' => $request->input('round_type'),
                    ]);
                }
            }

            if ($investorId) {
                StartupInvestor::updateOrCreate(
                    [
                        'startup_id' => $request->input('startup_id'),
                        'investor_id' => $investorId,
                    ],
                    [
                        'investment_amount_usd' => $amountUsd,
                        'investment_date' => $request->input('funding_date'),
                        'equity_stake_percent' => $request->input('equity_diluted_percent'),
                        'investment_stage' => $request->input('round_type'),
                    ]
                );
            }

            $startup = Startup::query()->findOrFail($request->input('startup_id'));

            $startup->update([
                'total_funding_usd' => (int) $startup->total_funding_usd + $amountUsd,
                'valuation_usd' => $postMoney,
                'funding_stage' => $request->input('round_type'),
                'last_funding_date' => $request->input('funding_date'),
            ]);

            StartupUpdate::create([
                'startup_id' => $startup->id,
                'update_type' => 'Funding',
                'title' => $startup->startup_name . ' - ' . $request->input('round_type'),
                'description' => $startup->startup_name
                    . ' has raised $' . number_format($amountUsd)
                    . ' in a ' . $request->input('round_type')
                    . ' round, bringing total funding to $'
                    . number_format($startup->total_funding_usd) . '.',
                'update_date' => $request->input('funding_date'),
                'is_published' => false,
                'created_by' => auth()->id(),
            ]);

            Notification::create([
                'user_id' => auth()->id(),
                'startup_id' => $startup->id,
                'notification_type' => 'funding_update',
                'title' => 'New funding round recorded',
                'message' => $startup->startup_name
                    . ' raised $' . number_format($amountUsd)
                    . ' in ' . $request->input('round_type') . ' round.',
                'is_read' => false,
                'priority' => $request->boolean('is_publicly_announced') ? 'high' : 'medium',
            ]);

            if ($request->hasFile('term_sheet')) {
                $file = $request->file('term_sheet');
                $path = $file->store('documents/startups/' . $startup->id, 'local');

                StartupDocument::create([
                    'startup_id' => $startup->id,
                    'document_type' => 'Term Sheet',
                    'document_name' => $startup->slug . '-term-sheet.pdf',
                    'file_path' => $path,
                    'file_size_kb' => (int) round($file->getSize() / 1024),
                    'uploaded_by' => auth()->id(),
                    'status' => 'Pending',
                ]);
            }
        });

        return redirect()
            ->route('startups.show', $request->input('startup_id'))
            ->with('success', 'Funding round recorded successfully.');
    }

    public function searchStartups(Request $request)
    {
        $q = (string) $request->get('q', '');

        $results = Startup::query()
            ->with(['sector', 'state'])
            ->where('startup_name', 'like', "%{$q}%")
            ->orWhere('registration_number', 'like', "%{$q}%")
            ->limit(10)
            ->get()
            ->map(fn ($s) => [
                'id' => $s->id,
                'startup_name' => $s->startup_name,
                'registration_number' => $s->registration_number,
                'sector' => $s->sector?->sector_name ?? '',
                'state' => $s->state?->state_name ?? '',
                'current_stage' => $s->funding_stage,
                'total_funding_usd' => $s->total_funding_usd,
            ]);

        return response()->json($results);
    }

    public function edit(FundingRound $fundingRound)
    {
        return redirect()->route('funding.create');
    }

    public function update(StoreFundingRoundRequest $request, FundingRound $fundingRound)
    {
        return redirect()->route('funding.create');
    }

    public function destroy(FundingRound $fundingRound)
    {
        $startupId = $fundingRound->startup_id;
        $fundingRound->delete();

        return redirect()->route('startups.show', $startupId)
            ->with('success', 'Funding round deleted successfully.');
    }
}
