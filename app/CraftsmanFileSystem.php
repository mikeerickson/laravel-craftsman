<?php

namespace App;

use Mustache_Engine;

class CraftsmanFileSystem
{
    public function __construct()
    {

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

    public function createFile($type = null, $filename = null, $data = [])
    {
        switch ($type) {
            case 'controller':
                $path = $this->controller_path();
                break;
            case 'factory':
                $path = $this->factory_path();
                break;
            case 'migration':
                $path = $this->migration_path();
                break;
            case 'model':
                $path = $this->model_path();
                break;
            case 'seed':
                $path = $this->seed_path();
                break;
            default:
                $path = '';
        }

        $src = config('craftsman.templates.' . $type);
        $dest = $this->path_join($path, $filename . ".php");

        $vars = [
            "name" => $filename,
            "model" => class_basename($data["model"]),
            "model_path" => str_replace("/", "\\", $data["model"])
        ];

        $template = file_get_contents($src);

        $mustache = new Mustache_Engine;

        $template_data = $mustache->render($template, $vars);

        try {
            file_put_contents($dest, $template_data);
            $result = [
                "status" => "success",
                "message" => "{$dest} Created Successfully",
            ];
        } catch (\Exception $e) {
            $result = [
                "status" => "error",
                "message" => $e->getMessage()
            ];
        }

        return $result;
    }

    public function controller_path()
    {
        return config('craftsman.paths.controllers');
    }

    public function factory_path()
    {
        return config('craftsman.paths.factories');
    }

    public function migration_path()
    {
        return config('craftsman.paths.migrations');
    }

    public function model_path($model_path = null)
    {
        if (!is_null($model_path)) {
            return $this->path_join(app_path(), $model_path);
        } else {
            return config('craftsman.paths.models');
        }
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

    public function seed_path()
    {
        return config('craftsman.paths.seeds');
    }

}
