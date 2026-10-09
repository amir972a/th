<?php
class Autoloader {
    public static function register() {
        spl_autoload_register(function ($class) {
            $file = __DIR__ . '/../../' . str_replace('\\', '/', $class) . '.php';
            if (file_exists($file)) {
                require $file;
            } else {
                // Try looking in app directories specifically if no namespace is used (fallback)
                $dirs = [
                    __DIR__ . '/../Controllers/',
                    __DIR__ . '/../Models/',
                    __DIR__ . '/../Core/'
                ];
                foreach ($dirs as $dir) {
                    $file = $dir . $class . '.php';
                    if (file_exists($file)) {
                        require_once $file;
                        return;
                    }
                }
            }
        });
    }
}
Autoloader::register();
