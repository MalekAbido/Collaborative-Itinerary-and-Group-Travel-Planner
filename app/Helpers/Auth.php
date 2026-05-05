<?php
namespace App\Helpers;

use App\Helpers\Session;
use App\Models\User;

class Auth
{
    // The strict hierarchy: 3 can do what 2 and 1 can do.
    private static $roles = [
        'Member' => 1,
        'Editor' => 2,
        'Leader' => 3,
    ];

    public static function check()
    {

        if (Session::get('user_id') !== null) {
            return true;
        }

        if (isset($_COOKIE[Session::getCookieName()])) {
            $plainToken  = $_COOKIE[Session::getCookieName()];
            $hashedToken = hash('sha256', $plainToken);

            $userId = User::getBySessionToken($hashedToken);

            if ($userId) {
                Session::set('user_id', $userId);
                return true;
            }
        }

        return false;
    }

    public static function id()
    {

        if (self::check()) {
            return Session::get('user_id');
        }

        return null;
    }

    public static function login($userId)
    {
        Session::set('userId', $userId);
        $plainToken  = bin2hex(random_bytes(32));
        $hashedToken = hash("sha256", $plainToken);
        User::updateSessionToken($userId, $hashedToken);
        $cookieName = Session::getCookieName();
        $lifetime   = time() + (60 * 60 * 24 * 30);
        setcookie($cookieName, $plainToken, $lifetime, '/', '', false, true);
    }

    public static function logout()
    {
        $userId = self::id();

        if ($userId) {
            User::updateSessionToken($userId, null);
        }

        Session::destroy();

        setcookie(Session::getCookieName(), '', time() - 3600, '/');
    }

    public static function hasRole($requiredRole, $currentRole)
    {
        $requiredLevel = self::$roles[$requiredRole] ?? 0;
        $currentLevel  = self::$roles[$currentRole] ?? 0;

        return $currentLevel >= $requiredLevel;
    }

    public static function requireRole($requiredRole, $currentRole)
    {

        if (! self::hasRole($requiredRole, $currentRole)) {
            Session::setFlash('error', 'You do not have permission to perform this action.');
            header('Location: /dashboard');
            exit;
        }
    }

    public static function requireLogin()
    {

        if (! self::check()) {
            Session::setFlash('error', 'Please log in to continue.');
            header('Location: /login');
            exit;
        }
    }
}
