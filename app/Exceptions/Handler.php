<?php

namespace App\Exceptions;

use App\Http\Presentation\Transformer\ErrorMessageTransformer;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Arr;
use Illuminate\Validation\ValidationException;
use Spatie\Fractalistic\ArraySerializer;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        ClientException::class
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];


    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            if (app()->bound('sentry')) {
                app('sentry')->captureException($e);
            }
        });

        $this->renderable(function (HttpException $e, $request) {
            $message = $e->getMessage();

            switch ($e->getStatusCode()) {
                case 403:
                    $message = __('Forbidden');
                    break;
                case 404:
                    $message = __('Not Found');
                    break;
                case 500:
                    $message = __('Internal Server Error');
                    break;
                default:
                    break;
            }

            return fractal($message, new ErrorMessageTransformer())
                ->serializeWith(new ArraySerializer())
                ->respond($e->getStatusCode());
        });

        $this->renderable(function (ValidationException $e, $request) {
            if ($request->is('api/*')) {
                $message = $e->getMessage();

                if ($e->errors()) {
                    $message = Arr::flatten($e->errors())[0];
                }

                return fractal($message, new ErrorMessageTransformer())
                    ->serializeWith(new ArraySerializer())
                    ->respond($e->status);
            }
        });
    }

    /**
     * AuthenticationExceptionのunauthenticatedメソッドをオーバーライド
     */
    protected function unauthenticated($request, AuthenticationException $e): JsonResponse
    {
        return fractal("Unauthorized", new ErrorMessageTransformer())
            ->serializeWith(new ArraySerializer())
            ->respond(401);
    }
}
