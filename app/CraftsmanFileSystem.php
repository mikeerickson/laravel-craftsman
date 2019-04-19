<?php

namespace App;

use Phar;
use Exception;
use Mustache_Engine;
use Illuminate\Support\Str;
use Illuminate\Config\Repository;
use Illuminate\Support\Facades\Log;
use Illuminate\Filesystem\Filesystem;
use Codedungeon\PHPMessenger\Facades\Messenger;
use Illuminate\Contracts\Filesystem\FileNotFoundException;


/**
 * Class CraftsmanFileSystem
 * @package App
 */
class CraftsmanFileSystem
{
    /**
     *
     */
    const SUCCESS = 0;
    /**
     *
     */
    const FILE_EXIST = -43;

    /**
     * @var Filesystem
     */
    protected $fs;

    /**
     * @var Mustache_Engine
     */
    protected $mustache;

    /**
     * CraftsmanFileSystem constructor.
     */
    public function __construct()
    {
        $this->fs = new Filesystem();
        $this->mustache = new Mustache_Engine();
    }

    /**
     * @param $dirname
     */
    public function rmdir($dirname)
    {
        if (is_dir($dirname)) {
            system("rm -rf ".escapeshellarg($dirname));
        }
    }

    /**
     * @param $asset
     * @param $data
     * @throws FileNotFoundException
     */
    public function createViewFiles($asset, $data)
    {
        $asset = strtolower($asset);
        $path = $this->getOutputPath("views");

        $noCreate = $data["noCreate"];
        $noEdit = $data["noEdit"];
        $noIndex = $data["noIndex"];
        $noShow = $data["noShow"];

        $createTemplate = $noCreate ? "" : "view-create";
        $editTemplate = $noEdit ? "" : "view-edit";
        $indexTemplate = $noIndex ? "" : "view-index";
        $showTemplate = $noShow ? "" : "view-show";

        $filenames = [];
        $overwrite = isset($data["overwrite"]) ? $data["overwrite"] : false;

        // craft create view
        if (!$noCreate) {
            $src = $this->getUserTemplate("./config.php", $createTemplate);
            if (!file_exists($src)) {
                $src = config("craftsman.templates.{$createTemplate}");
            }

            $src = $this->getPharPath().$src;

            $dest = $this->path_join($path, $asset, "create.blade.php");

            $filenames[] = $this->createMergeFile($src, $dest, $data);
        }

        // craft edit view
        if (!$noEdit) {
            $src = $this->getUserTemplate("./config.php", $editTemplate);
            if (!file_exists($src)) {
                $src = config("craftsman.templates.{$editTemplate}");
            }

            $src = $this->getPharPath().$src;

            $dest = $this->path_join($path, $asset, "edit.blade.php");

            $filenames[] = $this->createMergeFile($src, $dest, $data);
        }

        // craft index view
        if (!$noIndex) {
            $src = $this->getUserTemplate("./config.php", $indexTemplate);
            if (!file_exists($src)) {
                $src = config("craftsman.templates.{$indexTemplate}");
            }

            $src = $this->getPharPath().$src;

            $dest = $this->path_join($path, $asset, "index.blade.php");

            $filenames[] = $this->createMergeFile($src, $dest, $data);
        }

        // craft show view
        if (!$noShow) {
            $src = $this->getUserTemplate("./config.php", $showTemplate);
            if (!file_exists($src)) {
                $src = config("craftsman.templates.{$showTemplate}");
            }

            $src = $this->getPharPath().$src;

            $dest = $this->path_join($path, $asset, "show.blade.php");

            $filenames[] = $this->createMergeFile($src, $dest, $data);
        }

        return $filenames;
    }

    /**
     * @param $type
     * @return Repository|mixed|string|string[]|null
     */
    public function getOutputPath($type)
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
            case 'factories':
            case 'factory':
                $path = $this->factory_path();
                break;
            case 'migrations':
            case 'migration':
                $path = $this->migration_path();
                break;
            case 'model':
                $path = $this->model_path();
                break;
            case 'resource-controller':
            case 'resource':
                $path = $this->resource_path();
                break;
            case 'seeds':
            case 'seed':
                $path = $this->seed_path();
                break;
            case 'tests':
            case 'test':
                $path = $this->test_path();
                break;
            case 'view':
            case 'views':
                $path = $this->view_path();
                break;
            default:
                $path = '';
        }

        return $path;
    }

    /**
     * @return Repository|mixed
     */
    public function class_path()
    {
        return config('craftsman.paths.class');
    }

    /**
     * @return Repository|mixed
     */
    public function controller_path()
    {
        return getcwd().DIRECTORY_SEPARATOR.config('craftsman.paths.controllers');
    }

    /**
     * @return Repository|mixed
     */
    public function factory_path()
    {
        return getcwd().DIRECTORY_SEPARATOR.config('craftsman.paths.factories');
    }

    /**
     * @return Repository|mixed
     */
    public function migration_path()
    {
        return getcwd().DIRECTORY_SEPARATOR.config('craftsman.paths.migrations');
    }

    // TODO: This method needs refactoring

    /**
     * @param  null  $model_path
     * @return Repository|mixed|string|string[]|null
     */
    public function model_path($model_path = null)
    {
        if (is_null($model_path)) {
            return getcwd().DIRECTORY_SEPARATOR.config('craftsman.paths.models');
        } else {
            return getcwd().DIRECTORY_SEPARATOR.config('craftsman.paths.models').DIRECTORY_SEPARATOR.$model_path;
        }
    }

    public function resource_path()
    {
        return getcwd().DIRECTORY_SEPARATOR.config('craftsman.paths.resources');
    }

    /**
     * @return Repository|mixed
     */
    public function seed_path()
    {
        return getcwd().DIRECTORY_SEPARATOR.config('craftsman.paths.seeds');
    }

    /**
     * @return Repository|mixed
     */
    public function test_path()
    {
        return getcwd().DIRECTORY_SEPARATOR.config('craftsman.paths.tests');
    }

    /**
     * @return Repository|mixed
     */
    public function view_path()
    {
        return getcwd().DIRECTORY_SEPARATOR.config('craftsman.paths.views');
    }

    /**
     * @param  string  $userConfigFilename
     * @param  string  $type
     * @return mixed|string
     */
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

    /**
     * @return string
     */
    public function getPharPath()
    {
        $path = Phar::running(false);
        if (strlen($path) > 0) {
            $path = dirname($path).DIRECTORY_SEPARATOR;
        }
        return $path;
    }

    /**
     * @return string|string[]|null
     */
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

    /**
     * @param $src
     * @param $dest
     * @param $data
     * @return int
     * @throws FileNotFoundException
     */
    private function createMergeFile($src, $dest, $data)
    {
        $template = $this->fs->get($src);

        $data["useExtends"] = $data["extends"];
        $data["useSection"] = $data["section"];

        $merged_data = $this->mustache->render($template, $data);

        if (file_exists($dest) && !$data["overwrite"]) {
            Messenger::error("✖︎ {$dest} already exists\n");
            return self::FILE_EXIST;
        }

        try {
            $this->createParentDirectory($dest);
            $this->fs->put($dest, $merged_data);
            $result = [
                "filename" => $dest,
                "status" => "success",
                "message" => "{$dest} created successfully",
            ];
            Messenger::success("✓ {$result['message']}\n");
        } catch (Exception $e) {
            $result = [
                "status" => "error",
                "message" => $e->getMessage(),
            ];
        }

        return basename($dest);
    }

    /**
     * @param $filename
     */
    public function createParentDirectory($filename)
    {
        if (!is_dir(dirname($filename))) {
            mkdir(dirname($filename), 0777, true);
        }
    }

    /**
     * @param  null  $type
     * @param  null  $filename
     * @param  array  $data
     * @return array
     * @throws FileNotFoundException
     */
    public function createFile($type = null, $filename = null, $data = [])
    {
        $overwrite = false;
        if (isset($data["overwrite"])) {
            $overwrite = $data["overwrite"];
        }
        $path = $this->getOutputPath($type);

        $namespace = "";

        if (isset($data["template"])) {
            $src = $this->getTemplateFilename($data["template"]);
        } else {
            $src = $this->getUserTemplate("./config.php", $type);
            if (!file_exists($src)) {
                $src = config("craftsman.templates.{$type}");
            }

            $src = $this->getPharPath().$src;
            $src = str_replace("//", "/", $src);
        }

        if (!file_exists($src)) {
            printf("\n\n");
            Log::error("Unable to locate template './{$src}' Has it been deleted?");
//            throw new CraftsmanFileSystemException($errMsg);
            exit(1);
        }

        if (Str::contains($filename, "App")) {
            $dest = $this->path_join($filename.".php");
        } else {
            $dest = $this->path_join($path, $filename.".php");
        }

        if (file_exists($dest) && (!$overwrite)) {
            Messenger::error("✖︎ {$dest} already exists\n");
            return [
                "status" => self::FILE_EXIST,
                "message" => "{$dest} already exists",
            ];
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
        $model_path = "";

        $model = "";
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
            "collection" => isset($data["collection"]) ? $data["collection"] : false,
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

        $template = $this->fs->get($src);

        $mustache = new Mustache_Engine();

        $vars["model_path"] = str_replace("/", "\\", $vars["model_path"]);

        $template_data = $mustache->render($template, $vars);

        try {
            $this->createParentDirectory($dest);
            $this->fs->put($dest, $template_data);
            $result = [
                "filename" => $dest,
                "fullPath" => getcwd().DIRECTORY_SEPARATOR.$dest,
                "status" => "success",
                "message" => "{$dest} created successfully",
            ];
        } catch (Exception $e) {
            $result = [
                "filename" => $dest,
                "status" => "error",
                "message" => $e->getMessage(),
            ];
        }

        if ($result["status"] === "success") {
            Messenger::success("✔︎ {$dest} created successfully\n");
        }

        return $result;
    }

    /**
     * @param $type
     * @return Repository|mixed
     */
    public function getTemplateFilename($type)
    {
        if (strpos($type, "<project>") !== false) {
            $filename = str_replace("<project>", "", $type);
            $filename = getcwd().DIRECTORY_SEPARATOR.$filename;
            $filename = str_replace("//", "/", $filename);
            return $filename;
        }
        return config("craftsman.templates.{$type}");
    }

    /**
     * @param  string  $fields
     * @return bool|string
     */
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

    /**
     * @return string|string[]|null
     */
    public function pathJoin()
    {
        $paths = array();

        foreach (func_get_args() as $arg) {
            if ($arg !== '') {
                $paths[] = $arg;
            }
        }

        return preg_replace('#/+#', '/', join('/', $paths));
    }

    /**
     * @param  string  $dirname
     * @param  string  $partial
     * @return string
     */
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

        $dirname = getcwd().DIRECTORY_SEPARATOR.$dirname;

        return $dirname.DIRECTORY_SEPARATOR.$filename;
    }
}
