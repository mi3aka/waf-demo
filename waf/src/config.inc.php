<?php

return array(
    'path_whitelist' => "/^\/index\.php$/i",
    'base_dir' => '/tmp/waf/',
    'log_dir' => 'log/',
    'log_file_name' => date('y_m_d') . '.log',
    'request_dir' => 'request/',
    'block_ip_dir' => 'block_ip/',
    'block_ip_file_name' => 'ip.txt',
    'sql_monitor_enable' => true,
    'rce_monitor_enable' => true,
    'xss_monitor_enable' => true,
    'upload_monitor_enable' => true,
    'user_monitor_enable' => true,
    'other_monitor_enable' => true,
    'block_ip_enable' => false,
    'upload_whitelist' => array('jpeg', 'jpg', 'png', 'gif', 'pdf', 'webp', 'ico', 'svg', 'txt', 'mp3', 'mp4', 'pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx'),#文件上传白名单
    'rce_blacklist' => "/<\?php|<\?=|`|var_dump|str_rot13|serialize|base64_|strrev|eval|assert|file_put_contents|fwrite|exec|passthru|preg|create_function|call_user_func|array_|usort|stream_socket_server|system|chroot|scandir|chgrp|chown|proc_|popen|ini_|ld_preload|\w+\\$\{*\w+|\\$\(|whoami|ls|cat|pwd|echo|\/bin\/|\/etc\/|\/usr\/|\/tmp\/|\/sys\/|\/opt\/|\/\w*\?+\w*\/|\w+<>\w+/i",#命令注入&代码注入检测
    'sql_blacklist' => "/union[^\xff]+?select[^\xff]+?|handler[^\xff]+?read|select[^\xff]+?from|truncate[^\xff]+?|update[^\xff]+?\(|extractvalue[^\xff]+?\(|insert[^\xff]+?into[^\xff]+?values|delete[^\xff]+?from|drop[^\xff]+?table|into[^\xff]+?dumpfile|into[^\xff]+?outfile|load_file[^\xff]+?\(|benchmark[^\xff]+?\(|sleep[^\xff]+?\(|concat[^\xff]+?\(|length[^\xff]+?\(|ascii[^\xff]+?\(|substr[^\xff]+?\(|repeat[^\xff]+?\(|procedure[^\xff]+?analyse[^\xff]+?\(|between[^\xff]+?and|from[^\xff]+?for|regexp[^\xff]+?\(|multipoint[^\xff]+?\(|geometrycollection[^\xff]+?\(|polygon[^\xff]+?\(|linestring[^\xff]+?\(|if[^\xff]+?\([^\xff]+?,|case[^\xff]+?when[^\xff]+?|get_lock[^\xff]+?\(|rand[^\xff]+?\(|order[^\xff]+?by[^\xff]+?|group[^\xff]+?by[^\xff]+?|\|\||&&|@@|version[^\xff]+?\(|database[^\xff]+?\(|user[^\xff]+?\(|information_schema\.|mysql\.|sys\./i",#SQL检测
    'xss_blacklist' => "/document\.\w+|\w+\.cookie|window\.\w+|\.location\.|javascript:|<\/\w+|<[^>]*\s+(?:on|href)\w*\s*=[^`]*(?:top|prompt|alert|confirm)[^`]*\(|<[^>]*>[^`]*(?:top|prompt|alert|confirm)[^`]*\(|data:/i",#XSS检测
    'user_keyword' => "/login|reg|admin|backend|password|user|forget|reset|manager/i",#用户操作检测
);
