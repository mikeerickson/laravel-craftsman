<?php

namespace App;

use Illuminate\Support\Facades\DB;

function printl($data)
{
    return $data . PHP_EOL;
}

class DBTools
{
    const INVALID_TABLE = "INVALID_TABLE";
    const INVALID_COLUMN = "INVALID_COLUMN";

    protected $tablename;

    public function __construct($tablename = null)
    {
        $this->tablename = $this->verifyTablename($tablename)
            ? $tablename
            : null;
    }

    public function setTable($tablename = "")
    {
        if ($this->verifyTablename($tablename)) {
            $this->tablename = $tablename;
        }
    }
    public function getColumnType($colName = null)
    {
        if (!$this->isValidTablename()) {
            return self::INVALID_TABLE;
        }

        if (!$this->isValidColumn($colName)) {
            return self::INVALID_COLUMN;
        }

        var_dump($this->tablename . "." . $colName);

        $result = null;
        $columnInfo = DB::select(
            DB::raw('SHOW COLUMNS FROM ' . $this->tablename)
        );
        foreach ($columnInfo as $column) {
            if ($colName === $column->Field) {
                $result = $column->Type;
                break;
            }
        }
        return $result;
    }

    public function getColumnInfo($colName = null)
    {
        if (!$this->isValidTablename()) {
            return self::INVALID_TABLE;
        }

        $columnInfo = DB::select(
            DB::raw('SHOW COLUMNS FROM ' . $this->tablename)
        );

        $resultObject = array_filter($columnInfo, function ($e) use (
            &$colName
        ) {
            return $e->Field == $colName;
        });

        return $resultObject;
    }

    public function getTableList()
    {
        $sql = "SELECT table_name FROM information_schema.tables";
        return DB::select(DB::raw($sql));
    }

    public function getColumnList()
    {
        if (!$this->isValidTablename($this->tablename)) {
            return self::INVALID_TABLE;
        }

        return DB::getSchemaBuilder()->getColumnListing($this->tablename);
    }

    public function isValidTablename($tablename = null)
    {
        return $this->verifyTablename($this->tablename);
    }

    public function isValidColumn($colName = "")
    {
        $colInfo = $this->getColumnInfo($colName);
        return sizeof($colInfo) > 0;
    }

    public function isPrimaryKey($colName = null)
    {
        if (!$this->isValidTablename($this->tablename)) {
            return self::INVALID_TABLE;
        }

        if (!$this->isValidColumn($colName)) {
            dd("here");
            return self::INVALID_COLUMN;
        }

        $columnInfo = $this->getColumnInfo($colName);

        return end($columnInfo)->Key === "PRI";
    }

    public function isAutoIncrement($colName = null)
    {
        if (!$this->isValidTablename($this->tablename)) {
            return self::INVALID_TABLE;
        }

        $columnInfo = $this->getColumnInfo($colName);

        return end($columnInfo)->Extra === "auto_increment";
    }

    public function verifyTablename($tablename = null)
    {
        $tableList = $this->getTableList();
        $resultObject = array_filter($tableList, function ($e) use (&$tablename) {
            return $e->table_name == $tablename;
        });
        return sizeof($resultObject) >= 1;
    }
}

/**
 * Test

 * $type = DB::getSchemaBuilder()->getColumnType("users", "name");
 * $type = DB::getSchemaBuilder()->getColumnListing("users");

 */


$tablename = "userss";
$db = new DBTools($tablename);
$type = $db->getColumnType("id");
printl("${tablename}.id type ${type}");

var_dump("${tablename} column list: " . $db->getColumnList());

printl("================================================================");

$tablename = "users";

$db = new DBTools($tablename);

var_dump($db->getColumnList());

$type = $db->getColumnType("idd");

printl("${tablename}.idd type ${type}");

$type = $db->getColumnType("id");
printl("${tablename}.id type ${type}");

$type = $db->getColumnType("name");
printl("${tablename}.name type ${type}");

$type = $db->getColumnType("updated_at");
printl("${tablename}.updated_at type ${type}");

$isPrimary = $db->isPrimaryKey("id");
printl($isPrimary ? "${tablename}.id primary" : "${tablename}.id not primary");

$isPrimary = $db->isPrimaryKey("name");
printl(
    $isPrimary ? "${tablename}.name primary" : "${tablename}.name not primary"
);

$isPrimary = $db->isAutoIncrement("id");
printl(
    $isPrimary
        ? "${tablename}.id auto_increment"
        : "${tablename}.id not auto_increment"
);

$isPrimary = $db->isAutoIncrement("name");
printl(
    $isPrimary
        ? "${tablename}.name auto_increment"
        : "${tablename}.name not auto_increment"
);

printl("================================================================");
$info = $db->getColumnInfo("id");
var_dump($info);

printl("================================================================");
$tablename = "userss";
$db = new DBTools($tablename);
$type = $db->getColumnType("id");

printl("${tablename}.id type ${type}");
