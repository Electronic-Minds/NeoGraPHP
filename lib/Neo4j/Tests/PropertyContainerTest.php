<?php

namespace Neo4j\Tests;

use Neo4j\PropertyContainer;

/**
 * These Tests are taken from Todd Chaffes Neo4J-REST-PHP-API-client, see link for project
 * 
 * @author tchaffe, pr, fs
 * @link https://github.com/tchaffee/Neo4J-REST-PHP-API-client/tree/master/tests
 *
 */
class PropertyContainerTest extends Neo4jRestTestCase {
	protected $propertyContainer;
	
	public function setUp() {
		parent::setUp();
		$this->propertyContainer = new PropertyContainerImpl();
	}

	public function test__Set() {
		
		$propCont = $this->propertyContainer;
		$propVal = 'testing';
		$prop = 'newProp';
		
		$propCont->newProp = $propVal;
		$this->assertEquals($propVal, $propCont->newProp);
	}

	public function test__Get() {
		
		$propCont = $this->propertyContainer;
		$propVal = 'testing';
		
		$this->assertObjectNotHasAttribute('newProp', $propCont);
		$this->assertNull($propCont->newProp);
		
		$propCont->newProp = $propVal;
		$valRetrieved = $propCont->newProp;
		$this->assertEquals($propVal, $valRetrieved);
	}

	public function testSetProperties() {
		$propCont = $this->propertyContainer;
		
		$prop1 = 'prop1';
		$prop1Val = 'prop1Val';
		$prop2 = 'prop2';
		$prop2Val = 'prop2Val';
		
		$this->assertObjectNotHasAttribute($prop1, $propCont);
		$this->assertObjectNotHasAttribute($prop2, $propCont);
		
		$data = array(
			$prop1 => $prop1Val, 
			$prop2 => $prop2Val
		);
		
		$propCont->setProperties($data);
		
		$this->assertEquals($prop1Val, $propCont->prop1);
		$this->assertEquals($prop2Val, $propCont->prop2);
	
	}

	public function testGetProperties() {
		$propCont = $this->propertyContainer;
		
		$prop1 = 'prop1';
		$prop1Val = 'prop1Val';
		$prop2 = 'prop2';
		$prop2Val = 'prop2Val';
		
		$data = $propCont->getProperties();
		$this->assertEmpty(
			$data, 
			'New PropertyContainer should produce ' . 'empty result from getProperties().');
		
		$propCont->$prop1 = $prop1Val;
		$propCont->$prop2 = $prop2Val;
		
		$data = $propCont->getProperties();
		$this->assertInternalType('array', $data);
		$this->assertArrayHasKey($prop1, $data);
		$this->assertArrayHasKey($prop2, $data);
		$this->assertEquals($prop1Val, $data[$prop1]);
		$this->assertEquals($prop2Val, $data[$prop2]);
	}

}