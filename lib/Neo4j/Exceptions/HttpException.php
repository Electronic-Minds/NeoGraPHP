<?php

namespace Neo4j\Exceptions;

/**
 * Common HTTP Exception
 * @author pr
 * @package neo4j-rest-api
 */
class HttpException extends \Exception {
	public function __construct($message, $code) {
		parent::__construct($message, $code);
	}
}

?>
