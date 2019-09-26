<?php

namespace App\Commands;

use App\CraftsmanFileSystem;
use Illuminate\Filesystem\Filesystem;
use LaravelZero\Framework\Commands\Command;
use Codedungeon\PHPMessenger\Facades\Messenger;

/**
 * Class CraftClass
 * @package App\Commands
 */
class CraftPublish extends Command
{
    protected $signature = 'publish
                                {--o|overwrite : Overwite existing templates}
                                {--c|no-config : Skip Publishing Configuration File}
                                {--t|no-templates : Skip Publishing Templates}
                            ';

    protected $description = "Copy Craftsman Templates to Project Directory";

    protected $help = 'Copy Craftsman Templates to Project Directory
                     --no-config, -c    Skip Publishing Configuration File
                     --no-templates, -t    Skip Publishing Templates
                     --overwrite, -o    Overwite existing templates
            ';

    public function __construct()
    {
        parent::__construct();

        $this->fs = new CraftsmanFileSystem();

        $this->setHelp($this->help);
    }

    public function handle()
    {
        $overwrite = $this->option('overwrite');
        $noConfig = $this->option('no-config');
        $noTemplates = $this->option('no-templates');

        echo "\n";

        // copy templates
        if (!$noTemplates) {
            $src = $this->fs->getTemplatesDirectory();
            $dest = $this->fs->path_join(getcwd(), "templates");
            if (file_exists($dest) && !$overwrite) {
                $dest = $this->fs->tildify($dest);
                Messenger::error("{$dest} already exists", "ERROR");
            } else {
                (new Filesystem())->copyDirectory($src, $dest);
                Messenger::success("Templates Published Successfully", "SUCCESS");
            }
        }

        // copy config/craftsman
        if (!$noConfig) {
            $src = $this->fs->getAppConfigFilename();
            $dest = $this->fs->path_join(getcwd(), "config", "craftsman.php");
            if (file_exists($dest) && !$overwrite) {
                Messenger::error("{$dest} already exists", "ERROR");
            } else {
                if ($src !== $dest) {
                    copy($src, $dest);
                }
            }
        }
    }
}
