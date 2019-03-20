<?php

namespace App;

class CraftsmanFileSystem
{
    public function __construct()
    {
        // placeholder
    }

    public function controller_path()
    {
        return $this->path_join(app_path(), "Http", "Controllers");
    }

    public function path_join()
    {
        $paths = array();

        foreach (func_get_args() as $arg) {
            if ($arg !== '') {
                $paths[] = $arg;
            }
        }

        return preg_replace('#/+#', '/', join('/', $paths));
    }

    public function factory_path()
    {
        return $this->path_join(database_path(), "factories");
    }

    public function migration_path()
    {
        return $this->path_join(database_path(), "migrations");
    }

    public function seed_path()
    {
        return $this->path_join(database_path(), "seeds");
    }

    public function model_path($model_path = null)
    {
        if (!is_null($model_path)) {
            return $this->path_join(app_path(), $model_path);
        } else {
            return $this->path_join(app_path());
        }
    }

    public function valid_path($path)
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
