<?php

/** @Entity @Table(name="msns") */
class Msn
{
   /**
    * @Id @Column(type="integer")
    * @GeneratedValue(strategy="AUTO")
    */
   private $id;

   /** @Column(type="string", length=255) */
   private $msn;

   /** @Column(type="string", length=60) */
   private $user;

   /** @Column(type="string", length=60) */
   private $room;

   /** @Column(type="string", length=60, nullable=true) */
   private $enviat;


   function __construct(){
      $enviat = new DateTime('now');
      $this->enviat = $enviat->format('H:i');
      $this->room = 'master';
   }

   public function getId()
   {
      return $this->id;
   }

   public function getMsn()
   {
      return $this->msn;
   }

   public function setMsn($msn)
   {
      $this->msn = $msn;
   }

   public function getUser()
   {
      return $this->user;
   }

   public function setUser($user)
   {
      $this->user = $user;
   }

   public function getRoom()
   {
      return $this->room;
   }

   public function setRoom($room)
   {
      $this->room = $room;
   }

   public function getEnviat()
   {
      return $this->enviat;
   }

   public function setEnviat($enviat)
   {
      $this->enviat = $enviat;
   }

}