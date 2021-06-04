<?php
	
class Medicos
{
  public $nomes;

  function __construct($nomes)
  {
    $this->nomes = $nomes;
  }
}

	require "conexaoMysql.php";
	$pdo = mysqlConnect();

  $especialidade = $_GET['especialidade'];

  try{
     $sql = <<<SQL
     SELECT p.nome, m.especialidade, p.codigo, m.codigo
     FROM medico m, pessoa p
     WHERE p.codigo = m.codigo 
     SQL;

     $stmt = $pdo->query($sql);

 } 	catch(Exception $e) {
    exit('Ocorreu uma falha: ' . $e->getMessage());
  }
   
    $nomes = Array();
    
    while ($row = $stmt->fetch()) {
      if($especialidade == $row['especialidade']){
        $nomes [] = htmlspecialchars($row['nome']);                 
      }
    }
  $medicos = new Medicos($nomes);
      echo json_encode($medicos);

?>