<?php
namespace modules\freenom\classes;
use modules\freenom\classes\IO\Freenom_IO_Curl;

class Freenom_Service
{
	public function __construct(Freenom_Client $client)
	{
		$this->email     = $client->getApiLoginId();
		$this->password  = $client->getApiPassword();
		$this->curl      = new Freenom_IO_Curl;
	}

}