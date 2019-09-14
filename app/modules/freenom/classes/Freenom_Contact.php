<?php
namespace modules\freenom\classes;
class Freenom_Contact extends Freenom_Service
{
	const CONTACTS = 'https://api.freenom.com/v2/contact/list';
    const CREATECONTACT = 'https://api.freenom.com/v2/contact/register';
	public function getList()
	{
		$api_url = self::CONTACTS;

		$params = [
			'email'            => $this->email,
			'password'         => $this->password
		];

		$response  = $this->curl->executeGetJsonAPI($api_url, $params);

		return $response;
	}

    public function createContact($data)
    {
        $api_url = self::CREATECONTACT;
        $data['email']=$this->email;
        $data['password']=$this->password;
        $response  = $this->curl->executePostJsonAPI($api_url, $data);

        return $response;
    }

}