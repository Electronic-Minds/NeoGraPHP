<?php

namespace Neo4j;

/**
 * Traverser
 * @author pr
 * @package neo4j-rest-api
 */
class Traverser {
	/**
	 * Order breadth first
	 * @var string
	 */
	const ORDER_BREADTH_FIRST = 'breadth first';
	
	/**
	 * Order depth first
	 * @var string
	 */
	const ORDER_DEPTH_FIRST = 'depth first';
	
	/**
	 * Uniqueness node path
	 * @var string
	 */
	const UNIQUENESS_NODE_PATH = 'node path';
	
	/**
	 * Uniqueness node global
	 * @var string
	 */
	const UNIQUENESS_NODE_GLOBAL = 'node global';
	
	/**
	 * Return filter all
	 * @var string
	 */
	const RETURN_FILTER_ALL = 'all';
	
	/**
	 * Return filter all but start node
	 * @var string
	 */
	const RETURN_FILTER_ALL_BUT_START = 'all but start node';
	
	/**
	 * Return type Node
	 * @var string
	 */
	const RETURN_NODE = 'node';
	
	/**
	 * Return type relation
	 * @var string
	 */
	const RETURN_RELATION = 'relationship';
	
	/**
	 * Return type path
	 * @var string
	 */
	const RETURN_PATH = 'path';
	
	/**
	 * Return type full path
	 * @var string
	 */
	const RETURN_FULL_PATH = 'fullpath ';
	
	/**
	 * Order
	 * @var string
	 */
	protected $order;
	
	/**
	 * Uniqueness
	 * @var string
	 */
	protected $uniqueness;
	
	/**
	 * Relationships
	 * @var array
	 */
	protected $relationship;
	
	/**
	 * Prune Evaluator
	 * @var array
	 */
	protected $pruneEvaluator;
	
	/**
	 * Return filter
	 * @var array
	 */
	protected $returnFilter;
	
	/**
	 * Max Depth
	 * @var int
	 */	
	protected $depth;
	
	/**
	 * Constructor
	 */
	public function __construct() {
		/* */
	}
	
	/**
	 * Get Traverser settings
	 * @return array
	 */
	public function getSettings () {
		$data = array(
			'order'           => $this->order,
			'uniqueness'      => $this->uniqueness,
			'relationships'   => $this->relationship,
			'prune evaluator' => $this->pruneEvaluator,
			'return filter'   => $this->returnFilter,
			'max depth'       => $this->depth
		);
		
		return $data;
	}
	
	/**
	 * Set Order
	 * @param string $order
	 */
	public function setOrder ($order) {
		$this->order = $order;
	}
	
	/**
	 * Set uniqueness
	 * @param string $uniqueness
	 */
	public function setUniqueness ($uniqueness) {
		$this->uniqueness = $uniqueness;
	}
	
	/**
	 * Add a Relation
	 * @param string $type
	 * @param string $direction
	 */
	public function addRelation ($type, $direction = Relationship::DIRECTION_OUT) {
		$this->relationship[] = array(
			'type'      => $type,
			'direction' => strtolower($direction)
		);
	}
	
	/**
	 * set Prune Evaluator
	 * @param string $evaluator
	 * @param string $language
	 */
	public function setPruneEvaluator ($evaluator, $language = 'javascript') {
		$this->pruneEvaluator = array(
			'language' => $language,
			'body'     => $evaluator
		);
	}
	
	/**
	 * Set return filter
	 * @param string $filter
	 * @param string $language
	 */
	public function setReturnFilter ($filter, $language = 'builtin') {
		$this->returnFilter = array(
			'language' => $language,
			'name'     => $filter
		);
	}
	
	/**
	 * Set max depth
	 * @param int $depth
	 */
	public function setDepth ($depth) {
		$this->depth = (int)$depth;
	}
}

?>
