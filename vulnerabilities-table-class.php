<?php
//Block direct access to the file
if ( !function_exists( 'add_action' ) ) {
	exit; 
}
class Vulnerabilities_Table extends WP_List_Table {

	private $vulnerability_data = array();
	
	public function prepare_items() {
		$columns = $this->get_columns();
		$hidden = array();
		$sortable = $this->get_sortable_columns();
		$data = $this->vulnerability_data;
		usort( $data, array( &$this, 'sort_data' ) );
		$this->_column_headers = array($columns, $hidden, $sortable);
		$this->items = $data;
	}
	
	public function get_columns() {
		$columns = array(
			'name' => __('Plugin', 'plugin-vulnerabilities'),
			'versions' => __('Vulnerable Versions', 'plugin-vulnerabilities'),
			'type' => __('Type of Vulnerability', 'plugin-vulnerabilities'),
			'exploitation' => __('Likelihood of Exploitation', 'plugin-vulnerabilities'),
			'details' => __('Vulnerability Details', 'plugin-vulnerabilities')
		);
		return $columns;
	}

	public function get_sortable_columns() {
		$sortable_columns = array(
			'name' => array( 'name', true ),
			'type' => array( 'type', false ),
			'exploitation' => array( 'type', false )
		);

		return $sortable_columns;
	}

	public function column_default( $item, $column_name )
	{
		return $item[ $column_name ];
	}

	private function sort_data( $a, $b )
	{
		$orderby = 'name';
		$order = 'asc';
		if(!empty($_GET['orderby']))
		{
			$orderby = $_GET['orderby'];
		}
		if(!empty($_GET['order']))
		{
			$order = $_GET['order'];
		}
		$result = strcmp( $a[$orderby], $b[$orderby] );
		if($order === 'asc')
		{
			return $result;
		}
		return -$result;
	}
	
	public function add_data( $vulnerability_data ) {
		$this->vulnerability_data = $vulnerability_data;
	}

}

class False_Vulnerabilities_Table extends WP_List_Table {

	private $vulnerability_data = array();
	
	public function prepare_items() {
		$columns = $this->get_columns();
		$hidden = array();
		$sortable = $this->get_sortable_columns();
		$data = $this->vulnerability_data;
		usort( $data, array( &$this, 'sort_data' ) );
		$this->_column_headers = array($columns, $hidden, $sortable);
		$this->items = $data;
	}
	
	public function get_columns() {
		$columns = array(
			'name' => __('Plugin', 'plugin-vulnerabilities'),
			'type' => __('Claimed Type of Vulnerability', 'plugin-vulnerabilities'),
			'details' => __('Details', 'plugin-vulnerabilities')
		);
		return $columns;
	}

	public function get_sortable_columns() {
		$sortable_columns = array(
			'name' => array( 'name', true ),
			'type' => array( 'type', false )
		);

		return $sortable_columns;
	}

	public function column_default( $item, $column_name )
	{
		return $item[ $column_name ];
	}

	private function sort_data( $a, $b )
	{
		$orderby = 'name';
		$order = 'asc';
		if(!empty($_GET['orderby']))
		{
			$orderby = $_GET['orderby'];
		}
		if(!empty($_GET['order']))
		{
			$order = $_GET['order'];
		}
		$result = strcmp( $a[$orderby], $b[$orderby] );
		if($order === 'asc')
		{
			return $result;
		}
		return -$result;
	}
	
	public function add_data( $vulnerability_data ) {
		$this->vulnerability_data = $vulnerability_data;
	}

}