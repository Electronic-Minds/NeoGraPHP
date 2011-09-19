<?php

namespace Neo4j;

use Neo4j\Exceptions\HttpException;

/**
 * Index Manager
 * @author pr
 * @package neo4j-rest-api
 */
class IndexManager {
	/**
	 * Node Index
	 * @var string
	 */
	const INDEX_TYPE_NODE = 'node';
	
	/**
	 * Relationship index
	 * @var string
	 */
	const INDEX_TYPE_RELATION = 'relationship';
	
	/**
	 * Instance of GraphDatabaseSerice
	 * @var GraphDatabaseService
	 */
	protected $neoDb;
	
	/**
	 * Construct new Index Manager
	 * @param GraphDatabaseSerice $neo_db
	 */
	public function __construct($neoDb) {
		$this->neoDb = $neoDb;
	}
	
	/**
	 * create a new Instance of NodeIndex or RelationshipIndex
	 * @param string $name
	 * @param string $type
	 * @return NodeIndex, RelationshipIndex or false
	 */
	protected function spawnNewIndex ($name, $type) {
		if (IndexManager::INDEX_TYPE_NODE == $type) {
			$item = new NodeIndex($name, $this->neoDb);
		} else if (IndexManager::INDEX_TYPE_RELATION == $type) {
			$item = new RelationshipIndex($name, $this->neoDb);
		} else {
			throw new \RuntimeException();
		}
		
		return $item;
	}

	/**
	 * Create a new index with a custom config
	 * @param string $name
	 * @param string $type
	 * @param array $config
	 * @throws HttpException
	 * @return NodeIndex, RelationshipIndex or false
	 */
	public function create ($name, $type, $config = array()) {
		if (0 >= count($config)) {
			$config = array(
				'type' => 'exact',
				'provider' => 'lucene'
			);
		}
		
		$indexConfig = array(
			'name' => $name,
			'config' => $config
		);
		
		$indexUri = $this->neoDb->getBaseUri() . 'index/' . $type . '/';
		$response = HTTPUtility::post($indexUri, $indexConfig);
		
		if (201 != $response->getStatus()) {
			throw new HttpException($response->getStatus());
		}

		return $this->spawnNewIndex($name, $type);
	}
	
	/**
	 * Delete an index
	 * @param string $name
	 * @param string $type
	 * @throws Exception
	 * @throws HttpException
	 * @return boolean
	 */
	public function delete ($name, $type) {
		$indexUri = $this->neoDb->getBaseUri() . 'index/' . $type . '/' . $name;
		$response = HTTPUtility::delete($indexUri);
		
		if (204 != $response->getStatus()) {
			throw new HttpException($response->getStatus());
		}
		
		return true;
	}
	
	/**
	 * Get an existing Index, if it does not exist, it can be created
	 * @param string $name
	 * @param string $type
	 * @param boolean $create
	 * 
	 * @todo throw Exception
	 */
	public function get ($name, $type, $create = false) {
		if (true == $this->indexExists($name, $type)) {
			return $this->spawnNewIndex($name, $type);
		} else {
			if (true == $create) {
				return $this->create($name, $type);
			} else {
				return false;
			}
		}
	}
	
	/**
	 * Check if a given index exists
	 * @param string $name
	 * @param string $type
	 * @return boolean
	 */
	public function indexExists ($name, $type) {
		$indices = $this->getIndexList($type);
		return isset($indices[$name]);
	}
	
	/**
	 * Get list of all existing indices
	 * @param string $type
	 * @throws HttpException
	 * @return array
	 */
	public function getIndexList ($type) {
		$indexUri = $this->neoDb->getBaseUri() . 'index/' . $type . '/';
		$response = HTTPUtility::get($indexUri);

		if (200 != $response->getStatus() AND 204 != $response->getStatus()) {
			throw new HttpException($response->getStatus());
		}
		
		$indices = array();
		foreach ((array)$response->getResponse() as $name => $config) {
			$indices[$name] = $this->spawnNewIndex($name, $type);
		}
		
		return $indices;
	}
}

?>
