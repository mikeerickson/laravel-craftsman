<?php

namespace App;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Mustache_Engine;
use Phar;

class CraftsmanFileSystem
{
    protected $fs;

    protected $mustache;

    public function __construct()
    {
        $this->fs = new Filesystem();
        $this->mustache = new Mustache_Engine();
    }

    private function getPharPath()
    {
        $path = Phar::running(false);
        if (strlen($path) > 0) {
            $path = dirname($path).DIRECTORY_SEPARATOR;
        }
        return $path;
    }

    private function createMergeFile($src, $dest, $data)
    {
        $template = $this->fs->get($src);

        $data["useExtends"] = $data["extends"];
        $data["useSection"] = $data["section"];

        $merged_data = $this->mustache->render($template, $data);

        try {
            $this->createParentDirectory($dest);
            $this->fs->put($dest, $merged_data);
            $result = [
                "filename" => $dest,
                "status" => "success",
                "message" => "{$dest} Created Successfully",
            ];
        } catch (\Exception $e) {
            $result = [
                "status" => "error",
                "message" => $e->getMessage(),
            ];
        }

        return $result;
    }

    public function createViewFiles($asset, $data)
    {
        $path = $this->getTemplatePath("views");

        $noCreate = $data["noCreate"];
        $noEdit = $data["noEdit"];
        $noIndex = $data["noIndex"];
        $noShow = $data["noShow"];

        $createTemplate = $noCreate ? "" : "view-create";
        $editTemplate = $noEdit ? "" : "view-edit";
        $indexTemplate = $noIndex ? "" : "view-index";
        $showTemplate = $noShow ? "" : "view-show";

        // craft create view
        if (!$noCreate) {
            $src = $this->getUserTemplate("./config.php", $createTemplate);
            if (!file_exists($src)) {
                $src = config("craftsman.templates.{$createTemplate}");
            }

            $src = $this->getPharPath().$src;

            $dest = $this->path_join($path, $asset, "create.blade.php");

            $result = $this->createMergeFile($src, $dest, $data);
        }

        // craft edit view
        if (!$noEdit) {
            $src = $this->getUserTemplate("./config.php", $editTemplate);
            if (!file_exists($src)) {
                $src = config("craftsman.templates.{$editTemplate}");
            }

            $src = $this->getPharPath().$src;

            $dest = $this->path_join($path, $asset, "edit.blade.php");

            $result = $this->createMergeFile($src, $dest, $data);
        }

        // craft index view
        if (!$noIndex) {
            $src = $this->getUserTemplate("./config.php", $indexTemplate);
            if (!file_exists($src)) {
                $src = config("craftsman.templates.{$indexTemplate}");
            }

            $src = $this->getPharPath().$src;

            $dest = $this->path_join($path, $asset, "index.blade.php");

            $result = $this->createMergeFile($src, $dest, $data);
        }

        // craft show view
        if (!$noShow) {
            $src = $this->getUserTemplate("./config.php", $showTemplate);
            if (!file_exists($src)) {
                $src = config("craftsman.templates.{$showTemplate}");
            }

            $src = $this->getPharPath().$src;

            $dest = $this->path_join($path, $asset, "show.blade.php");

            $result = $this->createMergeFile($src, $dest, $data);
        }

        $filenames = [];
        if (!$noCreate) {
            $filenames[] = "create";
        }
        if (!$noEdit) {
            $filenames[] = "edit";
        }
        if (!$noIndex) {
            $filenames[] = "index";
        }
        if (!$noShow) {
            $filenames[] = "show";
        }

        $message = implode(", ", $filenames);
        return [
            "status" => "success",
            "message" => $message,
        ];

    }

    public function getTemplatePath($type)
    {
        switch ($type) {
            case 'class':
                $path = $this->class_path();
                break;
            case 'api-controller':
            case 'empty-controller':
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
            case 'test':
                $path = $this->test_path();
                break;
            case 'views':
                $path = $this->view_path();
                break;
            default:
                $path = '';
        }

        return $path;
    }

    public function buildFieldData($fields = "")
    {
        // format:
        // fieldName:fieldType@fieldSize:option1:option2
        //  eg --fields fname:string@25:nullable:unique,lname:string@50:nullable
        $fieldData = "";
        if (strlen($fields) !== 0) {
            $fieldList = preg_split("/,? ?,/", $fields);
            foreach ($fieldList as $field) {
                $parts = explode(":", trim($field));
                if (sizeof($parts) >= 2) {
                    $name = $parts[0];
                    $fieldType = $parts[1];
                } else {
                    $fieldType = "string";
                }

                $fieldSize = "";
                if (strpos($fieldType, "@") !== false) {
                    [$fieldType, $fieldSize] = explode("@", $fieldType);
                    $fieldSize = ",".$fieldSize;
                }

                $optional = "";
                if (sizeof($parts) >= 3) {
                    $parts = array_splice($parts, 2);
                    foreach ($parts as $part) {
                        $optional .= "->{$part}()";
                    }
                }

                // $this->string('first_name',255)->nullable()->unique();
                // $table->string('name');
                $fieldData .= "            \$table->{$fieldType}('{$name}'{$fieldSize}){$optional};".PHP_EOL;
            }
        }

        // strip last PHP_EOL so we have clean migration file
        if (strlen($fieldData) > 0) {
            $fieldData = substr($fieldData, 0, strlen($fieldData) - 1);
        }
        
        return $fieldData;
    }

    // TODO: This method needs refactoring
    public function createFile($type = null, $filename = null, $data = [])
    {
        $path = $this->getTemplatePath($type);

        $namespace = "";
        $src = $this->getUserTemplate("./config.php", $type);

        if (!file_exists($src)) {
            $src = config("craftsman.templates.{$type}");
        }

        $src = $this->getPharPath().$src;

        if (!file_exists($src)) {
            printf("\n\n");
            Log::error("Unable to locate template './{$src}' Has it been deleted?");
            exit(1);
        }

        if (Str::contains($filename, "App")) {
            $dest = $this->path_join($filename.".php");
        } else {
            $dest = $this->path_join($path, $filename.".php");
        }

        $tablename = "";

        if (isset($data["tablename"])) {
            $tablename = strtolower($data["tablename"]);
        } else {
            if (isset($data["model"])) {
                $tablename = Str::plural(strtolower(class_basename($data["model"])));
            }
        }

        $fields = "";
        if (isset($data["fields"])) {
            $fields = strtolower($data["fields"]);
        }

        $fieldData = $this->buildFieldData($fields);
        $model = "";
        $model_path = "";

        if (isset($data["model"])) {
            $model = class_basename($data["model"]);
            $model_path = $data["model"];
        } else {
            $model = class_basename($data["name"]);
            $namespace = str_replace("/", "\\", str_replace("/".$model, "", $data["name"]));
        }

        $vars = [
            "name" => $filename,
            "model" => $model,
            "model_path" => $model_path,
            "tablename" => $tablename,
            "fields" => $fieldData,
        ];

        if (isset($data["namespace"])) {
            $vars["namespace"] = $data["namespace"];
        } else {
            if (strlen($namespace) > 0) {
                $vars["namespace"] = $namespace;
            }
        }

        if (isset($data["namespace"])) {
            if ($vars["model"] === $vars["namespace"]) {
                $vars["namespace"] = "App";
            }
        }

        // this variable is only used in seed
        if (isset($data["num_rows"])) {
            $vars["num_rows"] = (int) $data["num_rows"] ?: 1;
        }

        // this variable is only used in migration
        if (isset($data["down"])) {
            $vars["down"] = $data["down"];
        }

        // this variable is only used in test
        if (isset($data["extends"])) {
            $vars["extends"] = $data["extends"];
        }

        // this variable is only used in test
        if (isset($data["setup"])) {
            $vars["setup"] = $data["setup"];
        }

        // this variable is only used in test
        if (isset($data["teardown"])) {
            $vars["teardown"] = $data["teardown"];
        }

        // this variable is only used in migration
        if (isset($data["constructor"])) {
            $vars["constructor"] = $data["constructor"];
        } else {
            $vars["constructor"] = false;
        }

//        $vars["model_path"] = str_replace("/", "\\", $vars["model_path"]);

        $template = $this->fs->get($src);

        $mustache = new Mustache_Engine();

        $vars["model_path"] = str_replace("/", "\\", $vars["model_path"]);

        $template_data = $mustache->render($template, $vars);

        try {
            $this->createParentDirectory($dest);
            $this->fs->put($dest, $template_data);
            $result = [
                "filename" => $dest,
                "status" => "success",
                "message" => "{$dest} Created Successfully",
            ];
        } catch (\Exception $e) {
            $result = [
                "status" => "error",
                "message" => $e->getMessage(),
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

    public function test_path()
    {
        return config('craftsman.paths.tests');
    }

    public function view_path()
    {
        return config('craftsman.paths.views');
    }

    public function createParentDirectory($filename)
    {
        if (!is_dir(dirname($filename))) {
            mkdir(dirname($filename), 0777, true);
        }
    }

    public function getUserTemplate($userConfigFilename = "./config.php", $type = "")
    {
        if (file_exists($userConfigFilename)) {
            $config = include($userConfigFilename);
            if (isset($config["templates"])) {
                if (isset($config["templates"][$type])) {
                    return $config["templates"][$type];
                }
            }
        }

        return ""; // we didnt find the entry, return null string
    }

    public function getLastFilename($dirname = "", $partial = "")
    {
        $files = array_reverse(scandir($dirname));
        $filename = $files[0];

        foreach ($files as $file) {
            if (strpos($file, $partial)) {
                $filename = $file;
                break;
            }
        }
        return $dirname.DIRECTORY_SEPARATOR.$filename;
    }
}
