<?php
namespace Models;

require_once('../vendor/PasswordCompat/password.php');

class LifeInsurance extends \RedBean_SimpleModel
{
    const TABLENAME = 'lifeinsurance';
    const DATE_FORMAT = 'Y-m-d H:i:s'; // SQL formatted date

    public function __construct()
    {
        if (isset($_SESSION['user']))
        {
           // $this->bean = $_SESSION['user'];
        }
        else
        {
           // $this->bean = \R::dispense(self::TABLENAME);
        }
    }

    public static function getActiveUsername()
    {
        if (isset($_SESSION['user']))
        {
            return $_SESSION['user']->username;
        }

        return '';
    }

    public function login($username, $password)
    {
			  //  \R::debug( TRUE );
        $user = \R::findOne(self::TABLENAME, 'email = ?', [$username]);

        if (null == $user)
        {
            return false;
        }

        $hash = password_hash($password, PASSWORD_BCRYPT, array('salt' => $user->salt));
        if ($hash != $user->password)
        {
            return false;
        }

        $user->last_login = date(self::DATE_FORMAT);
        \R::store($user);

        $this->bean = $user;
        $_SESSION['user'] = $user;

        return true;
    }

    // Returns two arrays, $errors and $fixes.
    // $errors contains keys of error type with boolean values
    // $fixes contains auto-keys with string values and may be empty
    public function create($userid, $firstname, $lastname, $password, $passwordConfirm)
    {

			
        $errors = array('email' => false, 'firstname' => false,'lastname' => false, 'password' => false);
        $fixes = array();
		
		

        if ('' == $email || '' == $firstname  || '' == $lastname || '' == $password)
        {
            $fixes[] = "All fields are required.";
        }


		
        if (0 != \R::count(self::TABLENAME, 'email = ?', [$email]))
        {
            $errors['email'] = true;
            $fixes[] = 'That email address is already in use.';
        }

      /*  if (0 != \R::count(self::TABLENAME, 'username = ?', [$username]))
        {
            $errors['username'] = true;
            $fixes[] = 'That username is already in use.';
        }
	*/
        if ($password != $passwordConfirm)
        {
            $errors['password'] = true;
            $fixes[] = 'The passwords entered do not match.';
        }

        if (0 == count($fixes))
        {
		
				
            $date = date(self::DATE_FORMAT);
            $user = $this->bean;

            $user->email = $email;
            $user->firstname = $firstname;
			$user->lastname = $lastname;
            $user->salt = password_hash($email . $date, PASSWORD_BCRYPT);
            $user->password = password_hash($password, PASSWORD_BCRYPT, array('salt' => $user->salt));
            $user->created = $date;
            $user->last_login = null;
            $user->loginAttempts = 0;
			$user->identifier="ident"; // used by hybridauth
			$user->avatar_url = "somewhere"; // used by hybridauth
            // Add any other attributes you want a User to have here (or not,
            // Redbean will add them when you use them if the DB isn't frozen).
            \R::store($user);

            $this->bean = $user;
            $_SESSION['user'] = $user;
        }

        return array($errors, $fixes);
    }

    public function getUsername($email)
    {
        $user = \R::findOne(self::TABLENAME, 'email = ?', [$email]);
        if (null == $user)
            return '';

        return $user->username;
    }

    public function resetPassword($email)
    {
        $user = \R::findOne(self::TABLENAME, 'email = ?', [$email]);
        if (null == $user)
        {
            return array(false, '');
        }

        $newPassword = $this->generatePassword(8);
        // TODO: Change this to create a one-time key instead of changing the password.
        // Then, the user can follow a link from an email to reset when they're ready.
        $user->password = password_hash($newPassword, PASSWORD_BCRYPT, array('salt' => $user->salt));
        $user->resetRequired = true;
        \R::store($user);

        return array(true, $newPassword);
    }

    private function generatePassword($length)
    {
        $key = '';
        $keys = array_merge(range(0, 9), range('a', 'z'), range('A', 'Z'), range('!', '+'));

        for ($i = 0; $i < $length; $i++) {
            $key .= $keys[array_rand($keys)];
        }

        return $key;
    }
}
