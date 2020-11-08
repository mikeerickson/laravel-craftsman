<?php

namespace App\Generators;

use App\CraftsmanFileSystem;
use App\Traits\CommandDebugTrait;
use App\Commands\CraftsmanResultCodes;

class ClassGenerator implements GeneratorInterface
{
    use CommandDebugTrait;

    protected $fs;
    protected $vars;
    protected $type;

    public function __construct($args)
    {
        if ($args->option('debug')) {
            $this->debug($args->options());
            $this->debug($args->arguments());
        }

        $this->type = "class";
        $this->fs = new CraftsmanFileSystem();

        $data = array_merge($args->arguments(), $args->options());

        $this->vars = $this->setupCommandVariables($data);
    }

    public function setupCommandVariables(array $data): array
    {
        $vars = [
            "name" => $data["name"],
            "className" => $this->fs->getClassName($data["name"]),
            "constructor" => isset($data["constructor"]) ? $data["constructor"] : false,
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

        if ($templateResult["status"] === CraftsmanResultCodes::FILE_NOT_FOUND) {
            return $templateResult;
        }

        $path = $this->fs->getOutputPath($this->type);

        $this->vars["namespace"] = $this->fs->getNamespace("class", $this->vars["name"]);;
        $this->vars["name"] = str_replace("App/", "", $this->vars["name"]);

        $dest = $this->fs->path_join($path, $this->vars["name"].".php");

        $result = $this->fs->mergeFile($src, $dest, $this->vars);

        return $result;
    }
}
