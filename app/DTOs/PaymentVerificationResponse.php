<?php

namespace App\DTOs;

class PaymentVerificationResponse
{
    public function __construct(
        public readonly bool $success,
        public readonly string $status,
        public readonly float $amount,
        public readonly string $reference,
        public readonly array $metadata = [],
        public readonly ?string $message = null
    ) {}

    public function toArray(): array
    {
        return [
            'success' => $this->success,
            'status' => $this->status,
            'amount' => $this->amount,
            'reference' => $this->reference,
            'metadata' => $this->metadata,
            'message' => $this->message,
        ];
    }

    public function isSuccessful(): bool
    {
        return $this->success && $this->status === 'success';
    }
}
