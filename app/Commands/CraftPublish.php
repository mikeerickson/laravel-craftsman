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
                            ';

    protected $description = "Copy Craftsman Templates to Project Directory";

    protected $help = 'Copy Craftsman Templates to Project Directory
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

        $src = $this->fs->getTemplatesDirectory();
        $dest = $this->fs->path_join(getcwd(), "templates");

        echo "\n";

        if (file_exists($dest) && !$overwrite) {
            $dest = $this->fs->tildify($dest);
            Messenger::error("{$dest} already exists", "ERROR");
        } else {
            (new Filesystem)->copyDirectory($src, $dest);
            Messenger::success("Templates Published Successfully", "SUCCESS");
        }
    }
}
