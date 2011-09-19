<?php

namespace Neo4j;

/**
 * Dynamic class loader for Neo4j REST API
 * @author pr
 * @package neo4j-rest-api
 */
class ClassLoader {

	/**
	 * Includes the class file
	 * @param string $class
	 */
	public static function loadClass($class) {
		$path = __DIR__ . '/../';
		$class = str_replace('\\', '/', $class) . '.php';
		
		require_once($path . $class);
	}
}

?>
