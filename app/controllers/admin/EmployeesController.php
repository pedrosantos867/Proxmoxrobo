<?php

namespace admin;

use email\Email;
use model\Client;
use model\Employee;
use model\EmployeeReminderCode;
use model\EmployeeSession;
use model\Languages;
use System\Cookie;
use System\Crypt;
use System\Router;
use System\Tools;
use System\Validation;
use System\View\View;

class EmployeesController extends FrontController
{

    public function actionListAjax()
    {
        $this->actionList(true);
    }

    public function actionList($ajax = false)
    {
        $view = $this->getView('employee/list.php');

        $employee   = new Employee();
        $view->ajax = 1;
        $order      = Tools::rPOST('order');
        if ($order['field'] && $order['type']) {
            $employee->order($order['field'], $order['type']);
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
                        $employee->where($field, 'LIKE', '%' . $value . '%');
                    } else if ($type == 'equal') {
                        $employee->where($field, $value);
                    }


                }
            }
        }
        $employee->limit($this->from, $this->count);
        $view->filter     = $vfilter;
        $view->employees  = $employee->getRows();
        $this->pagination($employee->lastQuery()->getRowsCount());

        $this->layout->import('content', $view);

    }

    public function actionEditAjax()
    {
        $this->actionEdit(true);
    }

    public function actionEdit($ajax = false)
    {

        $id_user = Router::getParam('id_user');
        $view = $this->getView('employee/edit.php');
        $user    = new Employee($id_user);

        if (Tools::rPOST()) {

            $valid = Validation::isUserName(Tools::rPOST('username'))   &&
                     Validation::isEmail(Tools::rPOST('email'))         &&
                     Validation::isFullName(Tools::rPOST('name'));

            if (!$valid) Tools::reload();

            $user->name     = Tools::rPOST('name');
            $user->username = Tools::rPOST('username');
            $user->email    = Tools::rPOST('email');
            //    $user->phone    = Tools::rPOST('phone');

            if (Tools::rPOST('pass') && Tools::rPOST('pass') != '________') {
                $user->password = Tools::passCrypt(Tools::rPOST('pass'));

                $EmployeeSession = new EmployeeSession();
                $EmployeeSession->where('employee_id', $user->id)->removeRows();
            }


            if ($user->validationFields()) {

                if(HB_DEMO_MODE && $ajax){
                    $this->returnAjaxAnswer(0, 'Функция не доступна в демо режиме!');
                }

                if ($user->save()) {
                    if ($ajax) {
                        $this->returnAjaxAnswer(1, 'Информация о администраторе успешно сохранена!');
                    } else {

                    }
                } else {
                    if ($ajax) {
                        $this->returnAjaxAnswer(0, 'Ошибка сохранения!');
                    } else {

                    }
                }
            } else {

                if ($ajax) {
                    $this->returnAjaxAnswer(0, 'Возникла ошибка сохранения!');
                } else {

                }
            }

        }


        $view->user = $user;
        $this->layout->import('content', $view);
    }

    public function actionRemoveAjax()
    {

        $id_user = Router::getParam('id_user');
        $user    = new Employee($id_user);

        if(HB_DEMO_MODE){
            $this->returnAjaxAnswer(0, 'Функция не доступна в демо режиме!');
        }

        $user->remove();
        echo json_encode(array('result' => 1));
    }

    public function actionValidateAjax()
    {
        $field = Tools::rPOST('field');
        $val   = Tools::rPOST('value');
        if ($field == 'username') {
            $employee = new Employee();
            $r        = $employee->where('username', $val)->getRow();
            if ($r) {
                echo json_encode(['result' => 0]);
            } else {
                echo json_encode(['result' => 1]);
            }
        }
    }
}