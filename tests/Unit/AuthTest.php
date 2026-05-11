<?php

use PHPUnit\Framework\TestCase;
use App\Services\Auth;
use App\Services\Session;

class AuthTest extends TestCase
{
    protected function setUp(): void
    {
        Session::$store = [];
    }

    public function testCheckPaths()
    {
        // Path 1: Session has userId
        Session::set('userId', 123);
        $this->assertTrue(Auth::check());

        // Path 2: Session empty
        Session::$store = [];
        $this->assertFalse(Auth::check());
    }
}
