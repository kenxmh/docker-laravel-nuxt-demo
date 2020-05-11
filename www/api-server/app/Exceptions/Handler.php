<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * @param  \Exception  $exception
     * @return void
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $exception)
    {
        if ($exception instanceof MethodNotAllowedHttpException) {
            return response()->json([
                "error_code" => 405,
                "message"    => 'Method Not Allowed',
            ], 405);
        } else if ($request->is("api/*") && $exception instanceof ValidationException) {
            return response()->json([
                "error_code" => 406,
                "message"    => array_values($exception->errors())[0][0],
            ], 406);
        } else if ($exception instanceof AuthenticationException) {
            return response()->json([
                'error_code' => 401,
                'message'    => 'Unverified Token.',
            ], 401);

        } else if ($exception instanceof NotFoundHttpException) {
            return response()->json([
                'error_code' => 400,
                'data' => $request->getUri(),
                'message'    => 'Route Not Found.',
            ], 400);
        }
        return parent::render($request, $exception);
    }
}
