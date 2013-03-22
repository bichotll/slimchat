<?php

require_once "bootstrap.php";
require_once 'entities/Msn.php';


$msn = new Msn();
$date = new DateTime('now');
$msn->setMsn( 'test de msn' );
$msn->setUser( 'test' );
$msn->setEnviat( 'testhora' );

$entityManager->persist($msn);
$entityManager->flush();


/*$date = new \DateTime("now");
print $date;*/

echo '<hr>';

$productRepository = $entityManager->getRepository('Msn');
$products = $productRepository->findAll();

foreach ($products as $product) {
    //echo sprintf("-%s\n", $product->getMsn());
    echo $product->getId.'|'.$product->getRoom().'- <b>'.$product->getUser().'</b>: '.$product->getMsn().'</b>: '.$product->getEnviat().'<br>';
}

echo '<hr>';

/*
$q = $entityManager->createQuery("select m from Msn m");
$msns = $q->getResult();

print_r($msns); 
*/



print '<hr>';




$qb = $entityManager->getRepository('Msn');

$qb = $entityManager->createQueryBuilder()
        ->add('select', 'm.id')
        ->add('from', 'Msn m')
        ->add('select', 'm')
        ->add('where', 'm.room = :r')
        ->add('orderBy', 'm.id DESC')
        ->setMaxResults( '1' )
        ->setParameter('r', 'master');

$query = $qb->getQuery()->execute();
print_r($query);

print $query[0]->getId();