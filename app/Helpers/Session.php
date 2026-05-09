<?php
namespace App\Helpers;

class Session
{
    private static $sessionName = 'ItineraryPlannerSession';
    private static $cookieName  = 'ItineraryPlannerCookie';
    public const FLASH_SUCCESS  = 'success';
    public const FLASH_ERROR    = 'error';
    public const FLASH_INFO     = 'info';
    /**
     * Resumes the existing session or starts a new one.
     */
    public static function start()
    {

        if (session_status() === PHP_SESSION_NONE) {
            session_name(self::$sessionName);
            $lifetime = 60 * 60 * 24 * 30;
            session_set_cookie_params([
                'lifetime' => $lifetime,
                'path'     => '/',
                'secure'   => false,
                'httponly' => true,
            ]);
            session_start();
        }
    }

    /**
     * Stores a key-value pair in the session array.
     */
    public static function set($key, $value)
    {
        $_SESSION[$key] = $value;
    }

    /**
     * Retrieves a value from the session, returning a default if not found.
     */
    public static function get($key, $default = null)
    {

        if (isset($_SESSION[$key])) {
            return $_SESSION[$key];
        } else {
            return $default;
        }
    }

    /**
     * Completely destroys the session file.
     */
    public static function destroy()
    {
        session_unset();
        session_destroy();
        setcookie(self::$sessionName, '', time() - 3600, '/');
    }

    /**
     * Sets a one-time flash message.
     */
    public static function setFlash($key, $message)
    {
        $_SESSION['flashMessages'][$key] = $message;
    }

    /**
     * Checks if a flash message exists.
     */
    public static function hasFlash($key)
    {
        return isset($_SESSION['flashMessages'][$key]);
    }

    /**
     * Gets and immediately deletes a flash message.
     */
    public static function getFlash($key)
    {

        if (isset($_SESSION['flashMessages'][$key])) {
            $message = $_SESSION['flashMessages'][$key];

            unset($_SESSION['flashMessages'][$key]);
            return $message;
        } else {
            return "";
        }
    }

    public static function getSessionName()
    {
        return self::$sessionName;
    }

    public static function getCookieName()
    {
        return self::$cookieName;
    }
}
