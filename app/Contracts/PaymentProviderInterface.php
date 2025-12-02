<?php

namespace App\Contracts;

use App\DTOs\PaymentRequest;
use App\DTOs\PaymentResponse;
use App\DTOs\PaymentVerificationResponse;
use App\DTOs\RefundRequest;
use App\DTOs\RefundResponse;
use Illuminate\Http\Request;

interface PaymentProviderInterface
{
    /**
     * Initialize a payment transaction
     */
    public function initializePayment(PaymentRequest $request): PaymentResponse;

    /**
     * Verify a payment transaction
     */
    public function verifyPayment(string $reference): PaymentVerificationResponse;

    /**
     * Handle webhook notifications from payment provider
     */
    public function handleWebhook(Request $request): bool;

    /**
     * Process a refund
     */
    public function refund(RefundRequest $request): RefundResponse;

    /**
     * Get the provider name
     */
    public function getName(): string;
}
