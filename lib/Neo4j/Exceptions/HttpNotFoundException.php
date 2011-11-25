<?php
namespace Neo4j\Exceptions;

use Neo4j\Exceptions\HttpException;

class HttpNotFoundException extends HttpException {
	public function __construct($message) {
 		parent::__construct($message, 404);
	}
}

?>