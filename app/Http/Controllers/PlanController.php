<?php

namespace App\Http\Controllers;
use App\Models\Plan;
use Illuminate\Http\Request;

class PlanController extends Controller
{
    public function purchase(Request $request)
    {
        $planId = $request->input('plan_id');
        $plan = Plan::findOrFail($planId);
        $mollie = new \Mollie\Api\MollieApiClient();
            $mollie->setApiKey("test_bcCAhNsRUbRMgnFJvTfAPWpEdTuKQ2");

            $payment = $mollie->payments->create([
                "amount" => [
                    "currency" => "EUR",
                    "value" => number_format($plan->price, 2, '.', ''),
                ],
                "description" => "Order #" . 1,
                "redirectUrl" => route('cart.success', [
                    'order_id' => 1,
                ]), 
                "webhookUrl" => route('webhook.mollie'),
                "metadata" => [
                    "order_id" => 1,
                ],
            ]);

            return response()->json([
                'checkoutUrl' => $payment->getCheckoutUrl(),
            ]);
        return back()->with('success', 'Plan purchased successfully!');
    }
}
