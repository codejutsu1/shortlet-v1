<?php

namespace App\Services\Payment;

use App\Contracts\PaymentProviderInterface;
use Illuminate\Support\Facades\Log;

abstract class PaymentProvider implements PaymentProviderInterface
{
    /**
     * Log payment activity
     */
    protected function log(string $message, array $context = []): void
    {
        Log::info("[{$this->getName()}] {$message}", $context);
    }

    /**
     * Log payment errors
     */
    protected function logError(string $message, array $context = []): void
    {
        Log::error("[{$this->getName()}] {$message}", $context);
    }

    /**
     * Convert amount to the smallest currency unit (e.g., kobo for NGN, cents for USD)
     */
    protected function convertToSmallestUnit(float $amount): int
    {
        return (int) ($amount * 100);
    }

    /**
     * Convert amount from smallest currency unit to main unit
     */
    protected function convertFromSmallestUnit(int $amount): float
    {
        return $amount / 100;
    }

    /**
     * Generate a unique payment reference
     */
    protected function generateReference(string $prefix = 'PAY'): string
    {
        return strtoupper($prefix).'-'.time().'-'.bin2hex(random_bytes(4));
    }
}
