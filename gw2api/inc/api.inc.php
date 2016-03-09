<?php

class API {

    public function __construct() {
        $this->memcached = new Memcached();
        $this->memcached->addServer('127.0.0.1', 11211);
    }
    public function v1($endpoint, $options = []) {
        $url = "https://api.guildwars2.com/v1/".$endpoint;
        return $this->call($url, $options);
    }
    public function v2($endpoint, $options = []) {
        $url = "https://api.guildwars2.com/v2/".$endpoint;
        return $this->call($url, $options);
    }
    private function call($url, $options) {
        if (isset($options['params'])) {
            $url = $url.'?'.http_build_query($options['params'], null, ini_get('arg_separator.output'),  PHP_QUERY_RFC3986);
        }
        
        if (isset($options['auth']) || isset($options['post'])) {
            $return = $this->curl_call($url, $options);
        } else {
            $return = $this->memcached_call($url, $options);
        }

        if ((isset($options['json']) && $options['json'] == true) ||
            !isset($options['json'])) {
            if ($return !== FALSE) {
                $return = json_decode($return);
            }
        }
        return $return;
    }
    private function curl_call($url, $options) {
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
    private function memcached_call($url, $options) {
        $return = $this->memcached->get('gw2apicache_'.$url);
        if ($this->memcached->getResultCode() != Memcached::RES_SUCCESS) {
            $return = $this->curl_call($url, $options);
            $this->memcached->set('gw2apicache_'.$url, $return, 60*60*24);
        }
        return $return;
    }
    private $memcached;
}

function api() {
    static $api = null;
    if ($api == null) {
        $api = new API();
    }
    return $api;
}

