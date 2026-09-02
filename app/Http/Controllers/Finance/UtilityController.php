<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUtilityBillingRequest;
use App\Http\Requests\StoreUtilityMeterRequest;
use App\Http\Requests\StoreUtilityTypeRequest;
use App\Models\Beneficiary;
use App\Models\Campus;
use App\Models\UtilityBilling;
use App\Models\UtilityMeter;
use App\Models\UtilityType;
use App\Support\CsvExporter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UtilityController extends Controller
{
    public function index(Request $request): View
    {
        $meters = UtilityMeter::query()->with(['type', 'campus', 'beneficiary'])->withCount('billings')
            ->when($request->string('q')->toString(), fn (Builder $query, string $q): Builder => $query->where(fn (Builder $nested): Builder => $nested->where('meter_number', 'like', "%{$q}%")->orWhere('reference', 'like', "%{$q}%")))
            ->when($request->integer('campus_id'), fn (Builder $query, int $id): Builder => $query->where('campus_id', $id))->latest();

        return view('admin.utilities.index', ['meters' => $meters->paginate(12)->withQueryString(), 'types' => UtilityType::query()->orderBy('name')->get(), 'campuses' => Campus::query()->orderBy('name')->get(), 'abnormalCount' => UtilityBilling::query()->where('is_abnormal', true)->count()]);
    }

    public function createMeter(): View
    {
        return view('admin.utilities.meter-form', ['types' => UtilityType::query()->where('is_active', true)->orderBy('name')->get(), 'campuses' => Campus::query()->orderBy('name')->get(), 'beneficiaries' => Beneficiary::query()->orderBy('full_name_organization')->get()]);
    }

    public function storeMeter(StoreUtilityMeterRequest $request): RedirectResponse
    {
        UtilityMeter::query()->create($request->validated() + ['reference' => 'MTR-'.str()->upper(str()->random(8)), 'created_by' => $request->user()->id, 'updated_by' => $request->user()->id]);

        return redirect()->route('utilities.index')->with('success', 'Utility meter registered.');
    }

    public function storeType(StoreUtilityTypeRequest $request): RedirectResponse
    {
        UtilityType::query()->create($request->validated() + ['is_active' => $request->boolean('is_active', true)]);

        return back()->with('success', 'Utility type configured.');
    }

    public function show(UtilityMeter $meter): View
    {
        return view('admin.utilities.show', ['meter' => $meter->load(['type', 'campus', 'beneficiary', 'creator', 'updater']), 'billings' => $meter->billings()->with(['creator', 'updater'])->latest('billing_period')->paginate(15)]);
    }

    public function storeReading(StoreUtilityBillingRequest $request): RedirectResponse
    {
        $meter = UtilityMeter::query()->findOrFail($request->validated('utility_meter_id'));
        $data = $request->validated();
        $consumption = (float) $data['current_reading'] - (float) $data['previous_reading'];
        UtilityBilling::query()->create($data + ['reference' => 'UTL-'.str()->upper(str()->random(8)), 'utility_type_id' => $meter->utility_type_id, 'campus_id' => $meter->campus_id, 'beneficiary_id' => $meter->beneficiary_id, 'consumption' => $consumption, 'rate' => $meter->rate, 'charge' => round($consumption * (float) $meter->rate, 2), 'is_abnormal' => $meter->abnormal_threshold !== null && $consumption > (float) $meter->abnormal_threshold, 'created_by' => $request->user()->id, 'updated_by' => $request->user()->id]);

        return redirect()->route('utilities.show', $meter)->with('success', 'Meter reading and charge recorded.');
    }

    public function export(): mixed
    {
        $rows = UtilityBilling::query()->with(['meter', 'type', 'campus', 'beneficiary'])->latest('billing_period')->cursor()->map(fn (UtilityBilling $billing): array => [$billing->reference, $billing->meter->meter_number, $billing->type->name, $billing->campus->name, $billing->beneficiary?->full_name_organization, $billing->billing_period->format('Y-m'), $billing->previous_reading, $billing->current_reading, $billing->consumption, $billing->rate, $billing->charge, $billing->payment_status, $billing->is_abnormal ? 'Yes' : 'No']);

        return CsvExporter::download('upams-utilities-'.today()->format('Y-m-d').'.csv', ['Reference', 'Meter', 'Type', 'Campus', 'Beneficiary', 'Period', 'Previous', 'Current', 'Consumption', 'Rate', 'Charge', 'Payment Status', 'Abnormal'], $rows);
    }
}
