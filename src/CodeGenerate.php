<?php

namespace SoftHouse\CodeGenerate;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CodeGenerate
{
    public const length = 4;
    public const prefix = '';
    public const field = 'code';

    /**
     * @throws Exception
     */
    public static function generate($class): string
    {
        $length = config('code-generate.length') ?? self::length;
        $prefix = config('code-generate.prefix') ?? self::prefix;

        $table = '';

        if ($class instanceof Model) {
            $table = $class->getTable();
        }

        if (gettype($class) === "string") {
            $class = new $class();
            $table = $class->getTable();
        }

        $fieldInfo = (new self)->getFieldType($table);
        $tableFieldType = $fieldInfo['type'];
        $tableFieldLength = $fieldInfo['length'];

        if (in_array($tableFieldType, ['int', 'integer', 'bigint', 'numeric']) && !is_numeric($prefix)) {
            throw new Exception(self::field . " field type is $tableFieldType but prefix is string");
        }

        if ($length > $tableFieldLength) {
            throw new Exception('Generated ID length is bigger then table field length');
        }

        $prefixLength = strlen($prefix);
        $idLength = $length - $prefixLength;
        $whereString = '';

        $totalQuery = sprintf("SELECT count(%s) total FROM %s", self::field, $table);

        $total = DB::select(trim($totalQuery));

        if ($total[0]->total) {
            $maxQuery = sprintf("SELECT MAX(%s) AS maxid FROM %s", self::field, $table);
            $queryResult = DB::select($maxQuery);

            $maxFullId = $queryResult[0]->maxid;

            $maxId = substr($maxFullId, $prefixLength, $idLength);

            return $prefix . str_pad((int)$maxId + 1, $idLength, '0', STR_PAD_LEFT);

        } else {
            return $prefix . str_pad(1, $idLength, '0', STR_PAD_LEFT);
        }
    }

    /**
     * @throws Exception
     */
    private function getFieldType($table): array
    {
        $connection = config('database.default');
        $driver = DB::connection($connection)->getDriverName();
        $database = DB::connection($connection)->getDatabaseName();

        if ($driver == 'mysql') {
            $sql = 'SELECT column_name AS "column_name",data_type AS "data_type",column_type AS "column_type" FROM information_schema.columns ';
            $sql .= 'WHERE table_schema=:database AND table_name=:table';
        } else {
            $sql = 'SELECT column_name AS "column_name",data_type AS "data_type" FROM information_schema.columns ';
            $sql .= 'WHERE table_catalog=:database AND table_name=:table';
        }

        $rows = DB::select($sql, ['database' => $database, 'table' => $table]);

        $fieldType = null;
        $fieldLength = 20;

        foreach ($rows as $col) {
            if (self::field == $col->column_name) {

                $fieldType = $col->data_type;

                if ($driver == 'mysql') {
                    preg_match("/(?<=\().+?(?=\))/", $col->column_type, $tblFieldLength);
                    if (count($tblFieldLength)) {
                        $fieldLength = $tblFieldLength[0];
                    }
                }
                break;
            }
        }

        if ($fieldType == null) {
            throw new Exception(self::field . " not found in $table table");
        }
        return ['type' => $fieldType, 'length' => $fieldLength];
    }
}
