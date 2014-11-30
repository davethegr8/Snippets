<?php

class DBRow implements ArrayAccess {

	protected $_database;

	protected $_tableName = null; // The row's table name
	protected $_primaryKey = null; // The ID of the row
	protected $_primaryKeyName = null; // The ID of the row

	protected $_fields = array(); // The data in the row

	protected $_inTable = false; // Is the row in the table
	protected $_dirty = false; // The data in the fields array is different then the database
	protected $_readonly = false; // Make the object readonly
	protected $_destroyed = false; // Is the object being destroyed

	// Get the table row from the database
	function __construct(Database $database, $tableName, $primaryKey = null) {
		$this->_database = $database;

		$this->_tableName = $tableName;
		$this->_primaryKeyName = 'id';
		$this->_primaryKey = is_numeric($primaryKey) ? (int) $primaryKey : null;

		$this->refresh();
	}

	// Called when garbage collected, or script finishes execution
	function __destruct() {
		$this->_destroyed = true;
		if($this->_dirty) $this->save();
	}

	function offsetExists($offset) {
		print_R($this->fields);
		return array_key_exists($offset, $this->_fields);
	}

	function offsetGet($offset) {
		if(!$this->offsetExists($offset)) {
			trigger_error("Offset '".$offset."' does not exist in table '".$this->_tableName."'", E_USER_WARNING);
			return;
		}

		return $this->_fields[$offset];
	}

	function offsetSet($offset, $value) {
		if(!$this->offsetExists($offset)) {
			trigger_error("Offset '".$offset."' does not exist in table '".$this->_tableName."'", E_USER_WARNING);
			return;
		}

		if(!$this->_readonly) {
			$this->_dirty = true;
			$this->_fields[$offset] = $value;
		}
	}

	function offsetUnset($offset) {
		if(!$this->offsetExists($offset)) {
			trigger_error("Offset '".$offset."' does not exist in table '".$this->_tableName."'", E_USER_WARNING);
			return;
		}

		if(!$this->_readonly) {
			$this->_dirty = true;
			$this->_fields[$offset] = null;
		}
	}

	// Saves the database row
	// TODO: Should ->save() use DB Replace?
	function save() {
		$succ = true;

		if($this->_inTable && isset($this->_fields[$this->_primaryKeyName])) {
			if(array_key_exists('updated', $this->_fields)) {
				$this->_fields['updated'] = date("Y-m-d H:i:s");
			}

			$succ && $succ = $this->_database->update($this->_tableName, $this->_fields, "$this->_primaryKeyName = :$this->_primaryKeyName");
			$succ = $succ && ($this->_primaryKey = $this->_fields[$this->_primaryKeyName]);
		}
		else {
			if(array_key_exists('created', $this->_fields)) {
				$this->_fields['created'] = date("Y-m-d H:i:s");
			}
			if(array_key_exists('updated', $this->_fields)) {
				$this->_fields['updated'] = date("Y-m-d H:i:s");
			}

			$succ = $succ && $this->_database->insert($this->_tableName, $this->_fields);
			$succ = $succ && ($this->_primaryKey = (int) $this->_database->insertID());
		}

		// Will load up any default data, or any fields that didn't save
		if($succ && !$this->_destroyed) {
			$succ = $succ && $this->refresh();
		}

		return (bool) $succ;
	}

	// Reload the row from the database (dropping all data)
	function refresh($row = array()) {
		//get table data
		$tableRows = $this->_database->getFields($this->_tableName);

		foreach($tableRows as $item) {
			$this->_fields[$item] = null;
		}

		$this->_dirty = false;

		// Load row from db
		if(isset($this->_primaryKey)) {
			$sql = "SELECT *
					FROM $this->_tableName
					WHERE $this->_primaryKeyName = :$this->_primaryKeyName";
			$row = $this->_database->selectRow($sql, array(
				$this->_primaryKeyName => $this->_primaryKey
			));
		}

		// Add row to object
		if($row) {
			$this->_inTable = true;
			$this->_fields = $row;
		}
		// Load fields with empty data
		else {
			$this->_inTable = false;
		}

		return count($this->_fields) > 0;
	}

	// Has data been changed and not saved
	function isDirty() {
		return (bool) $this->_dirty;
	}

	// Returns the table name for the row
	function getTableName() {
		return $this->_tableName;
	}

	// Returns an array of the fields in the row's table
	function getFields() {
		return array_keys($this->_fields);
	}

	// Gets/Sets the readonly state
	function readonly($set = null) {
		if(isset($set)) {
			$this->_readonly = (bool) $set;
		}
		return (bool) $this->_readonly;
	}

	// Duplicates a database row, returning the new one
	function duplicate() {
		$copy = new self($this->_tableName);
		$copy->_fields = $this->_fields;
		$copy->_primaryKey = $copy->_fields[$copy->_primaryKeyName] = null;
		$copy->save();
		return $copy;
	}

	// Update the row with data from an array (like $_POST)
	function populate($data = array(), $nullifyMissing = false) {
		$fields = $this->getFields();
		foreach($fields as $field) {
			if(isset($data[$field])) {
				$this->_fields[$field] = $data[$field];
			}
			else if($nullifyMissing) {
				$this->_fields[$field] = null;
			}
		}
	}

	// Delete row from table
	function delete() {
		$succ = $this->_database->delete($this->_tableName, $this->_primaryKey);
		if($succ) {
			$this->_inTable = false;
		}
		return (bool) $succ;
	}

	// Returns a row only if it exists in the table
	static function select($tableName, $rowID) {
		$row = new self($tableName, $rowID);
		return $row->_inTable ? $row : null;
	}

}
