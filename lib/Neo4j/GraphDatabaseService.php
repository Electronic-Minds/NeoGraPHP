<?php

namespace Neo4j;

use Neo4j\Exceptions\HttpNotFoundException;
use Neo4j\Exceptions\HttpException;

/**
 * Graph database service to access Neo4j graphs
 * @author pr
 * @package neo4j-rest-api
 */

class GraphDatabaseService {
	/**
	 * Base URI to database
	 * @var string
	 */
	protected $base_uri;
	
	/**
	 * Constructor
	 * @param string $base_uri
	 */
	public function __construct($base_uri) {
		$this->base_uri = $base_uri;
	}
	
	/**
	 * Get a specific node by it's ID
	 * @param int $node_id
	 * @throws HttpException
	 * @return mixed
	 */
	public function getNodeById($node_id) {
		$uri = $this->getBaseUri() . 'node/' . $node_id;
		$response = HTTPUtility::get($uri);	
		
		if (200 == $response->getStatus()) {
			return Node::inflateFromResponse($this, $response->getResponse());
		} else if (404 == $response->getStatus()) {
			throw new HttpNotFoundException($response->getResponseAsJson());
		} else {
			throw new HttpException($response->getStatus());
		}
	}

	/**
	 * Create a new node
	 * @return Node
	 */
	public function createNode() {
		return new Node($this);
	}
	
	/**
	 * Get URI for Database
	 * @return string
	 */
	public function getBaseUri() {
		return $this->base_uri;
	}
	
	/**
	 * Create a new Traverser
	 * @return Traverser
	 */
	public function createTraverser () {
		return new Traverser();
	}
	
	/**
	 * Create a new Index Manager
	 * @return IndexManager
	 */
	public function getIndexManager () {
		return new IndexManager($this);
	}
}

?>
