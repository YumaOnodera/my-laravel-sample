<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        //
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
            //
        });

        $this->renderable(function (Throwable $e) {
            // 422 バリデーションエラー
            if ($e instanceof ValidationException) {
                return response()->json(
                    [
                        'message' => $e->errors(),
                    ],
                    $e->status,
                );
            }

            // HttpException
            if ($this->isHttpException($e)) {
                return response()->json(
                    [
                        'message' => $this->createMessage($e),
                    ],
                    $e->getStatusCode(),
                );
            }

            return response()->json(
                [
                    'message' => 'サーバーで処理中にエラーが発生しました。',
                ],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        });
    }

    /**
     * @param  Throwable  $e
     * @return string
     */
    protected function createMessage(Throwable $e): string
    {
        // 401
        if ($e instanceof UnauthorizedHttpException) {
            return '認証に失敗しました。';
        }

        // 403
        if ($e instanceof AccessDeniedHttpException) {
            return '許可されていない操作です。';
        }

        // 404
        if ($e instanceof NotFoundHttpException) {
            return 'URLが存在しません。';
        }

        // 405
        if ($e instanceof MethodNotAllowedHttpException) {
            return '未定義のHTTPメソッドが指定されました。';
        }

        // 500
        return 'サーバーで処理中にエラーが発生しました。';
    }
}
