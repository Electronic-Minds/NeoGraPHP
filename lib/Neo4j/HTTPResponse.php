<?php

namespace Neo4j;

/**
 * HTTPUtility Response
 * @see HTTPUtility::request()
 * @author pr
 * @package neo4j-rest-api
 */
class HTTPResponse {
	/**
	 * URL
	 * @var string
	 */
	protected $url;
	
	/**
	 * HTTP Method
	 * @var string
	 */
	protected $method;
	
	/**
	 * HTTP Response Header
	 * @var array
	 */
	protected $header;
	
	/**
	 * HTTP Response
	 * @var string
	 */
	protected $response;
	
	/**
	 * Construct a new Response
	 * @param string $url
	 * @param string $method
	 * @param array $header
	 * @param string $response
	 */
	public function __construct($url, $method, $header, $response) {
		$this->url      = $url;
		$this->method   = $method;
		$this->header   = $header;
		$this->response = $response;
	}
	
	/**
	 * Get URL
	 * @return string
	 */
	public function getUrl () {
		return $this->url;
	}
	
	/**
	 * Get Method
	 * @return string
	 */
	public function getMethod () {
		return $this->method;
	}
	
	/**
	 * Get HTTP Header
	 * @return array
	 */
	public function getHeader () {
		return $this->header;
	}
	
	/**
	 * Get HTTP Status
	 * @return int
	 */
	public function getStatus () {
		return (int)$this->header['http_code'];
	}
	
	/**
	 * Get HTTP Response
	 * @param boolean $assoc
	 * @return array|stdClass
	 */
	public function getResponse ($assoc = true) {
		return json_decode($this->response, $assoc);
	}
	
	/**
	 * Get HTTP Resonse as JSON
	 * @return various
	 */
	public function getResponseAsJson () {
		return $this->response;
	}
}

?>