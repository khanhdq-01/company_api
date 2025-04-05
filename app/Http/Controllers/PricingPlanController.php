<?php

namespace App\Http\Controllers;

use App\Models\PricingPlan;
use App\Models\PricingTab;
use App\Models\PricingItem;
use App\Models\PricingFeature;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class PricingPlanController extends Controller
{
    // Get all pricing plans
    public function index()
    {
        $plans = PricingPlan::with('tabs.items.features')->get();
        return response()->json($plans, 200);
    }

    // Get a single pricing plan
    public function show($id)
    {
        $plan = PricingPlan::with('tabs.items.features')->find($id);
        if (!$plan) {
            return response()->json(['message' => 'Pricing plan not found'], 404);
        }
        return response()->json($plan, 200);
    }

    // Create a new pricing plan
    public function store(Request $request)
    {
        try {
            // Log incoming request data for debugging
            Log::info("Store Pricing Plan Request:", $request->all());

            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'tabs' => 'sometimes|array',
                'tabs.*.name' => 'required|string|max:255',
                'tabs.*.items' => 'sometimes|array',
                'tabs.*.items.*.title' => 'required|string|max:255',
                'tabs.*.items.*.price' => 'required|string',
                'tabs.*.items.*.period' => 'required|string|max:255',
                'tabs.*.items.*.features' => 'sometimes|array',
                'tabs.*.items.*.features.*.feature' => 'required|string|max:255',
            ]);

            if ($validator->fails()) {
                Log::error("Validation failed:", $validator->errors()->toArray());
                return response()->json(['errors' => $validator->errors()], 422);
            }

            // Create pricing plan
            $plan = PricingPlan::create(['name' => $request->name]);

            // Handle tabs
            if ($request->has('tabs')) {
                foreach ($request->tabs as $tabData) {
                    $tab = PricingTab::create([
                        'pricing_plan_id' => $plan->id,
                        'name' => $tabData['name'],
                    ]);

                    // Handle items
                    if (isset($tabData['items'])) {
                        foreach ($tabData['items'] as $itemData) {
                            $item = PricingItem::create([
                                'pricing_tab_id' => $tab->id,
                                'title' => $itemData['title'],
                                'price' => $itemData['price'],
                                'period' => $itemData['period'],
                            ]);

                            // Handle features
                            if (isset($itemData['features'])) {
                                foreach ($itemData['features'] as $featureData) {
                                    PricingFeature::create([
                                        'pricing_item_id' => $item->id,
                                        'feature' => $featureData['feature'],
                                    ]);
                                }
                            }
                        }
                    }
                }
            }

            return response()->json($plan->load('tabs.items.features'), 201);
        } catch (\Exception $e) {
            Log::error("Error creating pricing plan: " . $e->getMessage(), ['exception' => $e]);
            return response()->json(['message' => 'Error creating pricing plan', 'error' => $e->getMessage()], 500);
        }
    }

    // Update a pricing plan
    public function update(Request $request, $id)
    {
        try {
            $plan = PricingPlan::find($id);
            if (!$plan) {
                return response()->json(['message' => 'Pricing plan not found'], 404);
            }

            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'tabs' => 'array',
                'tabs.*.id' => 'sometimes|exists:pricing_tabs,id',
                'tabs.*.name' => 'required|string',
                'tabs.*.items' => 'array',
                'tabs.*.items.*.id' => 'sometimes|exists:pricing_items,id',
                'tabs.*.items.*.title' => 'required|string',
                'tabs.*.items.*.price' => 'required|string',
                'tabs.*.items.*.period' => 'required|string',
                'tabs.*.items.*.features' => 'array', // Changed from pricing_features
                'tabs.*.items.*.features.*.id' => 'sometimes|exists:pricing_features,id',
                'tabs.*.items.*.features.*.feature' => 'required|string',
            ]);

            if ($validator->fails()) {
                return response()->json($validator->errors(), 422);
            }

            $plan->update(['name' => $request->name]);

            // Handle tabs
            $existingTabIds = $plan->tabs->pluck('id')->toArray();
            $submittedTabIds = collect($request->tabs)->pluck('id')->filter()->toArray();

            // Delete tabs not in request
            PricingTab::whereIn('id', array_diff($existingTabIds, $submittedTabIds))->delete();

            foreach ($request->tabs as $tabData) {
                $tab = PricingTab::updateOrCreate(
                    ['id' => $tabData['id'] ?? null, 'pricing_plan_id' => $plan->id],
                    ['name' => $tabData['name']]
                );

                // Handle items
                $existingItemIds = $tab->items->pluck('id')->toArray();
                $submittedItemIds = collect($tabData['items'])->pluck('id')->filter()->toArray();

                // Delete items not in request
                PricingItem::whereIn('id', array_diff($existingItemIds, $submittedItemIds))->delete();

                foreach ($tabData['items'] as $itemData) {
                    $item = PricingItem::updateOrCreate(
                        ['id' => $itemData['id'] ?? null, 'pricing_tab_id' => $tab->id],
                        [
                            'title' => $itemData['title'],
                            'price' => $itemData['price'],
                            'period' => $itemData['period'],
                        ]
                    );

                    // Handle features
                    $existingFeatureIds = $item->features->pluck('id')->toArray();
                    $submittedFeatureIds = collect($itemData['features'])->pluck('id')->filter()->toArray(); // Changed from pricing_features

                    // Delete features not in request
                    PricingFeature::whereIn('id', array_diff($existingFeatureIds, $submittedFeatureIds))->delete();

                    foreach ($itemData['features'] as $featureData) { // Changed from pricing_features
                        PricingFeature::updateOrCreate(
                            ['id' => $featureData['id'] ?? null, 'pricing_item_id' => $item->id],
                            ['feature' => $featureData['feature']]
                        );
                    }
                }
            }

            return response()->json($plan->load('tabs.items.features'), 200);
        } catch (\Exception $e) {
            Log::error("Error updating pricing plan: " . $e->getMessage());
            return response()->json(['message' => 'Error updating pricing plan'], 500);
        }
    }
    

    // Delete a pricing plan
    public function destroy($id)
    {
        $plan = PricingPlan::find($id);
        if (!$plan) {
            return response()->json(['message' => 'Pricing plan not found'], 404);
        }

        $plan->delete();
        return response()->json(['message' => 'Pricing plan deleted'], 200);
    }
}
