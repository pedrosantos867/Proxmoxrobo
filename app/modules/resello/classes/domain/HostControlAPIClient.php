<?php
namespace modules\resello\classes\domain;

use System\Exception;
use modules\resello\classes\domain\Customer;
use modules\resello\classes\domain\Order;
use modules\resello\classes\domain\Domain;
use modules\resello\classes\domain\ResourceWrapper;

class HostControlAPIClientError extends Exception {}


class HostControlAPIClient
{
    const HTTP_METHOD_GET = 'GET';
    const HTTP_METHOD_POST = 'POST';
    const HTTP_METHOD_PUT = 'PUT';
    const HTTP_METHOD_DELETE = 'DELETE';

    private $allowed_methods = array(
        self::HTTP_METHOD_GET,
        self::HTTP_METHOD_POST,
        self::HTTP_METHOD_PUT,
        self::HTTP_METHOD_DELETE
    );

    public function __construct($baseurl, $apikey)
    {
        if(! $baseurl)
        {
            throw new HostControlAPIClientError('Please provide the HostControl API-url.');
        }

        if(! $apikey)
        {
            throw new HostControlAPIClientError('Please provide your HostControl API-key.');
        }

        $this->baseurl = preg_replace('#/$#', '', $baseurl);
        $this->apikey = $apikey;

        # Spawn resources
        $this->customer = new Customer($this);
        $this->domain = new Domain($this);
        $this->order = new Order($this);
    }

    public function get($path, $parameters = array())
    {
        return $this->execute_request(self::HTTP_METHOD_GET, $path, $parameters);
    }

    public function post($path, $parameters = array())
    {
        return $this->execute_request(self::HTTP_METHOD_POST, $path, $parameters);
    }

    public function put($path, $parameters = array())
    {
        return $this->execute_request(self::HTTP_METHOD_PUT, $path, $parameters);
    }

    public function delete($path, $parameters = array())
    {
        return $this->execute_request(self::HTTP_METHOD_DELETE, $path, $parameters);
    }

    private function execute_request($method, $path, $parameters)
    {
        if(! in_array($method, $this->allowed_methods))
        {
            throw new HostControlAPIClientError('Unsupported request method.');
        }

        $handler = curl_init();
        $url = $this->baseurl . $path;

        if($method == self::HTTP_METHOD_GET)
        {
            if(is_array($parameters) && count($parameters) > 0)
            {
                $query_string = http_build_query($parameters);
                $url .= $query_string;
            }
        }
        else
        {
            $parameters = json_encode($parameters);

            if($parameters === false)
            {
                throw new HostControlAPIClientError('Unable to encode parameters.');
            }

            curl_setopt($handler, CURLOPT_POSTFIELDS, $parameters);
        }

        curl_setopt($handler, CURLOPT_URL, $url);
        curl_setopt($handler, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($handler, CURLOPT_FRESH_CONNECT, true);
        curl_setopt($handler, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($handler, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($handler, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($handler, CURLOPT_HTTPHEADER, array("X-APIKEY: $this->apikey"));

        $response = curl_exec($handler);

        if(strlen($response) == 0)
        {
            throw new HostControlAPIClientError('No response received.');
        }

        $status_code = curl_getinfo($handler, CURLINFO_HTTP_CODE);

        if($status_code != 200)
        {
            throw new HostControlAPIClientError("Received status code '$status_code' .");
        }

        $response = json_decode($response);

        if(! is_object($response))
        {
            throw new HostControlAPIClientError('Invalid response received.');
        }

        if(! property_exists($response, 'success') || ! $response->success)
        {
            if(is_object($response->error->message))
            {
                $nested_errors = get_object_vars($response->error->message);
                $messages = array();
                foreach($nested_errors as $attribute => $error)
                {
                    $messages[] = ucfirst($attribute) . ": " . join(', ', $error);
                }
                $error = join(", ", $messages);
            }
            else
            {
                $error = $response->error->message;
            }

            throw new HostControlAPIClientError($error);
        }

        if(property_exists($response, 'result'))
        {
            return $response->result;
        }

        return null;
    }
}
