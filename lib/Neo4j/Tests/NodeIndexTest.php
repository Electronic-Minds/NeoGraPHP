<?php
namespace Neo4j\Tests;

use Neo4j\IndexManager;
use Neo4j\NodeIndex;
use Neo4j\Node;

/**
 * @author pr, fs
 *
 */
class NodeIndexTest extends AbstractIndexTest {
	/* (non-PHPdoc)
	 * @see Neo4j\Tests.AbstractIndexTest::createItem()
	 */
	public function createItem($save = true, $properties = null) {
		if (true === is_null($properties)) {
			$properties = self::$defaultProperties;
		}
		
		$node = $this->graphDb->createNode();
		foreach ($properties as $property => $value) {
			$node->$property = $value;
		}
		
		if (true == $save) {
			$node->save();
		}
		
		return $node;
	}

	/* (non-PHPdoc)
	 * @see Neo4j\Tests.AbstractIndexTest::getIndexType()
	 */
	public function getIndexType() {
		return IndexManager::INDEX_TYPE_NODE;		
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
	 * @see Neo4j\Tests.AbstractIndexTest::testDelete()
	 */
	public function testDelete() {
		$node = $this->createItem();
		$this->index->add($node);
		$this->index->delete($node);
		
		foreach (self::$defaultProperties as $property => $value) {
			$result = $this->index->get($property, $value);
			$this->assertTrue(is_array($result));
			$this->assertEquals(0, count($result));
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
	 * @see Neo4j\Tests.AbstractIndexTest::testGet()
	 */
	public function testGet() {
		$this->index->add($this->createItem());
		
		$i = 0;
		foreach (self::$defaultProperties as $property => $value) {
			$result = $this->index->get($property, $value);
			$this->assertTrue(is_array($result));
			
			foreach ($result as $node) {
				$this->assertInstanceof('Neo4j\Node', $node);
			}
			
			$i++;
		}
		
		$this->assertEquals(count(self::$defaultProperties), $i);
	}
}

?>
