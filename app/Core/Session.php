<?php
class Session {
    public static function set($key, $value) {
        $_SESSION[$key] = $value;
    }

    public static function get($key, $default = null) {
        return isset($_SESSION[$key]) ? $_SESSION[$key] : $default;
    }

    public static function remove($key) {
        if (isset($_SESSION[$key])) {
            unset($_SESSION[$key]);
        }
    }

    public static function destroy() {
        session_destroy();
    }

    public static function setFlash($type, $message) {
        $_SESSION['flash'][$type][] = $message;
    }

    public static function getFlash($type) {
        if (isset($_SESSION['flash'][$type])) {
            $messages = $_SESSION['flash'][$type];
            unset($_SESSION['flash'][$type]);
            return $messages;
        }
        return [];
    }
}
