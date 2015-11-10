<?php
namespace Controllers;

class Register extends BaseController
{
    protected function initRoutes()
    {
        $this->app->get('/register', array($this, 'showForm'))->
            name('register');

        $this->app->post('/register', array($this, 'addUser'));
		

    }

	
	public function showTest()
    {
       // $this->redirectIfLoggedIn();
		
       // $this->app->flashNow('hideRegister', true);
        $this->app->render('test.twig');
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
        $user = $_SESSION['user'];
		list($errors, $fixes) = $user->updateProfile($user,$req);
	}
	
	
	
    public function addUser()
    {
        $this->redirectIfLoggedIn();

        $req = $this->app->request;
        $user = new \Models\User();
        list($errors, $fixes) = $user->create($req->post('email'),
                                              $req->post('firstname'),
											   $req->post('lastname'),
                                              $req->post('password'),
                                              $req->post('confirmPassword'));

        if (0 == count($fixes))
        {
            $this->app->flashNow('registered', true);
        }
        else
        {
            if (!is_null($req->post('email')))
            {
                $this->app->flashNow('email', $req->post('email'));
            }

            if (!is_null($req->post('firstname')))
            {
                $this->app->flashNow('firstname', $req->post('firstname'));
            }

			if (!is_null($req->post('lastname')))
            {
                $this->app->flashNow('lastname', $req->post('lastname'));
            }
			
			
			echo "ERRORS";
			print("<pre>");
			print_r($errors);
			print_r($fixes);
			exit;
            $this->app->flashNow('errors', $errors);
            $this->app->flashNow('fixes', $fixes);
        }

        $this->app->flashNow('hideRegister', true);
        $this->app->render('register.twig', array('postLoginUrl' => $this->app->urlFor('home'))); // Change 'home' to be the page to go to after registering.
    }
}
