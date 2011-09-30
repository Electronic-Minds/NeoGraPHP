<?php

namespace Neo4j\Query;

use Neo4j\Node;

use Neo4j\Exceptions\HttpException;

use Neo4j\HTTPUtility;

/**
 * 
 * @author pr
 *
 */
class CypherQuery extends AbstractQuery {
	/* (non-PHPdoc)
	 * @see Neo4j\Query.AbstractQuery::runQuery()
	 */
	public function runQuery(AbstractQueryBuilder $query) {
		$url = $this->graphDb->getBaseUri() . 'ext/CypherPlugin/graphdb/execute_query';
		$response = HTTPUtility::post($url, $query->getQuery());
		
		if (200 == $response->getStatus()) {
			return $this->inflateResponse($response->getResponse());
		} else {
			throw new HttpException($response->getResponseAsJson(), $response->getStatus());
		}
	}
	
	/* (non-PHPdoc)
	 * @see Neo4j\Query.AbstractQuery::inflateResponse()
	 */
	protected function inflateResponse($result) {
		$nodes = array();
		foreach ($result['data'] as $item) {
			$nodes[] = Node::inflateFromResponse($this->graphDb, $item[0]);
		}
		
		return $nodes;
	}
}

?>