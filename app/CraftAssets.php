<?php

use App\CraftsmanFileSystem;

class CraftAssets
{
    protected $cfs;

    public function __construct()
    {
        $this->cfs = new CraftsmanFileSystem();
    }

    public function create_controller()
    {
    }
}
