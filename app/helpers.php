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

if (!function_exists("valid_path")) {
    /**
     * @param $path
     * @return bool
     */
    function valid_path($inPath = "")
    {
        $path = $inPath;

        // whether $path is unix or not
        $unipath = strlen($path) == 0 || $path{
            0} != '/';
        $unc = substr($path, 0, 2) == '\\\\' ? true : false;
        // attempts to detect if path is relative in which case, add cwd
        if (strpos($path, ':') === false && $unipath && !$unc) {
            $path = getcwd() . DIRECTORY_SEPARATOR . $path;
            if ($path{
                0} == '/') {
                $unipath = false;
            }
        }

        // resolve path parts (single dot, double dot and double delimiters)
        $path = str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $path);
        $parts = array_filter(explode(DIRECTORY_SEPARATOR, $path), 'strlen');
        $absolutes = array();
        foreach ($parts as $part) {
            if ('.' == $part) {
                continue;
            }
            if ('..' == $part) {
                array_pop($absolutes);
            } else {
                $absolutes[] = $part;
            }
        }
        $path = implode(DIRECTORY_SEPARATOR, $absolutes);
        // resolve any symlinks
        if (function_exists('readlink') && file_exists($path) && linkinfo($path) > 0) {
            $path = readlink($path);
        }
        // put initial separator that could have been lost
        $path = !$unipath ? '/' . $path : $path;
        $path = $unc ? '\\\\' . $path : $path;
        return is_dir($path);
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

if (!function_exists("valid_path")) {
    /**
     * @param $path
     * @return mixed|string
     */
    function valid_path($path)
    {
        // whether $path is unix or not
        $unipath = strlen($path) == 0 || $path[0] != '/';
        $unc = substr($path, 0, 2) == '\\\\' ? true : false;
        // attempts to detect if path is relative in which case, add cwd
        if (strpos($path, ':') === false && $unipath && !$unc) {
            $path = getcwd() . DIRECTORY_SEPARATOR . $path;
            if ($path[0] == '/') {
                $unipath = false;
            }
        }

        // resolve path parts (single dot, double dot and double delimiters)
        $path = str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $path);
        $parts = array_filter(explode(DIRECTORY_SEPARATOR, $path), 'strlen');
        $absolutes = array();
        foreach ($parts as $part) {
            if ('.' == $part) {
                continue;
            }
            if ('..' == $part) {
                array_pop($absolutes);
            } else {
                $absolutes[] = $part;
            }
        }
        $path = implode(DIRECTORY_SEPARATOR, $absolutes);
        // resolve any symlinks
        if (
            function_exists('readlink') &&
            file_exists($path) &&
            linkinfo($path) > 0
        ) {
            $path = readlink($path);
        }
        // put initial separator that could have been lost
        $path = !$unipath ? '/' . $path : $path;
        $path = $unc ? '\\\\' . $path : $path;
        return $path;
    }
}

if (!function_exists("get_build")) {
    /**
     * @return mixed
     */
    function get_build()
    {
        $data = file_get_contents(getcwd() . DIRECTORY_SEPARATOR . "package.json");
        $json = json_decode($data, true, 512);
        return $json["build"];
    }
}

if (!function_exists("get_version")) {
    /**
     * @return mixed
     */
    function get_version()
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
    /**
     * @return bool
     */
    function dlog($msg = "")
    {
        if (!is_phar()) {
            Log::info($msg);
        }
    }
}

if (!function_exists("msg_info")) {
    function msg_info($msg = "")
    {
        Messenger::info($msg);
    }
}

if (!function_exists("msg_debug")) {
    function msg_debug($msg = "")
    {
        Messenger::debug($msg);
    }
}

if (!function_exists("debug")) {
    function debug($msg = "")
    {
        Messenger::debug($msg);
    }
}
