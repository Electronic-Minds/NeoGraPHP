<?php
namespace Neo4j\Tests;

use Neo4j\GraphDatabaseService;

/**
 * These Tests are taken from Todd Chaffes Neo4J-REST-PHP-API-client, see link for project
 * 
 * @author tchaffe, pr, fs
 * @link https://github.com/tchaffee/Neo4J-REST-PHP-API-client/tree/master/tests
 *
 */
class Neo4jRestTestCase extends \PHPUnit_Framework_TestCase {

	/**
	 * 
	 * @var GraphDatabaseService
	 */
	protected $graphDb;

	/**
	 * 
	 * @var string
	 */
	protected $graphDbUri;
	
	protected function setUp() {
		$this->graphDbUri = 'http://10.13.110.114:7475/db/data/';
        
        $this->graphDb = new GraphDatabaseService($this->graphDbUri);
	}
}