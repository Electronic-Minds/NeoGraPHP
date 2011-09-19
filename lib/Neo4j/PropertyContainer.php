<?php

namespace Neo4j;

/**
 * Generic property container
 * @author pr
 * @package neo4j-rest-api
 */
abstract class PropertyContainer {
	/**
	 * List of all Properties
	 * @var array
	 */
	protected $data;
	
	/**
	 * Magic set function
	 * @param string $key
	 * @param various $val
	 */
	public function __set($key, $val) {
		if (null === $val && true == isset($this->data[$key])) { 
			unset($this->data[$key]);
		} else {
			$this->data[$key] = $val;
		}
	}
	
	/**
	 * Magic get function
	 * @param string $key
	 * @return various
	 */
	public function __get($key) {
		if (true == isset($this->data[$key])) {
			return $this->data[$key];
		} else {
			return NULL;
		}
	}
	
	/**
	 * Set properties
	 * @param array $data
	 */
	public function setProperties($data) {
		$this->data = $data;
	}
	
	/**
	 * Get all properties
	 * @return array
	 */
	public function getProperties()	{
		return $this->data;
	}
	
	/**
	 * Get a single property by it's name
	 * @param string $name
	 * @return various
	 */
	public function getProperty ($name) {
		if (false == isset($this->data[$name])) {
			return false;
		}
		
		return $this->data[$name];
	}
}

?>
