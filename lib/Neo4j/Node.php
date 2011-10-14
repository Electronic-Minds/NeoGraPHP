<?php

namespace Neo4j;

use Neo4j\Exceptions\HttpException;

/**
 * Node
 * @author pr
 * @package neo4j-rest-api
 * @see PropertyContainer
 */
class Node extends PropertyContainer {
 	/**
	 * Instance of GraphDatabaseSerice
	 * @var GraphDatabaseService
	 */
	protected $neoDb;
	
	/**
	 * Node ID
	 * @var int
	 */
	protected $id;
	
	/**
	 * If Node is not persisted, yet
	 * @var boolean
	 */
	protected $isNew;
	
	/**
	 * Construct a new Node
	 * @param GraphDatabaseService $neo_db
	 */
	public function __construct($neoDb) {
		$this->neoDb = $neoDb;
		$this->isNew = true;
	}
	
	public function getGraphService () {
		return $this->neoDb;
	}

	/**
	 * Delete current Node from database
	 * @param boolean $recursive
	 * @throws HttpException
	 */
	public function delete($deleteRelations = true)	{
		if (false == $this->isNew) {
			if (true == $deleteRelations) {
				foreach ($this->getRelationships() as $current) {
					$current->delete();
				}
			}
			
			$response = HTTPUtility::delete($this->getUri());
			if (204 != $response->getStatus()) {
				throw new HttpException($response->getStatus());
			}
			
			$this->id    = null;
			$this->isNew = true;
		}
	}
	
	/**
	 * Save node to database
	 * @throws HttpException
	 */
	public function save() {
		if (true == $this->isNew) {
			if (0 >= count($this->getProperties())) {
				$response = HTTPUtility::post($this->getUri(), null, true);
			} else {
				$response = HTTPUtility::post($this->getUri(), $this->getProperties());
			}
			
			
			if (201 != $response->getStatus()) {
				throw new HttpException($response->getStatus());
			}
		} else {
			$response = HTTPUtility::put($this->getUri() . '/properties', $this->getProperties());

			if (204 != $response->getStatus()) {
				throw new HttpException($response->getStatus());
			}
		}

		if (true == $this->isNew) {
			$data = $response->getResponse();
			$parts = explode("/", $data['self']);
			$this->id = end($parts);
			$this->isNew = false;
		}
	}
	
	/**
	 * Get ID of current node
	 * @return int
	 */
	public function getId() {
		return $this->id;
	}
	
	/**
	 * Check if node is saved to database
	 * @return boolean
	 */
	public function isSaved() {
		return !$this->isNew;
	}
	
	/**
	 * Get all relations from current node
	 * @param string $direction
	 * @param string or array $types
	 * @return array
	 */
	public function getRelationships($direction=Relationship::DIRECTION_BOTH, $types=NULL) {
		$uri = $this->getUri().'/relationships';
		
		switch($direction) {
			case Relationship::DIRECTION_IN:
				$uri .= '/in';
				break;
			case Relationship::DIRECTION_OUT:
				$uri .= '/out';
				break;
			default:
				$uri .= '/all';
		}
		
		if ($types) {
			if (is_array($types)) $types = implode("&", $types);
			
			$uri .= '/'.$types;
		}
		
		$response = HTTPUtility::get($uri);
		$relationships = array();
		
		if (true == is_array($response->getResponse())) {
			foreach($response->getResponse() as $result) {
				$relationships[] = Relationship::inflateFromResponse($this->neoDb, $result);
			}
		}
		
		return $relationships;
	}
	
	/**
	 * Create a new Relationship
	 * @see Relationship::__construct()
	 * @param Node $node
	 * @param string $type
	 * @return Relationship
	 */
	public function createRelationshipTo($node, $type) {
		$relationship = new Relationship($this->neoDb, $this, $node, $type);
		return $relationship;
	}
	
	/**
	 * Get URI for the current node
	 * @return string
	 */
	public function getUri() {
		$uri = $this->neoDb->getBaseUri() . 'node';
	
		if (false == $this->isNew) {
			$uri .= '/' . $this->getId();
		}
	
		return $uri;
	}
	
	/**
	 * Inflate json result to Nodes
	 * @param GraphDatabaseService $neo_db
	 * @param string $response
	 */
	public static function inflateFromResponse($neoDb, $response) {
		$node = new Node($neoDb);
		$node->isNew = false;
		$array = explode("/", $response['self']);
		$node->id = end($array);
		$node->setProperties($response['data']);

		return $node;
	}
	
	/**
	 * Run Traverser on current node
	 * @see Traverser
	 * @param Traverser $traverser
	 * @param string $returnType
	 * @throws HttpException
	 * @throws Exception
	 */
	public function runTraverser (Traverser $traverser, $returnType) {
		$url = $this->getUri() . '/traverse/' . $returnType;
		$response = HTTPUtility::post($url, $traverser->getSettings());

		if (200 != $response->getStatus()) {
			throw new HttpException($response->getStatus());
		}

		$temp = array();
		foreach ($response->getResponse() as $current) {
			if (Traverser::RETURN_NODE == $returnType) {
				$temp[] = Node::inflateFromResponse($this->neoDb, $current);
			} else if (Traverser::RETURN_RELATION == $returnType) {
				$temp[] = Relationship::inflateFromResponse($this->neoDb, $current);
			} else if (Traverser::RETURN_PATH == $returnType) {
				$temp[] = Path::inflateFromResponse($this->neoDb, $current);
			} else if (Traverser::RETURN_FULL_PATH == $returnType) {
				$temp[] = FullPath::inflateFromResponse($this->neoDb, $current);
			}
		}
		
		return $temp;
	}

	/**
	 * 
	 * @param GraphDatabaseService $targetDb
	 * @throws \RuntimeException
	 */
	public function cloneToDb(GraphDatabaseService $targetDb) {
		if (false == $this->isSaved()) {
			throw new \RuntimeException('Node must be save before cloning');
		}
		
		$this->isNew = true;
		$this->neoDb = $targetDb;
		
		$this->save();
	}
	
	/**
	 * Magic clone function
	 */
	public function __clone() {
		$this->isNew = true;
	}
	
	/**
	 * Magic function to dump a node in a human readable format
	 * @return string
	 */
	public function __toString() {
		$str = 'Node ' . $this->getId() . "\t\t\t\t\t\t\t\t" . json_encode($this->getProperties()) . "\n";
		
		foreach($this->getRelationships() as $current) {
			$startId = $current->getStartNode()->getId();
			$endId = $current->getEndNode()->getId();
			
			$str .= '  Relationship ' . $current->getId() . '  :  Node '.$startId . ' ---' . $current->getType() . '---> Node ' . $endId;
			$str .= "\t\t\t\t\t\t\t\t" . json_encode($current->getProperties()) . "\n";
		}
		
		return $str;
	}
}

?>
