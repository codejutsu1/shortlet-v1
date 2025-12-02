<?php

namespace App\Services\Payment;

use App\DTOs\PaymentRequest;
use App\DTOs\PaymentResponse;
use App\DTOs\PaymentVerificationResponse;
use App\DTOs\RefundRequest;
use App\DTOs\RefundResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class PaystackProvider extends PaymentProvider
{
    private string $secretKey;

    private string $publicKey;

    private string $baseUrl = 'https://api.paystack.co';

    public function __construct()
    {
        $this->secretKey = config('services.paystack.secret_key');
        $this->publicKey = config('services.paystack.public_key');
    }

    public function getName(): string
    {
        return 'paystack';
    }

    public function initializePayment(PaymentRequest $request): PaymentResponse
    {
        try {
            $this->log('Initializing payment', ['reference' => $request->reference]);

            $response = Http::withHeaders([
                'Authorization' => 'Bearer '.$this->secretKey,
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl.'/transaction/initialize', [
                'email' => $request->email,
                'amount' => $this->convertToSmallestUnit($request->amount),
                'reference' => $request->reference,
                'callback_url' => $request->callbackUrl,
                'metadata' => $request->metadata,
            ]);

            if ($response->successful() && $response->json('status')) {
                $data = $response->json('data');

                $this->log('Payment initialized successfully', [
                    'reference' => $request->reference,
                    'authorization_url' => $data['authorization_url'],
                ]);

                return new PaymentResponse(
                    success: true,
                    authorizationUrl: $data['authorization_url'],
                    reference: $data['reference'],
                    message: 'Payment initialized successfully'
                );
            }

            $errorMessage = $response->json('message', 'Failed to initialize payment');
            $this->logError('Payment initialization failed', [
                'reference' => $request->reference,
                'error' => $errorMessage,
            ]);

            return new PaymentResponse(
                success: false,
                message: $errorMessage
            );
        } catch (\Exception $e) {
            $this->logError('Payment initialization exception', [
                'reference' => $request->reference,
                'exception' => $e->getMessage(),
            ]);

            return new PaymentResponse(
                success: false,
                message: 'An error occurred while initializing payment'
            );
        }
    }

    public function verifyPayment(string $reference): PaymentVerificationResponse
    {
        try {
            $this->log('Verifying payment', ['reference' => $reference]);

            $response = Http::withHeaders([
                'Authorization' => 'Bearer '.$this->secretKey,
            ])->get($this->baseUrl.'/transaction/verify/'.$reference);

            if ($response->successful() && $response->json('status')) {
                $data = $response->json('data');

                $this->log('Payment verified', [
                    'reference' => $reference,
                    'status' => $data['status'],
                ]);

                return new PaymentVerificationResponse(
                    success: true,
                    status: $data['status'],
                    amount: $this->convertFromSmallestUnit($data['amount']),
                    reference: $data['reference'],
                    metadata: $data['metadata'] ?? [],
                    message: 'Payment verified successfully'
                );
            }

            $errorMessage = $response->json('message', 'Payment verification failed');
            $this->logError('Payment verification failed', [
                'reference' => $reference,
                'error' => $errorMessage,
            ]);

            return new PaymentVerificationResponse(
                success: false,
                status: 'failed',
                amount: 0,
                reference: $reference,
                message: $errorMessage
            );
        } catch (\Exception $e) {
            $this->logError('Payment verification exception', [
                'reference' => $reference,
                'exception' => $e->getMessage(),
            ]);

            return new PaymentVerificationResponse(
                success: false,
                status: 'error',
                amount: 0,
                reference: $reference,
                message: 'An error occurred while verifying payment'
            );
        }
    }

    public function handleWebhook(Request $request): bool
    {
        try {
            // Verify webhook signature
            $signature = $request->header('x-paystack-signature');
            $body = $request->getContent();

            if ($signature !== hash_hmac('sha512', $body, $this->secretKey)) {
                $this->logError('Invalid webhook signature');

                return false;
            }

            $event = $request->input('event');
            $this->log('Webhook received', ['event' => $event]);

            // Process only charge.success events
            if ($event === 'charge.success') {
                // The actual processing will be done in the controller
                // This method just validates the webhook
                return true;
            }

            return false;
        } catch (\Exception $e) {
            $this->logError('Webhook handling exception', [
                'exception' => $e->getMessage(),
            ]);

            return false;
        }
    }

    public function refund(RefundRequest $request): RefundResponse
    {
        try {
            $this->log('Processing refund', ['transaction_id' => $request->transactionId]);

            $payload = [
                'transaction' => $request->transactionId,
            ];

            if ($request->amount !== null) {
                $payload['amount'] = $this->convertToSmallestUnit($request->amount);
            }

            $response = Http::withHeaders([
                'Authorization' => 'Bearer '.$this->secretKey,
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl.'/refund', $payload);

            if ($response->successful() && $response->json('status')) {
                $data = $response->json('data');

                $this->log('Refund processed successfully', [
                    'transaction_id' => $request->transactionId,
                    'refund_id' => $data['id'] ?? null,
                ]);

                return new RefundResponse(
                    success: true,
                    refundId: (string) ($data['id'] ?? ''),
                    message: 'Refund processed successfully'
                );
            }

            $errorMessage = $response->json('message', 'Refund processing failed');
            $this->logError('Refund failed', [
                'transaction_id' => $request->transactionId,
                'error' => $errorMessage,
            ]);

            return new RefundResponse(
                success: false,
                message: $errorMessage
            );
        } catch (\Exception $e) {
            $this->logError('Refund exception', [
                'transaction_id' => $request->transactionId,
                'exception' => $e->getMessage(),
            ]);

            return new RefundResponse(
                success: false,
                message: 'An error occurred while processing refund'
            );
        }
    }

    /**
     * Get Paystack public key for frontend
     */
    public function getPublicKey(): string
    {
        return $this->publicKey;
    }
}
