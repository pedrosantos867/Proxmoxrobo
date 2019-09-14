<?php
namespace modules\resello\classes\domain;

class ResourceWrapper
{
    /**
     * @var RPAPIClient
     */
    protected $apiclient = null;

    protected $paths = array();

    public function __construct($apiclient)
    {
        $this->apiclient = $apiclient;
    }

    protected function get_request_path($path_name, $parameters = array())
    {
        if(! is_array($this->paths) || count($this->paths) == 0)
        {
            throw new HostControlAPIClientError('Please provide a paths attribute.');
        }

        foreach($parameters as $parameter)
        {
            if((! is_string($parameter) && ! is_int($parameter)) || strstr('/', $parameter))
            {
                throw new HostControlAPIClientError('Invalid parameter for URL resolving.');
            }
        }

        if(! array_key_exists($path_name, $this->paths))
        {
            throw new HostControlAPIClientError('Path does not exist.');
        }

        $path = $this->paths[$path_name];

        if(is_array($parameters) && count($parameters) > 0)
        {
            $path = vsprintf($path, $parameters);
        }

        return $path;
    }
}
