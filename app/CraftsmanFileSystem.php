<?php

namespace App;

use Phar;
use Exception;
use Mustache_Engine;
use Illuminate\Support\Str;
use Illuminate\Config\Repository;
use Illuminate\Support\Facades\Log;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Artisan;
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
            system("rm -rf " . escapeshellarg($dirname));
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

            $src = $this->getPharPath() . $src;

            $dest = $this->path_join($path, $asset, "create.blade.php");

            $filenames[] = $this->createMergeFile($src, $dest, $data);
        }

        // craft edit view
        if (!$noEdit) {
            $src = $this->getUserTemplate("./config.php", $editTemplate);
            if (!file_exists($src)) {
                $src = config("craftsman.templates.{$editTemplate}");
            }

            $src = $this->getPharPath() . $src;

            $dest = $this->path_join($path, $asset, "edit.blade.php");

            $filenames[] = $this->createMergeFile($src, $dest, $data);
        }

        // craft index view
        if (!$noIndex) {
            $src = $this->getUserTemplate("./config.php", $indexTemplate);
            if (!file_exists($src)) {
                $src = config("craftsman.templates.{$indexTemplate}");
            }

            $src = $this->getPharPath() . $src;

            $dest = $this->path_join($path, $asset, "index.blade.php");

            $filenames[] = $this->createMergeFile($src, $dest, $data);
        }

        // craft show view
        if (!$noShow) {
            $src = $this->getUserTemplate("./config.php", $showTemplate);
            if (!file_exists($src)) {
                $src = config("craftsman.templates.{$showTemplate}");
            }

            $src = $this->getPharPath() . $src;

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
            case 'binding-controller':
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
            case 'request':
                $path = $this->request_path();
                break;
            case 'resource-controller':
                $path = $this->controller_path();
                break;
            case 'resource':
                $path = $this->resource_path();
                break;
            case 'seeds':
            case 'seed':
                $path = $this->seed_path();
                break;
            case 'templates':
                $path = $this->templates_path();
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
    public function templates_path()
    {
        return config('craftsman.paths.templates');
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
        return getcwd() . DIRECTORY_SEPARATOR . config('craftsman.paths.controllers');
    }

    /**
     * @return Repository|mixed
     */
    public function factory_path()
    {
        return getcwd() . DIRECTORY_SEPARATOR . config('craftsman.paths.factories');
    }

    /**
     * @return Repository|mixed
     */
    public function migration_path()
    {
        return getcwd() . DIRECTORY_SEPARATOR . config('craftsman.paths.migrations');
    }

    public function model_path($model_path = null)
    {
        if (is_null($model_path)) {
            return getcwd() . DIRECTORY_SEPARATOR . config('craftsman.paths.models');
        } else {
            return getcwd() . DIRECTORY_SEPARATOR . config('craftsman.paths.models') . DIRECTORY_SEPARATOR . $model_path;
        }
    }

    public function request_path()
    {
        return getcwd() . DIRECTORY_SEPARATOR . config('craftsman.paths.requests');
    }

    public function resource_path()
    {
        return getcwd() . DIRECTORY_SEPARATOR . config('craftsman.paths.resources');
    }

    public function seed_path()
    {
        return getcwd() . DIRECTORY_SEPARATOR . config('craftsman.paths.seeds');
    }

    public function test_path()
    {
        return getcwd() . DIRECTORY_SEPARATOR . config('craftsman.paths.tests');
    }

    public function view_path()
    {
        return getcwd() . DIRECTORY_SEPARATOR . config('craftsman.paths.views');
    }

    public function getUserTemplate($userConfigFilename = "./config.php", $type = "")
    {
        if (file_exists($userConfigFilename)) {
            $config = include($userConfigFilename);
            if (isset($config["templates"])) {
                if (isset($config["templates"][$type])) {
                    return getcwd() . DIRECTORY_SEPARATOR . $config["templates"][$type];
                }
            }
        }

        return ""; // we didnt find the entry, return null string
    }

    public function getTemplatesDirectory()
    {
        return $this->getPharPath() . "templates";
    }

    /**
     * @return string
     */
    public function getPharPath()
    {
        $path = Phar::running(false);
        if (strlen($path) > 0) {
            $path = dirname($path) . DIRECTORY_SEPARATOR;
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

    public function getProjectTemplatesDiretory()
    {
        return $this->path_join(getcwd(), "templates");
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
        $shortFilename = $this->shortenFilename($dest);

        $template = $this->fs->get($src);

        $data["useExtends"] = $data["extends"];
        $data["useSection"] = $data["section"];

        $merged_data = $this->mustache->render($template, $data);

        if (file_exists($dest) && !$data["overwrite"]) {
            Messenger::error("{$shortFilename} already exists\n", "ERROR");
            return self::FILE_EXIST;
        }

        try {
            $this->createParentDirectory($dest);
            $this->fs->put($dest, $merged_data);
            $shortenFilename = $this->shortenFilename($dest);
            $dest = $this->tildify(($dest));

            $result = [
                "filename" => $dest,
                "status" => "success",
                "message" => "{$dest} created successfully",
            ];
            Messenger::success("{$shortenFilename} created successfully\n", "SUCCESS");
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

    public function copy_directory($src, $dst)
    {
        $dir = opendir($src);
        @mkdir($dst);
        while (($file = readdir($dir))) {
            if (($file != '.') && ($file != '..')) {
                if (is_dir($src . '/' . $file)) {
                    $this->copy_directory($src . '/' . $file, $dst . '/' . $file);
                } else {
                    copy($src . '/' . $file, $dst . '/' . $file);
                }
            }
        }
        closedir($dir);
    }

    public function tildify($filename)
    {
        return str_replace($this->getUserHome(), "~", $filename);
    }

    public function shortenFilename($filename)
    {
        $newFilename = str_replace(getcwd(), ".", $filename);
        if (!Str::startsWith($newFilename, ".")) {
            $newFilename = "./" . $newFilename;
        }
        return $newFilename;
    }

    public function getUserHome()
    {
        return getenv("HOME");
    }

    /**
     * @param  null  $model_path
     * @return Repository|mixed|string|string[]|null
     */
    public function model_request($model_path = null)
    {
        if (is_null($model_path)) {
            return getcwd() . DIRECTORY_SEPARATOR . config('craftsman.paths.requests');
        } else {
            return getcwd() . DIRECTORY_SEPARATOR . config('craftsman.paths.requests') . DIRECTORY_SEPARATOR . $model_path;
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
        $namespace = "";
        $overwrite = (isset($data["overwrite"])) ? $data["overwrite"] : false;
        $factory = (isset($data["factory"])) ? $data["factory"] : false;
        $all = (isset($data["all"])) ? $data["all"] : false;

        $path = $this->getOutputPath($type);

        if (isset($data["template"])) {
            $src = $this->getTemplateFilename($data["template"]);
        } else {

            $templateFilename = $this->path_join($this->getProjectTemplatesDiretory(), "{$type}.mustache");

            if (file_exists($templateFilename)) {
                $src = $templateFilename;
            } else {
                $src = $this->getUserTemplate("./config.php", $type);

                if (!file_exists($src)) {
                    $src = config("craftsman.templates.{$type}");
                }

                $src = $this->getPharPath() . $src;
                $src = str_replace("//", "/", $src);
            }
        }

        if (!file_exists($src)) {
            printf("\n");
            $src = str_replace($this->getUserHome(), "~", $src);
            Messenger::error("Unable to locate template '{$src}'", "ERROR");
            exit(1);
        }

        // if we have supplied a custom path (ie App/Models/Contact) it will be used instead of default path
        $dest = (Str::startsWith($filename, "App") || Str::startsWith($filename, "app"))
            ?  $this->path_join($filename . ".php")
            : $this->path_join($path, $filename . ".php");

        if (file_exists($dest) && (!$overwrite)) {
            $filename = $this->shortenFilename($dest);
            if ((Str::startsWith($filename, "./App") || Str::startsWith($filename, "./app"))) {
                $filename = str_replace("App", "app", $filename);
            }

            $dest = $this->tildify($dest);
            Messenger::error("{$filename} already exists\n", "ERROR");

            return [
                "status" => self::FILE_EXIST,
                "filename" => $filename,
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

        $fields = (isset($data["fields"])) ? strtolower($data["fields"]) : "";
        $rules = (isset($data["rules"])) ? strtolower($data["rules"]) : "";

        $fieldData = $this->buildFieldData($fields);

        $ruleData = "";
        if ($type === "request") {
            $ruleData = $this->buildRuleData($rules);
        }

        $model_path = "";
        $model = "";
        if (isset($data["model"])) {
            $model = class_basename($data["model"]);
            $model_path = $data["model"];
        } else {
            $model = class_basename($data["name"]);
            $namespace = str_replace("/", "\\", str_replace("/" . $model, "", $data["name"]));
        }

        $vars = [
            "name" => $filename,
            "model" => $model,
            "model_path" => $model_path,
            "all" => $all,
            "tablename" => $tablename,
            "fields" => $fieldData,
            "rules" => $ruleData,
            "collection" => isset($data["collection"]) ? $data["collection"] : false,
            "binding" => ""
        ];

        if (isset($data["binding"]) && $data["binding"]) {
            $vars["binding"] = "{$model} \$data";
        }

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
        $vars["num_rows"] = (isset($data["num_rows"])) ? (int) $data["num_rows"] : 1;

        // these variable is only used in test
        $vars["down"] = (isset($data["down"])) ? $data["down"] : false;
        $vars["extends"] = (isset($data["extends"])) ? $data["extends"] : false;
        $vars["setup"] = (isset($data["setup"])) ? $data["setup"] : false;
        $vars["teardown"] = (isset($data["teardown"])) ? $data["teardown"] : false;
        $vars["constructor"] = (isset($data["constructor"])) ? $data["constructor"] : false;
        $vars["foreign"] = (isset($data["foreign"])) ? $data["foreign"] : false;
        $vars["current"] = (isset($data["current"])) ? $data["current"] : false;
        $vars["create"] = (isset($data["create"])) ? $data["create"] : true;
        $vars["update"] = (isset($data["update"])) ? $data["update"] : false;

        if (isset($data["foreign"])) {
            $parts = explode(":", trim($data["foreign"]));
            if (sizeof($parts) >= 2) {
                $fk = $parts[0];
                $primaryInfo = explode(",", $parts[1]);
                list($pkid, $pktable) = $primaryInfo;
                $vars["foreign"] = true;
                $vars["fk"] = $fk;
                $vars["pkid"] = $pkid;
                $vars["pktable"] = $pktable;
            } else {
                $vars["foreign"] = false;
            }
        }
        $template = $this->fs->get($src);

        $mustache = new Mustache_Engine();

        // when creation migrations, the class method should be plural
        if ($type === "migration") {
            $vars["model"] = Str::plural($vars["model"]);
        }

        if ($model !== $model_path) {
            $vars["model_path"] = "use {$model_path};";
            $vars["model_path"] = str_replace("/", "\\", $vars["model_path"]);
        } else {
            $vars["model_path"] = "";
        }

        $template_data = $mustache->render($template, $vars);

        try {
            $this->createParentDirectory($dest);
            $this->fs->put($dest, $template_data);
            $shortenFilename = $this->shortenFilename($dest);
            $dest = $this->tildify($dest);
            $result = [
                "filename" => $dest,
                "fullPath" => getcwd() . DIRECTORY_SEPARATOR . $dest,
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
            Messenger::success("{$shortenFilename} created successfully\n", "SUCCESS");
        }

        $overwrite = $overwrite ? "--overwrite" : "";

        if ($factory && !$all) {
            Artisan::call("craft:factory {$model}Factory --model {$filename} {$overwrite}");
        }

        if ($all) {
            if ($data["collection"]) {
                Artisan::call("craft:resource {$model}sResource --collection {$overwrite}");
            } else {
                Artisan::call("craft:resource {$model}Resource {$overwrite}");
            }

            Artisan::call("craft:factory {$model}Factory --model {$filename} {$overwrite}");

            Artisan::call("craft:migration create_{$tablename}_table --model {$filename} --tablename {$tablename}");
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
            $filename = getcwd() . DIRECTORY_SEPARATOR . $filename;
            $filename = str_replace("//", "/", $filename);
            return file_exists($filename) ? $filename : $this->tildify($filename) . " Not Found";
        }

        if (strpos($type, "<root>") !== false) {
            $filename = str_replace("<root>", "", $type);
            $filename = getcwd() . DIRECTORY_SEPARATOR . $filename;
            $filename = str_replace("//", "/", $filename);
            return file_exists($filename) ? $filename : $this->tildify($filename) . " Not Found";
        }

        return getcwd() . DIRECTORY_SEPARATOR . config("craftsman.templates.{$type}");
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
                    $fieldSize = "," . $fieldSize;
                }

                $optional = "";
                if (sizeof($parts) >= 3) {
                    $parts = array_splice($parts, 2);
                    foreach ($parts as $part) {
                        $optional .= "->{$part}()";
                    }
                }

                $fieldData .= "            \$table->{$fieldType}('{$name}'{$fieldSize}){$optional};" . PHP_EOL;
            }
        }

        // strip last PHP_EOL so we have clean migration file
        return substr($fieldData, 0, -1);
    }

    public function buildRuleData($rules)
    {
        if (strlen($rules) === 0) {
            return "";
        }
        $ruleList = preg_split("/,? ?,/", $rules);

        $ruleData = "";
        foreach ($ruleList as $rule) {
            $parts = explode("?", trim($rule));
            if (count($parts) === 2) {
                $ruleName = $parts[0];
                $rules = $parts[1];
                $ruleData .= "\"{$ruleName}\" => \"{$rules}\",\n";
            }
        }

        return substr($ruleData, 0, -1);
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

        $dirname = getcwd() . DIRECTORY_SEPARATOR . $dirname;

        return $dirname . DIRECTORY_SEPARATOR . $filename;
    }
}
