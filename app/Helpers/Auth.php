<?php
namespace App\Helpers;

use App\Helpers\Session;
use App\Models\TripMember;
use App\Models\User;
use App\Models\TripMember;


class Auth
{
    private static $roles = [
        'Member'    => 1,
        'Editor'    => 2,
        'Organizer' => 3,
    ];

    public static function check()
    {

        if (Session::get('userId') !== null) {
            return true;
        }

        if (isset($_COOKIE[Session::getCookieName()])) {
            $plainToken  = $_COOKIE[Session::getCookieName()];
            $hashedToken = hash('sha256', $plainToken);

            $userId = User::getBySessionToken($hashedToken);

            if ($userId) {
                Session::set('userId', $userId);
                return true;
            }
        }

        return false;
    }

    public static function id()
    {

        if (self::check()) {
            return Session::get('userId');
        }

        return null;
    }

    // Add this inside your Auth class
    public static function user()
    {
        $userId = self::id();

        if ($userId) {
            $user = new User();
            if ($user->read($userId)) {
                return $user;
            }
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
            Session::setFlash(Session::FLASH_ERROR, 'You do not have permission to perform this action.');
            header("Location: " . $_SERVER['HTTP_REFERER']);
            exit;
        }
    }

    public static function requireLogin()
    {

        if (! self::check()) {
            Session::set('intended_url', $_SERVER['REQUEST_URI']);
            
            Session::setFlash(Session::FLASH_ERROR, 'Please log in to continue.');
            header('Location: /login');
            exit;
        }
    }

    public static function requireMembership($itineraryId) {
        $userId = Auth::id();
        $member = TripMember::getByUserAndItinerary($userId, $itineraryId);

        if (!$member) {
            Session::setFlash(Session::FLASH_ERROR, 'Access denied. You are not a member of this itinerary.');
            header("Location: /dashboard");
            exit;
        }

        return $member;
    }
}
