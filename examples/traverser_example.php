<?php

use Neo4j\GraphDatabaseService;
use Neo4j\Traverser;
use Neo4j\Relationship;

/**
 * Example of traversal access
 *
 * @author prehfeldt
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
$graphDb = new GraphDatabaseService('http://10.13.110.56:7474/db/data/'); // http://127.0.0.1:7474/db/data/

/**
 * Create a root node
 */
$root = $graphDb->createNode();
$root->name = 'Traverser Demo Root Node';
$root->save();

/**
 * Create a nested structure
 */
$nodes = array();
for ($i = 0; $i < 10; $i++) {
	$node = $graphDb->createNode();
	$node->name = 'Child ' . $i;
	$node->foo = 'bar';
	$node->save();

	$nodes[] = $node;
	
	// create some subchilds
	for ($n = 0; $n < 3; $n++) {
		$child = $graphDb->createNode();
		$child->name = 'Subchild ' . $i . '.' . $n;
		$child->save();

		$nodes[] = $child;
		
		$node->createRelationshipTo($child, 'KNOWS');
	}
	
	$root->createRelationshipTo($node, 'KNOWS')->save();
}

/**
 * Create a new Traverser and set parameters
 */
$traverser = $graphDb->createTraverser();
$traverser->setUniqueness(Traverser::UNIQUENESS_NODE_GLOBAL);
$traverser->addRelation('KNOWS', Relationship::DIRECTION_OUT);
$traverser->setDepth(100);

/**
 * Run traverser on node
 */
$result = $root->runTraverser($traverser, Traverser::RETURN_NODE);

/**
 * Display all found nodes
 */
echo '<pre>';
foreach ($result as $current) {
	echo $current;
}

/**
 * Delete Nodes
 */
foreach ($nodes as $node) {
	$node->delete();
}
$root->delete();
?>
