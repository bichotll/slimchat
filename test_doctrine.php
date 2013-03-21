<?php

require_once "bootstrap.php";
require_once 'entities/Msn.php';



$msn = new Msn();
$msn->setMsn('_msn d test');
$msn->setUser('usuari');
$msn->setRoom('room');

$entityManager->persist($msn);
$entityManager->flush();





$productRepository = $entityManager->getRepository('Msn');
$products = $productRepository->findAll();

foreach ($products as $product) {
    //echo sprintf("-%s\n", $product->getMsn());
    echo $product->getRoom().'-<b>'.$product->getUser().'</b>: '.$product->getMsn().'<br>';
}