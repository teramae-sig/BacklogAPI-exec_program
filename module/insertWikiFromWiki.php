<?php
use BACKLOG_API\Env;

if (empty($argv[1]) || empty($argv[2])) {
    echo 'error:必要な引数がありません';
    exit;
}
$keyword = $argv[1];
$exclude = $argv[2];

// ファイル読み込み --------------------------------------------------------------------------------
require_once('require/base.php');
require_once('require/BackLogRequester.class.php');

/*
    wiki情報を登録する
    指定したPJから取得したwiki情報をベースにする
*/
$source = new BackLogRequester(Env::get('SRC_BL_DOMAIN'), Env::get('SRC_BL_ACCESSKEY'));
$register = new BackLogRequester(Env::get('REGISTER_BL_DOMAIN'), Env::get('REGISTER_BL_ACCESSKEY'));

// 取得元から情報取得 ----------------------------------------------------------------------
$params = [
    // 取得元のプロジェクトID
    'projectIdOrKey' => Env::get('SRC_PJ_ID'),
    // 検索キーワード
    'keyword' => $keyword
];
$response = $source->get('wikis', $params);

// 更新用の配列に詰め変える
$pages = [];
$i = 0;
foreach($response as $val){
    // 除外対象の単語が含まれるページ名だった場合、処理しない
    if(!strpos($val['name'], $exclude)){
        continue;
    }

    $pages[$i]['projectId'] = Env::get('REGISTER_PJ_ID');
    $pages[$i]['name'] = $val['name'];
    $pages[$i]['content'] = $val['content'];
    // 取得結果のチェック用
    // $check[$val['id']] = $val['name'];
    $i++;
}


// 登録先に登録 ----------------------------------------------------------------------
foreach($pages as $pageInfo) {
    echo '登録:'.$pageInfo['name'].PHP_EOL;
    $response = $register->post('wikis', $pageInfo);

    // var_dump($response);exit;
}

// 成功した旨を出力する
echo 'success'.PHP_EOL;
