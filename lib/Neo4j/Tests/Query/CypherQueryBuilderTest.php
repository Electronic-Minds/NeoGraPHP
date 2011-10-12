<?php

namespace Neo4j\Tests\Query;

use Neo4j\Query\CypherQueryBuilder;
use Neo4j\Tests\Neo4jRestTestCase;

class CypherQueryBuilderTest extends Neo4jRestTestCase {
	public $queryBuilder;
	
	public function setUp() {
		parent::setUp();
		
		$this->queryBuilder = new CypherQueryBuilder();
	}
	
	public function testBuildQueryWithoutQuery() {
		$result = $this->queryBuilder->buildQuery();
		
		$this->assertEquals('RETURN startNode', $result);
	}
	
	public function testBuildQueryWithQuery() {
		$this->queryBuilder->start(42)
						   ->match('');
		
		$result = $this->queryBuilder->buildQuery();
		
		$this->assertEquals('START startNode = node(42) MATCH (startNode)-->(endNode) RETURN endNode', $result);
	}
}

?>