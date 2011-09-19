<?php

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
$graphDb = new GraphDatabaseService('http://10.13.110.56:7474/db/data/'); // http://127.0.0.1:7474/db/data/

/**
 *	Lets create some nodes
 *	Note: Unlike the java API, these nodes are NOT saved until you call the save() method (see below)
 */
$firstNode = $graphDb->createNode();
$secondNode = $graphDb->createNode();
$thirdNode = $graphDb->createNode();

/**
 *	Assign some attributes to the nodes and save them
 */
$firstNode->message = 'Hello';
$firstNode->foo = 'bar';
$firstNode->save();

$firstNode->foo = NULL;	// Setting to null removes the property
$firstNode->save();


$secondNode->message = 'world!';
$secondNode->float = 42.23;
$secondNode->save();

$thirdNode->message = 'third node';
$thirdNode->integer = 42;
$thirdNode->save();

/**
 *	Create a relationship between some nodes. These can also have attributes.
 *	Note: Relationships also need to be saved before they exist in the DB.
 */
$relationship = $firstNode->createRelationshipTo($secondNode, 'KNOWS');
$relationship->message = 'brave Neo4j';
$relationship->blah = 'blah blah';
$relationship->save();

$relationship->blah = NULL; // Setting to NULL removed the property
$relationship->save();

$relationship2 = $thirdNode->createRelationshipTo($secondNode, 'LOVES');
$relationship2->save();

/**
 *	Dump each node we created (unsing magic function __tostring()
 */
echo '<pre>';
echo $firstNode;
echo $secondNode;
echo $thirdNode;

/**
 * Deleting Nodes
 */
$firstNode->delete();
$secondNode->delete();
$thirdNode->delete();
?>
