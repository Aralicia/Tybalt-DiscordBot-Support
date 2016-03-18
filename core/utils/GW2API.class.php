<?php

class GW2API {

    public static function setup() {
        self::$memcached = new Memcached();
        self::$memcached->addServer('127.0.0.1', 11211);
    }
    public static function v1($endpoint, $options = []) {
        $url = "https://api.guildwars2.com/v1/".$endpoint;
        return self::call($url, $options);
    }
    public static function v2($endpoint, $options = []) {
        $url = "https://api.guildwars2.com/v2/".$endpoint;
        return self::call($url, $options);
    }
    private static function call($url, $options) {
        if (isset($options['params'])) {
            $url = $url.'?'.http_build_query($options['params'], null, ini_get('arg_separator.output'),  PHP_QUERY_RFC3986);
        }
        echo ($url);
        
        if (isset($options['auth']) || isset($options['post'])) {
            $return = self::curl_call($url, $options);
        } else {
            $return = self::memcached_call($url, $options);
        }

        if ((isset($options['json']) && $options['json'] == true) ||
            !isset($options['json'])) {
            if ($return !== FALSE) {
                $return = json_decode($return);
            }
        }
        return $return;
    }
    private static function curl_call($url, $options) {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_FAILONERROR,true);

        if (isset($options['auth'])) {
            curl_setopt($curl, CURLOPT_USERPWD, $options['auth']);
        }
        if (isset($options['post'])) {
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $options['post']);
        }
        $return = curl_exec($curl);
        if ($return === FALSE) {
            //echo (curl_error($curl));
        }
        curl_close($curl);
        return $return;
    }
    private static function memcached_call($url, $options) {
        $return = self::$memcached->get('gw2apicache_'.$url);
        if (self::$memcached->getResultCode() != Memcached::RES_SUCCESS) {
            $return = self::curl_call($url, $options);
            self::$memcached->set('gw2apicache_'.$url, $return, 60*60*24);
        }
        return $return;
    }
    private static $memcached;
}
GW2API::setup();
