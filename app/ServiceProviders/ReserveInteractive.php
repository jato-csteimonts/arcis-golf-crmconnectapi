<?php

namespace App\ServiceProviders;

class ReserveInteractive extends Base {

	public function __construct() {

		$this->setAPIBaseURL(env('RESERVE_INTERACTIVE_BASE_URI'));
		//$this->setAPIVersion(Config::get( "services.contactually.API_VERSION" ));

		$config = [
			"base_uri" => $this->getAPIBaseURL(),
			[],
		];

		parent::__construct($config);
	}

	public function _request($Method = null, $Route = null, $Headers = [], $Body = []) {
		return $this->request($Method, $Route, [
			"headers" => $Headers,
			"body"    => json_encode($Body),
		]);
	}


	public function request($Method = null, $Route = null, array $Options = []) {

		//\Log::info("METHOD: {$Method}");
		//\Log::info("ROUTE: {$Route}");
		//\Log::info(print_r($Options,1));

		return parent::request($Method, $Route, $Options);

	}

}
