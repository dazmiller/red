<?php
namespace Models;

require_once('../vendor/PasswordCompat/password.php');

class User extends \RedBean_SimpleModel
{
    const TABLENAME = 'users';
    const DATE_FORMAT = 'Y-m-d H:i:s'; // SQL formatted date

    public function __construct()
    {
        if (isset($_SESSION['user']))
        {
            $this->bean = $_SESSION['user'];
        }
        else
        {
            $this->bean = \R::dispense(self::TABLENAME);
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
    public function create($email, $firstname, $lastname, $password, $passwordConfirm)
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

	
	// Returns two arrays, $errors and $fixes.
    // $errors contains keys of error type with boolean values
    // $fixes contains auto-keys with string values and may be empty
    public function updateProfile($req)
    {
        
		
		$state=$req->post('state');
		$postcode = $req->post('postcode');
		$city = $req->post('city');
		$housenum = $req->post('housenum');
		$housename = $req->post('housename');
		$street = $req->post('street');
		$street2 = $req->post('street2');
		$phone1=$req->post('phone1');
		$phone2=$req->post('phone2');
		$mob = $req->post('mobile');
		$contactemail=$req->post('contactemail');
		$gender = $req->post('gender');
		$dob = $req->post('dob');
		
		
        $errors = array('state' => false, 'postcode' => false,'city' => false, 'gender' => false, 'dob' =>false);
        $fixes = array();
		
		// || '' == $gender || '' == $dob
		
        if ('' == $state || '' == $postcode  || '' == $city )
        {
            $fixes[] = "All fields are required.";
        }
		// stop recursion clear sub bean
		 $this->bean['bean']=null;


	
        if (0 == count($fixes))
        {
			
            $date = date(self::DATE_FORMAT);
            //$user = $user->bean;
			
            $this->state = $state;
            $this->postcode = $postcode;
			$this->city = $city;
            $this->housenum = $housenum;
			$this->housename=$housename;
			$this->street=$street;
			$this->street2=$street2;
			$this->phone1=$phone1;
			$this->phone2=$phone2;
			$this->mob=$mob;
			$this->contactemail=$contactemail;
			$this->gender=$gender;
			$this->dob=$dob;
			
            
            \R::store($this);
			
            //$this->bean = $u;
            $_SESSION['userprofile'] = $this;
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
