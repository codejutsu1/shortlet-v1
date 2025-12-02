<?php

namespace App\DTOs;

class RefundResponse
{
    public function __construct(
        public readonly bool $success,
        public readonly ?string $refundId = null,
        public readonly ?string $message = null
    ) {}

    public function toArray(): array
    {
        return [
            'success' => $this->success,
            'refund_id' => $this->refundId,
            'message' => $this->message,
        ];
    }
}
