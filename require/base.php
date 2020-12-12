<?php
namespace BACKLOG_API;

/**
 * ベースとなる処理を記載するファイル
 */

// composer内容を読み込み
require_once(__DIR__.'/../vendor/autoload.php');

// https://mindtrust.jp/techfirst/php-dotenv/
use Dotenv\Dotenv;

class Env {
    /** Dotenvクラスのインスタンス変数(シングルトン) */
    private static $dotenv;

    public static function get($key){
        if((self::$dotenv instanceof Dotenv) === false){
            self::$dotenv = Dotenv::createImmutable(dirname(__DIR__));
            self::$dotenv->load();
        }
        return array_key_exists($key, $_ENV) ? $_ENV[$key] : null;
    }
}