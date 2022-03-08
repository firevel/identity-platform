<?php

namespace Firevel\IdentityPlatform\Services;

class IdentityPlatformService
{
    /**
     * Access token.
     *
     * @var string
     */
    protected $token;

    /**
     * Guzzle client.
     *
     * @var \GuzzleHttp\Client
     */
    protected $client;

    /**
     * Api endpoint.
     *
     * @var string
     */
    protected $api;

    /**
     * Project.
     *
     * @var string
     */
    protected $project;

    public function __construct($api, $token = null)
    {
    	$this->setApi($api);
        $this->setToken($token);
    }

    /**
     * Get guzzle client.
     *
     * @return Client
     */
    protected function getClient()
    {
    	if (empty($this->client)) {
    		$this->client = new \GuzzleHttp\Client();
    	}

    	return $this->client;
    }

    /**
     * Get default headers.
     *
     * @return array
     */
    protected function getDefaultHeaders()
    {
    	return [ 'Authorization' => 'Bearer ' . $this->token ];
    }

    /**
     * Batch user creation.
     * 
     * Ref.: https://cloud.google.com/identity-platform/docs/reference/rest/v1/projects.accounts/batchCreate
     *
     * @param  array $users
     * @param  array $options
     * @return array GuzzleHttp\Psr7\Response
     */
	public function batchCreate($users, $options = [])
	{
		if (empty($options['hashAlgorithm'])) {
			$options['hashAlgorithm'] = config('identityplatform.algorithm');
		}

		$payload = $options;
		$payload['users'] = $users;

		return $this->call(
			'POST',
			'accounts:batchCreate',
			$payload
		);
	}

	/**
	 * Call api endpoint.
	 *
	 * @param  string $method   HTTP method ex.: POST
	 * @param  string $endpoint Api endpoint ex.: accounts:batchCreate
	 * @return GuzzleHttp\Psr7\Response
	 */
	public function call($method, $endpoint, $payload)
	{
		$url = $this->getApi();

		if ($this->hasProject()) {
			$url = $url . 'projects/' . $this->getProject();
		}

		$url = $url . '/' . $endpoint;

        return $this
        	->getClient()
        	->request(
	            $method,
	            $url,
	            [
	                'json' => $payload,
	                'headers' => [ 'Authorization' => 'Bearer ' . $this->getToken() ],
	            ]
	        );
	}

    /**
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * @param string $token
     *
     * @return self
     */
    public function setToken($token)
    {
        $this->token = $token;

        return $this;
    }

    /**
     * @return string
     */
    public function getApi()
    {
        return $this->api;
    }

    /**
     * @param string $api
     *
     * @return self
     */
    public function setApi($api)
    {
        $this->api = rtrim($api, "/ \n\r\t\v\x00") . '/';

        return $this;
    }

    /**
     * @return string
     */
    public function hasProject()
    {
        return ! empty($this->project);
    }

    /**
     * @return string
     */
    public function getProject()
    {
        return $this->project;
    }

    /**
     * @param string $project
     *
     * @return self
     */
    public function setProject($project)
    {
        $this->project = $project;

        return $this;
    }
}