<?php
/**
  * User model
  *
  * This is the model for user data.
  * User data consists of application settings as well as profile information.
  * Application settings include things like default permissions and auto increment ids.
  * Profile information includes things like email address.
  * @author Jaisen Mathai <jaisen@jmathai.com>
  */
class User
{
  /**
    * A user object that caches the value once it's been fetched from the remote datasource.
    * @access private
    * @var array
    */
  private static $user;

  /**
    * Get the next ID to be used for a photo.
    * The ID is a base 32 string that represents an autoincrementing integer.
    * @return string 
    */
  public static function getNextPhotoId()
  {
    $user = self::getUserRecord();    
    if($user === false)
      return false;

    if(!isset($user['lastPhotoId']))
      $user['lastPhotoId'] = '';
    $nextIntId = base_convert($user['lastPhotoId'], 31, 10) + 1;
    $nextId = base_convert($nextIntId, 10, 31);
    self::update(array('lastPhotoId' => $nextId));
    return $nextId;
  }

  /**
    * Get the next ID to be used for an action.
    * The ID is a base 32 string that represents an autoincrementing integer.
    * @return string 
    */
  public static function getNextActionId()
  {
    $user = self::getUserRecord();    
    if($user === false)
      return false;

    if(!isset($user['lastActionId']))
      $user['lastActionId'] = '';
    $nextIntId = base_convert($user['lastActionId'], 31, 10) + 1;
    $nextId = base_convert($nextIntId, 10, 31);
    self::update(array('lastActionId' => $nextId));
    return $nextId;
  }

  /**
    * Get the user record from the remote database.
    * If the record does not exist then attempt to create it before returning.
    * Returns false if no user record could be obtained or crated.
    * Returns the user array on success.
    *
    * @return mixed  FALSE on error, array on success
    */
  public static function getUserRecord()
  {
    // we cache the user entry per request
    if(self::$user)
      return self::$user;

    $res = getDb()->getUser();
    // if null create, onerror return false
    if($res === null)
    {
      // user entry does not exist, create it
      $res = self::create();
      if(!$res)
        return false;
    }
    elseif($res === false)
    {
      return false;
    }

    self::$user = $res;
    return self::$user;
  }

  public static function isOwner()
  {
  }

  /**
    * Create a new user record.
    * This method should only be called if no record already exists.
    * The user record has a key of 1 and default attributes specified by self::getDefaultAttributes().
    * Differs from self::update on the implementation at the adapter layer.
    *
    * @return boolean 
    */
  private static function create()
  {
    return getDb()->putUser(1, self::getDefaultAttributes());
  }

  /**
    * Default attributes for a new user record.
    *
    * @return array 
    */
  private static function getDefaultAttributes()
  {
    return array('lastPhotoId' => '', 'lastActionId' => '');
  }

  /**
    * Update an existing user record.
    * This method updates an existing user record.
    * Differs from self::create on the implementation at the adapter layer.
    *
    * The user record has a key of 1 and default attributes specified by self::getDefaultAttributes().
    *
    * @return boolean 
    */
  private static function update($params)
  {
    return getDb()->postUser(1, $params);
  }
}