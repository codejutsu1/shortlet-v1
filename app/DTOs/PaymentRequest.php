<?php

namespace App\DTOs;

class PaymentRequest
{
    public function __construct(
        public readonly float $amount,
        public readonly string $email,
        public readonly string $reference,
        public readonly string $callbackUrl,
        public readonly array $metadata = []
    ) {}

    public function toArray(): array
    {
        return [
            'amount' => $this->amount,
            'email' => $this->email,
            'reference' => $this->reference,
            'callback_url' => $this->callbackUrl,
            'metadata' => $this->metadata,
        ];
    }
}
