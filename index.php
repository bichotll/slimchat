<?php
//require 'vendor/autoload.php';

//doctrine
require_once "bootstrap.php";
require_once 'entities/Msn.php';



$app = new \Slim\Slim(array(
    'templates.path' => './templates'
));

//html
$app->get('/', function () use ($app) {
    $app->render('/index.php');
});


//send_msn
$app->post('/send_msn', function() use ($app, $entityManager){
    $msn = new Msn();
    $date = new DateTime('now');

    $msn->setMsn( $_POST['msn'] );
    $msn->setUser( $_POST['user'] );
    $msn->setRoom( $_POST['room'] );
    $msn->setEnviat($date->format('H:i'));

    $entityManager->persist($msn);
    $entityManager->flush();
});

//refresh
$app->post('/refresh', function() use ($app, $msn, $entityManager){
    $qb = $entityManager->createQueryBuilder();
    $qb->add('select', 'm.id')
            ->add('from', 'Msn m')
            ->add('select', 'm')
            ->add('where', 'm.room = :r')
            ->add('orderBy', 'm.id DESC')
            ->setMaxResults( '1' )
            ->setParameter('r', $_POST['room']);
    $query = $qb->getQuery()->getResult();
    
    $qb2 = $entityManager->createQueryBuilder();
    
    if ( !isset($_POST['last_id_group']) )
        $_POST['last_id_group'] = 0;
    
    
    
    if ( $query[0]->getId() != $_POST['last_id_group'] ){
        $qb2->add('select', 'm.id')
                ->add('from', 'Msn m')
                ->add('select', 'm')
                ->add('where', 'm.room = :room and m.id > :lastid')
                ->add('orderBy', 'm.id DESC')
                ->setMaxResults( '10' )
                ->setParameters(
                        array(
                    'room' => $_POST['room'],
                    'lastid' => $_POST['last_id_group']
                )
                        );
        $qry = $qb2->getQuery()->getArrayResult();
        echo json_encode($qry);
    } else {
        print true;
    }
});


$app->run();