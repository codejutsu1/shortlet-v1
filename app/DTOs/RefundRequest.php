<?php

namespace App\DTOs;

class RefundRequest
{
    public function __construct(
        public readonly string $transactionId,
        public readonly ?float $amount = null,
        public readonly ?string $reason = null
    ) {}

    public function toArray(): array
    {
        return array_filter([
            'transaction_id' => $this->transactionId,
            'amount' => $this->amount,
            'reason' => $this->reason,
        ], fn ($value) => $value !== null);
    }
}
