<?php

use model\Client;
use model\Employee;
use System\Tools;

class ApiController extends IndexController {

    const ACCESS_LEVEL_NULL  = 0;
    const ACCESS_LEVEL_USER  = 1;
    const ACCESS_LEVEL_ADMIN = 2;

    public $access = 0;

    public $data;

    public function __construct()
    {

        parent::__construct();

        $this->auth();
    }

    protected function auth(){

        $username = Tools::rPOST('username');
        $password = Tools::rPOST('password');


        $clientObject = Client::factory()
            ->where('username', $username)
            ->where('password', Tools::passCrypt($password))
            ->where('api_enabled', 1)
            ->getRow();


        $clientObject = new Client($clientObject);

        if (!$clientObject->isLoadedObject()) {
            $this->access = self::ACCESS_LEVEL_NULL;
        } else {
            $this->access = self::ACCESS_LEVEL_USER;
        }



        $adminObject = Employee::factory()
            ->where('username', $username)
            ->where('password', Tools::passCrypt($password))
            ->getRow();


        $employeeObject = new Employee($adminObject);

        if ($employeeObject->isLoadedObject()) {
            $this->access = self::ACCESS_LEVEL_ADMIN;
        }

        if (!$this->access) {
            $this->returnAnswer(0, ['error' => 'Auth error']);
        }

        $this->data = json_decode(Tools::rPOST('data'));

    }

    protected function returnAnswer($code, $data){
        echo json_encode(['code' => $code, 'data' => $data]);
        exit();
    }
}