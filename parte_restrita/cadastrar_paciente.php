<?php

class Response
{
  public $success;

  function __construct($success)
  {
    $this->success = $success;
  }
}

require "../conexaoMysql.php";
$pdo = mysqlConnect();

$codigo = $nome = $sexo = $email = $telefone = $cep = $logradouro = "";
$cidade = $estado = $peso = $altura = $tipoSanguineo = "";

if (isset($_POST["nome"])) $nome = $_POST["nome"];
if (isset($_POST["sexo"])) $sexo = $_POST["sexo"];
if (isset($_POST["email"])) $email = $_POST["email"];
if (isset($_POST["telefone"])) $telefone = $_POST["telefone"];
if (isset($_POST["cep"])) $cep = $_POST["cep"];
if (isset($_POST["logradouro"])) $logradouro = $_POST["logradouro"];
if (isset($_POST["cidade"])) $cidade = $_POST["cidade"];
if (isset($_POST["estado"])) $estado = $_POST["estado"];

if (isset($_POST["peso"])) $peso = $_POST["peso"];
if (isset($_POST["altura"])) $altura = $_POST["altura"];
if (isset($_POST["tipoSanguineo"])) $tipoSanguineo = $_POST["tipoSanguineo"];

try {
  //Pega o ultimo registro de codigo na tabela pessoa
  $sql = <<<SQL
    SELECT codigo FROM pessoa ORDER BY codigo DESC limit 1  
  SQL;

  $stmt = $pdo->query($sql);
} 
catch (Exception $e) {
  exit('Ocorreu uma falha: ' . $e->getMessage());
}

while ($row = $stmt->fetch()) {                                    
  //Novo codigo recebe o anterior + 10
  $codigo = $row['codigo'] + 10;
}

  $sql1 = <<<SQL
  INSERT INTO pessoa (codigo, nome, sexo, email, telefone, 
                       cep, logradouro, cidade, estado)
  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
  SQL;

$sql2 = <<<SQL
  INSERT INTO paciente 
    (codigo, peso, altura, tipoSanguineo)
  VALUES (?, ?, ?, ?)
  SQL;
  
  try {
    $pdo->beginTransaction();
  
    // Inserção na tabela pessoa
    $stmt1 = $pdo->prepare($sql1);
    if (!$stmt1->execute([
      $codigo, $nome, $sexo, $email, $telefone,
      $cep, $logradouro, $cidade, $estado
    ])) throw new Exception('Falha na primeira inserção');
  
    // Inserção na tabela paciente
    $idNovoPaciente = $pdo->lastInsertId();
    $stmt2 = $pdo->prepare($sql2);
    if (!$stmt2->execute([
      $codigo, $peso, $altura, $tipoSanguineo
    ])) throw new Exception('Falha na segunda inserção'); 
    
  
    // Efetiva as operações
    $pdo->commit();
    $response = new Response(true);
    
  } 
  catch (Exception $e) {
    $pdo->rollBack();
    if ($e->errorInfo[1] === 1062)
      exit('Dados duplicados: ' . $e->getMessage());
    else
      exit('Falha ao cadastrar os dados: ' . $e->getMessage());
      
    $response = new Response(false);
  }

  echo json_encode($response);
?>
