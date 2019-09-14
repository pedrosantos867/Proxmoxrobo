<?php

namespace admin;

use email\Email;
use model\Employee;
use model\EmployeeReminderCode;
use model\EmployeeSession;
use System\Cookie;
use System\Crypt;
use System\Notifier;
use System\Router;
use System\Tools;

class AuthController extends FrontController{
    public $auth = 0;

    public function actionLogin()
    {

        $this->carcase = $this->getView('carcase.php');
        $this->carcase->import('content', $v = $this->getView('login.php'));

        $v->error = '';
        if (Tools::rPOST('username') && Tools::rPOST('pass')) {
            $emloyee = new Employee();
            $row     = $emloyee->where('username', Tools::rPOST('username'))->where('password', Tools::passCrypt(Tools::rPOST('pass')))->getRow();
            if ($row && $row->password === Tools::passCrypt(Tools::rPOST('pass'))) {
                $hash        = Tools::passCrypt(uniqid());
                $employee_id = $row->id;

                $us              = new EmployeeSession();
                $us->hash        = $hash;
                $us->employee_id = $employee_id;
                $us->save();

                $crypt = new Crypt();
                setcookie('employee', $crypt->encrypt($employee_id), time() + 3600 * 35 * 36 * 36, '/');
                setcookie('employee_hash', ($hash), time() + 3600 * 35 * 36 * 36, '/');

                Tools::redirect('/admin');
            } else{
                $v->error = 'login_error';
            }

        }

    }

    /**
     *
     */
    public function actionReminder()
    {

        $this->carcase = $this->getView('carcase.php');
        $this->carcase->import('content', $view = $this->getView('reminder.php'));

        if (Tools::rPOST()) {
            $user = new Employee(Employee::getInstance()->where('username', Tools::rPOST('username'))->getRow());
            if ($user->isLoadedObject()) {
                $code              = new EmployeeReminderCode();
                $code->employee_id = $user->id;
                $code->code        = Tools::generateCode(6);
                if ($code->save()) {
                    Notifier::AdminRemindPassword($user, $code->code);
                    Tools::redirect('admin/login?send_code=1');
                }

            }
        }

        if (Router::getParam('code')) {

            $code    = Router::getParam('code');
            $remcode = new EmployeeReminderCode();
            $remcode = new EmployeeReminderCode($remcode->where('code', $code)->getRow());

            if ($remcode->employee_id) {
                $employee           = new Employee($remcode->employee_id);
                $pass               = Tools::generateCode(5);
                $employee->password = Tools::passCrypt($pass);
                $employee->save();

                Notifier::AdminRemindPasswordNew($employee, $pass);

                $remcode->remove();

                Tools::redirect('admin/login?send=1');
            }
        }


    }
    public function actionLogout()
    {
        Cookie::remove('employee');
        Cookie::remove('employee_hash');
        Tools::redirect('/admin/');
    }

}