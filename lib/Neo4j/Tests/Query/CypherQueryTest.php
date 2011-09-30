<?php

namespace Neo4j\Tests\Query;

use Neo4j\Node;
use Neo4j\Query\CypherQueryBuilder;
use Neo4j\Query\CypherQuery;
use Neo4j\Tests\Neo4jRestTestCase;

class CypherQueryTest extends Neo4jRestTestCase {
	public $query;

	public function setUp() {
		parent::setUp();
		
		$this->query = new CypherQuery($this->graphDb);
	}
	
	/**
	 * @expectedException Neo4j\Exceptions\HttpException
	 */
	public function testRunQueryWithBadQuery() {
		$query = new CypherQueryBuilder('');
		$tesult = $this->query->runQuery($query);
	}
	
	public function testRunQueryWithGoodQuery() {
		$query = new CypherQueryBuilder('START a = (0) RETURN a');
		$result = $this->query->runQuery($query);
		
		$this->assertTrue(is_array($result));
		$this->assertTrue($result[0] instanceof Node);
	}
}

?>