<?php

namespace Neo4j\Query;

/**
 * 
 * @author pr
 *
 */
abstract class AbstractQueryBuilder {
	protected $query;
	protected $parts;	
	protected $format;
	
	abstract public function buildQuery();
	
	public function __construct($query = '', $format = 'json-data-table') {
		$this->query = $query;
	}
	
	/**
	 * @return the $format
	 */
	public function getFormat() {
		return $this->format;
	}

	/**
	 * @param field_type $format
	 */
	public function setFormat($format) {
		$this->format = $format;
	}

	/**
	 * 
	 * @param string $query
	 */
	public function setQuery($query) {
		$this->query = $query;
	}
	
	/**
	 * @return array
	 */
	public function getQuery() {
		if (0 >= mb_strlen($this->query)) {
			$this->query = $this->buildQuery();
		}
		
		$query = array(
			'query' 	=> $this->query,
			'format' 	=> $this->format
		);
		
		return $query;
	}
}

?>