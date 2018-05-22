<?php


//ini_set('display_errors', 'On');
//error_reporting(E_ALL | E_STRICT);
error_reporting(0);

require_once 'vendor/autoload.php';
require_once 'controller/data.php';

Twig_Autoloader::register();

$loader = new Twig_Loader_Filesystem('controller/');
$twig = new Twig_Environment($loader, array(
    'debug' => true,
    // ...
));
$twig->addExtension(new Twig_Extension_Debug());
$d = new Data;
//var_dump($d->indexAction());

	
$template = $twig->loadTemplate('iframe.html.twig');

echo $template->render($d->indexAction()); 
 
?>