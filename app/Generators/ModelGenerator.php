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
            $this->debug(array_merge($args->arguments(), $args->options()));
        }

        $this->type = "model";

        $this->fs = new CraftsmanFileSystem();

        //        $data = array_merge($args->arguments(), $args->options());

        $this->vars = $this
            ->setupCommandVariables(array_merge($args->arguments(), $args->options()));
    }

    public function setupCommandVariables(array $data): array
    {
        $vars = [
            "name" => $data["name"],
            "className" => $this->fs->getClassName($data["name"]),
            "table" => $data["table"],
            "tablename" => $data["table"],
            "all" => $data["all"],
            "controller" => $data["controller"],
            "factory" => $data["factory"],
            "migration" => $data["migration"],
            "seed" => $data["seed"],
            "template" => $data["template"],
            "overwrite" => $data["overwrite"],
        ];

        return $vars;
    }

    public function createFile(): array
    {
        $templateFilename = (strlen($this->vars["template"]) === 0)
            ? $this->type
            : $this->vars["template"];

        $src = $this->fs->getTemplateFilename($templateFilename);

        $templateResult = $this->fs->verifyTemplate($src);

        // if we have a bad template, short circut and bail out
        if ($templateResult["status"] === CraftsmanResultCodes::FILE_NOT_FOUND) {
            return $templateResult;
        }

        $modelPath = $this->fs->model_path("models");

        if (file_exists($modelPath)) {
            if (strpos($this->vars["name"], "App/Models") === false) {
                $this->vars["name"] = "App/Models/" . $this->vars["name"];
            }
        }

        $this->vars["namespace"] = $this->fs->getNamespace("model", $this->vars["name"]);;
        $this->vars["model"] = $this->fs->getModel($this->vars["name"]);

        //        $this->vars["name"] = str_replace("App/Models/", "", $this->vars["name"]);
        $this->vars["name"] = str_replace("App/", "", $this->vars["name"]);

        // setup tablename, extract from model name
        $tablename = $this->vars["table"];
        if (strlen($tablename) === 0) {
            $tablename = Str::plural(strtolower($this->vars["model"]));
        }
        $this->vars["table"] = $this->vars["tablename"] = $tablename;


        $path = $this->fs->getOutputPath($this->type);

        $dest = $this->fs->path_join($path, $this->vars["name"] . ".php");
        $dest = str_replace("models/Models/", "Models/", $dest);

        return $this->fs->mergeFile($src, $dest, $this->vars);
    }
}
