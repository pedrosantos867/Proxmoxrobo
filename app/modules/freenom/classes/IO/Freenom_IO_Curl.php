<?php
namespace modules\freenom\classes\IO;
use System\Exception;
use System\Logger;

class Freenom_IO_Curl
{
	public function executeGetAPI($api_url)
	{
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $api_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);


        $output = curl_exec($ch);

        curl_close($ch);

        return $output;
	}

	public function executePostAPI($api_url, $params)
	{
	    $curl_handler = curl_init();

	    curl_setopt($curl_handler, CURLOPT_URL, $api_url);
	    curl_setopt($curl_handler, CURLOPT_USERAGENT, 'Freenom API Class');
	    curl_setopt($curl_handler, CURLOPT_RETURNTRANSFER, true);
	    curl_setopt($curl_handler, CURLOPT_SSL_VERIFYPEER, false);
	    curl_setopt($curl_handler, CURLOPT_HEADER, false);
	    curl_setopt($curl_handler, CURLOPT_FOLLOWLOCATION, false);
	    curl_setopt($curl_handler, CURLOPT_POST, 1);
	    curl_setopt($curl_handler, CURLOPT_POSTFIELDS, $params);
	    curl_setopt($curl_handler, CURLOPT_FORBID_REUSE, true);
	    curl_setopt($curl_handler, CURLOPT_FRESH_CONNECT, true);

	    $response = curl_exec($curl_handler);

	    curl_close($curl_handler);
	    return $response;
	}

	public function executePostJsonAPI($api_url, $params)
	{
		return $this->parseJSON($this->executePostAPI($api_url, $params));
	}

	public function executeGetJsonAPI($api_url, $params = [])
	{
		return $this->parseJSON($this->executeGetAPI($api_url.$this->parseGetParams($params)));
	}

	public function parseGetParams($data)
	{
		$parsed = '?';
		$params = [];

		foreach ($data as $name => $value) {
			$params[] = $name.'='.$value;
		}

		return $parsed . implode('&', $params);
	}

	public function parseJSON($rawData)
	{
		$response = json_decode($rawData,TRUE);

		if(isset($response['status']) && $response['status'] == 'error') {
            Logger::log(json_encode($response));
            throw new Exception();
		}

		return (is_null($response)) ? $rawData : $response;
	}
}