<?php

abstract class MStripe_Util
{
  /**
   * Whether the provided array (or other) is a list rather than a dictionary.
   *
   * @param array|mixed $array
   * @return boolean True if the given object is a list.
   */
  public static function isList($array)
  {
    if (!is_array($array))
      return false;

    // TODO: generally incorrect, but it's correct given Stripe's response
    foreach (array_keys($array) as $k) {
      if (!is_numeric($k))
        return false;
    }
    return true;
  }

  /**
   * Recursively converts the PHP Stripe object to an array.
   *
   * @param array $values The PHP Stripe object to convert.
   * @return array
   */
  public static function convertStripeObjectToArray($values)
  {
    $results = array();
    foreach ($values as $k => $v) {
      // FIXME: this is an encapsulation violation
      if ($k[0] == '_') {
        continue;
      }
      if ($v instanceof MStripe_Object) {
        $results[$k] = $v->__toArray(true);
      } else if (is_array($v)) {
        $results[$k] = self::convertStripeObjectToArray($v);
      } else {
        $results[$k] = $v;
      }
    }
    return $results;
  }

  /**
   * Converts a response from the Stripe API to the corresponding PHP object.
   *
   * @param array $resp The response from the Stripe API.
   * @param string $apiKey
   * @return Stripe_Object|array
   */
  public static function convertToStripeObject($resp, $apiKey)
  {
    $types = array(
      'card' => 'MStripe_Card',
      'charge' => 'MStripe_Charge',
      'coupon' => 'MStripe_Coupon',
      'customer' => 'MStripe_Customer',
      'list' => 'MStripe_List',
      'invoice' => 'MStripe_Invoice',
      'invoiceitem' => 'MStripe_InvoiceItem',
      'event' => 'MStripe_Event',
      'transfer' => 'MStripe_Transfer',
      'plan' => 'MStripe_Plan',
      'recipient' => 'MStripe_Recipient',
      'refund' => 'MStripe_Refund',
      'subscription' => 'MStripe_Subscription',
      'fee_refund' => 'MStripe_ApplicationFeeRefund',
      'bitcoin_receiver' => 'MStripe_BitcoinReceiver',
      'bitcoin_transaction' => 'MStripe_BitcoinTransaction'
    );
    if (self::isList($resp)) {
      $mapped = array();
      foreach ($resp as $i)
        array_push($mapped, self::convertToStripeObject($i, $apiKey));
      return $mapped;
    } else if (is_array($resp)) {
      if (isset($resp['object'])
          && is_string($resp['object'])
          && isset($types[$resp['object']])) {
        $class = $types[$resp['object']];
      } else {
        $class = 'MStripe_Object';
      }
      return MStripe_Object::scopedConstructFrom($class, $resp, $apiKey);
    } else {
      return $resp;
    }
  }
}
