<?php

namespace Neo4j\Tests;

use Neo4j\GraphDatabaseService;
use Neo4j\IndexManager;
use Neo4j\Node;
use Neo4j\Relationship;
use Neo4j\RelationshipIndex;
use Neo4j\HttpException;

/**
 * @author pr, fs
 *
 */
class RelationshipIndexTest extends AbstractIndexTest {
	public function getIndexType() {
		return IndexManager::INDEX_TYPE_RELATION;
	}

	public function createItem($save = true, $properties = null) {
		if (true === is_null($properties)) {
			$properties = self::$defaultProperties;
		}
		
		$startNode = $this->graphDb->createNode();
		$startNode->foo = 'bar';
		$startNode->save();
		
		$endNode = $this->graphDb->createNode();
		$endNode->bar = 'foo';
		$endNode->save();
		
		$relationship = $startNode->createRelationshipTo($endNode, 'KNOWS');
		foreach ($properties as $property => $value) {
			$relationship->$property = $value;
		}
		
		if (true == $save) {
			$relationship->save();
		}
		
		return $relationship;
	}

	/* (non-PHPdoc)
	 * @see Neo4j\Tests.AbstractIndexTest::testCheckItemWithWrongItem()
	 */
	public function testCheckItemWithWrongItem() {
		try {
			$this->index->checkItem(new \stdClass());
			$this->fail();
		} catch (\InvalidArgumentException $e) {
			$this->assertTrue(true);
		}
	}

	/* (non-PHPdoc)
	 * @see Neo4j\Tests.AbstractIndexTest::testCheckItemWithCorrectUnsavedItem()
	 */
	public function testCheckItemWithCorrectUnsavedItem() {
		try {
			$this->index->checkItem($this->createItem($save = false));
			$this->fail();
		} catch (\RuntimeException $e) {
			$this->assertTrue(true);
		}
	}
	
	/* (non-PHPdoc)
	 * @see Neo4j\Tests.AbstractIndexTest::testCheckItemWithCorrectSavedItemWithoutProperties()
	 */
	public function testCheckItemWithCorrectSavedItemWithoutProperties() {
		try {
			$this->index->checkItem($this->createItem($save = true, $properties = array()));
			$this->fail();
		} catch (\RuntimeException $e) {
			$this->assertTrue(true);
		}
	}
	
	/* (non-PHPdoc)
	 * @see Neo4j\Tests.AbstractIndexTest::testAdd()
	 */
	public function testGet() {
		$this->index->add($this->createItem());
		
		$i = 0;
		foreach (self::$defaultProperties as $property => $value) {
			$result = $this->index->get($property, $value);
			$this->assertTrue(is_array($result));
			
			foreach ($result as $relationship) {
				$this->assertInstanceof('Neo4j\Relationship', $relationship);
			}
			
			$i++;
		}
		
		$this->assertEquals(count(self::$defaultProperties), $i);
	}
	
	/* (non-PHPdoc)
	 * @see Neo4j\Tests.AbstractIndexTest::testDeleteWithTooShortValue()
	 */
	public function testDeleteWithTooShortValue() {
		try {
			$this->index->delete($this->createItem($save = true, $properties = array('a' => '')));
			$this->fail();
		} catch (\InvalidArgumentException $e) {
			$this->assertTrue(true);
		}
	}
	
	/* (non-PHPdoc)
	 * @see Neo4j\Tests.AbstractIndexTest::testDeleteWithTooShortKey()
	 */
	public function testDeleteWithTooShortKey() {
		try {
			$this->index->delete($this->createItem($save = true, $properties = array('' => 'b')));
			$this->fail();
		} catch (\InvalidArgumentException $e) {
			$this->assertTrue(true);
		}
	}

	/* (non-PHPdoc)
	 * @see Neo4j\Tests.AbstractIndexTest::testDeleteWithTooLongValue()
	 */
	public function testDeleteWithTooLongValue() {
		try {
			$this->index->delete($this->createItem($save = true, $properties = array('a' => str_repeat('b', 1025))));
			$this->fail();
		} catch (\InvalidArgumentException $e) {
			$this->assertTrue(true);
		}
	}
	
	/* (non-PHPdoc)
	 * @see Neo4j\Tests.AbstractIndexTest::testDeleteWithTooLongKey()
	 */
	public function testDeleteWithTooLongKey() {
		try {
			$this->index->delete($this->createItem($save = true, $properties = array(str_repeat('a', 513) => 'b')));
			$this->fail();
		} catch (\InvalidArgumentException $e) {
			$this->assertTrue(true);
		}
	}
	
	/* (non-PHPdoc)
	 * @see Neo4j\Tests.AbstractIndexTest::testDelete()
	 */
	public function testDelete() {
		$relationship = $this->createItem();
		$this->index->add($relationship);
		$this->index->delete($relationship);
		
		foreach (self::$defaultProperties as $property => $value) {
			$result = $this->index->get($property, $value);
			$this->assertTrue(is_array($result));
			$this->assertEquals(0, count($result));
		}
	}
}

?>
