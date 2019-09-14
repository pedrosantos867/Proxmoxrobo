<?php

namespace modules\stripe\classes\stripe\Util;

use modules\stripe\classes\stripe\StripeObject;

abstract class Util
{
    private static $isMbstringAvailable = null;

    /**
     * Whether the provided array (or other) is a list rather than a dictionary.
     *
     * @param array|mixed $array
     * @return boolean True if the given object is a list.
     */
    public static function isList($array)
    {
        if (!is_array($array)) {
            return false;
        }

        // TODO: generally incorrect, but it's correct given Stripe's response
        foreach (array_keys($array) as $k) {
            if (!is_numeric($k)) {
                return false;
            }
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
            if ($v instanceof StripeObject) {
                $results[$k] = $v->__toArray(true);
            } elseif (is_array($v)) {
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
     * @param array $opts
     * @return StripeObject|array
     */
    public static function convertToStripeObject($resp, $opts)
    {
        $types = array(
            'account' => 'modules\\stripe\\classes\\stripe\\Account',
            'alipay_account' => 'modules\\stripe\\classes\\stripe\\AlipayAccount',
            'apple_pay_domain' => 'modules\\stripe\\classes\\stripe\\ApplePayDomain',
            'bank_account' => 'modules\\stripe\\classes\\stripe\\BankAccount',
            'balance_transaction' => 'modules\\stripe\\classes\\stripe\\BalanceTransaction',
            'card' => 'modules\\stripe\\classes\\stripe\\Card',
            'charge' => 'modules\\stripe\\classes\\stripe\\Charge',
            'country_spec' => 'modules\\stripe\\classes\\stripe\\CountrySpec',
            'coupon' => 'modules\\stripe\\classes\\stripe\\Coupon',
            'customer' => 'modules\\stripe\\classes\\stripe\\Customer',
            'dispute' => 'modules\\stripe\\classes\\stripe\\Dispute',
            'list' => 'modules\\stripe\\classes\\stripe\\Collection',
            'invoice' => 'modules\\stripe\\classes\\stripe\\Invoice',
            'invoiceitem' => 'modules\\stripe\\classes\\stripe\\InvoiceItem',
            'event' => 'modules\\stripe\\classes\\stripe\\Event',
            'file' => 'modules\\stripe\\classes\\stripe\\FileUpload',
            'token' => 'modules\\stripe\\classes\\stripe\\Token',
            'transfer' => 'modules\\stripe\\classes\\stripe\\Transfer',
            'transfer_reversal' => 'modules\\stripe\\classes\\stripe\\TransferReversal',
            'order' => 'modules\\stripe\\classes\\stripe\\Order',
            'order_return' => 'modules\\stripe\\classes\\stripe\\OrderReturn',
            'plan' => 'modules\\stripe\\classes\\stripe\\Plan',
            'product' => 'modules\\stripe\\classes\\stripe\\Product',
            'recipient' => 'modules\\stripe\\classes\\stripe\\Recipient',
            'refund' => 'modules\\stripe\\classes\\stripe\\Refund',
            'sku' => 'modules\\stripe\\classes\\stripe\\SKU',
            'source' => 'modules\\stripe\\classes\\stripe\\Source',
            'subscription' => 'modules\\stripe\\classes\\stripe\\Subscription',
            'subscription_item' => 'modules\\stripe\\classes\\stripe\\SubscriptionItem',
            'three_d_secure' => 'modules\\stripe\\classes\\stripe\\ThreeDSecure',
            'fee_refund' => 'modules\\stripe\\classes\\stripe\\ApplicationFeeRefund',
            'bitcoin_receiver' => 'modules\\stripe\\classes\\stripe\\BitcoinReceiver',
            'bitcoin_transaction' => 'modules\\stripe\\classes\\stripe\\BitcoinTransaction',
        );
        if (self::isList($resp)) {
            $mapped = array();
            foreach ($resp as $i) {
                array_push($mapped, self::convertToStripeObject($i, $opts));
            }
            return $mapped;
        } elseif (is_array($resp)) {
            if (isset($resp['object']) && is_string($resp['object']) && isset($types[$resp['object']])) {
                $class = $types[$resp['object']];
            } else {
                $class = 'modules\\stripe\\classes\\stripe\\StripeObject';
            }
            return $class::constructFrom($resp, $opts);
        } else {
            return $resp;
        }
    }

    /**
     * @param string|mixed $value A string to UTF8-encode.
     *
     * @return string|mixed The UTF8-encoded string, or the object passed in if
     *    it wasn't a string.
     */
    public static function utf8($value)
    {
        if (self::$isMbstringAvailable === null) {
            self::$isMbstringAvailable = function_exists('mb_detect_encoding');

            if (!self::$isMbstringAvailable) {
                trigger_error("It looks like the mbstring extension is not enabled. " .
                    "UTF-8 strings will not properly be encoded. Ask your system " .
                    "administrator to enable the mbstring extension, or write to " .
                    "support@stripe.com if you have any questions.", E_USER_WARNING);
            }
        }

        if (is_string($value) && self::$isMbstringAvailable && mb_detect_encoding($value, "UTF-8", true) != "UTF-8") {
            return utf8_encode($value);
        } else {
            return $value;
        }
    }
}
