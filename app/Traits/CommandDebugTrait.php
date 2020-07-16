<?php

namespace App\Traits;

use Illuminate\Support\Facades\Log;
use Codedungeon\PHPMessenger\Facades\Messenger;

trait CommandDebugTrait
{
    function handleDebug()
    {
        $this->isDebug() ? $this->debugMessage() : null;
    }

    function isDebug()
    {
        return (in_array("--debug", $_SERVER['argv']));
        // return $this->option("debug");
    }

    function debugMessage()
    {
        $data = array_merge($this->arguments(), $this->options());
        $msg = "";
        foreach ($data as $key => $value) {
            if ($value) {
                if (($key === "command") || ($key === "name")) {
                    $msg .= $value .  " ";
                } else {
                    $msg .= "--" . $key;
                    $msg .= (gettype($value) === "boolean") ? " " : "=" . "\"" . $value . "\" ";
                }
            }
        }

        $len = strlen($msg) + 2;

        Messenger::status("\n" . " +" . str_repeat("-", $len) . "+");
        Messenger::status("   " . trim($msg));
        Messenger::status(" +" . str_repeat("-", $len) . "+");
        Messenger::status("\n");
    }

    function debug($msg)
    {
        if ($this->isDebug()) {
            [$childClass, $caller] = debug_backtrace(false, 2);
            dump($caller["file"] . "::" . $caller["function"] . "." . $caller["line"]);
            dump($childClass["file"] . "::" . $childClass["function"] . "." . $childClass["line"]);
            echo ("\n");
            dump($msg);
            echo ("\n");
        }
    }

    function dump($msg)
    {
        $this->debug($msg);
    }

    function log($msg)
    {
        Log::info($msg);
    }
}
