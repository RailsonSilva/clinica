<?php

class Response
{
  public $success;

  function __construct($success)
  {
    $this->success = $success;
  }
}

  require "conexaoMysql.php";
  $pdo = mysqlConnect();

  $cep = $logradouro = $cidade = $estado = "";

  if (isset($_POST["cep"])) $cep = $_POST["cep"];
  if (isset($_POST["logradouro"])) $logradouro = $_POST["logradouro"];
  if (isset($_POST["cidade"])) $cidade = $_POST["cidade"];
  if (isset($_POST["estado"])) $estado = $_POST["estado"];

  try {
      $sql = <<<SQL
    
    INSERT INTO base_enderecos_ajax (cep, logradouro, cidade, estado)
    VALUES (?, ?, ?, ?)
    SQL;
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
      $cep, $logradouro, $cidade, $estado
    ]);

    $response = new Response(true);
    
    } 
  catch (Exception $e) {  
    if ($e->errorInfo[1] === 1062)
      exit('Dados duplicados: ' . $e->getMessage());
    else
      exit('Falha ao cadastrar os dados: ' . $e->getMessage());

      $response = new Response(false);
  }

  echo json_encode($response);
?>