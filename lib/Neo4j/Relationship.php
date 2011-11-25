<?php

namespace Neo4j;

use Neo4j\Exceptions\HttpException;

/**
 * Relationship
 * @author pr
 * @package neo4j-rest-api
 */
class Relationship extends PropertyContainer {
	/**
	 * Relationship direction Both
	 * @var string
	 */
	const DIRECTION_BOTH = 'BOTH';
	
	/**
	 * Relationship direction In
	 * @var string
	 */
	const DIRECTION_IN   = 'IN';
	
	/**
	 * Relationship direction Out
	 * @var string
	 */
	const DIRECTION_OUT  = 'OUT';
	
	/**
	 * Indicates if relation is persisted
	 * @var boolean
	 */
	protected $isNew;
	
 	/**
	 * Instance of GraphDatabaseSerice
	 * @var GraphDatabaseService
	 */
	protected $neoDb;
	
	/**
	 * Relationship id
	 * @var int
	 */
	protected $id;
	
	/**
	 * Relationship type
	 * @var string
	 */
	protected $type;
	
	/**
	 * Start node
	 * @var Node
	 */
	protected $startNode;
	
	/**
	 * End node
	 * @var Node
	 */
	protected $endNode;
	
	/**
	 * Construct a new Relationship
	 * @param GraphDatabaseService $neo_db
	 * @param Node $start_node
	 * @param Node$end_node
	 * @param string $type
	 */
	public function __construct(GraphDatabaseService $neoDb, Node $startNode, Node $endNode, $type) {
		if (false == $startNode->isSaved() OR false == $endNode->isSaved()) {
			throw new \RuntimeException('$startNode and $endNode must be saved!');
		}
		
		if (false == is_string($type) OR 0 >= mb_strlen($type)) {
			throw new \InvalidArgumentException('$type must not be empty');
		}
		
		$this->neoDb     = $neoDb;
		$this->isNew     = true;
		$this->type      = $type;
		$this->startNode = $startNode;
		$this->endNode   = $endNode;
	}
	
	/**
	 * Get id of current relationship
	 * @return int
	 */
	public function getId() {
		return $this->id;
	}
	
	/**
	 * Check if relationship has been saved
	 * @return boolean
	 */
	public function isSaved() {
		return !$this->isNew;
	}
	
	/**
	 * Get relationship type
	 * @return string
	 */
	public function getType() {
		return $this->type;		
	}
	
	/**
	 * Compare type
	 * @param string $type
	 * @return boolean
	 */
	public function isType($type) {
		return $this->type == $type;
	}
	
	/**
	 * Get Start Node
	 * @return Node
	 */
	public function getStartNode() {
		return $this->startNode;
	}
	
	/**
	 * Get End Node
	 * @return Node
	 */
	public function getEndNode() {
		return $this->endNode;
	}
	
	/**
	 * Get opposing node to given
	 * @param Node $node
	 * @return Node
	 */
	public function getOpposingNode (Node $node) {
		if ($node->getID() == $this->getStartNode()->getId()) {
			return $this->getEndNode();
		} else {
			return $this->getStartNode();
		}
	}

	/**
	 * Save relationship to database
	 * @throws HttpException
	 */
	public function save() {
		if (true == $this->isNew) {
			$payload = array(
				'to'   => $this->getEndNode()->getUri(),
				'type' => $this->type,
				'data' => $this->getProperties()
			);
			
			$response = HTTPUtility::post($this->getUri(), $payload);
			
			if (201 != $response->getStatus()) {
				throw new HttpException($response->getResponseAsJson(), $response->getStatus());
			}
		} else {
			$response = HTTPUtility::put($this->getUri() . '/properties', $this->getProperties());

			if (204 != $response->getStatus()) {
				throw new HttpException($response->getResponseAsJson(), $response->getStatus());
			}
		}
				
		if (true == $this->isNew) {
			$data        = $response->getResponse();
			$parts 		 = explode("/", $data['self']);
			$this->id    = end($parts);
			$this->isNew = false;
		}
	}
	
	/**
	 * Delete relationship from database
	 * @throws HttpException
	 */
	public function delete() {
		if (false == $this->isNew) {
			$response = HTTPUtility::delete($this->getUri());

			if (204 != $response->getStatus()) {
				throw new HttpException($response->getResponseAsJson(), $response->getStatus());
			}
			
			$this->id = null;
			$this->isNew = true;
		}
	}
	
	/**
	 * Get URI of current relationship
	 * @return string
	 */
	public function getUri() {
		if ($this->isNew) {
			$uri = $this->getStartNode()->getUri().'/relationships';
		} else {
			$uri = $this->neoDb->getBaseUri() . 'relationship/' . $this->getId();
		}
	
		return $uri;
	}
	
	/**
	 * Inflate json result to Relation
	 * @param GraphDatabaseService $neo_db
	 * @param string $response
	 * @return Relationship
	 */
	public static function inflateFromResponse($neoDb, $response) {
		$array = explode("/", $response['start']);
		$start_id = end($array);
		
		$array = explode("/", $response['end']);
		$end_id = end($array);

		$start = $neoDb->getNodeById($start_id);
		$end = $neoDb->getNodeById($end_id);
		
		$relationship = new Relationship($neoDb, $start, $end, $response['type']);
		$relationship->isNew = false;
		$array = explode("/", $response['self']);
		$relationship->id = end($array);
		$relationship->setProperties($response['data']);
		
		return $relationship;
	}
}

?>
