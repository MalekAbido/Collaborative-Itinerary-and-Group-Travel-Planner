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
}
