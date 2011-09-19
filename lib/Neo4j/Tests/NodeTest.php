<?php
namespace Neo4j\Tests;

use Neo4j\Node;
use Neo4j\Relationship;

/**
 * These Tests are taken from Todd Chaffes Neo4J-REST-PHP-API-client, see link for project
 * 
 * @author tchaffe, pr, fs
 * @link https://github.com/tchaffee/Neo4J-REST-PHP-API-client/tree/master/tests
 *
 */
class NodeTest extends Neo4jRestTestCase
{
    /**
     * @var Node
     */
    protected $node;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        parent::setUp();
        $this->node = $this->graphDb->createNode();
        $this->node->save();
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        try {
            $this->node->delete();
        }
        catch (\Exception $e) {
        }
    }

    /**
     * @todo Should we be checking if the node really was deleted from the
     *    graph db?
     */
    public function testDelete()
    {
        
        $this->node->save();
        
        $this->node->delete();
        
        $this->assertNull($this->node->getId());
        $this->assertEquals(FALSE, $this->node->isSaved());
        
    }

    /**
     * @todo Not sure if we should be retrieving the node back from 
     * 	the graph db to make sure it actually was saved.
     */
    public function testSave()
    {
        $node = $this->node;
        
        $node->save();
        $this->assertEquals(TRUE, $node->isSaved());
        
        // Add a property.  Should save properties to existing node in graph db.
        $prop1 = 'test';
        $node->prop1 = 'test';
        $node->save();
        
        $nodeRetrieved = $this->graphDb->getNodebyId($node->getId());
        $this->assertEquals($node, $nodeRetrieved);
        $this->assertEquals($prop1, $nodeRetrieved->prop1);
        
    }

    /**
     * @todo Implement testGetId().
     */
    public function testGetId()
    {
        
        $node = $this->graphDb->createNode();
        
        $this->assertInternalType('null', $node->getId(), 
            'New node should have Id of type "null"');
        
        $node->save();
        $this->assertInternalType('string', $node->getId(), 
            'Saved node should have Id of type "string"');
        
        $node->delete();
        $this->assertInternalType('null', $node->getId(),
            'Deleted node should have Id of type "null"');                
    }

    /**
     * 
     */
    public function testIsSaved()
    {
    	$node = $this->graphDb->createNode();
        
        $this->assertEquals(false, $node->isSaved(),
            'Newly created node should not show status of "saved".');
        
        $node->save();
        $this->assertEquals(true, $node->isSaved(),
            'Saved node should show status of "saved"');

        $node->delete();
        $this->assertEquals(false, $node->isSaved(),
            'Deleted node should not show status of "saved"');
    }

    /**
     * @todo Implement testGetRelationships().
     */
    public function testGetRelationships()
    {
        // New node should have no relationships.
        $node = $this->node;
        $relationships = $node->getRelationships();
        
        $this->assertInternalType('array', $relationships);
        $this->assertEmpty($relationships, 
            'New node should have no relationships.');
        
        // Create relationship and make sure we can get it back.
        $relType = 'TEST';
        $otherNode = new Node($this->graphDb);
        $node->save();
        $otherNode->save();
        $rel = $this->node->createRelationshipTo($otherNode, 
            $relType);
        $rel->save();
        
        $rels = $node->getRelationships(Relationship::DIRECTION_OUT, 
            array($relType));
        $this->assertInternalType('array', $rels);
        $this->assertEquals(1, sizeof($rels));
        $this->assertInstanceOf('Neo4j\Relationship', $rels[0]);
        $this->assertEquals(TRUE, $rel == $rels[0]);
        
        
        $rel2 = $otherNode->createRelationshipTo($node, $relType);
        $rel2->save();
        $rels = $node->getRelationships();
        $this->assertEquals(2, sizeof($rels), 
            'After adding 2nd relationship the total count should be 2.');
        
        // Cleanup
        $rel->delete();
        $rel2->delete();
        $otherNode->delete();
        
    }

    /**
     * @todo Implement testCreateRelationshipTo().
     */
    public function testCreateRelationshipTo()
    {
        
        $node = $this->node;
        
        $relType = 'TEST';
        $otherNode = new Node($this->graphDb);
        $node->save();
        $otherNode->save();
        $rel = $node->createRelationshipTo($otherNode, 
            $relType);

        $this->assertInstanceOf('Neo4j\Relationship', $rel);
        $this->assertEquals($node, $rel->getStartNode());
        $this->assertEquals($otherNode, $rel->getEndNode());
        $this->assertEquals($relType, $rel->getType());
        
    }

    /**
     * @todo Implement testGetUri().
     */
    public function testGetUri()
    {
        $uri = $this->node->getUri();
        
        $this->assertInternalType('string', $uri);
        $this->assertStringStartsWith($this->graphDb->getBaseUri(), $uri);
                
    }

    /**
     * Node can be inflated from a response
     */
    public function testInflateFromResponse()
    {        
        // Create a dummy response with some properties.
        $id = 10;
        $prop1 = 'prop1';
        $prop1val = 'prop1val';
        $prop2 = 'prop2';
        $prop2val = 'prop2val';
        
        $response = array('self' => $this->node->getUri() . '/' . $id,
            'data' => array($prop1 => $prop1val, $prop2 => $prop2val));        
        
        $node = $this->node->inflateFromResponse($this->graphDb, $response);
        
        $this->assertInstanceOf('Neo4j\Node', $node);
        $this->assertTrue($node->isSaved());
        $this->assertEquals($id, $node->getId());
        $this->assertEquals($prop1val, $node->prop1);
        $this->assertEquals($prop2val, $node->prop2);
    }

    /**
     * @todo Implement testFindPaths().
     */
//    public function testFindPaths()
//    {
//        $node = $this->node;
//        $otherNode = new Node($this->graphDb);
//        
//        $gotException = FALSE;
//        try {
//            $node->findPaths($otherNode);
//        }
//        catch (\Exception $e) {
//            if ($e->getCode() == 405) {
//                $gotException = TRUE;
//            }
//        }
//        
//        $this->assertEquals(TRUE, $gotException, 
//            'Finding a path with an unsaved node should raise ' . 
//            'Neo4jRest_HttpException with code 405');
//        
//        // Now save the two nodes and we should get 204 
//        // Neo4jRest_NotFoundException because they are now valid nodes 
//        // in the graph db, but there is still no path between the nodes.
//        $node->save();
//        $otherNode->save();
//        
//        $gotException = FALSE;
//        try {
//            $paths = $node->findPaths($otherNode);
//        }
//        catch (\Exception $e) {
//            $this->assertEquals(0, $e->getCode());
//            if ($e->getCode() == 0) {
//                $gotException = TRUE;
//            }
//        }
//        
//        $this->assertEquals(TRUE, $gotException, 
//            'Finding paths with an unsaved node should raise ' . 
//            'Neo4jRest_NotFoundException with code 0');
//
//        // Now create a path between the two nodes and we should get back a 
//        // valid list of paths.
//        $relType = 'TEST';
//        $rel = $node->createRelationshipTo($otherNode, $relType);
//        $rel->save();
//        
//        $maxDepth = 1;
////        $relDesc = new RelationshipDescription($relType, 
////            RelationshipDirection::OUTGOING);
////        $paths = $node->findPaths($otherNode, $maxDepth, $relDesc);
////        
////        $this->assertInternalType('array', $paths);
////        $path = $paths[0];
////        $this->assertInstanceOf('Neo4jRest\Path', $path);
////        $this->assertEquals($node, $path->startNode());
////        $this->assertEquals($otherNode, $path->endNode());
////        $rels = $path->relationships();
////        $this->assertEquals($rel, $rels[0]);
////        $this->assertEquals(1, $path->length());
////        
////        $nodesOnPath = $path->nodes();
////        $this->assertEquals(2, sizeof($nodesOnPath));
////        $this->assertEquals($node, $nodesOnPath[0]);
////        $this->assertEquals($otherNode, $nodesOnPath[1]);
//        
//        // Cleanup. $node is automatically cleaned up.
//        $rel->delete();
//        $otherNode->delete();
//        
//    }

    /**
     * 
     */
//    public function testFindPath()
//    {
//        
//        $node = $this->node;
//        $otherNode = new Node($this->graphDb);
//        
//        $gotException = FALSE;
//        try {
//            $node->findPath($otherNode);
//        }
//        catch (\Exception $e) {
//            if ($e->getCode() == 405) {
//                $gotException = TRUE;
//            }
//        }
//        
//        $this->assertEquals(TRUE, $gotException, 
//            'Finding a path with an unsaved node should raise ' . 
//            'Neo4jRest_HttpException with code 405');
//        
//        // Now save the two nodes and we should get 404 
//        // Neo4jRest_NotFoundException
//        // because they are now valid nodes in the graph db, but there is
//        // still no path between the nodes.
//        $node->save();
//        $otherNode->save();
//        
//        $gotException = FALSE;
//        try {
//            $node->findPath($otherNode);
//        }
//        catch (\Exception $e) {
//            if ($e->getCode() == 404) {
//                $gotException = TRUE;
//            }
//        }
//        
//        $this->assertEquals(TRUE, $gotException, 
//            'Finding a path with an unsaved node should raise ' . 
//            'Neo4jRest_NotFoundException with code 404');
//
//        // Now create a path between the two nodes and we should get back a 
//        // valid path.
//        $relType = 'TEST';
//        $rel = $node->createRelationshipTo($otherNode, $relType);
//        $rel->save();
//        
//        $maxDepth = 1;
////        $relDesc = new RelationshipDescription($relType, 
////            RelationshipDirection::OUTGOING);
////        $path = $node->findPath($otherNode, $maxDepth, $relDesc);
////        
////        $this->assertInstanceOf('Neo4jRest\Path', $path);
////        $this->assertEquals($node, $path->startNode());
////        $this->assertEquals($otherNode, $path->endNode());
////        $rels = $path->relationships();
////        $this->assertEquals($rel, $rels[0]);
//        
//        // Cleanup. $node is automatically cleaned up.
//        $rel->delete();
//        $otherNode->delete();        
//        
//    }
}
?>
