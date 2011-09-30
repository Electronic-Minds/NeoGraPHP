Neo4j REST API Client for PHP 5.3+
==================================

Usage
-----
    $graphDb = new GraphDatabaseService('http://path/to/Neo4j'); 
    $node = $graphDb->createNode();
    $node->message = 'Hello World';
    $node->save();
    
    $helloNode = $graphDb->getNodeById($node->getId());
    echo $helloNode;

More examples can be found in the examples/ folder.

If you're looking for a Symfony2 Bundle to access Neo4j, you might be interested in [NeoGraPHP Symfony2 Bundle](https://github.com/Electronic-Minds/NeoGraPHPBundle).

Tests
-----
The UnitTests are located in lib/Neo4j/Tests/ and can be run with [phpunit](https://github.com/sebastianbergmann/phpunit).

Documentations
--------------
Use [DocBlox](https://github.com/mvriel/Docblox) to create a documentation.

License
-------
GNU GENERAL PUBLIC LICENSE Version 3, 29 June 2007

Credits
-------
Provided by [Electronic Minds GmbH](http://www.electronic-minds.de/) as is and without any warranty.
Based on the work from [onewheelgood](https://github.com/onewheelgood) and [tchaffe](https://github.com/tchaffee)
