<?php

namespace api;

use ApiController;

class TestController extends ApiController {
    public function actionTest(){

        $this->returnAnswer(1, array('connection' => '1'));
    }
}