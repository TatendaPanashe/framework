<?php

namespace Kodomo\Models;

use Kodomo\Core\Database;

class Model
{
    protected static $table;
    protected $attributes = [];

    public static function getTable()
    {
        if (!static::$table) {
            // Automatically determine table name from class name
            $className = (new \ReflectionClass(static::class))->getShortName();
            static::$table = strtolower($className) . 's';
        }
        return static::$table;
    }

    public static function all()
    {
        $db = Database::getInstance();
        $table = static::getTable();
        return $db->fetchAll("SELECT * FROM {$table}");
    }

    public static function find($id)
    {
        $db = Database::getInstance();
        $table = static::getTable();
        return $db->fetch("SELECT * FROM {$table} WHERE id = ?", [$id]);
    }

    public static function where($column, $value)
    {
        $db = Database::getInstance();
        $table = static::getTable();
        return $db->fetchAll("SELECT * FROM {$table} WHERE {$column} = ?", [$value]);
    }

    public function save()
    {
        $db = Database::getInstance();
        $table = static::getTable();
        
        if (empty($this->attributes)) {
            return false;
        }

        $columns = implode(', ', array_keys($this->attributes));
        $placeholders = implode(', ', array_fill(0, count($this->attributes), '?'));
        
        $sql = "INSERT INTO {$table} ({$columns}) VALUES ({$placeholders})";
        $db->query($sql, array_values($this->attributes));
        
        return $db->lastInsertId();
    }

    public function __set($name, $value)
    {
        $this->attributes[$name] = $value;
    }

    public function __get($name)
    {
        return $this->attributes[$name] ?? null;
    }
}