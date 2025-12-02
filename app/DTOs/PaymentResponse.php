<?php

namespace App\DTOs;

class PaymentResponse
{
    public function __construct(
        public readonly bool $success,
        public readonly ?string $authorizationUrl = null,
        public readonly ?string $reference = null,
        public readonly ?string $message = null
    ) {}

    public function toArray(): array
    {
        return [
            'success' => $this->success,
            'authorization_url' => $this->authorizationUrl,
            'reference' => $this->reference,
            'message' => $this->message,
        ];
    }
}
