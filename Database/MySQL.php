<?php

class MySQL implements Database {

	public $handler;
	public $driver = 'mysql';

	public function __construct($host, $user, $pass, $database) {
		$this->handler = $this->connect($host, $user, $pass, $database);
	}

	public function connect($host, $user, $pass, $database) {
		$dsn = "{$this->driver}:dbname=$database;host=$host;charset=UTF8";
		return new PDO($dsn, $user, $pass);
	}

	public function query($sql) {
		return $this->handler->query($sql);
	}

	public function select($sql, array $data = array()) {
		$statement = $this->handler->prepare($sql);
		$statement->execute($data);
		return $statement->fetchAll(PDO::FETCH_ASSOC);
	}

	public function selectRow($sql, array $data = array()) {
		$statement = $this->handler->prepare($sql);
		$statement->execute($data);
		return $statement->fetch(PDO::FETCH_ASSOC);
	}

	public function selectValue($sql, array $data = array()) {
		$statement = $this->handler->prepare($sql);
		$statement->execute($data);
		return array_shift($statement->fetch(PDO::FETCH_ASSOC));
	}

	public function insert($table, array $data = array()) {
		$fields = implode(", ", array_keys($data));
		$values = array();

		foreach($data as $key => $value) {
			$values[] = ":$key";
		}

		$values = implode(', ', $values);
		$sql = "INSERT INTO $table ($fields) VALUES ($values)";
		$statement = $this->handler->prepare($sql);
		$statement->execute($data);
		return $statement->rowCount();
	}

	public function replace($table, array $data = array()) {
		$fields = implode(", ", array_keys($data));
		$values = array();

		foreach($data as $key => $value) {
			$values[] = ":$key";
		}

		$values = implode(', ', $values);
		$sql = "REPLACE INTO $table ($fields) VALUES ($values)";
		$statement = $this->handler->prepare($sql);
		$statement->execute($data);
		return $statement->rowCount();
	}

	public function update($table, array $data, $where) {
		$fields = array();
		foreach($data as $key => $value) {
			$fields[] = "`$key`= :$key";
		}
		$fields = implode(', ', $fields);

		$sql = "UPDATE `$table` SET $fields WHERE $where";
		$statement = $this->handler->prepare($sql);
		$statement->execute($data);
		return $statement->rowCount();
	}

	public function delete($table, $where, array $data = array()) {
		$sql = "DELETE FROM $table WHERE $where";
		$statement = $this->handler->prepare($sql);
		$statement->execute($data);
		return $statement->rowCount();
	}

	public function insertId() {
		return $this->handler->lastInsertId();
	}

	public function fetchRow($sql) {
		$result = $this->query($sql);
		return $result->fetch(PDO::FETCH_ASSOC);
	}
	public function fetchAll($sql) {
		$result = $this->query($sql);
		return $result->fetchAll(PDO::FETCH_ASSOC);
	}

	public function numRows($statement) {
		return $statement->rowCount();
	}

	public function getFields($table) {
		$statement = $this->handler->prepare("DESCRIBE $table");
		$statement->execute();
		return $statement->fetchAll(PDO::FETCH_COLUMN);
	}

	public function getEnumValues($table, $column) {
		$statement = $this->handler->prepare("DESCRIBE $table $column");
		$statement->execute();
		$data = $statement->fetch(PDO::FETCH_ASSOC);

		$values = explode(',', $data['Type']);
		foreach($values as $index => $value) {
			if($index == 0) {
				$value = substr($value, 5);
			}
			if($index == count($values) - 1) {
				$value = substr($value, 0, -1);
			}

			$value = substr($value, 1, -1);

			$values[$index] = $value;
		}

		return $values;
	}
}
