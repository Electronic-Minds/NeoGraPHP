<?php

namespace Neo4j;

use Neo4j\Exceptions\HttpException;

/**
 * Path
 * @author pr
 * @package neo4j-rest-api
 */
class Path {
 	/**
	 * Instance of GraphDatabaseSerice
	 * @var GraphDatabaseService
	 */
	private $neoDb;
	
	/**
	 * Start Node
	 * @var Node
	 */
	protected $start;
	
	/**
	 * End Node
	 * @var Node
	 */
	protected $end;
	
	/**
	 * Length of path
	 * @var int
	 */
	protected $length;
	
	/**
	 * List of all Nodes on the path
	 * @var array
	 */
	protected $nodes;
	
	/**
	 * 
	 * List of all Relationships on the path
	 * @var array
	 */
	protected $relations;
	
	/**
	 * Construct new Path
	 * @param GraphDatabaseService $neo_db
	 * @param Node $start
	 * @param Node $end
	 * @param int $length
	 * @param array $nodes
	 * @param array $relations
	 */
	public function __construct ($neoDb, $start, $end, $length, $nodes, $relations) {
		$this->neoDb     = $neoDb;
		$this->start     = $start;
		$this->end       = $end;
		$this->length    = (int)$length;
		$this->nodes     = $nodes;
		$this->relations = $relations;
	}
	
	/**
	 * Magic get function
	 * @param string $name
	 * @throws Exception
	 * @return various
	 */
	public function __get ($name) {
		if ('neoDb' == $name) throw new \Exception('Forbidden!');
		return $this->$name;
	}
	
	/**
	 * Inflate json result to Path
	 * @param GraphDatabaseService $neo_db
	 * @param string $response
	 * @throws HttpException
	 * @return Path
	 */
	public static function inflateFromResponse ($neo_db, $response) {
		$startNode = $neo_db->getNodeById(extractID($response['start']));
		$endNode   = $neo_db->getNodeById(extractID($response['end']));
		
		$nodes = array();
		foreach ($response['nodes'] as $current) {
			$nodes[] = $neo_db->getNodeById(extractID($current));
		}
		
		$relations = array();
		foreach ($response['relationships'] as $current) {
			$response = HTTPUtility::get($current);
			
			if (200 != $response->getStatus()) {
				throw new HttpException($response->getResponseAsJson(), $response->getStatus());
			}
			
			$relations[] = Relationship::inflateFromResponse($neo_db, $response->getResponse());
		}
		
		return new Path($neo_db, $startNode, $endNode, 0, $nodes, $relations);
	}
}

/**
 * Extract ID from Neo4j URI
 * @param string $url
 * @return string
 */
function extractID ($url) {
	return (int)end(explode('/', $url));
}

?>
