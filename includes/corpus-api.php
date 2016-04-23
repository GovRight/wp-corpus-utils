<?php

class CorpusAPI {
    protected static $_instance;
    public static function getInstance() {
        if(self::$_instance) {
            return self::$_instance;
        }
        self::$_instance = new CorpusApiServer();
        return self::$_instance;
    }
}

class CorpusApiServer {
    protected $_cache = array();
    public function __get($name) {
        return $this->_getModel($name);
    }
    public function __call($name, $args) {
        $model = $this->_getModel($name);
        if(empty($args[0]) || is_array($args[0])) {
            if(is_array($args[0])) {
                $query = isset($args[0]['filter']) ? $args[0] : array('filter' => $args[0]);
            } else {
                $query = array();
            }
            return $model->get(array('query' => $query));
        } else {
            if(!empty($args[1]) && is_array($args[1])) {
                $query = isset($args[1]['filter']) ? $args[0] : array('filter' => $args[1]);
            } else {
                $query = array();
            }
            return $model->get(array(
                'id' => $args[0],
                'query' => $query
            ));
        }
    }
    protected function _getModel($name) {
        // Basic check for plural
        if(substr($name, -1) !== 's') {
            $name .= 's';
        }
        if(!empty($this->_cache[$name])) {
            return $this->_cache[$name];
        }
        $this->_cache[$name] = new CorpusApiModel($name);
        return $this->_cache[$name];
    }
}

class CorpusApiModel {
    protected $_name;
    protected $_apiUrl;
    public function __construct($name) {
        $this->_name = $name;
        $this->_apiUrl = corpus_get_api_url() . '/';
    }

    public function __call($method, $args) {
        $params = array(
            'method' => $method
        );
        if(!empty($args[0])) {
            if($method === 'findOne' && !isset($args[0]['filter'])) {
                $params['query'] = array('filter' => $args[0]);
            } else {
                $params['query'] = $args[0];
            }
        } else {
            $params['query'] = array();
        }
        return $this->get($params);
    }

    public function get($params) {
        $url = $this->_apiUrl . $this->_name;
        if(!empty($params['id'])) {
            $url .= '/' . $params['id'];
        }
        if(!empty($params['method'])) {
            $url .= '/' . $params['method'];
        }
        if(!empty($params['query'])) {
            $url .= '?' . str_replace(array('%5B', '%5D'), array('[', ']'), http_build_query($params['query']));
        }
        $resp = wp_remote_get($url);
        if(!is_wp_error($resp)) {
            $res = json_decode($resp['body'], true);
            if(!empty($res['error'])) {
                $res['error']['url'] = $url;
                return new WP_Error('corpus-api-error', $res['error']['message'], $res['error']);
            }
            return $res;
        }
        return $resp;
    }
}