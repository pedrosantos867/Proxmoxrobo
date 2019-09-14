<?php

namespace model;

use System\ObjectModel;

class ServiceCategories extends ObjectModel{

    public static $table = 'service_categories';
    protected $_sortable = true;

    public function remove()
    {
        $Service = new Service();
        $Service->where('category_id', $this->id)->removeRows();

        return parent::remove();
    }
}