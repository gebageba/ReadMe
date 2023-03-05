<?php

namespace App\Http\Presentation\Transformer;

use League\Fractal;

class ErrorMessageTransformer extends Fractal\TransformerAbstract
{
    public function transform(string $message): array
    {
        return [
            'message' => $message,
        ];
    }
}
