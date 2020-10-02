<?php
/**
 * Main handler that pipes everything to the appropriate controller, view, etc.
 */

use rizwanjiwan\common\web\RequestHandler;
use rizwanjiwan\common\web\routes\ControllerRoute;
use rizwanjiwan\common\web\routes\ViewRoute;

date_default_timezone_set('America/Toronto');

//update to point to the autoloader you want to use
require_once realpath(dirname(__FILE__)).'/../vendor/autoload.php';

$rh=new RequestHandler();
$rh->registerForShutdownErrors();
$rh->addRoute(new ControllerRoute('/','MyController.myMethod'));
$rh->addRoute(new ViewRoute('/somepage.xml','my.view',array('param'=>'hi','another'=>'bye')));
$rh->handle();
