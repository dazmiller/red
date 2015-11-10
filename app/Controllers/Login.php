<?php
namespace Controllers;

class Login extends BaseController
{
    protected function initRoutes()
    {
        $this->app->get('/login', array($this, 'showLogin'))->
            name('login');

        $this->app->post('/login', array($this, 'loginUser'));

        $this->app->get('/logout', array($this, 'logoutUser'))->
            name('logout');

        $this->app->get('/forgot', array($this, 'forgotUser'))->
            name('forgot');

        $this->app->post('/forgot', array($this, 'doLookup'));
    }

    public function showLogin()
    {
		
        $this->redirectIfLoggedIn();

        $this->app->flashNow('hideLogin', true);
        $this->app->render('login.twig');
    }

    public function loginUser()
    {
			

        $this->redirectIfLoggedIn();

        $req = $this->app->request;
        $user = new \Models\User();


		
        if ($user->login($req->post('username'), $req->post('password')))
        {
		
            if (isset($_SESSION['redirect']))
            {
                $this->app->redirect($_SESSION['redirect']);
            }
            $this->app->redirect($this->app->urlFor('home')); // Change to default user page after login
        }
        else
        {
	
            $this->app->flashNow('loginError', true);
            $this->app->render('login.twig');
        }
    }

    public function logoutUser()
    {
        if (isset($_SESSION['user']))
        {
            unset($_SESSION['user']);
        }
        $this->app->redirect($this->app->urlFor('home'));
    }

    public function forgotUser()
    {
        $this->app->render('forgot.twig');
    }

    public function doLookup()
    {
        $req = $this->app->request;
        $user = new \Models\User();
        $email = $req->post('email');

        if ('' == $email)
        {
            $this->app->flashNow('lookupError', true);
            $this->app->render('forgot.twig');

            return;
        }

        if ('findUsername' == $req->post('lookupOptions'))
        {
            $username = $user->getUsername($email);

            if ('' == $username)
            {
                $this->app->flashNow('lookupError', true);
                $this->app->render('forgot.twig');

                return;
            }

            $this->app->flashNow('foundUsername', $username);
            $this->app->flashNow('email', $email);
            $this->app->flash('username', $username);

            $this->app->render('forgot.twig');
        }
        else if ('resetPassword' == $req->post('lookupOptions'))
        {
            list($success, $newPass) = $user->resetPassword($email);

            if (!$success)
            {
                $this->app->flashNow('lookupError', true);
                $this->app->render('forgot.twig');

                return;
            }

            $this->app->flashNow('email', $email);
            $this->app->flashNow('resetPassword', true);

            // TODO: Send email with new password.

            $this->app->render('forgot.twig');
        }
        else
        {
            $this->app->notFound();
        }
    }
}
