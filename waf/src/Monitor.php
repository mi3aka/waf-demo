<?php

namespace waf;

class Monitor
{
    private $config;
    /**
     * @var Log
     */
    private $log;
    /**
     * @var array
     */
    private $cookie;
    /**
     * @var array
     */
    private $session;
    /**
     * @var array
     */
    private $request;
    /**
     * @var array
     */
    private $files;


    public function __construct($config)
    {
        $this->config = $config;
        $this->log = new Log($this->config);
        $this->cookie = $this->parse_array($_COOKIE);
        $this->session = $this->parse_array($_SESSION);
        $this->request = $this->parse_array($_REQUEST);
        $this->files = $_FILES;
    }

    private function parse_array($arr)
    {
        $array = [];
        if (is_array($arr)) {
            foreach ($arr as $key => $val) {
                if (is_array($key)) {
                    $array = array_merge($array, $this->parse_array($key));
                } else {
                    $array[] = $key;
                }
                if (is_array($val)) {
                    $array = array_merge($array, $this->parse_array($val));
                } else {
                    $array[] = $val;
                }
            }
        } else {
            $array[] = $arr;
        }
        $array[] = $GLOBALS['HTTP_RAW_POST_DATA'];
        return array_unique($array);
    }

    public function sql_monitor()//SQL监控
    {
        $array = array_merge($this->cookie, $this->session, $this->request);
        foreach ($array as $val) {
            if (preg_match($this->config->get('sql_blacklist'), $val)) {
                $original_request = $this->log->general_original_request();
                $this->log->write('HIGH', 'SQL Injection Detect', 'Original Request File ' . $original_request);
                echo "<script>alert('SQL Injection Detect')</script>";
                die();
            }
        }
    }

    public function rce_monitor()//RCE监控
    {
        $array = array_merge($this->cookie, $this->session, $this->request);
        foreach ($array as $val) {
            if (preg_match($this->config->get('rce_blacklist'), $val)) {
                $original_request = $this->log->general_original_request();
                $this->log->write('WARN', 'Command Injection Detect', 'Original Request File ' . $original_request);
                $this->log->block_ip();
                echo "<script>alert('Command Injection Detect')</script>";
                die();
            }
        }
    }

    public function xss_monitor()//XSS监控
    {
        $array = array_merge($this->cookie, $this->session, $this->request);
        foreach ($array as $val) {
            if (preg_match($this->config->get('xss_blacklist'), $val)) {
                $original_request = $this->log->general_original_request();
                $this->log->write('HIGH', 'XSS Detect', 'Original Request File ' . $original_request);
                echo "<script>alert('XSS Detect')</script>";
                die();
            }
        }
    }

    public function upload_monitor()//文件上传监控
    {
        foreach ($this->files as $file) {
            if ($file['error'] === UPLOAD_ERR_OK) {
                $filename = trim($file["name"]);
                $ext = trim(substr(strrchr($filename, '.'), 1));
                if (in_array($ext, $this->config->get('upload_whitelist'), true)) {
                    $file_contents = file_get_contents($file['tmp_name']);
                    if (preg_match("/<\?php|<\?=/i", $file_contents)) {//恶意文件内容
                        $original_request = $this->log->general_original_request();
                        $this->log->write('WARN', 'Malicious Files Contents', 'Original Request File ' . $original_request);
                        $this->log->block_ip();
                        @unlink($file['tmp_name']);
                        echo "<script>alert('Malicious Files Contents')</script>";
                        die();
                    }
                } else {//恶意文件后缀
                    $original_request = $this->log->general_original_request();
                    $this->log->write('WARN', 'Malicious Files Extension', 'File Name ' . $file['name'] . "\n" . 'Original Request File ' . $original_request);
                    $this->log->block_ip();
                    @unlink($file['tmp_name']);
                    echo "<script>alert('Malicious Files Extension')</script>";
                    die();
                }
            }
        }
    }

    public function user_monitor()//用户操作监控
    {
        $checklist = [];
        $checklist[] = $_SERVER['REQUEST_URI'];
        foreach ($_GET as $key => $value) {
            $checklist[] = $key;
        }
        foreach ($_POST as $key => $value) {
            $checklist[] = $key;
        }
        foreach ($checklist as $value) {
            if (preg_match($this->config->get('user_keyword'), $value)) {
                $original_request = $this->log->general_original_request();
                $this->log->write('INFO', 'User Activity Detect', 'Keyword ' . $value . "\n" . 'Original Request File ' . $original_request);
                break;
            }
        }
    }

    public function other_monitor()//其余类型监控,如路径穿越,敏感文件读取等
    {
        foreach ($this->request as $value) {
            if (preg_match("/\.\.|\/.+?\//i", $value)) {//路径穿越
                $original_request = $this->log->general_original_request();
                $this->log->write('HIGH', 'Path Traversal', 'Keyword ' . $value . "\n" . 'Original Request File ' . $original_request);
                break;
            } elseif (preg_match("/\/passwd|\/shadow|\/locate|\/htaccess|\.ini|\.git|\.svn|\.ssh|\/id_rsa|\/known_hosts|\/authorized_keys|docker|history|shrc|\.profile|\.log|\.xml/i", $value)) {//敏感文件读取
                $original_request = $this->log->general_original_request();
                $this->log->write('HIGH', 'Sensitive Files', 'Keyword ' . $value . "\n" . 'Original Request File ' . $original_request);
                break;
            } elseif (preg_match("/file:\/\/|php:\/\/|zlib:\/\/|data:\/\/|glob:\/\/|phar:\/\/|ssh2:\/\/|rar:\/\/|ogg:\/\/|expect:\/\//i", $value)) {//PHP伪协议
                $original_request = $this->log->general_original_request();
                $this->log->write('HIGH', 'Wrapper Detect', 'Keyword ' . $value . "\n" . 'Original Request File ' . $original_request);
                break;
            }
        }
    }
}
