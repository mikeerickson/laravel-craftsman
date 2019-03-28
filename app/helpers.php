<?php

if (!function_exists('path_join')) {
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

if (!function_exists("valid_path")) {
    function valid_path($path)
    {
        // whether $path is unix or not
        $unipath = strlen($path) == 0 || $path{0} != '/';
        $unc = substr($path, 0, 2) == '\\\\' ? true : false;
        // attempts to detect if path is relative in which case, add cwd
        if (strpos($path, ':') === false && $unipath && !$unc) {
            $path = getcwd().DIRECTORY_SEPARATOR.$path;
            if ($path{0} == '/') {
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
        $path = !$unipath ? '/'.$path : $path;
        $path = $unc ? '\\\\'.$path : $path;
        return is_dir($path);
    }
}

if (!function_exists("create_parent_dir")) {
    function create_parent_dir($filename)
    {
        if (!is_dir(dirname($filename))) {
            mkdir(dirname($filename), 0777, true);
        }
    }
}

if (!function_exists("valid_path")) {
    function valid_path($path)
    {
        // whether $path is unix or not
        $unipath = strlen($path) == 0 || $path[0] != '/';
        $unc = substr($path, 0, 2) == '\\\\' ? true : false;
        // attempts to detect if path is relative in which case, add cwd
        if (strpos($path, ':') === false && $unipath && !$unc) {
            $path = getcwd().DIRECTORY_SEPARATOR.$path;
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
        $path = !$unipath ? '/'.$path : $path;
        $path = $unc ? '\\\\'.$path : $path;
        return $path;
    }
}

if (!function_exists("get_build")) {
    function get_build()
    {
        $data = file_get_contents(getcwd().DIRECTORY_SEPARATOR."package.json");
        $json = json_decode($data, true, 512);
        return $json["build"];
    }
}
