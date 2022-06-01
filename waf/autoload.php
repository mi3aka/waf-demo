<?php
spl_autoload_register(function ($class) {
    $namespace = "waf\\";
    $length = strlen($namespace);
    if (strncmp($namespace, $class, $length) !== 0) {
        return;
    }
    $filename = sprintf("%s/%s/%s.php", __DIR__, 'src', str_replace('\\', DIRECTORY_SEPARATOR, substr($class, $length)));
    if (file_exists($filename)) {
        require_once $filename;
    }
});
