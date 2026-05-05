<?php
namespace Core;

class Controller
{
    public function view($view, $data = [])
    {
        extract($data);
        $viewFile = dirname(__DIR__) . "/app/Views/$view.php";

        if (file_exists($viewFile)) {
            require_once $viewFile;
        } else {
            die("<h2>⚠️ View Not Found</h2><p>Looking for: <code>$viewFile</code></p>");
        }
    }

    protected function requireAuth()
    {
        if (isset($_SESSION['user_id'])) {
            return true;
        }

        if (isset($_COOKIE['remember_user'])) {
            $_SESSION['user_id'] = $_COOKIE['remember_user'];
            return true;
        }

        header("Location: /login");
        exit();
    }
}