<?php
namespace modules\freenom\classes;
class Freenom_Domain extends Freenom_Service
{
	const SEARCH      = 'https://api.freenom.com/v2/domain/search';
	const REGISTER    = 'https://api.freenom.com/v2/domain/register';
	const RENEW       = 'https://api.freenom.com/v2/domain/renew';
	const MODIFY      = 'https://api.freenom.com/v2/domain/modify';

	public function search($data)
	{
		$api_url =  self::SEARCH;

		$params = [
			'domainname' => $data['name'],
			'domaintype' => $data['type']
		];
		$response  = $this->curl->executeGetJsonAPI($api_url, $params);

		return $response;
	}

	public function register($data)
	{
		$api_url = self::REGISTER;

        $data.="&email=$this->email";
        $data.="&password=$this->password";

		$response  = $this->curl->executePostJsonAPI($api_url, $data);

		return $response;
	}

	public function renew($data)
	{
		$api_url = self::RENEW;
        $data['email']=$this->email;
        $data['password']=$this->password;
		$response  = $this->curl->executePostJsonAPI($api_url, $data);

		return $response;
	}

	public function modify($data)
	{
		$api_url = self::MODIFY;

        $data.="&email=$this->email";
        $data.="&password=$this->password";

		$response  = $this->curl->executePostJsonAPI($api_url, $data);
		return $response;
	}


}