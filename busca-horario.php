<?php

class Horarios
{
  public $horarios;

  function __construct($horarios)
  {
    $this->horarios = $horarios;
  }
}
require "conexaoMysql.php";
$pdo = mysqlConnect();

$especialidade = $medico = '';
  $data = $horario = '';  
  $codMed = $horaAg = $dataInf = '';

if (isset($_GET["especialidade"])) $especialidade = $_GET["especialidade"];
if (isset($_GET["medico"])) $medico = $_GET["medico"];
if (isset($_GET["data"])) $data = $_GET["data"];

function codigo($especialidade, $medico){
    $pdo = mysqlConnect();
    try{
    $sql = <<<SQL
          SELECT m.codigo, m.especialidade
          FROM medico m, pessoa p 
          WHERE p.nome = ? 
          AND m.codigo = p.codigo
          SQL;
    
      $stmt = $pdo->prepare($sql);
      $stmt->execute([$medico]);
    } 
    catch (Exception $e) {
      exit('Ocorreu uma falha: ' . $e->getMessage());
    }
    
    $codigo = '';
    while($row = $stmt->fetch()) {
      if($especialidade == $row['especialidade']);{
        $codigo = htmlspecialchars($row['codigo']);
        return $codigo;
      }
    }
  }

function horarioAgendado($codMed, $dataInf){
    $pdo = mysqlConnect();
    $sql = <<<SQL
          SELECT codigoMedico, dataConsulta, horario
          FROM agenda
          WHERE codigoMedico = ? AND dataConsulta = ?
          ORDER BY `agenda`.`horario` ASC
          SQL;
    try {
      
      $stmt = $pdo->prepare($sql);
      $stmt->execute([$codMed, $dataInf]);
    } 
    catch (Exception $e) {
      exit('Ocorreu uma falha: ' . $e->getMessage());
    }
    $horarioAg = Array();
    while($row = $stmt->fetch()) {

      if($dataInf == $row['dataConsulta']);{
        $horarioAg[] = htmlspecialchars($row['horario']); 
      }
    }
    return $horarioAg;
  }

  $codMed = codigo($especialidade, $medico);
  
  $horarios = horarioAgendado($codMed, $data); 
  $horarioAg =  new Horarios($horarios);

  echo json_encode($horarioAg);

?>