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

// Inicializa e resgata dados do funcionario
$codigo = $nome = $sexo = $email = $telefone = "";
$cep = $logradouro = $cidade = $estado = "";
$dataContrato = $salario = $senha = "";
$especialidade = $crm = "";

//Pessoa
if (isset($_POST["nome"])) $nome = $_POST["nome"];
if (isset($_POST["sexo"])) $sexo = $_POST["sexo"];
if (isset($_POST["email"])) $email = $_POST["email"];
if (isset($_POST["telefone"])) $telefone = $_POST["telefone"];
if (isset($_POST["cep"])) $cep = $_POST["cep"];
if (isset($_POST["logradouro"])) $logradouro = $_POST["logradouro"];
if (isset($_POST["cidade"])) $cidade = $_POST["cidade"];
if (isset($_POST["estado"])) $estado = $_POST["estado"];
//Funcionário
if (isset($_POST["dataContrato"])) $dataContrato = $_POST["dataContrato"];
if (isset($_POST["salario"])) $salario = $_POST["salario"];
if (isset($_POST["senha"])) $senha = $_POST["senha"];
//Médico
if (isset($_POST["funcao"])) $funcao = $_POST["funcao"];
if (isset($_POST["especialidade"])) $especialidade = $_POST["especialidade"];
if (isset($_POST["crm"])) $crm = $_POST["crm"];

// calcula um hash de senha seguro para armazenar no BD
$hashsenha = password_hash($senha, PASSWORD_DEFAULT);

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
  INSERT INTO funcionario 
    (codigo, dataContrato, salario, senhaHash)
  VALUES (?, ?, ?, ?)
  SQL;

  $sql3 = <<<SQL
  INSERT INTO medico 
    (codigo, especialidade, crm)
  VALUES (?, ?, ?)
  SQL;

try {
  $pdo->beginTransaction();

  // Inserção na tabela pessoa
  $stmt1 = $pdo->prepare($sql1);
  if (!$stmt1->execute([
    $codigo, $nome, $sexo, $email, $telefone,
    $cep, $logradouro, $cidade, $estado
  ])) throw new Exception('Falha na primeira inserção');

  // Inserção na tabela funcionario
  $idNovoFunc = $pdo->lastInsertId();
  $stmt2 = $pdo->prepare($sql2);
  if (!$stmt2->execute([
    $codigo, $dataContrato, $salario, $hashsenha
  ])) throw new Exception('Falha na segunda inserção');

    if($funcao=='medico') {
      // Inserção na tabela medico
       $idNovoMed = $pdo->lastInsertId();
       $stmt3 = $pdo->prepare($sql3);
      if (!$stmt3->execute([
        $codigo, $especialidade, $crm
      ])) throw new Exception('Falha na terceira inserção');

    } 

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