<?php

namespace modules\resello\classes\domain;
class Customer extends ResourceWrapper
{
    protected $paths = array(
        'collection' => '/customer',
        'single' => '/customer/%s',
        'lookup' => '/customer?email=%s',
    );

    public function get($customer_id)
    {
        $path = $this->get_request_path('single', array($customer_id));
        return $this->apiclient->get($path);
    }

    public function lookup($email)
    {
        $path = $this->get_request_path('lookup', array($email));
        return $this->apiclient->get($path);
    }

    public function create($customer_info)
    {
        return $this->apiclient->post($this->get_request_path('collection'), $customer_info);
    }
}
