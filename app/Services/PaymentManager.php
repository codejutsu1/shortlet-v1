<?php

namespace App\Services;

use App\Contracts\PaymentProviderInterface;
use App\DTOs\PaymentRequest;
use App\DTOs\PaymentResponse;
use App\DTOs\PaymentVerificationResponse;
use App\DTOs\RefundRequest;
use App\DTOs\RefundResponse;
use App\Services\Payment\FlutterwaveProvider;
use App\Services\Payment\PaystackProvider;
use Illuminate\Http\Request;

class PaymentManager
{
    private PaymentProviderInterface $provider;

    public function __construct()
    {
        $this->provider = $this->resolveProvider();
    }

    /**
     * Resolve the active payment provider based on configuration
     */
    private function resolveProvider(): PaymentProviderInterface
    {
        $providerName = config('payment.default_provider', 'paystack');

        return match ($providerName) {
            'flutterwave' => new FlutterwaveProvider,
            'paystack' => new PaystackProvider,
            default => new PaystackProvider,
        };
    }

    /**
     * Get the active provider instance
     */
    public function getProvider(): PaymentProviderInterface
    {
        return $this->provider;
    }

    /**
     * Get the active provider name
     */
    public function getProviderName(): string
    {
        return $this->provider->getName();
    }

    /**
     * Initialize a payment
     */
    public function initializePayment(PaymentRequest $request): PaymentResponse
    {
        return $this->provider->initializePayment($request);
    }

    /**
     * Verify a payment
     */
    public function verifyPayment(string $reference): PaymentVerificationResponse
    {
        return $this->provider->verifyPayment($reference);
    }

    /**
     * Handle webhook from payment provider
     */
    public function handleWebhook(Request $request): bool
    {
        return $this->provider->handleWebhook($request);
    }

    /**
     * Process a refund
     */
    public function refund(RefundRequest $request): RefundResponse
    {
        return $this->provider->refund($request);
    }

    /**
     * Get provider public key for frontend integration
     */
    public function getPublicKey(): string
    {
        $provider = $this->provider;

        if (method_exists($provider, 'getPublicKey')) {
            return $provider->getPublicKey();
        }

        return '';
    }
}
