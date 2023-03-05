<?php

namespace App\Exceptions;

use App\Http\Presentation\Transformer\ErrorMessageTransformer;
use Spatie\Fractalistic\ArraySerializer;

class JWTException extends ClientException
{
    public $message;

    public function __construct(string $message)
    {
        $this->message = $message;
        parent::__construct();
    }

    public function render()
    {
        return fractal($this->message, new ErrorMessageTransformer())
            ->serializeWith(new ArraySerializer())
            ->respond(401);
    }
}
