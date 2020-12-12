<?php
use BACKLOG_API\Env;

if (empty($argv[1])) {
    echo 'error:必要な引数がありません';
    exit;
}
$keyword = $argv[1];

// ファイル読み込み --------------------------------------------------------------------------------
require_once('require/base.php');
require_once('require/BackLogRequester.class.php');

/*
    指定したプロジェクトからwiki情報一覧を取得して出力する
*/
$source = new BackLogRequester(Env::get('SELECT_BL_DOMAIN'), Env::get('SELECT_BL_ACCESSKEY'));

// 取得元 --------------------------------------------------------------------------------

// パラメーター設定
$params = [
    // 取得元のプロジェクトID
    'projectIdOrKey' => Env::get('SELECT_PJ_ID'),
    // 検索キーワード
    'keyword' => $keyword
];
$response = $source->get('wikis', $params);

foreach($response as $val) {
    var_dump($val['name']);
}

// 成功した旨を出力する
echo 'success'.PHP_EOL;
