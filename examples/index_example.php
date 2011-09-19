<?php

use Neo4j\GraphDatabaseService;
use Neo4j\IndexManager;

/**
 * Example of index usage
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
 * Get IndexManager
 */
$indexManager = $graphDb->getIndexManager();

/**
 * Create a new Index
 */
$indexName = 'Neo4j-REST-API-Client_Testindex_' . md5(microtime());
$index = $indexManager->create($indexName, IndexManager::INDEX_TYPE_NODE);

/**
 * Create a root node
 */
$root = $graphDb->createNode();
$root->name = 'Index Demo Root Node';
$root->save();

/**
 * Push root node to index
 */
$index->add($root);

/**
 * Add some more nodes
 */
$nodes = array();
for ($i = 0; $i < 10; $i++) {
	$node = $graphDb->createNode();
	$node->name = 'Child ' . $i;
	$node->foo = 'bar';
	$node->save();
	
	$nodes[] = $node;
	
	// Add node to index
	$index->add($node);
	
	$root->createRelationshipTo($node, 'KNOWS')->save();
}

/**
 * Search index
 * @link http://lucene.apache.org/java/2_4_0/queryparsersyntax.html
 */
$result = $index->getByQuery('name:*');

/**
 * Display all found nodes
 */
echo '<pre>';
foreach ($result as $current) {
	echo $current;
}

/**
 * Delete Index
 */
$indexManager->delete($indexName, IndexManager::INDEX_TYPE_NODE);

/**
 * Delete Nodes
 */
foreach ($nodes as $node) {
	$node->delete();
}
$root->delete();
?>
