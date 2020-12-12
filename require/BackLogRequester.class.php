<?php
namespace BACKLOG_API;

/**
 * BackLog APIへのリクエストクラス
 */
class BackLogRequester {
    /** @var string $base ベースとなるURL */
    public $base = '';
    /** @var string $akey APIのKEY */
    public $akey = '';

    /**
     * コンストラクタ
     * @param array $info バックログの情報
     */
    function __construct(string $domain, string $akey) {
        // ベースとなるURLを作成
        $this->base = 'https://' . $domain . '/api/v2/';
        // API_KEYを設定
        $this->akey = $akey;
    }

    /**
     * バックログにGETリクエストを送る
     * @param string $name API名
     * @param array  $params URLパラメーター情報
     * @return array レスポンス情報(連想配列)
     */
    function get(string $name, array $params) {
        // URLを作成する
        $url = $this->base.$name.'?apiKey='.$this->akey;

        // パラメータがあればそれも付与
        if(!empty($params)) {
            $url .= '&' . http_build_query($params, '','&');
        }

        // APIにリクエストを送る
        echo '送信先URL:'.$url.PHP_EOL;
        $respnse = $this->getReq($url);

        // 結果をjson形式で取得する
        return json_decode($respnse, true);
    }

    /**
     * バックログにPOSTリクエストを送る
     * @param string $name API名
     * @param array $name POSTするデータ
     * @return array レスポンス情報(連想配列)
     */
    function post(string $name , array $params) {
        // POSTデータがなければエラー
        if(empty($params)) {
            throw new Exception('POSTデータがありません');
        }

        // APIにリクエストを送る
        $url = $this->base.$name.'?apiKey='.$this->akey;

        echo '送信先URL:'.$url.PHP_EOL;
        return $this->postReq($url, $params);
    }

    /**
     * cURLでGETリクエストを送る
     * @param string $url URL
     */
    private function getReq(string $url){
        // cURLセッションを初期化する
        $curl = curl_init();

        // オプションを設定
        curl_setopt($curl, CURLOPT_URL, $url); // 取得するURLを指定
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true); // 実行結果を文字列で返す
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); // サーバー証明書の検証を行わない
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'GET'); // リクエスト時のメソッド

        // cURLリクエスト実施
        $response = curl_exec($curl);

        // セッションを終了
        curl_close($curl);

        return $response;
    }

    /**
     * cURLでPOSTリクエストを送る
     * @param string $url URL
     * @param array $postData POSTするデータ
     */
    private function postReq(string $url, array $postData) {
        // cURLセッションを初期化する
        $curl = curl_init();

        // オプションを設定
        curl_setopt($curl, CURLOPT_URL, $url); // 取得するURLを指定
        curl_setopt($curl,CURLOPT_POST, TRUE); // POST通信をONにする
        //curl_setopt($curl,CURLOPT_POSTFIELDS, $POST_DATA); // ↓はmultipartリクエストを許可していないサーバの場合はダメ
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($postData));
        curl_setopt($curl,CURLOPT_SSL_VERIFYPEER, FALSE);  // サーバー証明書の検証を行わない(オレオレ証明書対策)
        curl_setopt($curl,CURLOPT_SSL_VERIFYHOST, FALSE);  //
        curl_setopt($curl,CURLOPT_RETURNTRANSFER, TRUE); // 実行結果を文字列で返す

        // cURLリクエスト実施
        $response = curl_exec($curl);

        // セッションを終了
        curl_close($curl);

        return $response;
    }

}