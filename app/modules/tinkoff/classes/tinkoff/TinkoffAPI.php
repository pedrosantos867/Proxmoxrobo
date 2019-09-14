<?php

namespace modules\tinkoff\classes\tinkoff;

use HttpException;
use payment\PaymentAPI;
use System\Exception;

class TinkoffAPI extends PaymentAPI
{
    public function setFormAction()
    {
        return null;
    }

    public function getFormValues()
    {
        return null;
    }

    public function getFields()
    {
        return null;
    }

    public function getSign($fields)
    {
        return null;
    }

    final protected function _checkSignature(array $source)
    {
        $token = $source['Token'];
        unset($source['Token']);
        return $token == $this->_genToken($source);
    }


    public function getPayment()
    {
        $source = $_GET;

        if (!$source || empty($source) || !isset($source['Success'])) {
            throw new Exception('Source not exist');
        }


        if ($this->_checkSignature($source))
            $this->verified = true;
        else {
            throw new Exception('Signature does not match the data');
        }
        $params = array();
        if ($source['TerminalKey'] != $this->_shop['terminal_name']) {
            throw new Exception('TerminalKey is not equal');
        }


        $this->_id = $params['OrderId'];
        $this->_amount = $params['Amount'] / 100;
        $this->_state = $params['Status'];

        return $this;

    }

    public function createPayment(array $options)
    {
        $data = array(
            'Language' => 'ru',
            'Amount' => $options['amount'] * 100,
            'OrderId' => $options['id'],
            'Description' => $options['description'],
            'DATA' => 'Email=' . urlencode($options['email'])
        );
        return $this->buildQuery('Init', $data);
    }

    public function __get($name)
    {
        switch ($name) {
            case 'paymentId':
                return $this->_paymentId;
            case 'status':
                return $this->_status;
            case 'error':
                return $this->_error;
            case 'paymentUrl':
                return $this->_paymentUrl;
            case 'response':
                return htmlentities($this->_response);
            default:
                if ($this->_response) {
                    if ($json = json_decode($this->_response, true)) {
                        foreach ($json as $key => $value) {
                            if (strtolower($name) == strtolower($key)) {
                                return $json[$key];
                            }
                        }
                    }
                }

                return false;
        }
    }

    public function getState($args)
    {
        return $this->buildQuery('GetState', $args);
    }

    /**
     * Confirm 2-staged payment
     *
     * @param mixed $args Can be associative array or string
     *
     * @return mixed
     */

    public function confirm($args)
    {
        return $this->buildQuery('Confirm', $args);
    }

    /**
     * Performs recursive (re) payment - direct debiting of funds from the
     * account of the Buyer's credit card.
     *
     * @param mixed $args Can be associative array or string
     *
     * @return mixed
     */
    public function charge($args)
    {
        return $this->buildQuery('Charge', $args);
    }

    /**
     * Registers in the terminal buyer Seller. (Init do it automatically)
     *
     * @param mixed $args Can be associative array or string
     *
     * @return mixed
     */
    public function addCustomer($args)
    {
        return $this->buildQuery('AddCustomer', $args);
    }

    /**
     * Returns a list of bounded card from the buyer.
     *
     * @param mixed $args Can be associative array or string
     *
     * @return mixed
     */
    public function getCardList($args)
    {
        return $this->buildQuery('GetCardList', $args);
    }


    /**
     * Builds a query string and call sendRequest method.
     * Could be used to custom API call method.
     *
     * @param string $path API method name
     * @param mixed $args query params
     *
     * @return mixed
     * @throws HttpException
     */
    public function buildQuery($path, $args)
    {
        $url = $this->_shop['url'];
        if (is_array($args)) {
            if (!array_key_exists('TerminalKey', $args)) {
                $args['TerminalKey'] = $this->_shop['terminal_name'];
            }
            if (!array_key_exists('Token', $args)) {
                $args['Token'] = $this->_genToken($args);
            }
        }
        $url = $this->_combineUrl($url, $path);
        return $this->_sendRequest($url, $args);
    }

    /**
     * Generates token
     *
     * @param array $args array of query params
     *
     * @return string
     */
    private function _genToken($args)
    {
        $token = '';
        $args['Password'] = $this->_shop['secret_key'];
        ksort($args);
        $token = implode('', $args);
        $token = hash('sha256', $token);

        return $token;
    }

    /**
     * Combines parts of URL. Simply gets all parameters and puts '/' between
     *
     * @return string
     */
    private function _combineUrl()
    {
        $args = func_get_args();
        $url = '';
        foreach ($args as $arg) {
            if (is_string($arg)) {
                if ($arg[strlen($arg) - 1] !== '/') {
                    $arg .= '/';
                }
                $url .= $arg;
            } else {
                continue;
            }
        }

        return $url;
    }

    /**
     * Main method. Call API with params
     *
     * @param string $api_url API Url
     * @param array $args API params
     *
     * @return mixed
     */
    private function _sendRequest($api_url, $args)
    {
        $this->_error = '';
        //todo add string $args support
        //$proxy = 'http://192.168.5.22:8080';
        //$proxyAuth = '';
        if (is_array($args)) {
            $args = http_build_query($args);
        }
        if ($curl = curl_init()) {
            curl_setopt($curl, CURLOPT_URL, $api_url);
            curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $args);
            $out = curl_exec($curl);

            $this->_response = $out;
            $json = json_decode($out);
            if ($json) {
                if (@$json->ErrorCode !== "0") {
                    $this->_error = @$json->Details;
                } else {
                    $this->_paymentUrl = @$json->PaymentURL;
                    $this->_paymentId = @$json->PaymentId;
                    $this->_status = @$json->Status;
                }
            }

            curl_close($curl);

            return $out;

        } else {
            throw new HttpException(
                'Can not create connection to ' . $api_url . ' with args '
                . $args, 404
            );
        }
    }

}