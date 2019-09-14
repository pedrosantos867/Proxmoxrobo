<?php

namespace modules\payture\classes\payture;

use payment\PaymentAPI;
use System\Exception;
use System\Logger;
use System\Tools;

class PaytureAPI extends PaymentAPI
{
    const PROTOCOL = 'https://';
    const HOST = 'payture.com/apim/';
    //API Methods
    const INIT = 'Init?';
    const PAY = 'Pay?';
    const CHARGE = 'Charge?';
    const UNBLOCK = 'Unblock?';
    const REFUND = 'Refund?';
    const PAYSTATUS = 'PayStatus?';
    protected $sessionId;
    private $_host;
    private $_key;
    private $_password;
    private $_requestUrl;
    private $_clientIP;
    private $_params;
    protected $transactionStates = array(
        'New' => 'платеж зарегистрирован в шлюзе, но его обработка в процессинге не начата',
        'PreAuthorized3DS' => 'пользователь начал аутентификацию по протоколу 3D Secure, на этом операции по платежу завершились',
        'PreAuthorizedAF' => 'пользователь начал аутентификацию с помощью сервиса AntiFraud, на этом операции по платежу завершились',
        'Authorized' => 'средства заблокированы, но не списаны (2-х стадийный платеж)',
        'Voided' => 'средства на карте были заблокированы и разблокированы (2-х стадийный платеж)',
        'Charged' => 'денежные средства списаны с карты Пользователя, платёж завершен успешно',
        'Refunded' => 'успешно произведен полный или частичный возврат денежных средств на карту Пользователя',
        'Rejected' => 'последняя операция по платежу отклонена',
        'Error' => 'последняя операция по платежу завершена с ошибкой'
    );
    protected $errors = array(
        'NONE' => 'Операция выполнена без ошибок',
        'ACCESS_DENIED' => 'Доступ с текущего IP или по указанным параметрам запрещен',
        'AMOUNT_ERROR' => 'Неверно указана сумма транзакции',
        'AMOUNT_EXCEED' => 'Сумма транзакции превышает доступный остаток средств на выбранно счете',
        'API_NOT_ALLOWED' => 'Данный API не разрешен к использованию',
        'COMMUNICATE_ERROR' => 'Ошибка возникла при передаче данных в МПС',
        'DUPLICATE_ORDER_ID' => 'Номер заказа уже использовался ранее',
        'DUPLICATE_CARD' => 'Карта уже зарегистрирована',
        'DUPLICATE_USER' => 'Пользователь уже зарегистрирован',
        'EMPTY_RESPONSE' => 'Ошибка процессинга',
        'FORMAT_ERROR' => 'Неверный формат переданных данных',
        'FRAUD_ERROR' => 'Недопустимая транзакция согласно настройкам антифродового фильтра',
        'FRAUD_ERROR_BIN_LIMIT' => 'Превышен лимит по карте(BINу, маске) согласно настройкам антифродового фильтра',
        'FRAUD_ERROR_BLACKLIST_BANKCOUNTRY' => 'Страна данного BINа находится в черном списке или не находится в списке допустимых стран',
        'FRAUD_ERROR_BLACKLIST_AEROPORT' => 'Аэропорт находится в черном списке',
        'FRAUD_ERROR_BLACKLIST_USERCOUNTRY' => 'Страна данного IP находится в черном списке или не находится в списке допустимых стран',
        'FRAUD_ERROR_CRITICAL_CARD' => 'Номер карты(BIN, маска) внесен в черный список антифродового фильтра',
        'FRAUD_ERROR_CRITICAL_CUSTOMER' => 'IP-адрес внесен в черный список антифродового фильтра',
        'ILLEGAL_ORDER_STATE' => 'Попытка выполнения недопустимой операции для текущего состояния платежа',
        'INTERNAL_ERROR' => 'Неправильный формат транзакции с точки зрения сети',
        'INVALID_FORMAT' => 'Неправильный формат транзакции с точки зрения сети',
        'ISSUER_CARD_FAIL' => 'Банк-эмитент запретил интернет транзакции по карте',
        'ISSUER_FAIL' => 'Владелец карты пытается выполнить транзакцию, которая для него не разрешена банком-эмитентом, либо внутренняя ошибка эмитента',
        'ISSUER_LIMIT_FAIL' => 'Предпринята попытка, превышающая ограничения банка-эмитента на сумму или количество операций в определенный промежуток времени',
        'ISSUER_LIMIT_AMOUNT_FAIL' => 'Предпринята попытка выполнить транзакцию на сумму, превышающую (дневной) лимит, заданный банком-эмитентом',
        'ISSUER_LIMIT_COUNT_FAIL' => 'Превышен лимит на число транзакций: клиент выполнил максимально разрешенное число транзакций в течение лимитного цикла и пытается провести еще одну',
        'ISSUER_TIMEOUT' => 'Нет связи с банком эмитентом',
        'LIMIT_EXCHAUST' => 'Время или количество попыток, отведенное для ввода данных, исчерпано',
        'MERCHANT_RESTRICTION' => 'Превышен лимит Магазина или транзакции запрещены Магазину',
        'NOT_ALLOWED' => 'Отказ эмитента проводить транзакцию. Чаще всего возникает при запретах наложенных на карту',
        'OPERATION_NOT_ALLOWED' => 'Действие запрещено',
        'ORDER_NOT_FOUND' => 'Не найдена транзакция',
        'ORDER_TIME_OUT' => 'Время платежа (сессии) истекло',
        'PROCESSING_ERROR' => 'Ошибка функционирования системы, имеющая общий характер. Фиксируется платежной сетью или банком-эмитентом',
        'PROCESSING_TIME_OUT' => 'Таймаут в процессинге',
        'REAUTH_NOT_ALOWED' => 'Изменение суммы авторизации не может быть выполнено',
        'REFUND_NOT_ALOWED' => 'Возврат не может быть выполнен',
        'REFUSAL_BY_GATE' => 'Отказ шлюза в выполнении операции',
        'RETRY_LIMIT_EXCEEDED' => 'Превышено допустимое число попыток произвести возврат (Refund)',
        'THREE_DS_FAIL' => 'Невозможно выполнить 3DS транзакцию',
        'THREE_DS_TIME_OUT' => 'Срок действия транзакции был превышен к моменту ввода данных карты',
        'USER_NOT_FOUND' => 'Пользователь не найден',
        'WRONG_CARD_INFO' => 'Введены неверные параметры карты',
        'WRONG_CARD_PAN' => 'Неверный номер карты',
        'WRONG_CARDHOLDER_NAME' => 'Недопустимое имя держателя карты',
        'WRONG_PARAMS' => 'Неверный набор или формат параметров',
        'WRONG_PAY_INFO' => 'Некорректный параметр PayInfo (неправильно сформирован или нарушена криптограмма)',
        'WRONG_AUTH_CODE' => 'Неверный код активации',
        'WRONG_CARD' => 'Переданы неверные параметры карты, либо карта в недопустимом состоянии',
        'WRONG_CONFIRM_CODE' => 'Неверный код подтверждения',
        'WRONG_USER_PARAMS' => 'Пользователь с такими параметрами не найден',
    );

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
        return null;
    }


    public function init(array $options)
    {
        $url = self::PROTOCOL . $this->_shop['host'] . '.' . self::HOST . self::INIT;
        $params = array();
        $params[] = 'SessionType=Pay';
        $params[] = 'OrderId=' . $options['id'];
        $params[] = 'Product=' . $options['description'];
        $params[] = 'Total=' . ($options['amount'] * 100);
        $params[] = 'Amount=' . ($options['amount'] * 100);
        $params[] = 'IP=' . $this->getRealIP();
        $params[] = 'Url=' . $this->_shop['host_serv'] . '/modules/payture/return?id_bill={orderid}&success={success}';

        $url .= 'Key=' . $this->_shop['key'] . '&Data=' . $this->getRequestDataUrlString($params);
        $responseXML = $this->getResponseXML($url);
        if (!$responseXML['success']) {
            //Если ошибка, то вернем ошибку
            return $responseXML;
        }
        $responseAttributes = $this->getXmlAttributesArray($responseXML['data']);
        $checkSessionId = $this->isResponseSucceed($responseAttributes);

        if (!$checkSessionId['success']) {
            return $checkSessionId;
        }
        return array(
            'success' => true,
            'SessionId' => $responseAttributes['SessionId']
        );
    }


    public function createPayment(array $options)
    {
        $rezult_init = $this->init($options);
        $sessionId = "";
        if ($rezult_init['success']) {
            $sessionId = $rezult_init['SessionId'];
        } else {
            return $rezult_init;
        }
        return $this->pay($sessionId);


    }

    public function getPayment()
    {

    }

    //Process payment
    public function pay($sessionId)
    {
        return self::PROTOCOL . $this->_shop['host'] . '.' . self::HOST . self::PAY . 'SessionId=' . $sessionId;
    }

    //Check payment
    public function payStatus($orderId)
    {
        $url = self::PROTOCOL . $this->_shop['host'] . '.' . self::HOST . self::PAYSTATUS;
        $url .= 'Key=' . $this->_shop['key'] . '&OrderId=' . $orderId;
        $responseXML = $this->getResponseXML($url);
        if (!$responseXML['success']) {
            //Если ошибка, то вернем ошибку
            return $responseXML;
        }
        $responseAttributes = $this->getXmlAttributesArray($responseXML['data']);
        $checkSessionId = $this->isResponseSucceed($responseAttributes);
        if (!$checkSessionId['success']) {
            return $checkSessionId;
        }
        $this->_id = $responseAttributes['OrderId'];
        $this->_amount = $responseAttributes['Amount'] / 100;
        $this->_description = $this->transactionStates[$responseAttributes['State']];
        $this->_state = $responseAttributes['State'];
        if ($this->_state == true) {
            $this->verified = true;

        } else {
            $this->verified = false;
        }
    }

    public function isResponseSucceed($attributesArray)
    {
        if ($attributesArray['Success'] == 'False') {
            return array(
                'success' => false,
                'error' => $this->errors[$attributesArray['ErrCode']],
                'code' => $attributesArray['ErrCode']
            );
        }
        return array(
            'success' => true
        );
    }

    public function getXmlAttributesArray($xmlNode)
    {
        $attributes = (array)$xmlNode->attributes();
        return $attributes = $attributes['@attributes'];
    }

    public function getResponseXML($path)
    {
        if (($responseXmlData = file_get_contents($path)) === false) {
            return array(
                'success' => false,
                'error' => 'Error fetching XML'
            );
        }
        libxml_use_internal_errors(true);
        $data = simplexml_load_string($responseXmlData);
        if (!$data) {
            return array(
                'success' => false,
                'error' => 'Error fetching XML\n' . implode('\n', libxml_get_errors())
            );
        }
        return array(
            'success' => true,
            'data' => $data
        );
    }

    public function getRequestDataUrlString($params)
    {
        return urlencode(implode(';', $params));
    }

    public function getRealIP()
    {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {//check ip from share internet
            return $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) { //to check ip is pass from proxy
            return $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            return $_SERVER['REMOTE_ADDR'];
        }
    }

}