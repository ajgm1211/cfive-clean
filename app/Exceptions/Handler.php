<?php

namespace App\Exceptions;

use ErrorException;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\HttpException;
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
        if ($exception instanceof NotFoundHttpException) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'message' => 'Resource not found',
                ], 404);
            }
        }

        if ($exception instanceof HttpException) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'message' => $exception->getMessage(),
                ], 403);
            }
        }

        if ($exception instanceof ErrorException) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'message' => $exception->getMessage(),
                ], 500);
                /*return response()->json([
                    'message' => 'Something went wrong on our side',
                ], 500);*/
            }
        }

        if ($exception instanceof ModelNotFoundException) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'message' => 'Record not found',
                ], 404);
            }
        }

        return parent::render($request, $exception);
    }
}
