<?php
namespace Tests\Models;

require_once '../vendor/RedBean/rb.php';
require_once 'Models/User.php';

class UserTest extends \PHPUnit_Framework_TestCase
{
    private $email = 'test@user.net';
    private $username = 'testuser';
    private $password = 'password!';

    protected function setUp()
    {
        $this->user = new \Models\User();
    }

    /**
    * @dataProvider providerTestCreateFixes
    */
    public function testCreateFixes($email, $username, $password, $passconf)
    {
        \R::nuke();
        list($ignore, $fixes) = $this->user->create($email, $username, $password, $passconf);

        $this->assertTrue(count($fixes) > 0);
    }

    public function providerTestCreateFixes()
    {
        return array(
            array('', 'testuser', 'testpass', 'testpass'), // Missing email
            array('test@email.com', '', 'testpass', 'testpass'), // Missing username
            array('test@email.com', 'testuser', '', 'testpass'), // Missing password
            array('test@email.com', 'testuser', 'testpass', ''), // Missing password confirmation
            array('test@email.com', 'testuser', 'testpass1', 'testpass2'), // Password mismatch
        );
    }

    public function testCreate()
    {
        \R::nuke();
        $this->user->create($this->email, $this->username, $this->password, $this->password);
        $id = $this->user->unbox()->id;

        $this->assertEquals(1, $id);
    }

    public function testConstructor()
    {
        $this->assertEquals(0, $this->user->unbox()->id);
    }

    /**
    * @depends testCreate
    */
    public function testCreateEmailError()
    {
        list($errors, $ignore) = $this->user->create($this->email, 'user', 'pass', 'pass');

        $this->assertTrue($errors['email']);
    }

    /**
    * @depends testCreate
    */
    public function testCreateUsernameError()
    {
        list($errors, $ignore) = $this->user->create('test@newemail.com', $this->username, 'pass', 'pass');

        $this->assertTrue($errors['username']);
    }

    /**
    * @depends testCreate
    */
    public function testCreatePasswordError()
    {
        list($errors, $ignore) = $this->user->create('test@newemail.com', 'user', 'pass1', 'pass');

        $this->assertTrue($errors['password']);
    }

    public function testLoginFails()
    {
        $this->assertFalse($this->user->login('', ''));
        $this->assertFalse($this->user->login('noUser', 'whatever'));
    }

    /**
    * @depends testCreate
    */
    public function testLoginSuccess()
    {
        $this->assertTrue($this->user->login($this->username, $this->password));
    }

    /**
    * @depends testLoginSuccess
    */
    public function testGetActiveUsername()
    {
        // Must login first to set the session value for the test.
        // The @depends is to make sure this has already been tested before calling.
        $_SESSION = array();
        $this->user->login($this->username, $this->password);

        $this->assertEquals($this->username, $this->user->getActiveUsername());
    }

    /**
    * @depends testLoginSuccess
    */
    public function testConstructorLoadsFromSession()
    {
        $_SESSION = array();
        $this->user->login($this->username, $this->password);

        $this->assertEquals(1, $this->user->unbox()->id);
    }

    /**
    * @depends testCreate
    */
    public function testGetUsername()
    {
        $username = $this->user->getUsername($this->email);
        $this->assertEquals($this->username, $username);
    }

    public function testGetUsernameNotFound()
    {
        $username = $this->user->getUsername('not@user.com');
        $this->assertEquals('', $username);
    }

    /**
    * @depends testCreate
    */
    public function testResetPassword()
    {
        list($success, $newPass) = $this->user->resetPassword($this->email);
        $this->assertTrue($success);
        $this->assertEquals(8, strlen($newPass));

        $user = new \Models\User();
        $this->assertFalse($user->login($this->username, $this->password));
    }

    public function testResetPasswordNotFound()
    {
        list($success, $newPass) = $this->user->resetPassword('not@user.com');
        $this->assertFalse($success);
        $this->assertEquals('', $newPass);
    }
}
