# ReposotoryFuncitosPhp
Repository of PHP functions. helps database connection by pdo and other general functions

//Example getFieldWhere
public function listFriendLikeSeries(){
		$return = array();
		$select = "SELECT amigos FROM amigo WHERE id_usuario=".$this->idUser; //object variable
		$result = $GLOBALS['conn']->prepare($select); // connection to bbdd
		if(!$result->execute()){
			//recogida de error, con funcion pdo de errorInfo, parametro 2 (solo mensajes)
			$aErrSql = $result->errorInfo();
			throw new Exception("Mensaje error: ".$aErrSql[2]."\nSentencia: ".$select);
		}
		$fetch = $result->fetchAll(PDO::FETCH_ASSOC);
		foreach($fetch as $key => $row){
				$return[]=array(
					"id" => $row['amigos'],
					"logo" => getFieldWhere("ubicacion","imagen_usuario","id_usuario=".$row['amigos']." and seleccionado='S'"),
					"name" => getFieldWhere("nombre","usuario","id =".$row['amigos']),
					"points" => getFieldWhere("num_punto","punto","id_usuario =".$row['amigos'])
				);
			}
		}
		return $return;
	}
