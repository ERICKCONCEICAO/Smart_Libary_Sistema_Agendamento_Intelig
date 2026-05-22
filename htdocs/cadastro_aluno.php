<?php
/* ativar relatórios de erros*/
//mysqli_report(MYSQLI_REPORT_ALL);
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// Verifica se o formulário foi enviado via POST
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Validação e sanitização
    $acao  = isset($_POST['acao']) ? $_POST['acao'] : 'NADA';
    $raAluno = isset($_POST['raAluno']) ? $_POST['raAluno'] : '0';
    $nomeAluno = isset($_POST['nomeAluno']) ? $_POST['nomeAluno'] : 'NADA';
    $dtNascimentoAluno = isset($_POST['dtNascimentoAluno']) ? $_POST['dtNascimentoAluno'] : '0';
    $tpEnsinoAluno = isset($_POST['tpEnsinoAluno']) ? $_POST['tpEnsinoAluno'] : 'NADA';
    $turmaAluno = isset($_POST['turmaAluno']) ? $_POST['turmaAluno'] : 'NADA';
}

// CONEXÃO COM BANCO DE DADOS
// $link = mysqli_connect("localhost", "user", "password", "nome_da_base_de_dados");
$link = mysqli_connect("sql111.infinityfree.com", "if0_41441083", "A2026S1N7", "if0_41441083_db_smartlibrary");

// VERIFICAR CONEXÃO
if($link === false){
  die("ERRO DE CONEXÃO COM BANCO DE DADOS " . mysqli_connect_error());
}

// EXECUTAR AÇÃO SOLICITADA NA TABELA

switch ($acao) {
    case "INSERT":
      $sql="INSERT INTO tb_aluno (raAluno, nomeAluno, dt_nascimentoAluno, tp_ensinoAluno, turmaAluno)";
      $sql.=" VALUES ('$raAluno', '$nomeAluno', '$dtNascimentoAluno', '$tpEnsinoAluno', '$turmaAluno')";
    break;
    case "UPDATE":
      $sql="UPDATE tb_aluno SET nomeAluno = '$nomeAluno', dt_nascimentoAluno = '$dtNascimentoAluno'";
      $sql.=", tp_ensinoAluno = '$tpEnsinoAluno', turmaAluno = '$turmaAluno' WHERE raAluno = $raAluno";
    break;
    case "DELETE":
      $sql = "DELETE FROM tb_aluno WHERE raAluno = $raAluno";
    break;
}

try {
  $result = mysqli_query($link, $sql);
  $linhas_afetadas = mysqli_affected_rows($link);
} catch (mysqli_sql_exception $e) {
  $query_erro = mysqli_errno($link);
}
mysqli_close($link); 

$message = "";
// VOLTAR A PAGINA DO FORMULARIO
if (($acao <> "INSERT") && $linhas_afetadas == 0){
   $message = "Aluno não cadastrado";
}  elseif (($acao == "INSERT") && ($query_erro == 1062)){
   $message = "Aluno já existe";
   } else { $message = 'Sucesso';
     }

$url = "cadastro_aluno.html";
echo "<script> 
  alert(" . json_encode($message) . "); 
  window.location.href = " . json_encode($url) . "; 
</script>";

// header ('Location: cadastro_aluno.html');
// exit;

?>