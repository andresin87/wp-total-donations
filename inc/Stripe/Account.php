<?php

class MStripe_Account extends MStripe_SingletonApiResource
{
  /**
    * @param string|null $apiKey
    *
    * @return Stripe_Account
    */
  public static function retrieve($apiKey=null)
  {
    $class = get_class();
    return self::_scopedSingletonRetrieve($class, $apiKey);
  }
}
