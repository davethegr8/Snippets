<?php

interface Database {
	public function connect($host, $user, $password, $database);

	public function query($sql);

	public function select($sql);
	public function selectRow($sql);
	public function selectValue($sql);

	public function fetchRow($result);
	public function fetchAll($result);

	public function numRows($result);

	public function insert($table, array $data);
	public function replace($table, array $data);
	public function update($table, array $data, $where);
	public function delete($table, $where);

	public function insertId();

	public function getFields($table);
	public function getEnumValues($table, $column);

}
