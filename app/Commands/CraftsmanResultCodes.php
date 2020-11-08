<?php

namespace App\Commands;

class CraftsmanResultCodes
{
    const SUCCESS = 0;
    const CREATED = 1;
    const FAIL = -1;
    const UNEXPECTED = -2;
    const INCOMPLETE = -3;
    const FILE_EXIST = -1;
    const FILE_NOT_EXIST = -43;
    const FILE_NOT_FOUND = -1;
    const DIRECTORY_EXISTS = -1;
}
