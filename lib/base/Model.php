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
		//parse_ini_file — Analiza un fichero de configuración
		$settings = parse_ini_file(CONFIG_PATH . 'settings.ini', true);

		// conectamos a la base de datos
		try {
			$this->_dbh = new PDO(
				sprintf(
					"%s:host=%s;dbname=%s",
					$settings['database']['driver'],
					$settings['database']['host'],
					$settings['database']['dbname']
				),
				$settings['database']['user'],
				$settings['database']['password'],
				array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8")
			);

			$this->createTables();
		} catch (PDOException $ex) {
			//sino existe, la creamos
			$this->_dbh = new PDO(
				sprintf(
					"%s:host=%s;",
					$settings['database']['driver'],
					$settings['database']['host2']
				),
				$settings['database']['user2'],
				$settings['database']['password2'],
				array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8")
			);

			$this->createDB();
			$this->createTables();
		}
	}

	public function createDB()
	{
		$sql = "CREATE DATABASE IF NOT EXISTS `mission_list`; 
		USE `mission_list`;";

		$this->_dbh->exec($sql);
	}
	public function createTables()

	{
		$sql = "CREATE TABLE `mission` (
		  `id` int DEFAULT NULL,
		  `title` text,
		  `champ` text,
		  `tag` int DEFAULT NULL,
		  `end_date` text,
		  `status` int DEFAULT NULL,
		  `starred` int DEFAULT NULL,
		  `date_record` text,
		  `user_id` int DEFAULT NULL
		) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
		DROP TABLE IF EXISTS `user`;
		CREATE TABLE `user` (
		  `id` int unsigned NOT NULL,
		  `username` varchar(16) NOT NULL,
		  `name` varchar(45) NOT NULL,
		  `lastname` varchar(45) NOT NULL,
		  `mail` varchar(255) NOT NULL,
		  `password` varchar(100) NOT NULL,
		  PRIMARY KEY (`id`),
		  UNIQUE KEY `username_UNIQUE` (`username`),
		  UNIQUE KEY `password_UNIQUE` (`password`),
		  UNIQUE KEY `id_UNIQUE` (`id`),
		  UNIQUE KEY `mail_UNIQUE` (`mail`)
		) ENGINE = InnoDB DEFAULT CHARSET=utf8mb3;";


		$this->_dbh->exec($sql);
	}
	/**
	 * Sets the database table the model is using
	 * @param string $table the table the model is using
	 */
	protected function _setTable($table)
	{
		$this->_table = $table;
	}

	public function fetchAll()
	{
		$sql = 'select * from ' . $this->_table;

		$statement = $this->_dbh->prepare($sql); //PDO::prepare — Prepara una sentencia para su ejecución y devuelve un objeto sentencia
		$statement->execute(); //PDOStatement::execute — Ejecuta una sentencia preparada

		return $statement->fetchAll(PDO::FETCH_OBJ); //PDOStatement::fetchAll — Devuelve un array que contiene todas las filas del conjunto de resultados
	}
	public function fetchSome($user_id)
	{
		$sql = 'select * from ' . $this->_table;
		$sql .= ' where user_id = ?';

		$statement = $this->_dbh->prepare($sql);
		$statement->execute(array($user_id));

		return $statement->fetchAll(PDO::FETCH_OBJ); //PDOStatement::fetch — Obtiene la siguiente fila de un conjunto de resultados
	}

	/**
	 * Saves the current data to the database. If an key named "id" is given,
	 * an update will be issued.
	 * @param array $data the data to save
	 * @return int the id the data was saved under
	 */
	public function save($data = array())
	{
		$sql = '';

		$values = array();

		if (array_key_exists('id', $data)) {
			//array_key_exists — Verifica si el índice o clave dada ('id') existe en el array
			$sql = 'update ' . $this->_table . ' set ';

			$first = true;
			foreach ($data as $key => $value) {
				if ($key != 'id') {
					$sql .= ($first == false ? ',' : '') . ' ' . $key . ' = ?';

					$values[] = $value;

					$first = false;
				}
			}

			// adds the id as well
			$values[] = $data['id'];

			$sql .= ' where id = ?'; // . $data['id'];
			echo $sql;

			$statement = $this->_dbh->prepare($sql);
			return $statement->execute($values);
		} else {
			$sql = 'insert into ' . $this->_table;
			$sql .= ' values (';

			$dataValues = array_values($data);
			$first = true;
			foreach ($dataValues as $value) {
				$sql .= ($first == false ? ',?' : '?');

				$values[] = $value;

				$first = false;
			}

			$sql .= ')';

			$statement = $this->_dbh->prepare($sql);
			if ($statement->execute($values)) {
				return $this->_dbh->lastInsertId();
			}
		}

		return false;
	}

	/**
	 * Deletes a single entry
	 * @param int $user_id the user_id of the entry to delete
	 * @return boolean true if all went well, else false.
	 */
	public function delete($id)
	{
		$statement = $this->_dbh->prepare("delete from " . $this->_table . " where user_id = ?");
		return $statement->execute(array($id));
	}
}
