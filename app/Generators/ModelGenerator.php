<?php

namespace App\Generators;

use Illuminate\Support\Str;
use App\CraftsmanFileSystem;
use App\Traits\CommandDebugTrait;
use App\Commands\CraftsmanResultCodes;

class ModelGenerator implements GeneratorInterface
{
    use CommandDebugTrait;

    protected $type;
    protected $fs;
    protected $vars;

    public function __construct($args)
    {
        if ($args->option('debug')) {
            $this->debug($args->argument('name'));
            $this->debug($args->options());
            $this->debug($args->arguments());
        }

        $this->type = "model";
        $this->fs = new CraftsmanFileSystem();

        $data = array_merge($args->arguments(), $args->options());

        $this->vars = $this->setupCommandVariables($data);
    }

    public function setupCommandVariables(array $data): array
    {
        $vars = $data;

        $name = $data["name"];
        $tablename = $data["table"];

        $vars = [
            "name" => $name,
            "className" => $this->fs->getClassName($name),
            "table" => $tablename,
            "tablename" => $tablename,
            "all" => isset($data["all"]) ? $data["all"] : false,
            "controller" => isset($data["controller"]) ? $data["controller"] : false,
            "factory" => isset($data["factory"]) ? $data["factory"] : false,
            "migration" => isset($data["migration"]) ? $data["migration"] : false,
            "seed" => isset($data["seed"]) ? $data["seed"] : false,
            "template" => isset($data["template"]) ? $data["template"] : "",
            "overwrite" => isset($data["overwrite"]) ? $data["overwrite"] : false,
        ];

        return $vars;
    }

    public function createFile(): array
    {
        $templateFilename = (strlen($this->vars["template"]) === 0)
            ? $this->type : $this->vars["template"];

        $src = $this->fs->getTemplateFilename($templateFilename);
        $templateResult = $this->fs->verifyTemplate($src);

        // if we have a bad template, short circut and bail out
        if ($templateResult["status"] === CraftsmanResultCodes::FAIL) {
            return $templateResult;
        }

        $path = $this->fs->getOutputPath($this->type);

        $parts = explode("/", $this->vars["name"]);
        $model = array_pop($parts);
        $namespace = count($parts) > 0 ? implode("\\", $parts) : "App";

        $this->vars["namespace"] = $namespace;
        $this->vars["model"] = $model;
        $this->vars["name"] = str_replace("App/", "", $this->vars["name"]);

        // setup tablename, extract from model name
        $tablename = $this->vars["table"];
        if (strlen($tablename) === 0) {
            $tablename = Str::plural(strtolower($model));
        }
        $this->vars["table"] = $this->vars["tablename"] = $tablename;

        $dest = $this->fs->path_join($path, $this->vars["name"].".php");

//        dd($this->vars);
        $result = $this->fs->mergeFile($src, $dest, $this->vars);

        return $result;
    }
}
