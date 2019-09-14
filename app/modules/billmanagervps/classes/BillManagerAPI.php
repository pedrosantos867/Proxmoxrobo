<?php

namespace modules\billmanagervps\classes;

class BillManagerAPI {

    private $hostname;
    private $query = '';
    private $username;
    private $password;
    private $res = null;

    private $link = '';

    private $execError = null;
    private $_error;

    public function __construct($url, $login, $password)
    {
        $this->curl     = curl_init();
        $this->hostname = $url . '/manager/billmgr?authinfo=' . $login . ':' . $password . '&out=json';
        $this->query = '';


    }

    public function payVds($billorder_id){
        $this->query .= '&' . http_build_query([
                'func'          => 'payment.add',
                'billorder'     => $billorder_id,
                'sok'           => 'ok',
                'clicked_button'=> 'fromsubaccount'
            ]);


        $this->exec();


        if(isset($this->res->doc->ok)){
            return true;
        }

        return false;
    }

    public function suspendVds($vds_order_id){
        $this->query .= '&' . http_build_query(['func' => 'vds.suspend', 'elid' => $vds_order_id ]);
        $this->exec();
        return true;
    }

    public function removeVds($vds_order_id){
        $this->query .= '&' . http_build_query(['func' => 'vds.delete', 'elid' => $vds_order_id ]);
        $this->exec();

        return true;
    }

    public function resumeVds($vds_order_id){
        $this->query .= '&' . http_build_query(['func' => 'vds.resume', 'elid' => $vds_order_id ]);
        $this->exec();
        return true;
    }

    public function getVdsInfo($vds_order_id){
        $this->query .= '&' . http_build_query(['func' => 'vds.edit', 'elid' => $vds_order_id ]);
        $this->exec();



        if(isset($this->res->doc->userpassword)) {
            return array(
                'ip'        =>  $this->res->doc->ip->{'$'},
                'username'  => $this->res->doc->username->{'$'},
                'userpassword'  => $this->res->doc->userpassword->{'$'},
                'password'  => $this->res->doc->password->{'$'});
        }

        return array();
    }

    public function createVds( $pricelist, $period, $ostempl, $addons=array(), $domain = ''){

        $query = array(
            'func'      => 'vds.order',
            'pricelist' => $pricelist,
            'period'    => $period,
            'ostempl'   => $ostempl,
            'domain'    => $domain,
            'sok' => 'ok'
        );


        foreach ($addons as $key=> $value) {

            $value = str_replace(array("\n", "\r"), '', $value);

            $query[$key] = $value;
        }

        $this->query .= '&' . http_build_query($query);

        $this->exec();

        if(isset($this->res->doc->ok)){

            return array(
                'billorder_id' => $this->res->doc->{'billorder.id'}->{'$'},
                'order_id' => $this->res->doc->{'id'}->{'$'}
            );

        }

        return array();
    }
    private function exec()
    {
        $this->execError = null;
//echo $this->hostname . $this->query;
        curl_setopt($this->curl, CURLOPT_URL, $this->hostname . $this->query);

        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($this->curl, CURLOPT_SSL_VERIFYHOST, false);
        $this->res = json_decode(curl_exec($this->curl));
        //   print_r($this->res);
        $this->query = '';
        // echo curl_error($this->curl);
        if ($this->res == '') {
            return false;
        }

        if (isset($this->res->doc->error)) {
            if (isset($this->res->doc->error->{'$type'})&&($this->res->doc->error->{'$type'}) == 'auth') {

                return false;
            }

            if (isset($this->res->doc->error->{'$object'})&&$this->res->doc->error->{'$object'} == 'badpassword') {

                return false;
            }

            return false;
        }

        return true;
    }
}