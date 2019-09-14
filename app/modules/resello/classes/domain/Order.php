<?php
namespace modules\resello\classes\domain;
class Order extends ResourceWrapper
{
    protected $paths = array(
        'collection' => '/order',
    );

    public function order_domain($customer, $domain)
    {
        return $this->apiclient->post($this->get_request_path('collection'), array(
            'customer' => intval($customer),
            'type' => 'new',
            'order' => array(
                array(
                    'type' => 'domain-register-order',
                    'name' => $domain,
                    'interval' => 12,
                )
            )
        ));
    }

}
