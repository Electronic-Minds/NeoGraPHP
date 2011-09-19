<?php

namespace Neo4j\Tests;

use Neo4j\Relationship;
use Neo4j\Node;
use Neo4j\GraphDatabaseService;
use Neo4j\Exceptions\HttpNotFoundException;

/**
 * These Tests are taken from Todd Chaffes Neo4J-REST-PHP-API-client, see link for project
 * 
 * @author tchaffe, pr, fs
 * @link https://github.com/tchaffee/Neo4J-REST-PHP-API-client/tree/master/tests
 *
 */
class GraphDatabaseServiceTest extends Neo4jRestTestCase {

	// Todd Chaffee: this seems like a not very useful test.  It is just
	// testing that the graphDB stores a string correctly...
	public function testUrlSetting() {
		$this->assertEquals(
			$this->graphDbUri, 
			$this->graphDb->getBaseUri(), 
			'Hm, it seems like we can not get the URI back.');
	}

	public function testCreateNode() {
		$node = $this->graphDb->createNode();
		
		$this->assertInstanceOf('Neo4j\Node', $node);
	
	}

	// Shallow test.  No properties.
	public function testGetReferenceNodeById() {
		$node = $this->graphDb->createNode();
		$node->save();
		
		$foundNode = $this->graphDb->getNodeById($node->getId());
		
		$this->assertInstanceOf('Neo4j\Node', $foundNode);
		
		$this->assertEquals($node->getId(), $foundNode->getId());
	
	}

	/**
	 * @expectedException Neo4j\Exceptions\HttpNotFoundException
	 */
	public function testGetNodeById() {
		$randomValue = md5(microtime());
		
		$originalNode = $this->graphDb->createNode();
		$originalNode->foobar = $randomValue;
		$originalNode->save();
		
		$originalNodeId = $originalNode->getId();
		unset($originalNode);
		
		$newNode = $this->graphDb->getNodeById($originalNodeId);
		
		$this->assertEquals($randomValue, $newNode->foobar);
		$this->assertEquals($originalNodeId, $newNode->getId());
		
		$newNode->delete();
		
		$newNode = $this->graphDb->getNodeById($originalNodeId);
	}

	/**
	 * @expectedException Neo4j\Exceptions\HttpNotFoundException
	 */
	public function testGetNodeByIdWithInvalidNodeId() {
		$this->graphDb->getNodeById(-1);
	}
}