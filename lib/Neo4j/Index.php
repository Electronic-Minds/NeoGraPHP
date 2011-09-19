<?php

namespace Neo4j;

use Neo4j\Exceptions\HttpException;

/**
 * Generic Index
 * @author pr
 * @package neo4j-rest-api
 */
abstract class Index {
	/**
	 * Maxiumum length of keys
	 * @var int
	 */
	const MAX_INDEX_KEY_LENGTH = 512;
	
	/**
	 * Maximum length of values
	 * @var int
	 */
	const MAX_INDEX_VALUE_LENGTH = 1024;
	
	/**
	 * Instance of GraphDatabaseSerice
	 * @var GraphDatabaseService
	 */
	protected $neoDb;
	
	/**
	 * Index name
	 * @var string
	 */
	protected $name;
	
	/**
	 * Construct a new index
	 * @param string $name
	 * @param GraphDatabaseService $neo_db
	 */
	public function __construct($name, $neo_db) {
		$this->name  = $name;
		$this->neoDb = $neo_db;
	}
	
	/**
	 * Base URI for all indices
	 * @return string
	 */
	public function getIndexBaseUri () {
		$indexBaseUri = $this->neoDb->getBaseUri() . 'index/';
		return $indexBaseUri;		
	}
	
	/**
	 * Name of the Index
	 * @return string
	 */
	public function getIndexName () {
		return $this->name;
	}
	
	/**
	 * Synonym for add()
	 * @param string $item
	 */
	public function update ($item) {
		$this->add($item);
	}
	
	/**
	 * Get Item by key & value
	 * @param string $key
	 * @param string $value
	 * @return Node or Relationship
	 */
	public function get ($key, $value) {
		$query = $key . ':' . strtolower($value);
		return $this->getByQuery($query);
	}
	
	public function getIndexConfig ($type) {
		$indexUri = $this->getIndexBaseUri() . $type . '/';
		$response = HTTPUtility::get($indexUri);

		if (200 != $response->getStatus() AND 204 != $response->getStatus()) {
			throw new HttpException($response->getStatus());
		}
		
		$indices = $response->getResponse(); 
		return $indices[$this->getIndexName()];
	}
	
	abstract public function checkItem($item);
	
	/**
	 * @see NodeIndex::getIndexUri() or RelationshipIndex::getIndexUri()
	 */
	abstract protected function getIndexUri();
	
	/**
	 * @see NodeIndex::add() or RelationshipIndex::add()
	 */
	abstract public function add ($item);
	
	/**
	 * @see NodeIndex::delete() or RelationshipIndex::delete()
	 */
	abstract public function delete ($item);
	
	/**
	 * @see NodeIndex::getByQuery() or RelationshipIndex::getByQuery()
	 */
	abstract public function getByQuery ($query);
}

?>
