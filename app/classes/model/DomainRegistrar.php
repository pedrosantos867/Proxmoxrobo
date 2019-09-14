<?php

namespace model;

use System\ObjectModel;

class DomainRegistrar extends ObjectModel
{
    protected static $table = 'domain_registrars';

    public function remove()
    {

        $Domain = new Domain();
        $Domain->where('registrant_id', $this->id)->removeRows();

        parent::remove();
    }
}