<?php

namespace App\Http\Controllers;

use App\DTOs\PaymentRequest;
use App\Models\Booking;
use App\Models\Payment;
use App\Services\PaymentManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class PaymentController extends Controller
{
    public function __construct(
        private PaymentManager $paymentManager
    ) {
    }

    /**
     * Initialize payment for a booking
     */
    public function initialize(Booking $booking)
    {
        // Ensure user owns the booking
        if ($booking->user_id !== Auth::id()) {
            abort(403);
        }

        // Check if booking is pending
        if ($booking->status !== 'pending') {
            return back()->withErrors(['error' => 'This booking cannot be paid for.']);
        }

        // Create payment request DTO
        $paymentRequest = new PaymentRequest(
            amount: (float) $booking->total_price,
            email: Auth::user()->email,
            reference: 'BK-' . $booking->id . '-' . time(),
            callbackUrl: route('payments.callback'),
            metadata: [
                'booking_id' => $booking->id,
                'user_id' => Auth::id(),
                'provider' => $this->paymentManager->getProviderName(),
            ]
        );

        // Initialize payment with the active provider
        $response = $this->paymentManager->initializePayment($paymentRequest);

        if ($response->success) {
            // Create payment record
            Payment::create([
                'booking_id' => $booking->id,
                'amount' => $booking->total_price,
                'payment_method' => $this->paymentManager->getProviderName(),
                'payment_reference' => $response->reference,
                'status' => 'pending',
            ]);

            // Redirect to payment gateway checkout
            return redirect($response->authorizationUrl);
        }

        return back()->withErrors(['error' => $response->message ?? 'Failed to initialize payment. Please try again.']);
    }

    /**
     * Handle payment callback from payment provider
     */
    public function callback(Request $request)
    {
        $reference = $request->query('reference') ?? $request->query('tx_ref');

        if (!$reference) {
            return redirect()->route('payments.failed')
                ->with('error', 'Payment reference not found.');
        }

        // Verify payment with the active provider
        $verification = $this->paymentManager->verifyPayment($reference);

        if ($verification->isSuccessful()) {
            $bookingId = $verification->metadata['booking_id'] ?? null;

            if ($bookingId) {
                $booking = Booking::find($bookingId);
                $payment = Payment::where('payment_reference', $reference)->first();

                if ($booking && $payment) {
                    // Update payment status
                    $payment->update([
                        'status' => 'successful',
                        'paid_at' => now(),
                    ]);

                    // Update booking status
                    $booking->update(['status' => 'confirmed']);

                    Log::info('Payment completed successfully', [
                        'booking_id' => $bookingId,
                        'reference' => $reference,
                        'provider' => $this->paymentManager->getProviderName(),
                    ]);

                    return redirect()->route('payments.success', $booking)
                        ->with('success', 'Payment successful!');
                }
            }
        }

        return redirect()->route('payments.failed')
            ->with('error', $verification->message ?? 'Payment verification failed.');
    }

    /**
     * Handle payment provider webhook
     */
    public function webhook(Request $request)
    {
        // Validate webhook with the active provider
        if (!$this->paymentManager->handleWebhook($request)) {
            Log::warning('Invalid webhook received', [
                'provider' => $this->paymentManager->getProviderName(),
            ]);

            return response()->json(['error' => 'Invalid webhook'], 400);
        }

        // Extract data based on provider
        $data = $request->input('data');
        $event = $request->input('event');

        // Determine reference based on provider
        $reference = $data['reference'] ?? $data['tx_ref'] ?? null;
        $status = $data['status'] ?? null;

        // Process successful payment events
        if (($event === 'charge.success' || $event === 'charge.completed') && $status === 'successful') {
            $bookingId = $data['metadata']['booking_id'] ?? $data['meta']['booking_id'] ?? null;

            if ($bookingId && $reference) {
                $booking = Booking::find($bookingId);
                $payment = Payment::where('payment_reference', $reference)->first();

                if ($booking && $payment && $payment->status === 'pending') {
                    $payment->update([
                        'status' => 'successful',
                        'paid_at' => now(),
                    ]);

                    $booking->update(['status' => 'confirmed']);

                    Log::info('Payment confirmed via webhook', [
                        'booking_id' => $bookingId,
                        'provider' => $this->paymentManager->getProviderName(),
                    ]);
                }
            }
        }

        return response()->json(['status' => 'success']);
    }

    /**
     * Payment success page
     */
    public function success(Booking $booking)
    {
        if ($booking->user_id !== Auth::id()) {
            abort(403);
        }

        return Inertia::render('Payments/Success', [
            'booking' => $booking->load(['property.images']),
        ]);
    }

    /**
     * Payment failed page
     */
    public function failed()
    {
        return Inertia::render('Payments/Failed');
    }
}
