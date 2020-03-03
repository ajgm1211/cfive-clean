<?php

namespace App\Helpers;

class EventCrisp{

  static $web_id = '011f006f-3864-44b5-9443-d700e87df5f7';
  static $token_indentifier = '7012d8d8-532f-4d0e-83a5-407abd5f07d3';
  static $token_key = 'bf601822e3743e4a611dc964254a71ac1347586ee691d60ce98f247e0047ae55';
  static $CrispClient;

  public function __construct(){

    self::$CrispClient = new \Crisp();
    self::$CrispClient->authenticate(self::$token_indentifier, self::$token_key);
  }

  public function findByEmail($email){
    $obj  = self::$CrispClient;   
    $people =   $obj->websitePeople->findByEmail(self::$web_id,$email);
    return $people;
  }

  public function checkIfExist($email){
    $obj  = self::$CrispClient;   
    $people =   $obj->websitePeople->checkPeopleProfileExists(self::$web_id,$email);
    return $people;


  }

  public function createProfile($params){
    $obj  = self::$CrispClient;   
    $people =  $obj->websitePeople->createNewPeopleProfile(self::$web_id, $params);
    return $people;
  }

  public function updateProfile($params,$email){
    $obj  = self::$CrispClient;
    $exist = self::checkIfExist($email);
    $people='';
    if($exist == 'true'){
      $people =  self::findByEmail($email);
      $people = $obj->websitePeople->updatePeopleProfile(self::$web_id, $people['people_id'], $params);
    }

    return $people;
  }

  public function deleteProfile($email){
    $obj  = self::$CrispClient;
    $exist = self::checkIfExist($email);
    $people='';
    if($exist == 'true'){
      $people =  self::findByEmail($email);
      $people =  $obj->websitePeople->removePeopleProfile(self::$web_id, $people['people_id']);
    }

    return $people;

  }

}