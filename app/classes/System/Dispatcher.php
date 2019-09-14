<?php
namespace System;

use \System\Interfaces\IDispatcher;

class Dispatcher implements IDispatcher {

    public function EventGetHelpersForView(){
        return array(
           '_' => new \helpers\Helper()
        );
    }
}