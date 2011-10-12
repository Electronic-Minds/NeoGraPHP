<?php

namespace Neo4j\Tests;

use Neo4j\GraphDatabaseService;
use Neo4j\IndexManager;
use Neo4j\Node;

/**
 * @author pr, fs
 *
 */
abstract class AbstractIndexTest extends Neo4jRestTestCase {
	protected $index;
	protected $indexManager;
	
	protected static $defaultProperties = array('foo' => 'bar');
	
	public function setUp() {
		parent::setUp();
		
		$this->indexManager = $this->graphDb->getIndexManager();
		$this->index = $this->indexManager->create(md5(microtime()), $this->getIndexType());
	}

	public function tearDown() {
		parent::tearDown();
		
		if (true == isset($this->index)) {
			$this->indexManager->delete($this->index->getIndexName(), $this->getIndexType());
		}
	}
	
	/*
	 * Helper
	 */
	abstract public function getIndexType();
	abstract public function createItem($save = true, $properties = null);

	/*
	 * checkItem
	 */
	abstract public function testCheckItemWithWrongItem();
	abstract public function testCheckItemWithCorrectUnsavedItem();
	abstract public function testCheckItemWithCorrectSavedItemWithoutProperties();

	/*
	 * get
	 */
	abstract public function testGet();
	
	/*
	 * delete
	 */
	abstract public function testDeleteWithTooLongKey();
	abstract public function testDeleteWithTooLongValue();
	abstract public function testDeleteWithTooShortKey();
	abstract public function testDeleteWithTooShortValue();
	abstract public function testDelete();
}

?>