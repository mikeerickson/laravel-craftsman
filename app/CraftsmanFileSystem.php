<?php

namespace App;

use Phar;
use Exception;
use Mustache_Engine;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use App\Traits\CommandDebugTrait;
use Illuminate\Config\Repository;
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
    use CommandDebugTrait;

    const SUCCESS = 0;
    const FILE_EXIST = -1;
    const FILE_NOT_EXIST = -43;

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
    public function rmdir(string $dirname)
    {
        if (is_dir($dirname)) {
            system("rm -rf " . escapeshellarg($dirname));
        }
    }

    public function getConfigValue($key)
    {
        $localConfigFilename = $this->getLocalConfigFilename();
        $appConfigFilename = $this->getAppConfigFilename();

        if (file_exists($localConfigFilename)) {
            $configData = array_replace_recursive(
                require($appConfigFilename),
                require($localConfigFilename)
            );
            return Arr::get($configData, $key);
        }
        return config($key);
    }

    public function getLocalConfigFilename()
    {
        if ($this->isPhar()) {
            $localConfigFilename = $this->path_join(getcwd(), "config", "craftsman.php");
        } else {
            // sandbox only used for development testing
            $localConfigFilename = $this->path_join(getcwd(), "tests", "config", "craftsman.php");
        }

        if (!file_exists($localConfigFilename)) {
            return null;
        }
        return $localConfigFilename;
    }

    public function isPhar()
    {
        return strlen(Phar::running(false)) > 0;
    }

    /**
     * @return string|string[]|null
     */
    public function path_join()
    {
        $paths = [];

        foreach (func_get_args() as $arg) {
            if ($arg !== '') {
                $paths[] = $arg;
            }
        }

        return preg_replace('#/+#', '/', join('/', $paths));
    }

    public function getAppConfigFilename()
    {
        if (strlen($this->getPharPath()) > 0) {
            return $this->path_join($this->getPharPath(), "config", "craftsman.php");
        }
        return $this->path_join(getcwd(), "config", "craftsman.php");
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

    public function mergeConfigFrom($path, $key)
    {
        $config = $this->app['config']->get($key, []);

        $this->app['config']->set($key, array_replace_recursive(require $path, $config));
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

    /*
     * Craftsman Path Configuration
     * 1. Define path location `getOutputPath`
     * 2. Create associated pathname helper (ie rule_path)
     * 3. Update `config/craftsman.path` section
     * 4. Update `config/craftsman.template` section
     */

    public function getOutputPath(string $type): string
    {
        switch ($type) {
            case 'class':
                $path = $this->class_path();
                break;
            case 'binding-controller':
            case 'api-controller':
            case 'empty-controller':
            case 'invokable-controller':
            case 'controller':
                $path = $this->controller_path();
                break;
            case 'command':
                $path = $this->command_path();
                break;
            case 'event':
                $path = $this->event_path();
                break;
            case 'listener':
                $path = $this->listener_path();
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
            case 'provider':
                $path = $this->provider_path();
                break;
            case 'request':
                $path = $this->request_path();
                break;
            case 'rule':
                $path = $this->rule_path();
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

    public function class_path(): string
    {
        return config('craftsman.paths.class');
    }

    public function controller_path(): string
    {
        return getcwd() . DIRECTORY_SEPARATOR . config('craftsman.paths.controllers');
    }

    public function command_path(): string
    {
        return getcwd() . DIRECTORY_SEPARATOR . config('craftsman.paths.commands');
    }

    public function event_path(): string
    {
        return getcwd() . DIRECTORY_SEPARATOR . config('craftsman.paths.events');
    }

    public function listener_path(): string
    {
        return getcwd() . DIRECTORY_SEPARATOR . config('craftsman.paths.listeners');
    }

    public function factory_path(): string
    {
        return getcwd() . DIRECTORY_SEPARATOR . config('craftsman.paths.factories');
    }

    public function migration_path(): string
    {
        return getcwd() . DIRECTORY_SEPARATOR . config('craftsman.paths.migrations');
    }

    public function model_path(string $model_path = null): string
    {
        if (is_null($model_path)) {
            return getcwd() . DIRECTORY_SEPARATOR . config('craftsman.paths.models');
        } else {
            return getcwd() . DIRECTORY_SEPARATOR . config('craftsman.paths.models') . DIRECTORY_SEPARATOR . $model_path;
        }
    }

    public function provider_path(string $provider_path = null): string
    {
        if (is_null($provider_path)) {
            return getcwd() . DIRECTORY_SEPARATOR . config('craftsman.paths.providers');
        } else {
            return getcwd() . DIRECTORY_SEPARATOR . config('craftsman.paths.providers') . DIRECTORY_SEPARATOR . $provider_path;
        }
    }

    public function request_path(): string
    {
        return getcwd() . DIRECTORY_SEPARATOR . config('craftsman.paths.requests');
    }

    public function rule_path(): string
    {
        return getcwd() . DIRECTORY_SEPARATOR . config('craftsman.paths.rules');
    }

    public function resource_path(): string
    {
        return getcwd() . DIRECTORY_SEPARATOR . config('craftsman.paths.resources');
    }

    public function seed_path(): string
    {
        return getcwd() . DIRECTORY_SEPARATOR . config('craftsman.paths.seeds');
    }

    public function templates_path(): string
    {
        return config('craftsman.paths.templates');
    }

    public function test_path(): string
    {
        return getcwd() . DIRECTORY_SEPARATOR . config('craftsman.paths.tests');
    }

    public function view_path(): string
    {
        return getcwd() . DIRECTORY_SEPARATOR . config('craftsman.paths.views');
    }

    public function getUserTemplate(string $userConfigFilename = "./config.php", string $type = ""): string
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

    /**
     * @param $src
     * @param $dest
     * @param $data
     * @return int
     * @throws FileNotFoundException
     */
    private function createMergeFile(string $src, string $dest, array $data): string
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

    public function shortenFilename(string $filename): string
    {
        $newFilename = str_replace(getcwd(), ".", $filename);
        if (!Str::startsWith($newFilename, ".")) {
            $newFilename = "./" . $newFilename;
        }
        return $newFilename;
    }

    /**
     * @param $filename
     */
    public function createParentDirectory(string $filename): void
    {
        if (!is_dir(dirname($filename))) {
            mkdir(dirname($filename), 0777, true);
        }
    }

    public function tildify(string $filename): string
    {
        return str_replace($this->getUserHome(), "~", $filename);
    }

    public function getUserHome(): string
    {
        return getenv("HOME");
    }

    /**
     * @param $path
     */
    public function delete(string $path): void
    {
        unlink($path);
    }

    public function getTemplatesDirectory(): string
    {
        return $this->getPharPath() . "templates";
    }

    public function copy_directory(string $src, string $dst): void
    {
        $dir = opendir($src);
        @mkdir($dst);
        while (($file = readdir($dir))) {
            if (($file !== '.') && ($file !== '..')) {
                if (is_dir($src . '/' . $file)) {
                    $this->copy_directory($src . '/' . $file, $dst . '/' . $file);
                } else {
                    copy($src . '/' . $file, $dst . '/' . $file);
                }
            }
        }
        closedir($dir);
    }

    /**
     * @param  null  $model_path
     * @return Repository|mixed|string|null
     */
    public function model_request(string $model_path = null): string
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
    public function createFile(string $type = null, string $filename = null, array $data = []): array
    {
        $namespace = "";

        $debug = (isset($data["debug"])) ? $data["debug"] : in_array("--debug", $_SERVER["argv"]);

        $overwrite = (isset($data["overwrite"])) ? $data["overwrite"] : false;
        $factory = (isset($data["factory"])) ? $data["factory"] : false;
        $listener = (isset($data["listener"])) ? $data["listener"] : false;
        $all = (isset($data["all"])) ? $data["all"] : false;
        $controller = (isset($data["controller"])) ? $data["controller"] : false;
        $seed = (isset($data["seed"])) ? $data["seed"] : false;

        // NOTE: Modify getOutputPath whenever adding new craft commands
        $path = $this->getOutputPath($type);

        if (isset($data["template"])) {
            $src = $this->getTemplateFilename($data["template"]);
        } else {

            if (!empty($data["pest"])) {
                $templateFilename = $this->path_join($this->getProjectTemplatesDirectory(), "pest-{$type}.mustache");
            } else {
                $templateFilename = $this->path_join($this->getProjectTemplatesDirectory(), "{$type}.mustache");
            }

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

        if (!file_exists($src) || (is_dir($src))) {
            printf("\n");
            $template = $this->path_join($this->getProjectTemplatesDirectory(), $data["template"]);
            $src = str_replace($this->getUserHome(), "~", $template);
            Messenger::error("Unable to locate template '{$src}'", "ERROR");
            exit(1);
        }

        // if we have supplied a custom path (ie App/Models/Contact) it will be used instead of default path
        $dest = (Str::startsWith($filename, "App") || Str::startsWith($filename, "app"))
            ? $this->path_join($filename . ".php")
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
            "debug" => $debug,
            "name" => $filename,
            "path" => $path,
            "model" => $model,
            "model_path" => $model_path,
            "all" => $all,
            "tablename" => $tablename,
            "fields" => $fieldData,
            "rules" => $ruleData,
            "controller" => isset($data["controller"]) ? $data["controller"] : false,
            "binding" => "",
            "broadcast" => isset($data["no-broadcast"]) ? !$data["no-broadcast"] : true,
            "listener" => isset($data["listener"]) ? !$data["listener"] : true,
        ];

        if (isset($data["binding"]) && $data["binding"]) {
            $vars["binding"] = "{$model} \$data";
        }

        $namespace = $this->getNamespace($type, $vars["name"]);

        if (isset($data["namespace"])) {
            $vars["namespace"] = $data["namespace"];
        } else {
            if (strlen($namespace) > 0) {
                $vars["namespace"] = $namespace;
            }
        }

        $vars["namespace"] = str_replace("app", "App", $vars["namespace"]);

        // this variable is only used in seed
        $vars["num_rows"] = (isset($data["num_rows"])) ? (int) $data["num_rows"] : 1;

        // these variable is only used in test
        $vars["down"] = (isset($data["down"])) ? $data["down"] : false;
        $vars["extends"] = (isset($data["extends"])) ? $data["extends"] : false;
        $vars["setup"] = (isset($data["setup"])) ? $data["setup"] : false;
        $vars["teardown"] = (isset($data["teardown"])) ? $data["teardown"] : false;
        $vars["constructor"] = (isset($data["constructor"])) ? $data["constructor"] : false;
        $vars["foreign"] = (isset($data["foreign"])) ? $data["foreign"] : false;
        $vars["current"] = (isset($data["current"])) ? $data["current"] : config("craftsman.miscellaneous.useCurrentDefault");
        $vars["create"] = (isset($data["create"])) ? $data["create"] : true;
        $vars["update"] = (isset($data["update"])) ? $data["update"] : false;
        $vars["signature"] = (isset($data["signature"])) ? $data["signature"] : false;
        $vars["description"] = (isset($data["description"])) ? $data["description"] : false;
        $vars["controller"] = (isset($data["controller"])) ? $data["controller"] : false;

        if (isset($data["foreign"])) {
            $parts = explode(":", trim($data["foreign"]));
            $vars["foreign"] = true;
            $fk = $parts[0];
            $vars["fk"] = $fk;
            if (sizeof($parts) >= 2) {
                $primaryInfo = explode(",", $parts[1]);
                [$pkid, $pktable] = $primaryInfo;
                $vars["pkid"] = $pkid;
                $vars["pktable"] = $pktable;
            } else {
                $primaryInfo = explode("_", $parts[0]);
                [$pktable, $pkid] = $primaryInfo;
                $vars["pkid"] = $pkid;
                $vars["pktable"] = Str::plural($pktable);
            }
        }
        $template = $this->fs->get($src);

        $mustache = new Mustache_Engine();

        // when creation migrations, the class method should be plural
        // if we have supplied tablename, do not plurarlize

        // if ($type === "migration") {
        //     $vars["model"] = Str::plural($vars["model"]);
        // }

        if ($model !== $model_path) {
            $vars["model_path"] = "use {$model_path};";
            $vars["model_path"] = str_replace("/", "\\", $vars["model_path"]);
        } else {
            $vars["model_path"] = "";

            // NOTE: This will be true when creating seed from `craft:mode --seed`
            if ($type === "seed") {
                $vars["model_path"] = "use App/{$model_path};";
                $vars["model_path"] = str_replace("/", "\\", $vars["model_path"]);
            }
        }

        $vars["name"] = $this->getClassName($vars["name"]);

        $mergeVars = array_merge($data, $vars);

        if (!isset($mergeVars["className"])) {
            $mergeVars["className"] = $mergeVars["model"];
        }

        $template_data = $mustache->render($template, $mergeVars);

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

        if ($controller && !$all) {
            Artisan::call("craft:controller {$model}Controller {$overwrite}");
        }

        if ($seed && !$all) {
            Artisan::call("craft:seed {$model}sTableSeeder --model {$data["name"]} {$overwrite}");
        }

        if ($all) {
            Artisan::call("craft:controller {$model}Controller {$overwrite}");

            Artisan::call("craft:factory {$model}Factory --model {$filename} {$overwrite}");

            Artisan::call("craft:migration create_{$tablename}_table --model {$filename} --table {$tablename}");
        }

        if ($listener) {
            Artisan::call("craft:listener {$model}Listener --event {$model}");
        }

        return $result;
    }

    /**
     * @param $type
     * @return Repository|mixed
     */
    public function getTemplateFilename(string $type): string
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

    public function getProjectTemplatesDirectory(): string
    {
        return $this->path_join(getcwd(), "templates");
    }

    /**
     * @param  string  $fields
     * @return bool|string
     */
    public function buildFieldData(string $fields = ""): string
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

    public function buildRuleData(string $rules): string
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

    public function getNamespace(string $type, string $name): string
    {
        $parts = explode("/", $name);
        if (sizeof($parts) >= 2) {
            array_pop($parts);
            return implode("\\", $parts);
        }

        switch ($type) {
            case "class":
                $namespace = "App";
                break;
            case "event":
                $namespace = "App\Events";
                break;
            case "listener":
                $namespace = "App\Listeners";
                break;
            case "provider":
                $namespace = "App\Providers";
                break;
            case "rule":
                $namespace = "App\Rules";
                break;
            case "service":
                $namespace = "App\Services";
                break;
            default:
                $namespace = $name;
                break;
        }
        return $namespace;
    }

    public function getClassName(string $name): string
    {
        $parts = explode("/", $name);
        return end($parts);
    }

    /**
     * @return string|null
     */
    public function pathJoin(): string
    {
        $paths = [];

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
    public function getLastMigrationFilename(string $dirname = "", string $partial = ""): ?string
    {
        if (!file_exists($dirname)) {
            return null;
        }

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
