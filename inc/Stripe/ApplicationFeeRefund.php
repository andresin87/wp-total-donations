<?php

class MStripe_ApplicationFeeRefund extends MStripe_ApiResource
{
  /**
   * @return string The API URL for this Stripe refund.
   */
  public function instanceUrl()
  {
    $id = $this['id'];
    $fee = $this['fee'];
    if (!$id) {
      throw new MStripe_InvalidRequestError(
          "Could not determine which URL to request: " .
          "class instance has invalid ID: $id",
          null
      );
    }
    $id = MStripe_ApiRequestor::utf8($id);
    $fee = MStripe_ApiRequestor::utf8($fee);

    $base = self::classUrl('Stripe_ApplicationFee');
    $feeExtn = urlencode($fee);
    $extn = urlencode($id);
    return "$base/$feeExtn/refunds/$extn";
  }

  /**
   * @return Stripe_ApplicationFeeRefund The saved refund.
   */
  public function save()
  {
    $class = get_class();
    return self::_scopedSave($class);
  }
}
