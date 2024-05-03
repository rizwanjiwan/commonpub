<?php

//example routes:

use rizwanjiwan\common\web\filters\CsrfAjaxFilter;
use rizwanjiwan\common\web\filters\CsrfFilter;
use rizwanjiwan\common\web\filters\TrimPostValuesFilter;
use rizwanjiwan\common\web\RequestHandler;
use rizwanjiwan\common\web\routes\ControllerRoute;

$rh=new RequestHandler();
$rh->registerForShutdownErrors();

//Controller which will render html.blade.php to show this list
$rh->addRoute(
    (new ControllerRoute('/list','ItemController.index')),
    'listItems');

//where we link to when the user clicks on a search result
$rh->addRoute(
    new ControllerRoute('/view','ItemController.view'),
    'viewItem');

//Where we send search REST requests to
$rh->addRoute(
    (new ControllerRoute('/api/search/','ItemController.searchApi'))
        ->addFilter(new CsrfAjaxFilter()),
    'filterFilesApi');

//Where we add an item
$rh->addRoute(
    (new ControllerRoute('/add/','ItemController.add'))
        ->addFilter(new TrimPostValuesFilter()),
    'addItem');
$rh->addRoute(
    (new ControllerRoute('/users/add/post','ItemController.addPost'))
        ->addFilter(new CsrfFilter(RequestHandler::getUrl('addItem')))
        ->addFilter(new TrimPostValuesFilter()),
    'addItemPost');

$rh->addRoute(
    (new ControllerRoute('/item/validate/','ItemController.validateRest'))
        ->addFilter(new CsrfAjaxFilter()),
    'itemValidate');