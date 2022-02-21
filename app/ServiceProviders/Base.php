<?php

namespace App\ServiceProviders;

use GuzzleHttp\Client;

class Base extends Client {

	//////////////
	// Providers
	//
	public static $PROVIDER_RESERVE_INTERACTIVE = "reserve_interactive";

	/////////////////////////
	// Protected Variables
	//
	protected $API_BASE_URL     = null;
	protected $API_VERSION      = null;

	public function __construct($config = []) {
		parent::__construct($config);
	}

	public function getAPIBaseURL() {
		return $this->API_BASE_URL;
	}

	public function getAPIVersion() {
		return $this->API_VERSION;
	}

	protected function setAPIBaseURL($url) {
		$this->API_BASE_URL = $url;
	}

	protected function setAPIVersion($version) {
		$this->API_VERSION = $version;
	}

	public function request($Method = null, $Route = null, array $Options = []) {

		\Log::info("METHOD: {$Method}");
		\Log::info("ROUTE: {$Route}");
		\Log::info(print_r($Options,1));

		try {
			\Log::info("STEIN SENDING!!!!!!!!!!!!!!!!");
			$Response = parent::request($Method, "{$Route}", $Options);
		} catch (\Exception $e) {
			\Log::info($e->getMessage());
			\Log::info($e->getFile());
			\Log::info($e->getLine());
		}

		\Log::info(print_r($Response,1));

		return $this->returnResponse($Response);

	}

	protected function returnResponse($Response) {

		$response = [
			"StatusCode"    => $Response->getStatusCode(),
			"ReasonPhrase"  => $Response->getReasonPhrase(),
			"Body"          => json_decode($Response->getBody()->getContents()),
		];

		\Log::info(print_r($response,1));

		return $response;
	}

}
