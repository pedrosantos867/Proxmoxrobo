<?php

namespace model;


use System\ObjectModel;

class DomainOrder extends ObjectModel
{

    protected static $table = 'domains_orders';

    public function remove()
    {

        $Bill = new Bill();
        $Bill->where('domain_order_id', $this->id)->removeRows();
        parent::remove();
    }
}