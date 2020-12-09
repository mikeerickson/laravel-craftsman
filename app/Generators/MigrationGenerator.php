<?php

namespace App\Generators;

use Carbon\Carbon;
use Illuminate\Support\Str;
use App\CraftsmanFileSystem;
use App\Traits\CommandDebugTrait;

class MigrationGenerator implements GeneratorInterface
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

        $this->type = "migration";
        $this->fs = new CraftsmanFileSystem();

        $data = array_merge($args->arguments(), $args->options());

        $this->vars = $this->setupCommandVariables($data);
    }

    public function setupCommandVariables(array $data): array
    {
        $vars = $data;

        $migrationName = $data["name"];
        $tablename = $data["table"];
        $model = $data["model"];
        $className = "Create{$model}Table";

        if (strlen($tablename) === 0 || (is_null($tablename))) {
            if (strlen($model) === 0) {
                $parts = explode("_", $migrationName);
                array_shift($parts);
                array_pop($parts);
                $tablename = Str::plural(implode("_", $parts));
                $model = str_replace("_", "", Str::title($tablename));
            } else {
                $parts = explode("/", $model);
                $tablename = Str::plural(array_pop($parts));
            }
        } else {
            if (strlen($model) === 0) {
                $model = str_replace("_", "", Str::title($tablename));
            }
        }

        $create = true;
        $update = false;
        $resourceParts = explode("_", $migrationName);

        if (count($resourceParts) >= 1) {
            if ($resourceParts[0] === 'update') {
                $update = true;
                $create = false;
            }
        }

        if ($vars["update"]) {
            $update = true;
            $create = false;
        }

        $className = Str::studly($migrationName);

        $vars = [
            "name" => $migrationName,
            "className" => $className,
            "create" => $create,
            "update" => $update,
            "model" => $model,
            "table" => $tablename,
            "tablename" => $tablename,
            "fields" => $data["fields"],
            "foreign" => $data["foreign"],
            "pivot" => $data["pivot"],
            "current" => $data["current"],
            "down" => $data["down"],
            "template" => isset($data["template"]) ? $data["template"] : "",
            "overwrite" => false,
        ];

        // format field data
        if ($vars["fields"]) {
            $vars["fields"] = $this->fs->buildFieldData($vars["fields"]);
        }

        $this->vars = $vars;

        // format foreign key values
        $vars = $this->parseForeign($vars);

        return $vars;
    }

    public function createFile(): array
    {
        $templateFilename = (strlen($this->vars["template"]) === 0)
            ? $this->type : $this->vars["template"];

        $src = $this->fs->getTemplateFilename($templateFilename);

        if (is_v8()) {
            $src = str_replace("migration", "migration_v8", $src);
        }

        $templateResult = $this->fs->verifyTemplate($src);
        if ($templateResult["status"] === -1) {
            return $templateResult;
        }

        $path = $this->fs->getOutputPath($this->type);

        // timestamp to be prepended to name
        $migrationFilename = Carbon::now()->format('Y_m_d_His') . "_" . $this->vars["name"] . ".php";

        $dest = $this->fs->path_join($path, $migrationFilename);

        $result = $this->fs->mergeFile($src, $dest, $this->vars);

        return $result;
    }

    protected function parseForeign(array $vars): array
    {
        $vars = $this->vars;
        if (isset($vars["foreign"])) {
            $parts = explode(":", trim($vars["foreign"]));
            $vars["foreign"] = true;
            $fk = $parts[0];
            $vars["fk"] = $fk;

            if (count($parts) >= 2) {
                $primaryInfo = explode(",", $parts[1]);
                [$pktable, $pkid] = $primaryInfo;
                $vars["pkid"] = $pkid;
                $vars["pktable"] = $pktable;
            } else {
                $primaryInfo = explode("_", $parts[0]);
                if (count($primaryInfo) === 2) {
                    $vars["pktable"] = Str::plural($primaryInfo[0]);
                    $vars["pkid"] = "id";
                    $vars["fk"] = $parts[0];
                } else {
                    $vars["pktable"] = $parts[0];
                    $vars["pkid"] = "id";
                    $vars["fk"] = Str::singular($vars["pktable"]) . "_id";
                    if (is_v8()) {
                        $vars["pktable"] = Str::title(Str::singular($parts[0]));
                        $vars["pkid"] = "";
                        $vars["fk"] = "";
                        if (is_dir("./app/models")) {
                            $vars["pktable"] = "App\\Models\\" . $vars["pktable"];
                        } else {
                            $vars["pktable"] = "App\\" . $vars["pktable"];
                        }
                    }
                }
            }
        }
        return $vars;
    }
}
