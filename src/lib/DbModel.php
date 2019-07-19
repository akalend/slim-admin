<?php
/**
 * 
 * base class DbModel
 *
 * @author akalend
 * @package mvc
 */

/**
 * class Db
 *
 */
abstract class DbModel {
		
	protected $db = null;
	private $isntCall = true;
	private $conf = null;
	private $sqlCache = [];
	private $tpl = null;
	private $rows =0;
	private $res;
	private $encoding = false;
	private $dsn = '';
	
	/**
	 * Базовый класс модели
	 */
	public function __construct($conf) {
		$this->conf = $conf;

	}
		
	public function setEncoding($encoding) {
		$this->encoding = $encoding;
	}

	
	/**
	 * protected function, check argument for not null
	 * using for check mysql paramers
	 *
	 * @param unknown_type $arg
	 * @return true if argument is good
	 */
	protected  function check($arg){
		if (is_null($arg))
			return false;
		if (is_string($arg) && trim($arg) == '')
			return false;
			
		if (!$arg)
			return false;
			
		return true;		
	}
	
	public function initialize(){
		
	}
	
	/**
	 * ����������� 
	 *
	 */
	public function finalize () {
		if ( count($this->sqlCache) ) {
			return $this->getStat();
		}
		
	}
	/**
	 * 
	 *
	 * @return array  sql/time - 
	 * 
	 */
	public function getStat() {
		return $this->sqlCache;
	}
	public function getUtf8() {
		$this->db->query("SET NAMES 'UTF8'");
	}
	/**
	 * @parm int $top   = 0, 
	 * @parm int $limit = 0
	 *
	 * @description получение списка модели заданного диапазона
	 */
	public function getList( $top = 0, $limit = 0, $orderBy = null, $where = null){
		if ($limit) { 
			$limit = ' LIMIT ' . (string) $limit; 
		} else {
			$limit = '';
		}
		if ($top > 0) { 
			$top = ' OFFSET ' . (string) $top; 
		} else {
			$top = '';
		}
		$order = $orderBy ? ' ORDER BY ' . $orderBy : '';
		$where = $where ? ' WHERE ' . $where : '';
		$query = 'SELECT * FROM ' . static::$_table . $where .$order . $limit . $top;


 // echo $query; exit;

		return $this->exec($query);
	}
	/**
	 * @parm int $top   = 0, 
	 * @parm int $limit = 0
	 *
	 * @description получение списка модели заданного диапазона
	 */
	public function getById( $id ){
		$query = 'SELECT * FROM ' . static::$_table . ' WHERE id=' . $id;
		$ret = $this->exec($query);
		return is_array($ret) ? $ret[0] : null;
	}				
	public function count($where = null) {
		$where = $where ? " WHERE $where" : '';
		$query = 'SELECT count(*) as `0` FROM ' . static::$_table . $where;	

// echo $query; exit;

		return $this->exec($query)[0][0];
	}
	/**
	 * @description инициализация класса db
	 */
	protected function init () {
		if ( $this->isntCall ) {
			$this->isntCall=false;
	
			$this->dsn = sprintf('mysql:dbname=%s;host=%s', 
					$this->conf['dbname'],
					$this->conf['host']
			);
		
			try {
			    $this->db =  new PDO( $this->dsn, 
			    			$this->conf['user'], 
			    			$this->conf['password'],
			    		    array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\'')
			    		);
				$this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
				$this->db->query('USE ' . $this->conf['dbname']);
			} catch (PDOException $e) {
				die( 'Подключение не удалось: ' . $e->getMessage()); 
			}
		}	
	}
	/**
	 * ��������� SQL ������
	 * ������������� ������� �� ����������
	 *
	 * @param string $sql - ������
	 * @param array $data - �� ������ 
	 * @return array - ��������� ���������� �������
	 */
	protected function exec ( $sql , array $data =array() ) {
			$this->init();
			
			$time_start = microtime();
			$res = $this->db->query( $sql );
			
			if (!$res) {
				echo "<font color=blue>$sql</font><br>";
				echo 'mysql error: '.$this->db->error;
				$result = false;				
				return false;
			} 
			$time = 0; //microtime()-$time_start;
			
			$this->sqlCache[] = $toSave = array( 'sql' => $sql , 'time'=>$time );
						
			if (!is_object($res)) return true;
			$result = [];
			while ($row = $res->fetch(PDO::FETCH_ASSOC)) {     		      
			    $result[] = $row;     		  
			}
    		
    		return $result;
	}
	
	/**
	 * ��������� SQL ������, 
	 * but not return data
	 * ������������� ������� �� ����������
	 *
	 * @param string $sql - ������
	 * @param array $data - �� ������ 
	 * @return array - ��������� ���������� �������
	 */
	public function query( $sql , array $data =array() ) {
			$this->init();
			if (!$this->db) {
				die('PDO object is not created');
			}
			 
			$time_start = microtime();
			
			$this->res =  $this->db->query($sql,  PDO::FETCH_ASSOC);
			if (!$this->res) {
				echo 'mysql error: '.$this->db->error;
				$result = false;				
			} 
			
			$time = 0; //(int)microtime() - (int)$time_start;
		
			$toSave = ['sql' =>$sql , 'time'=>$time ];
			
			$this->sqlCache[] = $toSave;
			//$this->rows = $this->db->affected_rows();
			if (!is_object($this->res)) return true;			
	}
	
	/**
	 * return one row as assoc array
	 *
	 * @return array result of one row
	 */
	public function iterate() {
		if (!$this->res) throw new Exception('the mysql result is null');
		return $this->res->fetch();
	}
	
	
	
	/**
	 * 
	 * UPDATE/DELETE
	 *
	 * @return unknown
	 */
	protected function getRowCount() {
		return $this->db->affected_rows;
	}
	
	protected function getId() {
		return $this->db->insert_id;
	}
	
	/**
	 * START TRANSACTION
	 *
	 */
	protected function start() {
		$this->db->query('START TRANSACTION;');		
	}
	/**
	 * ROLLBACK TRANSACTION
	 *
	 */
	protected function rollback() {
		$this->db->rollback();
	}
	/**
	 * COMMIT TRANSACTION
	 *
	 */
	protected function commit() {
		$this->db->commit();
	}
	
}