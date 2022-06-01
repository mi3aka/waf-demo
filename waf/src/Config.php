<?php

namespace waf;

class Config
{
    /**
     * @var mixed
     */
    private $config;

    public function __construct()
    {
        $this->config = require_once 'config.inc.php';
    }

    public function get($key)
    {
        if (isset($this->config[$key])) {
            return $this->config[$key];
        } else {
            echo 'WAF Config Error!!!';
            die();
        }
    }
}
