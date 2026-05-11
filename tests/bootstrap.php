<?php

/**
 * Bootstrap file for PHPUnit.
 * Handles mocking core classes and autoloading.
 */

namespace {
    require_once __DIR__ . '/../vendor/autoload.php';
}

namespace Core {
    class Database
    {
        private static $instance = null;
        public static $nextResult = [];
        public static $resultsQueue = []; // For handling multiple queries in one method

        public static function getInstance()
        {
            if (self::$instance === null) {
                self::$instance = new self();
            }
            return self::$instance;
        }

        public function getConnection()
        {
            return new class {
                public function prepare($sql)
                {
                    return new class($sql) {
                        private $sql;
                        public function __construct($sql)
                        {
                            $this->sql = $sql;
                        }
                        public function execute($params = [])
                        {
                            return true;
                        }

                        private function getResult()
                        {
                            if (!empty(Database::$resultsQueue)) {
                                return array_shift(Database::$resultsQueue);
                            }
                            return Database::$nextResult;
                        }

                        public function fetch($mode = 2)
                        {
                            $res = $this->getResult();
                            return $res[0] ?? [];
                        }

                        public function fetchColumn()
                        {
                            $res = $this->getResult();
                            if (is_array($res)) {
                                $row = $res[0] ?? null;
                                return is_array($row) ? reset($row) : $row;
                            }
                            return $res;
                        }

                        public function fetchAll($mode = 2)
                        {
                            return $this->getResult();
                        }
                    };
                }
                public function lastInsertId()
                {
                    return 1;
                }
            };
        }
    }
}

namespace App\Services {
    class Session
    {
        public static $store = [];
        public const FLASH_SUCCESS = 'success';
        public const FLASH_ERROR = 'error';
        public static function get($key)
        {
            return self::$store[$key] ?? null;
        }
        public static function set($key, $val)
        {
            self::$store[$key] = $val;
        }
        public static function getCookieName()
        {
            return 'test_cookie';
        }
        public static function setFlash($key, $msg)
        {
            self::$store['flash'][$key] = $msg;
        }
    }
}
