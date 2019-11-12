<?php

/**
 * Set the environment variables :
 * SLITE_LIB_PHP_PGSQL_NAME
 * SLITE_LIB_PHP_PGSQL_HOST (optional)
 * SLITE_LIB_PHP_PGSQL_USER (optional if you are running in non-secure mode)
 * SLITE_LIB_PHP_PGSQL_PASS (optional if you are running in non-secure mode)
 * SLITE_LIB_PHP_PGSQL_PORT (optional)
 * 
 * Construct this class and exectute a query like so:
 * 
 * <code>
 * $db = new CockroachSql();
 * $data = $db->get("SELECT * FROM users");
 * print_r($data);
 * </code>
 * 
 * 
 */
class CockroachSql
{
	public $showErrors = true;
	private $connection;
	
	public function __construct()
	{
		$name = getenv('SLITE_LIB_PHP_PGSQL_NAME');
		$host = getenv('SLITE_LIB_PHP_PGSQL_HOST');
		$user = getenv('SLITE_LIB_PHP_PGSQL_USER');
		$pass = getenv('SLITE_LIB_PHP_PGSQL_PASS');
		$port = getenv('SLITE_LIB_PHP_PGSQL_PORT');	
		
		if($host===FALSE) $host = 'localhost';
		if($user===FALSE) $user = 'root';
		if($pass===FALSE) $pass = 'root';
		if($name===FALSE) $name = 'postgres';
		if($port===FALSE) $port = '26257';
		
		
		$connectString = 'host='.$host.' dbname='.$name.' user='.$user.' password='.$pass.' port='.$port;
		$this->connection = pg_connect($connectString);
		
		if($this->connection===FALSE) throw new \Exception('Could not connect to database!');
	}

	/**
	 * Executes a query and returns the result as a 2 dimensional associative array.
	 * 
	 * @param type $sql
	 */
	public function get($sql)
	{
		$rs = pg_query($this->connection, $sql);
		
		if($rs!==FALSE)
			return pg_fetch_all($rs);
		else
		{
			if($this->showErrors)
			{
				echo pg_last_error($this->connection);
				echo $sql;
			}
		}
		
		return null;
	}
	
	public function getCell($sql)
	{
		$data = $this->get($sql);
		if($data!=null) foreach($data[0] as $value) return $value;
		else return null;
	}
	
	public function put($sql)
	{
		$rs = pg_query($this->connection, $sql);
		return pg_affected_rows($rs);
	}
}
