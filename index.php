<?php
//require 'vendor/autoload.php';

//doctrine
require_once "bootstrap.php";
require_once 'entities/Msn.php';


$msn = new Msn();

/*
$productRepository = $entityManager->getRepository('Msn');
$products = $productRepository->findAll();

foreach ($products as $product) {
    echo sprintf("-%s\n", $product->getMsn());
}


$msn = new Msn();
$msn->setMsn('msn d test');
$msn->setUser('usuari');
$msn->setRoom('room');

$entityManager->persist($msn);
$entityManager->flush();
*/


$app = new \Slim\Slim(array(
    'templates.path' => './templates'
));


//html
$app->get('/', function () use ($app) {
    $app->render('/index.php');
});


//send_msn
$app->post('/send_msn', function() use ($app, $msn, $entityManager){
    $msn->setMsn( $_POST['msn'] );
    $msn->setUser( $_POST['user'] );
    $msn->setRoom( $_POST['room'] );

    $entityManager->persist($msn);
    $entityManager->flush();

    #temp
    echo json_encode($_POST);
});

//refresh
$app->post('/refresh', function(){
    #temp
    $obj[0] = array(
        'user' => 'refresh works',
        'msn' => 'msn test',
        'date' => date('H:i')
    );
    echo json_encode($obj);
});


$app->run();