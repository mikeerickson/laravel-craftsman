<?php

namespace App\Commands;

class CraftsmanResultCodes
{
    const SUCCESS = 0;
    const FAIL = -1;
    const UNEXPECTED = -2;
    const INCOMPLETE = -3;
    const FILE_EXIST = -1;
    const FILE_NOT_EXIST = -43;
}
