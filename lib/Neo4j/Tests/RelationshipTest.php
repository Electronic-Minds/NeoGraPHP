<?php

namespace Neo4j\Tests;

use Neo4j\Relationship;

use Neo4j\Node;

/**
 * These Tests are taken from Todd Chaffes Neo4J-REST-PHP-API-client, see link for project
 * 
 * @author tchaffe, pr, fs
 * @link https://github.com/tchaffee/Neo4J-REST-PHP-API-client/tree/master/tests
 *
 */
class RelationshipTest extends Neo4jRestTestCase
{
    /**
     * @var Relationship
     */
    protected $relationship;
    protected $relType = 'TEST';
    protected $startNode = NULL;
    protected $endNode = NULL;
    
    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        parent::setUp();
        $this->startNode = $this->graphDb->createNode();
        $this->startNode->save();
        $this->endNode = $this->graphDb->createNode();
        $this->endNode->save();
        
        $this->relationship = $this->startNode->createRelationshipTo($this->endNode, $this->relType);
        $this->relationship->save();
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        try {
            $this->startNode->delete();
            
            if ($this->endNode) {
            	$this->endNode->delete();	
            }
            
            if ($this->relationship) {
            	$this->relationship->delete();
            }
        }
        catch(\Exception $e ) {}
    }

    
    /**
     * 
     */
    public function test__Contruct()
    {
        
        $this->startNode = new Node($this->graphDb);
        $this->startNode->save();
        $this->endNode = new Node($this->graphDb);
        $this->endNode->save();

        // Empty relationship type.
        try {
        	$this->relationship = $this->startNode->createRelationshipTo($this->endNode, '');
        	$this->relationship->save();
        	
        	$this->fail();
        } catch (\Exception $e) {
        	$this->assertTrue(true);
        }
        
        // Non-string relationship type.
        try {
        	$this->relationship = $this->startNode->createRelationshipTo($this->endNode, 42);
        	$this->relationship->save();
        	
        	$this->fail();
        } catch (\Exception $e) {
        	$this->assertTrue(true);
        }
    }
    
    /**
     * 
     */
    public function testGetId()
    {
        
        $rel = $this->startNode->createRelationshipTo($this->endNode, $this->relType);    
        
        $this->assertInternalType('null', $rel->getId(), 
            'New Relationship should have Id of type "null"');
        
        $rel->save();
        $this->assertInternalType('string', $rel->getId(), 
            'Saved Relationship should have Id of type "string"');
        
        $rel->delete();
        $this->assertInternalType('null', $rel->getId(),
            'Deleted Relationship should have Id of type "null"');
        
        
    }

    /**
     *
     */
    public function testIsSaved()
    {
        $rel = $this->startNode->createRelationshipTo($this->endNode, $this->relType);
        
        $this->assertEquals(FALSE, $rel->isSaved(),
            'Newly created Relationship should not show status of "saved".');
        
        $rel->save();
        $this->assertEquals(TRUE, $rel->isSaved(),
            'Saved Relationship should show status of "saved"');

        $rel->delete();
        $this->assertEquals(FALSE, $rel->isSaved(),
            'Deleted Relationship should not show status of "saved"');
    }

    /**
     *
     */
    public function testGetType()
    {
        $rel = $this->relationship;
        
        $this->assertEquals($this->relType, $rel->getType());
    }

    /**
     * 
     */
    public function testIsType()
    {
        $this->assertTrue($this->relationship->isType($this->relType));
    }

    /**
     * @todo Implement testGetStartNode().
     */
    public function testGetStartNode()
    {
        $rel = $this->relationship;
        
        $this->assertEquals($this->startNode, $rel->getStartNode());
    }

    /**
     * @todo Implement testGetEndNode().
     */
    public function testGetEndNode()
    {
        $rel = $this->relationship;
        
        $this->assertEquals($this->endNode, $rel->getEndNode());
    }

    /**
     * 
     */
    public function testGetOtherNode()
    {
        $relationship = $this->relationship;
        
        $node = $relationship->getOpposingNode($this->startNode);
        $this->assertEquals($this->endNode, $node);
        
        $node = $relationship->getOpposingNode($this->endNode);
        $this->assertEquals($this->startNode, $node);
        
        $bogusNode = $this->graphDb->createNode();
        try {
            $node = $relationship->getOpposingNode($bogusNode);
            $this->fail();
        } catch (\Exception $e) {
        	$this->assertTrue(true);
        }
    }

    /**
     * 
     */
    public function testSave()
    {
        $rel = $this->relationship;
        
        $rel->save();
        $this->assertEquals(TRUE, $rel->isSaved());
        
        // Add a property.  Should save properties to existing node in graph db.
        $prop1 = 'test';
        $rel->prop1 = 'test';
        $rel->save();
        
        $this->assertEquals($prop1, $rel->prop1);
    }

    /**
     * @todo Implement testDelete().
     */
    public function testDelete()
    {
        
        $rel = $this->relationship;
        $id = $rel->getId();
        
        $rel->save();
        
        $rel->delete();
        
        $this->assertNull($rel->getId());
        $this->assertEquals(FALSE, $rel->isSaved());
    }

    /**
     * @todo Implement testGetUri().
     */
    public function testGetUri()
    {
        $uri = $this->relationship->getUri();
        
        $this->assertInternalType('string', $uri);
        $this->assertStringStartsWith($this->graphDb->getBaseUri(), $uri);
    }
}
?>
