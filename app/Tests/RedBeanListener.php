<?php
require_once '../vendor/RedBean/rb.php';

class RedBeanListener extends \PHPUnit_Framework_BaseTestListener
{
    public function startTestSuite(PHPUnit_Framework_TestSuite $suite)
    {
        $dbAdapter = \R::getDatabaseAdapter();
        if (null == $dbAdapter)
        {
            \R::setup();
        }
    }

    public function endTestSuite(PHPUnit_Framework_TestSuite $suite)
    {
        \R::nuke();
        \R::close();
    }
}
