<?php

spl_autoload_register(function ($name) {
    $segments = explode('\\', $name);
    $fileName = array_slice($segments, -1)[0];

    array_pop($segments);

    if (!empty(APP_NAMESPACE) && count($segments) > 0 && $segments[0] == APP_NAMESPACE) {
        array_shift($segments);
    }

    $segments = array_map('strtolower', $segments);

    require_once ROOT_PATH . implode(DS, $segments) . DS . $fileName . '.php';
});