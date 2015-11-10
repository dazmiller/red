<?php
namespace Controllers;

abstract class BaseController
{
    protected $app;

    public function __construct()
    {
        $this->app = \Slim\Slim::getInstance();

        $this->initRoutes();
    }

    abstract protected function initRoutes();

    protected function getTwigArray($model)
    {
        return \R::exportAll($model->unbox())[0];
    }

    protected function verifyLogin()
    {
        unset($_SESSION['redirect']);
        if (!isset($_SESSION['user']))
        {
            $this->app->flash('autherror', true);
            $_SESSION['redirect'] = $this->app->request->getRootUri() . $this->app->request->getResourceUri();

            $this->app->redirect($this->app->urlFor('login'));
        }

        $this->app->flashNow('hideLogin', true);
        $this->app->flashNow('hideRegister', true);
    }

    protected function redirectIfLoggedIn($redirect = '/') // Change this to the default page for a user
    {
		
			
        if (isset($_SESSION['user']))
        {
				
            $this->app->redirect($redirect);
        }
    }
}
