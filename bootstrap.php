<?php

require_once 'lib/Neo4j/ClassLoader.php';

spl_autoload_register("Neo4j\ClassLoader::loadClass", true);