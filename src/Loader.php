<?php
spl_autoload_register(function ($name) {
    $segments = explode('\\', $name);
    $fileName = array_slice($segments, -1)[0];

    array_pop($segments);

    if (!empty(PHPMVC_APP_NAMESPACE) && count($segments) > 0 && $segments[0] == PHPMVC_APP_NAMESPACE) {
        array_shift($segments);
    }

    $path = PHPMVC_ROOT_PATH . implode(PHPMVC_DS, $segments) . PHPMVC_DS . $fileName . '.php';

    if (!is_file($path)) {
        $segments = array_map('strtolower', $segments);
        $path = PHPMVC_ROOT_PATH . implode(PHPMVC_DS, $segments) . PHPMVC_DS . $fileName . '.php';
    }

    require_once $path;
});