<?php

namespace Neo4j;

use Neo4j\Exceptions\HttpException;

/**
 * Node index
 * @see Index
 * @author pr
 * @package neo4j-rest-api
 */
class NodeIndex extends Index {
	/**
	 * Get Uri for node indices
	 * @see Index::getIndexUri()
	 * @param string $append
	 * @return string
	 */
	protected function getIndexUri ($append = '/') {
		$indexUri = $this->getIndexBaseUri() . 'node/' .
			$this->getIndexName() . $append;
		return $indexUri;
	}
	
	public function checkItem ($item) {
		if (!$item instanceof Node) {
			throw new \InvalidArgumentException('$node of type Neo4j/Relationship expected, '. get_class($item) . ' given.');
		}
		
		if (false === $item->isSaved()) {
			throw new \RuntimeException('can\'t add unsaved node to the index.');
		}
		
		if (0 >= count($item->getProperties())) {
			throw new \RuntimeException('Unable to save item without properties');
		}		
	}
	
	/**
	 * Add Node to index
	 * @see Index::add()
	 * @param Node $node
	 * @throws HttpException
	 */
	public function add ($node) {
		$this->checkItem($node);
		
		$nodeUri = $this->neoDb->getBaseUri() . 'node/' .  $node->getId();
		foreach ($node->getProperties() as $key => $value) {
			$data = array(
				'key' 		=> $key,
				'value' 	=> $value,
				'uri' 		=> $nodeUri
			);
			
			$response = HTTPUtility::post($this->getIndexUri(), $data);
			
			if (201 != $response->getStatus()) {
				throw new HttpException($response->getStatus());
			}
		}
	}

	/**
	 * Delete Node from Index
	 * @see Index::delete()
	 * @param Node $node
	 * @throws HttpException
	 */
	public function delete ($node) {
		$this->checkItem($node);
		
		foreach ($node->getProperties() as $key => $val) {
			$key = rawurlencode($key);
			$val = rawurlencode($val);
			
			if (Index::MAX_INDEX_KEY_LENGTH < strlen($key) OR Index::MAX_INDEX_VALUE_LENGTH < strlen($val)) {
				throw new \InvalidArgumentException('Key or Value too long!');
			}
			
			if (0 >= strlen($key) OR 0 >= strlen($val)) {
				throw new \InvalidArgumentException('Key or Value too short!');
			}
			
			$indexUri = $this->getIndexUri() . $key . '/' . $val . '/' . $node->getId();
			$response = HTTPUtility::delete($indexUri);
		
			if (204 != $response->getStatus() AND 404 != $response->getStatus()) {
				throw new HttpException($response->getStatus());
			}
		}
	}
	
	/**
	 * Get indexed Nodes by lucene query
	 * @see Index::getByQuery()
	 * @param string $query
	 * @return array
	 */
	public function getByQuery ($query) {
		$indexUri = $this->getIndexUri('') . '?query=' . rawurlencode($query);
		$response = HTTPUtility::get($indexUri);
		
		if (200 != $response->getStatus()) {
			throw new HttpException($response->getStatus());
		}
		
		if (0 >= count($response->getResponse())) {
			return array();
		}

		$nodes = array();
		foreach ($response->getResponse() as $current) {
			$nodes[] = Node::inflateFromResponse($this->neoDb, $current);
		}
		
		return $nodes;
	}
}

?>
