<?php

namespace App\Helpers;

class EventCrisp{

  static $web_id = '011f006f-3864-44b5-9443-d700e87df5f7';
  static $token_indentifier = 'e25cbac8-cfa6-4c5c-8bf1-bd7b202f3bb5';
  static $token_key = '728d485a6b67ce9c6538627290e7b1c2c0cb9f8e7d52b9a935a3f6c0b9a068d5';
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