<?php

class LoadEnv
{
    /**
     * Parse the .env file and load variables into the environment.
     * * @param string $path Absolute path to the .env file
     * @throws Exception If the file does not exist
     */
    public static function load(string $path): void
    {

        if (! file_exists($path)) {
            throw new Exception("Environment file missing at: " . $path);
        }

        // Read file into an array, ignoring empty lines and newlines
        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        foreach ($lines as $line) {
            if (strpos(trim($line), '#') === 0) {
                continue;
            }

            if (strpos($line, '=') !== false) {
                list($name, $value) = explode('=', $line, 2);

                $name  = trim($name);
                $value = trim($value);

                putenv(sprintf('%s=%s', $name, $value));
                $_ENV[$name]    = $value;
                $_SERVER[$name] = $value;
            }
        }
    }
}
