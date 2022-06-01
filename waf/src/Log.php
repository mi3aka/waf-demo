<?php

namespace waf;


class Log
{

    private $config;

    public function __construct($config)
    {
        $this->config = $config;
    }

    public function general_original_request()#生成原始请求
    {
        $path = $this->config->get('base_dir') . $this->config->get('request_dir');
        if (!is_dir($path)) {//创建原始请求目录
            mkdir($path, 0755, true);
        }
        $filename = $path . $_SERVER['REMOTE_ADDR'] . "_" . date('c') . "_" . rand() . ".txt";
        $headers = getallheaders();
        $original_request = $_SERVER['REQUEST_METHOD'] . " " . $_SERVER['REQUEST_URI'] . " " . $_SERVER['SERVER_PROTOCOL'] . "\r\n";
        foreach ($headers as $header => $value) {
            $original_request .= "$header: $value\r\n";
        }
        if (strstr($_SERVER['CONTENT_TYPE'], 'multipart/form-data')) {#针对multipart/form-data进行特殊处理
            $body = array();
            $boundary = array();
            parse_str($_SERVER['CONTENT_TYPE'], $boundary);
            foreach ($_FILES as $key => $value) {
                $body_part = "Content-Disposition: form-data; name=\"{$key}\"; filename=\"{$value['name']}\"\r\n";#生成Content-Disposition和Content-type
                $body_part .= "Content-type: {$value['type']}\r\n";
                $body_part .= "\r\n" . file_get_contents($value['tmp_name']);
                $body[] = $body_part;
            }
            foreach ($_POST as $key => $value) {
                $body_part = "Content-Disposition: form-data; name=\"{$key}\"\r\n";#生成Content-Disposition
                $body_part .= "\r\n" . $value;
                $body[] = $body_part;
            }
            $original_request .= "\r\n";
            $original_request .= "--" . current($boundary) . "\r\n";#补全boundary
            $original_request .= implode("\r\n--" . current($boundary) . "\r\n", $body);
            $original_request .= "\r\n--" . current($boundary) . "--";
            $original_request .= "\r\n\r\n";
        } else {
            $original_request .= "\r\n" . file_get_contents("php://input") . "\r\n\r\n";
        }
        file_put_contents($filename, $original_request, FILE_APPEND | LOCK_EX);
        return $filename;
    }

    public function block_ip()#记录ip
    {
        $path = $this->config->get('base_dir') . $this->config->get('block_ip_dir');
        if (!is_dir($path)) {//创建原始请求目录
            mkdir($path, 0755, true);
        }
        $filename = $path . $this->config->get('block_ip_file_name');
        $data = $_SERVER['REMOTE_ADDR'] . "\n";
        file_put_contents($filename, $data, FILE_APPEND | LOCK_EX);
    }

    public function write($log_level, $log_reason, $message)#写入日志
    {
        $path = $this->config->get('base_dir') . $this->config->get('log_dir');
        $full_time = date('c');
        if (!is_dir($path)) {//创建日志目录
            mkdir($path, 0755, true);
        }
        $ip = $_SERVER['REMOTE_ADDR'];//记录ip
        $url = $_SERVER['REQUEST_URI'];//记录访问的URL
        $log = "[{$log_level}] {$log_reason}\n$message";
        $filename = $path . $this->config->get('log_file_name');
        error_log("$full_time  $ip  $url\n$log\n\n\n", 3, $filename);
    }
}
