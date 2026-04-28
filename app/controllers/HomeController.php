<?php

class HomeController extends Controller
{
    public function index()
    {
        $this->view("home/index");
    }

    public function styleguide()
    {
        $this->view("home/styleguide");
    }
}
