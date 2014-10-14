<?php

class Api
{

    private static $userToken = '';
    private static $url = '';

    public static function init($url, $userToken)
    {
        self::$url = $url;
        self::$userToken = $userToken;
    }

    public static function request($url, $post = array())
    {
        $url = self::$url."api/".$url;
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'USER-TOKEN: '.self::$userToken,
        ));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_URL, $url);
        if (!empty($post)) {
            curl_setopt($ch,CURLOPT_POST, 1);
            curl_setopt($ch,CURLOPT_POSTFIELDS, $post);
        }
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        $result = curl_exec($ch);
        curl_close($ch);
        $json = json_decode($result);

        return $json->data;
    }
}
