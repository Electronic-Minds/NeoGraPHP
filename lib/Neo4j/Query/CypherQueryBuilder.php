<?php

namespace Neo4j\Query;

/**
 * 
 * @author pr
 *
 */
class CypherQueryBuilder extends AbstractQueryBuilder {
	const START_NODE_NAME = 'startNode';
	const END_NODE_NAME = 'endNode';
	
	public function buildQuery() {
		$format = 'RETURN %s';
		$this->parts['RETURN'] = sprintf($format, self::END_NODE_NAME);

		$query = join(' ', $this->parts); 
		
		if (false == array_key_exists('MATCH', $this->parts)) {
			$query = str_replace(self::END_NODE_NAME, self::START_NODE_NAME, $query);
		}
		
		return $query;
	}
	
	/**
	 * 
	 * @param integer $id
	 */
	public function start($id) {
		$format = 'START %s = (%d)';
		$this->parts['START'] = sprintf($format, self::START_NODE_NAME, $id);
		
		return $this;
	}
	
	/**
	 * 
	 * @param string $relation
	 */
	public function match($relation = '') {
		if (0 < mb_strlen($relation)) {
			$relation = '[:' . $relation . ']';
		}
		
		$format = 'MATCH (%s)-%s->(%s)';
		$this->parts['MATCH'] = sprintf($format, self::START_NODE_NAME, $relation, self::END_NODE_NAME);
		
		return $this;
	}
	
	/**
	 * 
	 * @param string $key
	 * @param string $value
	 */
	public function where($key, $value) {
		$format = 'WHERE %s.%s = "%s"';
		$this->parts['WHERE'] = sprintf($format, self::END_NODE_NAME, $key, $value);
		
		return $this;
	}
}

?>