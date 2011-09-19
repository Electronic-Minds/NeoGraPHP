<?php
namespace Neo4j\Tests;

use Neo4j\IndexManager;

/**
 * These Tests are taken from Todd Chaffes Neo4J-REST-PHP-API-client, see link for project
 * 
 * @author tchaffe, pr, fs
 * @link https://github.com/tchaffee/Neo4J-REST-PHP-API-client/tree/master/tests
 *
 */
class IndexManagerTest extends Neo4jRestTestCase
{
   /**
    * @var IndexManager
    */
   protected $indexMgr;

   /**
    * Sets up the fixture, for example, opens a network connection.
    * This method is called before a test is executed.
    */
   protected function setUp()
   {
      parent::setUp();
      $this->indexMgr = $this->graphDb->getIndexManager();
   }

   /**
    * Tears down the fixture, for example, closes a network connection.
    * This method is called after a test is executed.
    */
   protected function tearDown()
   {
   }

   /**
    * @todo Delete the index fixture when tests are complete. Not yet
    * 	available in the REST api.
    */
   public function testNodeIndexNames()
   {

      $response = $this->indexMgr->getIndexList(IndexManager::INDEX_TYPE_NODE);
      $countBefore =  count($response);
       
      // Create an index so we can test if it is listed, as well as making
      // sure there is at least one item on the list for checking keys.
      $indexName = strval(mt_rand());
      $index = $this->indexMgr->create($indexName, IndexManager::INDEX_TYPE_NODE);
       
      $response = $this->indexMgr->getIndexList(IndexManager::INDEX_TYPE_NODE);
      $countAfter = count($response);
      
      // Make sure we got back an Index
      $this->assertInstanceOf('Neo4j\Index', $index);
      
      $this->assertTrue($countAfter >= $countBefore, 'Count of total'. 
      	' indexes should have increased.');
      
      $this->assertArrayHasKey($indexName, $response, 'Newly created' . 
         ' index should be in the list');

   }

   /**
    * @todo Delete the index fixture when tests are complete. Not yet
    * 	available in the REST api.
    */
   public function testRelationshipIndexNames()
   {

      $response = $this->indexMgr->getIndexList(IndexManager::INDEX_TYPE_RELATION);
      $countBefore =  count($response);
       
      // Create an index so we can test if it is listed, as well as making
      // sure there is at least one item on the list for checking keys.
      $indexName = strval(mt_rand());
      $index = $this->indexMgr->create($indexName, IndexManager::INDEX_TYPE_RELATION);
       
      $response = $this->indexMgr->getIndexList(IndexManager::INDEX_TYPE_RELATION);
      $countAfter = count($response);
      
      $this->assertInstanceOf('Neo4j\RelationshipIndex', $index);
      
      $this->assertTrue($countAfter >= $countBefore, 'Count of total'. 
      	' indexes should have increased.');
      
      $this->assertArrayHasKey($indexName, $response, 'Newly created' . 
         ' index should be in the list');      
   }
}
?>
