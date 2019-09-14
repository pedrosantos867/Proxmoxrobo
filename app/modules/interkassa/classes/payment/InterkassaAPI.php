<?php
namespace modules\interkassa\classes\payment;

use payment\PaymentAPI;
use System\Exception;

class InterkassaAPI  extends  PaymentAPI
{

    private $_test_mode_enable = 0;


    public function setFormAction(){
        $this->_form_action = 'https://sci.interkassa.com/';
    }

    public function getFormValues()
    {
        $fields            = $this->getFields();
        $fields['ik_sign'] = $this->getSign($fields);

        return $fields;
    }

    public function getFields()
    {
        $fields = array(
            'ik_co_id'  => $this->_shop['id'],
            'ik_am'     => $this->getAmountAsString(),
            'ik_pm_no'  => $this->getId(),
            'ik_desc'   => $this->getDescription(),
            'ik_act' => 'payways'
        );

        if ($this->_test_mode_enable) {
            $fields['ik_pw_via'] = 'test_interkassa_test_xts';
        }
        $success_url = $this->getSuccessUrl();
        $fail_url    = $this->getFailUrl();
        $status_url  = $this->getStatusUrl();
        $locale      = $this->getLocale();
        $curr        = $this->getCurrency();

        if ($locale)
            $fields['ik_loc'] = $locale;

        $fields['ik_x_baggage'] = (string)$this->getBaggage();

        if ($success_url) {
            $fields['ik_suc_u'] = (string)$success_url;
            $fields['ik_suc_m'] = (string)$this->getSuccessMethod();
        }

        if ($fail_url) {
            $fields['ik_fal_u'] = (string)$fail_url;
            $fields['ik_fal_m'] = (string)$this->getFailMethod();
        }

        if ($status_url) {
            $fields['ik_ia_u'] = (string)$status_url;
            $fields['ik_ia_m'] = (string)$this->getStatusMethod();
        }

        if ($curr)
            $fields['ik_cur'] = (string)$curr;

        return $fields;
    }

    public function getSign($fields)
    {
        unset($fields['ik_sign']); //удаляем из данных строку подписи
        ksort($fields, SORT_STRING); // сортируем по ключам в алфавитном порядке элементы массива
        array_push($fields, $this->_shop['secret_key']); // добавляем в конец массива "секретный ключ"
        $signString = implode(':', $fields); // конкатенируем значения через символ ":"
        $sign       = base64_encode(md5($signString, true)); // берем MD5 хэш в бинарном виде по сформированной строке и кодируем в BASE64
        return $sign; // возвращаем результат


    }

    final protected function _checkSignature(array $source)
    {
        $post = $source;
        unset($post['ik_sign']);
        ksort($post, SORT_STRING);
        array_push($post, $this->_shop['secret_key']);
        $signature = base64_encode(md5(implode(':', $post), true));

        return $source['ik_sign'] === $signature;
    }

    public function getPayment()
    {
        $source = $_REQUEST;

        foreach (array(
                     'ik_co_id'    => 'Shop id',
                     'ik_pm_no'    => 'Payment id',
                     'ik_am'       => 'Payment amount',
                     'ik_desc'     => 'Payment description',
                     'ik_pw_via'   => 'Payway Via',
                     'ik_sign'     => 'Payment Signature',
                     'ik_cur'      => 'Currency',
                     'ik_inv_prc'  => 'Payment Time',
                     'ik_inv_st'   => 'Payment State',
                     'ik_trn_id'   => 'Transaction',
                     'ik_ps_price' => 'PaySystem Price',
                     'ik_co_rfn'   => 'Checkout Refund'
                 ) as $field => $title)
            if (!isset($source[$field]))
                throw new Exception($title . ' not received');

        $received_id = strtoupper($source['ik_co_id']);
        $shop_id     = strtoupper($this->_shop['id']);

        if ($received_id !== $shop_id)
            throw new Exception('Received shop id does not match current shop id');

        if ($this->_checkSignature($source))
            $this->verified = true;
        else
            throw new Exception('Signature does not match the data');

        $this->_id       = $source['ik_pm_no'];
        $this->_amount   = $source['ik_am'];
        $this->_currency = $source['ik_cur'];
        $this->_state = $source['ik_inv_st'];

        return $this;

    }

}