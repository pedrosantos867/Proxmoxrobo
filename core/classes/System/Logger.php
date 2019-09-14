<?php

namespace System;

class Logger {
    const MAX_LINES = 1000;
    private static $count_lines;
    public static function log($message){
        $get_file = @file_get_contents(Path::getRoot('app/logs/log.txt'));
        self::$count_lines = count(explode(PHP_EOL, $get_file));
        $r = debug_backtrace();
        $p_file = explode('public_html', $r[0]['file']);
        $file = isset($p_file[1]) ? $p_file[1] : $r[0]['file'];
        $message .= PHP_EOL . " in file: $file" . PHP_EOL . " on line: " . $r[0]['line'] . PHP_EOL . " in func: " . $r[1]['function'] . PHP_EOL . " in class " . $r[1]['class'];
        if (self::$count_lines >= self::MAX_LINES) {
            @file_put_contents(Path::getRoot('app/logs/log_old.txt'), $get_file);
            @file_put_contents(Path::getRoot('app/logs/log.txt'), date('Y-m-d h:i:s') . ' ' . $message . PHP_EOL);
            return;
        }

        @file_put_contents(Path::getRoot('app/logs/log.txt'), date('Y-m-d h:i:s') .' '. $message . PHP_EOL, FILE_APPEND);
    }
}