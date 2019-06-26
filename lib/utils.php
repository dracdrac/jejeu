<?php

function formatString($str, $data) {
    return preg_replace_callback('#{{(\w+?)(\.(\w+?))?}}#', function($m) use ($data){
        return count($m) === 2 ? $data[$m[1]] : $data[$m[1]][$m[3]];
    }, $str);
}
