<?php

namespace App\Exceptions;

use Exception;
use Codedungeon\PHPMessenger\Facades\Messenger;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        // \Illuminate\Database\Eloquent\ModelNotFoundException::class,
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception  $exception
     * @return void
     */
    public function report(Exception $exception)
    {
        // parent::report($exception);
        // dd($exception->getTrace()[2]["args"][0]);
        echo "\n";
        $msg = $exception->getMessage() . " Please review laravel-craftsman <command> --help for details.";
        Messenger::error($msg, "ERROR");
        exit;
    }
}
