<?php

namespace App;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;
use Mustache_Engine;

class CraftsmanFileSystem
{
    protected $fs;

    public function __construct()
    {
        $this->fs = new Filesystem();
    }

    public function createFile($type = null, $filename = null, $data = [])
    {
        switch ($type) {
            case 'class':
                $path = $this->class_path();
                break;
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
        if (Str::contains($filename, "App")) {
            $dest = $this->path_join($filename . ".php");
        } else {
            $dest = $this->path_join($path, $filename . ".php");
        }

        $tablename = "";
        if (isset($data["tablename"])) {
            $tablename = strtolower($data["tablename"]);
        }
        $vars = [
            "name" => $filename,
            "model" => class_basename($data["model"]),
            "model_path" => str_replace("/", "\\", $data["model"]),
            "tablename" => $tablename
        ];

        if (isset($data["namespace"])) {
            $vars["namespace"] = $data["namespace"];
        }

        // this variable is only used in seed
        if (isset($data["num_rows"])) {
            $vars["num_rows"] = (int)$data["num_rows"] ?: 1;
        }

        $template = $this->fs->get($src);

        $mustache = new Mustache_Engine();

        $template_data = $mustache->render($template, $vars);

        try {
            $this->createParentDirectory($dest);
            $this->fs->put($dest, $template_data);
            $result = [
                "status" => "success",
                "message" => "✔︎ {$dest} Created Successfully",
            ];
        } catch (\Exception $e) {
            $result = [
                "status" => "error",
                "message" => "✖ " . $e->getMessage()
            ];
        }

        return $result;
    }

    public function class_path()
    {
        return config('craftsman.paths.class');
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

    public function createParentDirectory($filename)
    {
        if (!is_dir(dirname($filename))) {
            mkdir(dirname($filename), 0777, true);
        }
    }
}
