<?php

namespace App\Generators;

use Illuminate\Support\Str;
use App\CraftsmanFileSystem;
use App\Traits\CommandDebugTrait;
use App\Commands\CraftsmanResultCodes;

class ScopeGenerator implements GeneratorInterface
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

        $this->type = "scope";

        $this->fs = new CraftsmanFileSystem();

        $this->vars = $this
            ->setupCommandVariables(array_merge($args->arguments(), $args->options()));
    }

    public function setupCommandVariables(array $data): array
    {
        $vars = [
            "name" => $data["name"],
            "className" => $this->fs->getClassName($data["name"]),
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

        $scopePath = $this->fs->scope_path("scopes");

        $this->vars["namespace"] = $this->fs->getNamespace("scope", $this->vars["name"]);;

        $this->vars["name"] = str_replace("App/", "", $this->vars["name"]);

        $path = $this->fs->getOutputPath($this->type);

        $dest = $this->fs->path_join($path, $this->vars["name"] . ".php");

        return $this->fs->mergeFile($src, $dest, $this->vars);
    }
}
