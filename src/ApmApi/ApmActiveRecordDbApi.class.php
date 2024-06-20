<?php
namespace ApmApi;

class ApmActiveRecordDbApi extends ApmDbApi
{
    static protected $tableSnakeCase = null;
    static protected $apiDb = null;
    static protected $pk = 'id';
    static protected $numericalOrder = null;

    static public function post($method, $whereAr = ARRAY(), $debug = false) {

        $result = self::_post(static::$tableSnakeCase . '.' . $method, static::$apiDb, $whereAr, $debug);
        return $result;
    }

    static public function listPkAr($whereAr = ARRAY(), $debug = false)
    {
        $method = 'index';
        if (static::$numericalOrder !== null) {
            $whereAr['numericalOrderFieldName'] = static::$numericalOrder;
        }
        return static::post($method, $whereAr, $debug);
    }
    public static function listByIdList($whereAr = ARRAY(), $debug = false)
    {
        $method = 'indexByIdList';
        return static::post($method, $whereAr, $debug);
    }
    static public function cou($whereAr = ARRAY(), $debug = false)
    {
        $method = 'create_or_update';
        return static::post($method, $whereAr, $debug);
    }

    static public function create($whereAr = ARRAY(), $debug = false)
    {
        $method = 'create';
        return static::post($method, $whereAr, $debug);
    }

    static public function update($whereAr = ARRAY(), $debug = false)
    {
        $method = 'update';
        return static::post($method, $whereAr, $debug);
    }


    static public function row($whereAr = ARRAY(), $debug = false)
    {
        $method = 'view';
        return static::post($method, $whereAr, $debug);
    }
    static public function find($pk, $debug = false)
    {
        $method = 'view';
        return static::post($method, ARRAY(static::$pk => $pk), $debug);
    }

    static public function deleteList($whereAr = ARRAY(), $debug = false)
    {
        $method = 'remove';
        return static::post($method, $whereAr, $debug);
    }
    static public function deleteByPk($pk, $debug = false)
    {

        $method = 'remove';
        return static::post($method, ARRAY(static::$pk => $pk), $debug);
    }

    static public function numericalOrderUpdate($whereAr = ARRAY(), $debug = false)
    {
        $method = 'sort_by_numerical_order';
        return static::post($method, $whereAr, $debug);
    }
    public function __call($name, $arguments) {
        $whereAr = [];
        $debug = false;
        if (!empty($arguments[0])) {
            $whereAr = $arguments[0];
        }
        if (!empty($arguments[1])) {
            $debug = $arguments[1];
        }

        return static::post($name, $whereAr, $debug);
    }
    public static function __callStatic($name, $arguments) {
        $whereAr = [];
        $debug = false;
        if (!empty($arguments[0])) {
            $whereAr = $arguments[0];
        }
        if (!empty($arguments[1])) {
            $debug = $arguments[1];
        }

        return static::post($name, $whereAr, $debug);
    }
}