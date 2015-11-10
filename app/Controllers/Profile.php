<?php
namespace Controllers;

class Profile extends BaseController
{
    protected function initRoutes()
    {
 

		$this->app->get('/profile', array($this, 'showProfile'))->
		name('profile');
		
		 $this->app->post('/profile', array($this, 'addProfile'));
    }

	
	public function showProfile()
    {
       // $this->redirectIfLoggedIn();
		
        $this->app->flashNow('hideRegister', true);
        $this->app->render('profile.twig');
    }
	
	
    public function showForm()
    {
        $this->redirectIfLoggedIn();

        $this->app->flashNow('hideRegister', true);
        $this->app->render('register.twig');
    }

	
	public function addProfile()
    {
        //$this->redirectIfLoggedIn();
		$req = $this->app->request;
		$user = new \Models\User();

		list($errors, $fixes) = $user->updateProfile($req);
		 $this->app->redirect($this->app->urlFor('home'));
	}
	
	
	
}
