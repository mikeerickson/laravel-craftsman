<?php

use Codedungeon\PHPMessenger\Facades\Messenger;

if (!function_exists('path_join')) {
    /**
     * @return string|string[]|null
     */
    function path_join()
    {
        $paths = array();

        foreach (func_get_args() as $arg) {
            if ($arg !== '') {
                $paths[] = $arg;
            }
        }

        return preg_replace('#/+#', '/', join('/', $paths));
    }
}

if (!function_exists("rmdir")) {
    /**
     * @param $dirname
     */
    function rmdir($dirname)
    {
        system("rm -rf " . escapeshellarg($dirname));
    }
}

if (!function_exists("create_parent_dir")) {
    /**
     * @param $filename
     */
    function create_parent_dir($filename)
    {
        if (!is_dir(dirname($filename))) {
            mkdir(dirname($filename), 0777, true);
        }
    }
}

if (!function_exists("get_build")) {
    /**
     * @return string
     */
    function get_build(): string
    {
        $data = file_get_contents(getcwd() . DIRECTORY_SEPARATOR . "package.json");
        $json = json_decode($data, true, 512);
        return $json["build"];
    }
}

if (!function_exists("get_version")) {
    /**
     * @return string
     */
    function get_version(): string
    {
        $data = file_get_contents(getcwd() . DIRECTORY_SEPARATOR . "package.json");
        $json = json_decode($data, true, 512);
        return $json["version"];
    }
}

if (!function_exists("is_phar")) {
    /**
     * @return bool
     */
    function is_phar()
    {
        $path = Phar::running(false);
        return strlen($path) > 0;
    }
}

if (!function_exists("dlog")) {
    function dlog($msg = "")
    {
        if (!is_phar()) {
            app('log')->info($msg);
        }
    }
}

if (!function_exists("msg_info")) {
    function msg_info($msg = ""): void
    {
        Messenger::info($msg);
    }
}

if (!function_exists("msg_debug")) {
    function msg_debug($msg = ""): void
    {
        Messenger::debug($msg);
    }
}

if (!function_exists("debug")) {
    function debug($msg = ""): void
    {
        Messenger::debug($msg);
    }
}
