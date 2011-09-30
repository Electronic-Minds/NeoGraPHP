<?php

namespace Neo4j\Query;

/**
 * 
 * @author pr
 *
 */
abstract class AbstractQuery {
	/**
	 * 
	 * @var GraphDatabaseService
	 */
	protected $graphDb;
	
	/**
	 * 
	 * @param GraphDatabaseService $graphDb
	 */
	public function __construct($graphDb) {
		$this->graphDb = $graphDb;
	}
	
	/**
	 * 
	 * @param AbstractQueryBuilder $query
	 */
	abstract public function runQuery(AbstractQueryBuilder $query);
	
	/**
	 * 
	 * @param array $result
	 */
	abstract protected function inflateResponse($result);
}

?>