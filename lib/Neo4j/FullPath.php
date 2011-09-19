<?php

namespace Neo4j;

/**
 * Full Path
 * @author pr
 * @package neo4j-rest-api
 */
class FullPath extends Path {
	/**
	 * Inflate json result to Path
	 * @param GraphDatabaseService $neo_db
	 * @param string $response
	 * @throws HttpException
	 * @return FullPath
	 */
	public static function inflateFromResponse ($neo_db, $response) {
		$startNode = $neo_db->getNodeById(extractID($response['start']['self']));
		$endNode   = $neo_db->getNodeById(extractID($response['end']['self']));
		
		$nodes = array();
		foreach ($response['nodes'] as $current) {
			$nodes[] = $neo_db->getNodeById(extractID($current['self']));
		}
		
		$relations = array();
		foreach ($response['relationships'] as $current) {
			$response = HTTPUtility::get($current['self']);
			
			if (200 != $response->getStatus()) {
				throw new HttpException($response->getStatus());
			}
			
			$relations[] = Relationship::inflateFromResponse($neo_db, $response->getResponse());
		}
		
		return new Path($neo_db, $startNode, $endNode, $response['length'], $nodes, $relations);
	}
}

?>
