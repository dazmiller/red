<?php
namespace Controllers;

class CustomErrors extends BaseController
{
    protected function initRoutes()
    {
        $app = $this->app;

        $app->notFound(function() use ($app) {
            $app->render('404.twig');
        });
    }
}
