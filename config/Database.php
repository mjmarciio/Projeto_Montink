<?php
class Database {
    private static $instance;
    public static function getConnection() {
        if (!self::$instance) {
            self::$instance = new PDO('mysql:host=localhost;dbname=banco_montink;charset=utf8mb4', 'root', '');
            self::$instance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
        }
        return self::$instance;
    }
}