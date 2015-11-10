<?php
namespace Controllers;

class Root extends BaseController
{
    protected function initRoutes()
    {
        $this->app->get('/', array($this, 'home'))->name('home');
    }

    public function home()
    {
        $this->app->render('index.twig', array('oneIsActive' => true));
    }
}
