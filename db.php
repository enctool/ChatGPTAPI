<?php
function db_connect($database=db_name ,$username=db_username ,$password=db_pass){
	try{
        $server="localhost";
        $dsn="mysql:host=$server;dbname=$database;charset=utf8mb4";
        
        $connect=new PDO($dsn,$username,$password ,array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET sql_mode=""') );
    	$connect->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		
		return ["status"=>true ,"detail"=>$connect];
	}catch(PDOException $e){
		return ["status"=>false ,"detail"=>"try_catch\n".$e->getMessage()];
	}
}

function db_insertOne($db ,$which ,$data){
	try{
		$build = ""; $build_v = ""; $final = [];
		if($final == []){
			foreach($data as $k=>$v){
				$build .= $k.",";
				$build_v .= ":".$k.",";
				$final[$k] = $data[$k];
			}
		}
				
		$res = $db->prepare( "INSERT INTO ".$which." (".trim($build,",").") VALUES (".trim($build_v,",").");" );
		if($res->execute($final)){
			return ["status"=>true ,"detail"=>$db->lastInsertId()];
    	}
		
		return ["status"=>false ,"detail"=>"execute"];
	}catch(PDOException $e){
		return ["status"=>false ,"detail"=>"try_catch\n".$e->getMessage()];
	}
}

function db_findOne($db ,$which ,$data ,$where=""){
	try{		
		$res = $db->query("SELECT $data FROM ".$which." ".(($where=="")?"":"WHERE $where")." LIMIT 1;")->fetchAll(PDO::FETCH_ASSOC);
		return ["status"=>true ,"detail"=> $res[0] ];
	}catch(PDOException $e){
		return ["status"=>false ,"detail"=>"try_catch\n".$e->getMessage()];
	}
}

function db_find($db ,$which ,$data ,$where=""){
	try{		
		$res = $db->query("SELECT $data FROM ".$which." ".(($where=="")?"":"WHERE $where").";")->fetchAll(PDO::FETCH_ASSOC);
		return ["status"=>true ,"detail"=> ($res) ];
	}catch(PDOException $e){
		return ["status"=>false ,"detail"=>"try_catch\n".$e->getMessage()];
	}
}

function db_update($db ,$which ,$where ,$set){
	try{							
		return ["status"=>true ,"detail"=> $db->query("UPDATE ".$which." SET $set WHERE $where;")->rowCount() ];
	}catch(PDOException $e){
		return ["status"=>false ,"detail"=>"try_catch\n".$e->getMessage()];
	}
}

function db_delete($db ,$which ,$where){
	try{		
		$res = $db->query("DELETE FROM ".$which." WHERE ".$where.";")->fetchAll(PDO::FETCH_ASSOC);
		return ["status"=>true ,"detail"=> ($res) ];
	}catch(PDOException $e){
		return ["status"=>false ,"detail"=>"try_catch\n".$e->getMessage()];
	}
}

?>