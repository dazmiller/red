<?php
spl_autoload_register(function ($className)
{
    $baseDir = __DIR__;

    if (substr($baseDir, -strlen($className)) === $className)
    {
        $baseDir = substr($baseDir, 0, -strlen($className));
    }

    $className = ltrim($className, '\\');
    $fileName  = $baseDir . DIRECTORY_SEPARATOR;
    $namespace = '';

    if ($lastNsPos = strripos($className, '\\'))
    {
        $namespace = substr($className, 0, $lastNsPos);
        $className = substr($className, $lastNsPos + 1);
        $fileName  .= str_replace('\\', DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR;
    }

    $fileName .= str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php';

    if (file_exists($fileName))
    {
        require_once $fileName;
    }
});
