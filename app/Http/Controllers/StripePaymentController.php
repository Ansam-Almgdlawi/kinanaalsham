<?php

namespace App\Http\Controllers;
use App\Models\Donation;

use App\Models\Fund;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Stripe\PaymentIntent;
use Stripe\Stripe;

class StripePaymentController extends Controller
{

    public function donate(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
            'currency' => 'required|string',
            'donor_email' => 'nullable|email',
        ]);

        Stripe::setApiKey(config('services.stripe.secret'));

        // 1. إنشاء PaymentIntent
        $paymentIntent = PaymentIntent::create([
            'amount' => $request->amount * 100, // بالـ cents
            'currency' => $request->currency,
            'receipt_email' => $request->donor_email,
            'description' => 'Donation to Kenana AlSham Association',
            'payment_method_types' => ['card'],
        ]);

        // 2. حفظ العملية بقاعدة البيانات (حاليا بس نعمل سجل فارغ)
        Donation::create([
            'amount' => $request->amount,
            'currency' => $request->currency,
            'donor_email' => $request->donor_email,
            'stripe_payment_id' => $paymentIntent->id,
        ]);

        // 3. رجع client_secret للفرونت/بوستمان ليكمل الدفع
        return response()->json([
            'client_secret' => $paymentIntent->client_secret,
            // أضفنا payment_intent_id لتسهيل الاختبار
            'payment_intent_id' => $paymentIntent->id,
        ]);
    }
//    public function handleWebhook(Request $request)
//    {
//        Log::info('-----------------------------------------');
//        Log::info('--- WEBHOOK RECEIVED ---');
//
//        $payload = $request->getContent();
//        $sigHeader = $request->header('Stripe-Signature');
//        $secret = env('STRIPE_WEBHOOK_SECRET');
//
//        if (!$secret) {
//            Log::error('Stripe Webhook Secret is not set in .env file.');
//            return response('Webhook secret not configured.', 500);
//        }
//
//        try {
//            $event = \Stripe\Webhook::constructEvent($payload, $sigHeader, $secret);
//            Log::info('Webhook signature verified successfully. Event type: ' . $event->type);
//        } catch (\Exception $e) {
//            Log::error('Webhook signature verification failed: ' . $e->getMessage());
//            return response('Invalid signature.', 400);
//        }
//
//        if ($event->type === 'payment_intent.succeeded') {
//            \Log::info('Event type is payment_intent.succeeded. Processing...');
//
//            $paymentIntent = $event->data->object;
//            $paymentIntentId = $paymentIntent->id;
//            Log::info('Looking for donation with stripe_payment_id: ' . $paymentIntentId);
//
//            // 1. ابحث عن سجل التبرع
//            $donation = Donation::where('stripe_payment_id', $paymentIntentId)->first();
//
//            if ($donation) {
//                Log::info('SUCCESS: Donation record found!', ['donation_id' => $donation->id]);
//
//                // 2. قم بتحديث رصيد الصندوق
//                $amount_to_add = $paymentIntent->amount / 100;
//                Log::info('Amount to add: ' . $amount_to_add);
//
//                $fund = Fund::firstOrCreate(
//                    ['name' => 'Main Account'],
//                    ['balance' => 0.00]
//                );
//                Log::info('Fund account found or created.', ['fund_id' => $fund->id, 'initial_balance' => $fund->balance]);
//
//                // --- هنا النقطة الحاسمة ---
//                $original_balance = $fund->balance;
//                $fund->balance += $amount_to_add;
//                $new_balance_before_save = $fund->balance;
//
//                Log::info('Balance calculation:', [
//                    'original_balance' => $original_balance,
//                    'amount_added' => $amount_to_add,
//                    'new_balance_before_save' => $new_balance_before_save
//                ]);
//
//                $fund->save();
//
//                // --- تحقق من القيمة بعد الحفظ مباشرة ---
//                $fund->refresh(); // أعد تحميل البيانات من قاعدة البيانات
//                Log::info('SUCCESS: Fund saved. Balance in database is now: ' . $fund->balance);
//
//            } else {
//                // هذا هو الاحتمال الأكبر الآن
//                Log::error('CRITICAL: Donation record NOT FOUND for stripe_payment_id: ' . $paymentIntentId);
//                Log::info('Checking all existing donation IDs in the database...');
//                $all_donations = Donation::pluck('stripe_payment_id')->toArray();
//                Log::info('Existing stripe_payment_ids:', $all_donations);
//            }
//        } else {
//            Log::info('Event type is not payment_intent.succeeded. Skipping.');
//        }
//
//        Log::info('--- WEBHOOK HANDLING FINISHED ---');
//        Log::info('-----------------------------------------');
//
//        return response('Webhook handled.', 200);
//    }


    public function handleWebhook(Request $request)
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $secret = env('STRIPE_WEBHOOK_SECRET');

        try {
            $event = \Stripe\Webhook::constructEvent($payload, $sigHeader, $secret);
        } catch (\Exception $e) {
            return response('Invalid signature.', 400);
        }

        if ($event->type === 'payment_intent.succeeded') {
            $paymentIntent = $event->data->object;
            $donation = Donation::where('stripe_payment_id', $paymentIntent->id)->first();

            if ($donation) {
                $amount_to_add = $paymentIntent->amount / 100;
                $fund = Fund::firstOrCreate(['name' => 'Main Account'], ['balance' => 0]);

                // هذه العملية ستعمل الآن بفضل $casts
                $fund->balance += $amount_to_add;
                $fund->save();
            }
        }

        return response('Webhook handled successfully', 200);
    }

}
