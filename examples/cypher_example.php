<?php

use Neo4j\Query\CypherQueryBuilder;
use Neo4j\GraphDatabaseService;

/**
 * Example of basic REST API usage
 *
 * @author onewheelgood, prehfeldt
 * @package neo4j-rest-api
 */
error_reporting(E_ALL);

/**
 * Include the API Class loader
 */
require_once('../lib/Neo4j/ClassLoader.php');
spl_autoload_register('Neo4j\ClassLoader::loadClass', true);

/**
 *	Create a graphDb connection 
 *	Note:	this does not actually perform any network access, 
 *			the server is only accessed when you use the database
 */
$graphDb = new GraphDatabaseService('http://10.13.110.114:7474/db/data/'); // http://127.0.0.1:7474/db/data/

/**
 * 
 * create a test node
 */
$node = $graphDb->createNode();
$node->foo = 'bar';
$node->save();
$nodeId = $node->getId();

/**
 * unset node
 */
unset($node);

/**
 * 
 * get cypher query manager
 */
$cypher = $graphDb->getQueryManager('cypher');

/**
 * 
 * cypher query to get our node
 */
$cypherQuery = new CypherQueryBuilder();
$cypherQuery->start($nodeId)
			->where('foo', 'bar');

/**
 * 
 * run query
 */
$result = $cypher->runQuery($cypherQuery);

/**
 * loop result and display it
 */
foreach ($result as $node) {
	echo $node;
	$node->delete();
}

?>