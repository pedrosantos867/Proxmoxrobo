<?php
namespace modules\freenom\classes;
use modules\freenom\classes\IO\Freenom_IO_Curl;

class Freenom_Client
{
  protected $apiLoginId;
  protected $apiPassword;

  /**
   * Returns an authorized API client.
   * @return Freenom_Service the authorized client object
   */
    public function __construct($email,$pass)
    {
        $this->apiLoginId     = $email;
        $this->apiPassword  = $pass;
        $this->curl      = new Freenom_IO_Curl;
    }
    public function getApiLoginId()
    {
        return $this->apiLoginId;
    }

    public function getApiPassword()
    {
        return $this->apiPassword;
    }
}