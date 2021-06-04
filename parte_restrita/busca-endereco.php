<?php

class Endereco
{  
  public $logradouro;
  public $cidade;
  public $estado;

  function __construct($logradouro, $cidade, $estado)
  {    
    $this->logradouro = $logradouro; 
    $this->cidade = $cidade;
    $this->estado = $estado; 
  }
}

require "../conexaoMysql.php";
$pdo = mysqlConnect();

$cep = $_GET["cep"];
$cep_banco = '';
try {
  $sql = <<<SQL
  SELECT cep, logradouro, cidade, estado
  FROM base_enderecos_ajax
  SQL;

  $stmt = $pdo->query($sql);
} catch (Exception $e) {
  exit('Ocorreu uma falha: ' . $e->getMessage());
}

while ($row = $stmt->fetch()) {
    if($cep == $row['cep']){
      
      $logradouro = htmlspecialchars($row['logradouro']);
      $cidade = htmlspecialchars($row['cidade']);
      $estado = htmlspecialchars($row['estado']);
      $cep_banco = $cep;
    }
}

if($cep != $cep_banco){
  
  $logradouro = '';
  $cidade = '';
  $estado = '';
}

$endereco1 = new Endereco($logradouro, $cidade, $estado);

$enderecos = array(
  $cep_banco => $endereco1
);

$cep = $_GET['cep'] ?? '';
  
$endereco = array_key_exists($cep, $enderecos) ? 
  $enderecos[$cep] : new Endereco('', '', '');
  
echo json_encode($endereco);

?>