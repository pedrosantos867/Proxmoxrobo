<?php

namespace model;

use System\ObjectModel;

class ModuleHook extends ObjectModel{

    /*List all of hooks*/

    /*End list hooks*/
    public static $hooks = array(
        'dailyCronUpdate'      => 91,

        'getListRegistrar'      => 1,
        'getListServicesForBills' => 11,
        'getListBills'              => 111,
        'afterGetListBills'              => 112,


        'getRegistrar'          => 2,

        'getFrontLayoutMenu'          => 3,
        'getAdminLayoutMenu'          => 31,

        'displayPaymentMethods' => 12,
        'displayBeforeContent'  => 21,
        'displayAfterContent'   => 22,

        'newBill'               => 31,
        'payBill'               => 32,
        'paidBill'               => 33
    );

    protected static $table = 'module_hooks';

}