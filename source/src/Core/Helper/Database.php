<?php
namespace API\Core\Helper;

use FluentPDO;
use PDO;
use API\Core\Configure\Config;

class Database{

	public $mysql;

	public function connectDb(){
		$config = new \API\Core\Configure\Config();
		$pdo = new PDO($config->dsn, $config->username, $config->password);
		$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$this->mysql = $pdo;
	}

	public function queryMySql($sqlText, $type='consultar', $fetchType='fetchArray'){ 
		$arrReturn = array();
		$config    = new Config();
		$this->connectDb();

		
		switch($type){
			case 'consultar':
				$statement = $this->mysql->prepare($sqlText);
				$statement->execute();
				
				switch($fetchType){
					case 'fetchArray':
						return $statement->fetchAll();
					break;
					
					case 'fetchRow':
						return $statement->fetch();
					break;
				}
			break;
			
			case 'executar':
			case 'inserir':
			case 'atualizar':
			case 'deletar':
				
				$statement = $this->mysql->beginTransaction();
				
				try{
					$statement = $this->mysql->prepare($sqlText);
					$statement->execute();
					$this->mysql->commit();
				}
				catch(Exception $e){
					$this->mysql->rollBack();
					$error = $e->getMessage();
					if($config->isDeveloper()){
						print_r($error);
					}
					return false;
				}
				return true;
			break;
		}
	}
}