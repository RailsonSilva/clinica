<?php

class Medico
{
  public $especialidade;

  function __construct($especialidade)
  {
    $this->especialidade = $especialidade;
  }
}
	require "conexaoMysql.php";
	$pdo = mysqlConnect();

	$especialidade = "";

	 try {

        $sql = <<<SQL
            SELECT DISTINCT especialidade
            FROM medico
        SQL;


        $stmt = $pdo->query($sql);
    } 
    catch (Exception $e) {
        exit('Ocorreu uma falha: ' . $e->getMessage());
    }
    $especialidade = Array();
    while ($row = $stmt->fetch()) {
    	$especialidade [] = htmlspecialchars($row['especialidade']);
      
    }
    $medico = new Medico($especialidade);
      echo json_encode($medico);

      
?>