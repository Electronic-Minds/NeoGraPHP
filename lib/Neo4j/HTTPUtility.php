<?php

namespace Neo4j;

/**
 * HTTP Utility
 * @author pr
 * @package neo4j-rest-api
 */
class HTTPUtility {
	/**
	 * HTTP REST GET
	 * @var string
	 */
	const GET = 'GET';
	
	/**
	 * HTTP REST POST
	 * @var string
	 */
	const POST = 'POST';
	
	/**
	 * HTTP REST PUT
	 * @var string
	 */
	const PUT = 'PUT';
	
	/**
	 * HTTP REST DELETE
	 * @var string
	 */
	const DELETE = 'DELETE';
	
	/**
	 * HTTP Request method
	 * @param string $url
	 * @param string $method
	 * @param string $data
	 * @param string $contentType
	 * @return HTTPResponse
	 */
	public static function request ($url, $method = HTTPUtility::GET, $data = null, $contentType = 'application/json') {
		$ch = curl_init();
		
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
	
		if (false == is_null($data)) {
			curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
			
			$header = array(
				'Content-Type: '.$contentType,
				'Content-Length: ' . strlen($data)
			);
			curl_setopt($ch, CURLOPT_HTTPHEADER, $header); 
		}
			
		$response = curl_exec($ch);
		$headers = curl_getinfo($ch);
		
		curl_close($ch);
		
		return new HTTPResponse($url, $method, $headers, $response);
	}
	
	/**
	 * Synonym for HTTPUtility::Request()
	 * @param string $url
	 * @return HTTPResponse
	 */
	public static function get ($url) {
		return self::request($url);
	}

	/**
	 * Synonym for HTTPUtility::Request()
	 * @param string $url
	 * @param various $data
	 * @param boolean $json
	 * @return HTTPResponse
	 */
	public static function post ($url, $data, $json = false) {
		if (false == $json) {
			$data = json_encode($data);
		}
		
		return self::request($url, HTTPUtility::POST, $data);
	}

	/**
	 * Synonym for HTTPUtility::Request()
	 * @param string $url
	 * @param various $data
	 * @param boolean $json
	 * @return HTTPResponse
	 */
	public static function put ($url, $data, $json = false) {
		if (false == $json) {
			$data = json_encode($data);
		}
		
		return self::request($url, HTTPUtility::PUT, $data);
	}
	
	/**
	 * Synonym for HTTPUtility::Request()
	 * @param string $url
	 * @return HTTPResponse
	 */
	public static function delete ($url) {
		return self::request($url, HTTPUtility::DELETE);
	}
}

?>