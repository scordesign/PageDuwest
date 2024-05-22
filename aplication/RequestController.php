<?php
session_start();
require_once('controller/ProductsController.php');
require_once('controller/UserController.php');
require_once('controller/FilterController.php');
require_once('controller/sessionController.php');
require_once('controller/NewsController.php');

require_once('connection/Connection.php');


$users = new users();
$Products = new Products();
$news = new News();
$Filters = new Filters();
$session = new session();

switch (strtolower($_SERVER["REQUEST_METHOD"])) {
    case "post":

        switch ($_POST["action"]) {
            case "RegiterUser":
                echo $users->RegisterUser();
                break;
            case "LoggingUser":
                echo $users->LogUser();
                break;
            case "addProduct":
                echo $Products->addProducts();
                break;
            case "deleteDocs":
                echo $Products->deleteDocs();
                break;
            case "editProducts":
                echo $Products->editProducts();
                break;
            case "deleteProducts":
                echo $Products->deleteProducts();
                break;
            case "addNews":
                echo $news->addNews();
                break;
            case "deleteImg":
                echo $news->deleteImg();
                break;
            case "editNews":
                echo $news->editNew();
                break;
            case "deleteNews":
                echo $news->deleteNews();
                break;
        }
        break;
    case "get":
        switch ($_GET["action"]) {
            case "getSession":
                $session-> getSession();
                break;
            case "destroySession":
                echo  $session-> destroySession();
                break;
            case "getfilters":
                echo $Filters->getFilters();
                break;
            case "getProducts":
                echo $Products->getProducts();
                break;
            case "getProduct":
                echo $Products->getProduct();
                break;
            case "getNews":
                echo $news->getnews();
                break;
            case "getNew":
                echo $news->getNew();
                break;
        }

        break;
}

