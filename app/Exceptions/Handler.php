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
        $errMsg = ($exception->getMessage());
        $isDebug = strpos($exception->getMessage(), "--debug") > 0;
        if ($isDebug) {
            Messenger::warn($errMsg, " DEBUG ");
            exit;
        }

        $msg = $exception->getMessage() . " \n\nPlease review laravel-craftsman <command> --help for details.";

        $ret = preg_match('/"([^"]+)"/', $msg, $matches);
        if ($ret > 0) {
            $command = $matches[$ret];
            $st = $exception->getTrace()[2]["args"][0];

            $str = (string) $st;
            $str = str_replace("craft:", "php artisan make:", $str);
            $str = str_replace("'", "", $str);
            $makeComand = str_replace("craft:", "make:", $command);
            echo "\n";
            Messenger::info("${command} does not exist, using artisan {$makeComand}...", "INFO");
            shell_exec($str);
        } else {
            echo "\n";
            Messenger::error($msg, " ERROR ");
        }

        exit;
    }
}
