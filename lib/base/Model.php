<?php

/**
 * A base model for handling the database connections
 */
class Model
{
	protected $_dbh = null;
	protected $_table = "";

	public function __construct()
	{
		try {
			$user = "itacademy";
			$pwd = "cortesia";
			$hostname = '@cluster0.s9viag0.mongodb.net';

			$bd = "missionlist";

			$uri = 'mongodb+srv://' . $user . ':' . $pwd . $hostname . '/?retryWrites=true&w=majority';
			$mongo = new MongoDB\Client($uri);

			$this->_dbh = $mongo->selectDatabase($bd);
			return $this->_dbh;
		} catch (\Throwable $th) {
			return $th->getMessage();
		}
	}

	/**
	 * Sets the database table the model is using
	 * @param string $table the table the model is using
	 */


	protected function _getTable($collectionName = "users")
	{
		try {
			$mongo = $this->__construct();
			$collection = $mongo->$collectionName;
			$data = $collection->find();

			$values = array();
			foreach ($data as $value) {
				$values[] = $value;
			};

			$this->_table = $values;

			return $this->_table;
		} catch (\Throwable $th) {
			return $th->getMessage();
		}
	}

	public function fetchAll()
	{
		$data = $this->_table;

		if (is_array($data)) {
			foreach ($data as $key => $val) {
				$data[] = $val;
			}
			return $data;
		}
	}

	public function fetchSome($user_id)
	{
		$data = array();
		if (is_array($this->_table)) {
			foreach ($this->_table as $val) {
				if ($val['user_id'] == $user_id) {
					$data[] = $val;
				}
			}
			return $data;
		}
	}

	/**
	 * Saves the current data to the database. If an key named "id" is given,
	 * an update will be issued.
	 * @param array $data the data to save
	 * @return int the id the data was saved under
	 */
	public function save($collectionName, $data = array(), $id = 0)
	{
		$mongo = $this->__construct();
		$collection = $mongo->$collectionName;

		if (array_key_exists('_id', $data)) {
			$update = $collection->updateOne(['_id' => new MongoDB\BSON\ObjectId($id)], ['$set' => $data], ['upsert' => true]);
			return $update;
		} else {
			$update = $collection->insertOne($data);
			return $update;
		}
		return false;
	}

	/**
	 * Deletes a single entry
	 * @param int $user_id the user_id of the entry to delete
	 * @return boolean true if all went well, else false.
	 */
	public function delete($collectionName, $id)
	{
		$mongo = $this->__construct();
		$collection = $mongo->$collectionName;

		echo $id;
		$update = $collection->deleteMany(['user_id' => new MongoDB\BSON\ObjectId($id)]);
		return $update;
	}
}
