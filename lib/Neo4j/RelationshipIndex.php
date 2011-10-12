<?php

namespace Neo4j;

use Neo4j\Exceptions\HttpException;

/**
 * Relationship index
 * @see Index
 * @author pr
 * @package neo4j-rest-api
 */
class RelationshipIndex extends Index {
	/**
	 * Get Uri for relationship indices
	 * @see Index::getIndexUri()
	 * @param string $append
	 * @return string
	 */
	protected function getIndexUri($append = '/') {
		$indexUri = $this->getIndexBaseUri() . 'relationship/' .
			$this->getIndexName() . $append;
		return $indexUri;
	}
	
	public function checkItem ($item) {
		if (!$item instanceof Relationship) {
			throw new \InvalidArgumentException('$node of type Neo4j/Relationship expected');
		}
		
		if (false === $item->isSaved()) {
			throw new \RuntimeException('can\'t add unsaved relationships to the index.');
		}
		
		if (0 >= count($item->getProperties())) {
			throw new \RuntimeException('Unable to save relationship without properties');
		}
	}
	
	/**
	 * Add relationship to index
	 * @see Index::add()
	 * @param Relationship $relation
	 * @throws HttpException
	 */
	public function add ($relation) {		
		$this->checkItem($relation);
		
		$relationUri = $this->neoDb->getBaseUri() . 'relationship/' .  $relation->getId();
		foreach ($relation->getProperties() as $key => $value) {
			$data = array(
				'key' 		=> $key,
				'value' 	=> $value,
				'uri' 		=> $relationUri
			);
			
			$response = HTTPUtility::post($this->getIndexUri(), $data);
			
			if (201 != $response->getStatus()) {
				throw new HttpException($response->getResponseAsJson(), $response->getStatus());
			}
		}
	}
	
	/**
	 * Delete relationship from Index
	 * @see Index::delete()
	 * @param Relationship $relation
	 * @throws HttpException
	 */
	public function delete ($relation) {
		$this->checkItem($relation);
		
		foreach ($relation->getProperties() as $key => $val) {
			$key = rawurlencode($key);
			$val = rawurlencode($val);

			if (Index::MAX_INDEX_KEY_LENGTH < strlen($key) OR Index::MAX_INDEX_VALUE_LENGTH < strlen($val)) {
				throw new \InvalidArgumentException('Key or Value too long!');
			}
			
			if (0 >= strlen($key) OR 0 >= strlen($val)) {
				throw new \InvalidArgumentException('Key or Value too short!');
			}
			
			$indexUri = $this->getIndexUri() . $key . '/' . $val . '/' . $relation->getId();
			
			$response = HTTPUtility::delete($indexUri);
		
			if (204 != $response->getStatus() AND 404 != $response->getStatus()) {
				throw new HttpException($response->getStatus());
			}
		}
	}
	
	/**
	 * Get indexed relationships by lucene query
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

		$relations = array();
		foreach ($response->getResponse() as $current) {
			$relations[] = Relationship::inflateFromResponse($this->neoDb, $current);
		}
		
		return $relations;
	}
}

?>
