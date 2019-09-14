<?php
namespace admin;

use email\Email;
use model\Client;

use model\ClientSession;
use model\Languages;
use System\Crypt;
use System\Notifier;
use System\Router;
use System\Tools;
use System\Validation;
use System\View\View;

class ClientsController extends FrontController
{
    public function actionIndex()
    {
        $view = $this->getView('empty-ajax-block.php');
        $this->layout->import('content', $view);
    }

    public function actionEditAjax()
    {
        $this->actionEdit(true);
    }

    public function actionValidateAjax()
    {
        $field = Tools::rPOST('field');
        $val   = Tools::rPOST('value');
        if ($field == 'username') {
            $client = new Client();
            $r      = $client->where('username', $val)->getRow();
            if ($r) {
                echo json_encode(['result' => 0]);
            } else {
                echo json_encode(['result' => 1]);
            }
        } else if ($field == 'email') {
            $client = new Client();
            $r      = $client->where('email', $val)->getRow();
            if ($r) {
                echo json_encode(['result' => 0]);
            } else {
                echo json_encode(['result' => 1]);
            }
        } else if ($field == 'phone') {
            $client = new Client();
            $r      = $client->where('phone', $val)->getRow();
            if ($r) {
                echo json_encode(['result' => 0]);
            } else {
                echo json_encode(['result' => 1]);
            }
        }
    }

    public function actionLoginClientFromAdmin()
    {
        $clientObject = new Client(Router::getParam('id_client'));

        if (!$clientObject->isLoadedObject()) {
            Tools::redirect('admin/clients');
        }

        $hash    = Tools::passCrypt(uniqid());
        $user_id = $clientObject->id;
        $browser = Tools::getBrowser();

        $us             = new ClientSession();
        $us->hash       = $hash;
        $us->client_id  = $user_id;
        $us->ip = 'SUPPORT AGENT';
        $us->browser = $browser['name'];
        $us->os = Tools::getOS();


        $us->save();

        $crypt = new Crypt();
        setcookie('fadm', 1, time() + 3600 * 35 * 36 * 36, '/');
        setcookie('user', $crypt->encrypt($user_id), time() + 3600 * 35 * 36 * 36, '/');
        setcookie('hash', ($hash), time() + 3600 * 35 * 36 * 36, '/');

        Tools::redirect('/');
    }

    public function actionEdit($ajax = false)
    {
        $id_user = Router::getParam('id_user');
        $view = $this->getView('client/edit.php');
        $user    = new Client($id_user);


        if (Tools::rPOST()) {

            $valid = Validation::isUserName(Tools::rPOST('username'))   &&
                     Validation::isEmail(Tools::rPOST('email'))         &&
                     Validation::isPhone(Tools::rPOST('phone'))         &&
                     Validation::isFullName(Tools::rPOST('name'));

            if ($ajax && !$valid) {
                $this->returnAjaxAnswer(0);
            }
            if (!$valid) {
                Tools::reload();
            }

            $user->name     = Tools::rPOST('name');
            $user->username = Tools::rPOST('username');
            $user->email    = Tools::rPOST('email');
            $user->phone    = Tools::rPOST('phone');
            $user->comment  = Tools::rPOST('comment');
            $user->api_enabled = Tools::rPOST('api_enabled', 0);

            if (Tools::rPOST('pass') && Tools::rPOST('pass') != '________') {
                $user->password = Tools::passCrypt(Tools::rPOST('pass'));
                $ClientSession = new ClientSession();
                $ClientSession->where('client_id', $user->id)->removeRows();
            }


            if ($user->validationFields()) {

                if(HB_DEMO_MODE && $ajax){
                    $this->returnAjaxAnswer(0, 'Функция не доступна в демо режиме!');
                } else if(HB_DEMO_MODE){
                    echo 'Function not available in the demo mode!!!';
                    exit();
                }


                if ($user->save()) {
                    if (!$id_user) {
                        Notifier::NewRegistration($user, Tools::rPOST('pass'));
                    }

                    if ($ajax) {
                        $this->returnAjaxAnswer(1, 'Клиент успешно сохранен!');
                    } else {
                        // Tools::redirect('/admin/clients');
                    }
                }
            } else {
//                print_r($errors);
                //$view->errors = $errors;
            }

        }
        $view->user = $user;

        // print_r($user);
        if (!$ajax) {
            $this->layout->import('content', $view);
        } else {
            $view->request = $this->request;
            $view->ajax    = 1;
            echo $view->fetch();
        }
    }

    public function actionEditInfoAjax()
    {

    }

    public function actionEditBalanceAjax()
    {
        $id_user = Router::getParam('id_user');
        $view = $this->getView('client/edit-balance.php');
        $user    = new Client($id_user);

        if (!$user->isLoadedObject()) {
            return;
        }

        if ($_POST) {
            $operation_type = Tools::rPOST('change_type');
            $value = Tools::rPOST('value');
            if ($operation_type === 'plus' && $value > 0){
                $user->balance += $value;
                $user->save();
            }
            elseif ($operation_type === 'minus' && $value > 0){
                /*if (($user->balance - $value) < 0){
                    $this->returnAjaxAnswer(0, 'Ошибка! Нельзя вычесть сумму, которая больше, чем текущий баланс');
                }*/

                $user->balance -= $value;
                $user->save();
            }
            else{
                echo json_encode(array('result' => 0, 'message' =>
                    Languages::translate('Ошибка!', 'admin/default', 'popup-messages')
                ));
                exit();
            }
            echo json_encode(array('result' => 1, 'message' =>
                Languages::translate('Баланс успешно изменен!', 'admin/default', 'popup-messages')
            ));
            exit();
        }
        $view->balance = $user->balance;
        $view->user_id = $user->id;
        $this->layout->import('content', $view);
    }

    public function actionInfoAjax()
    {
        $view = $this->getView('client/info.php');
        $id_client    = Router::getParam('id_client');
        $view->client = new Client($id_client);
        $this->layout->import('content', $view);
    }

    public function actionIndexAjax()
    {
        $view = $this->getView('client/list.php');
        $this->layout->import('content', $view);

/*
        for ($i=0;$i<100000; $i++ ) {
            $client = new Client();
            $client->username = 'user'.uniqid();
            $client->email = uniqid().'@gmail.com';
            $client->name = 'Test User '.uniqid();
            $client->phone = '+380'.Tools::generateCode(9, '1234567890');
            $client->password = md5(uniqid());
            $client->save();
        }
*/

        $client     = new Client();
        $view->ajax = 1;
        $order      = Tools::rPOST('order');
        if ($order['field'] && $order['type']) {
            $client->order($order['field'], $order['type']);
        }
        $filter  = Tools::rPOST('filter');
        $vfilter = array();
        if (isset($filter) && $filter != '') {
            foreach ($filter as $field => $option) {
                $value = Tools::clearXSS($option['value']);
                $type  = isset($option['type']) ? $option['type'] : 'like';
                $vfilter[$field] = $value;
                if ($field && $value != '') {
                    if ($type == 'like') {
                        $client->where($field, 'LIKE', '%' . $value . '%');
                    } else if ($type == 'equal') {
                        $client->where($field, $value);
                    }
                }
            }
        }
        $client->limit($this->from, $this->count);
        $view->filter     = $vfilter;
        $view->clients    = $client->getRows();

        
        $view->pagination = $this->pagination($client->lastQuery()->getRowsCount());

    }
    
    public function actionRemoveAjax()
    {
        $id_user = Router::getParam('id_user');
        $user    = new Client($id_user);

        if(HB_DEMO_MODE){
            $this->returnAjaxAnswer(0, 'Функция не доступна в демо режиме!');
        }

        if ($user->remove()) {
            echo json_encode(array('result' => 1));
        } else {
            echo json_encode(array('result' => 0));
        }
    }

    public function actionRemove()
    {
        $id_user = Router::getParam('id_user');
        $user    = new Client($id_user);
        $user->remove();
        Tools::redirect('/admin/clients');
    }
}