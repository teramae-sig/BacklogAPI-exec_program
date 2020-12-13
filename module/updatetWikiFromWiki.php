<?php
namespace BACKLOG_API;

use BACKLOG_API\Env;

if (empty($argv[1]) || empty($argv[2])) {
    echo 'error:必要な引数がありません';
    exit;
}
$keyword = $argv[1];
$exclude = $argv[2];

// ファイル読み込み ----------------------------------------------- ---------------------------------
require_once(__DIR__.'/../require/base.php');
require_once(__DIR__.'/../require/BackLogRequester.class.php');

/*
    wiki情報を更新する
    指定したPJから取得したwiki情報をベースにする
*/
$source = new BackLogRequester(Env::get('UPDATE_SRC_BL_DOMAIN'), Env::get('UPDATE_SRC_BL_ACCESSKEY'));
$register = new BackLogRequester(Env::get('UPDATE_REGISTER_BL_DOMAIN'), Env::get('UPDATE_REGISTER_BL_ACCESSKEY'));

// 取得元から情報取得 ----------------------------------------------------------------------
/*
    Wikiページ一覧の取得 リファレンス
    https://developer.nulab.com/ja/docs/backlog/api/2/get-wiki-page-list/#
*/
$params = [
    // 取得元のプロジェクトID
    'projectIdOrKey' => Env::get('UPDATE_SRC_PJ_ID'),
    // 検索キーワード
    'keyword' => $keyword
];
$response = $source->get('wikis', $params);

// 更新用の配列に詰め変える
$pages = [];
foreach($response as $val){
    // 対象の単語が含まれないページ名だった場合、処理しない
    if(!strpos($val['name'], $exclude)){
        continue;
    }

    $pages[$val['name']] = $val['content'];
}


// 更新対象ページを取得する ----------------------------------------------------------------------
$params = [
    // 取得元のプロジェクトID
    'projectIdOrKey' => Env::get('UPDATE_REGISTER_PJ_ID'),
    // 検索キーワード
    'keyword' => $keyword
];
$response = $register->get('wikis', $params);

// 更新用の配列に詰め変える
$tagets = [];
foreach($response as $val){
    // 対象の単語が含まれないページ名だった場合、処理しない
    if(!strpos($val['name'], $exclude)){
        continue;
    }

    // ページ名
    $tagets[$val['id']]['name'] = $val['name'];
    // ページ内容 変更する
    $tagets[$val['id']]['content'] = $pages[$val['name']];
    // メール送信 しない
    $tagets[$val['id']]['mailNotify'] = 'false';
}

// 更新する ----------------------------------------------------------------------

/*
    wikiページの更新 リファレンス
    https://developer.nulab.com/ja/docs/backlog/api/2/update-wiki-page/#
*/
foreach($tagets as $wikiId => $pageInfo) {
    // API名
    $apiName = 'wikis/'.$wikiId;

    // 更新情報を送信する
    $response = $source->patch($apiName, $pageInfo);

    // TODO: 成功したかログ出力する
    // var_dump($response);
}

// 成功した旨を出力する
echo 'success'.PHP_EOL;
