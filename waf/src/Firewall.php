<?php

namespace waf;

class Firewall
{
    private $config;
    /**
     * @var mixed
     */
    private $path;

    public function __construct($config)
    {
        $this->config = $config;
        $this->path = parse_url($_SERVER['REQUEST_URI'])['path'];
    }

    public function run()
    {
        if (preg_match($this->config->get('path_whitelist'), $this->path)) {#路径白名单,直接放行
            return;
        }
        $monitor = new Monitor($this->config);#功能加载
        if ($this->config->get('block_ip_enable')) {
            $this->block_ip();
        }
        if ($this->config->get('sql_monitor_enable')) {
            $monitor->sql_monitor();
        }
        if ($this->config->get('rce_monitor_enable')) {
            $monitor->rce_monitor();
        }
        if ($this->config->get('xss_monitor_enable')) {
            $monitor->xss_monitor();
        }
        if ($this->config->get('upload_monitor_enable')) {
            $monitor->upload_monitor();
        }
        if ($this->config->get('other_monitor_enable')) {
            $monitor->other_monitor();
        }
        if ($this->config->get('user_monitor_enable')) {
            $monitor->user_monitor();
        }
    }

    private function block_ip()#ip封禁
    {
        $ips = file_get_contents($this->config->get('base_dir') . $this->config->get('block_ip_dir') . $this->config->get('block_ip_file_name'));
        $ips = explode("\n", $ips);
        foreach ($ips as $ip) {
            if ($ip === $_SERVER['REMOTE_ADDR']) {
                die();
            }
        }
    }
}
