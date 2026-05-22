<?php

/* ativar relatórios de erros*/
//mysqli_report(MYSQLI_REPORT_ALL);
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
date_default_timezone_set("America/Sao_Paulo");

// Verifica se o formulário foi enviado via POST
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Validação e sanitização
    $acao  = isset($_POST['acao']) ? $_POST['acao'] : 'NADA';
    $raAluno = isset($_POST['raAluno']) ? $_POST['raAluno'] : '0';
    $isbn = isset($_POST['isbn']) ? $_POST['isbn'] : '0';
}

// CONEXÃO COM BANCO DE DADOS
// $link = mysqli_connect("localhost", "user", "password", "nome_da_base_de_dados");
$link = mysqli_connect("sql111.infinityfree.com", "if0_41441083", "A2026S1N7", "if0_41441083_db_smartlibrary");

// VERIFICAR CONEXÃO
if($link === false){
  die("ERRO DE CONEXÃO COM BANCO DE DADOS " . mysqli_connect_error());
}
$message = "";
$url = "controle_emprestimo.html";

//
// validar aluno: consultar tb_aluno
//
$sql="SELECT * FROM tb_aluno WHERE raAluno = '$raAluno'";
$result = mysqli_query($link, $sql);
$linhas_afetadas = mysqli_affected_rows($link);
$query_erro = mysqli_errno($link);
if ($linhas_afetadas <> 1){
  $message = "Aluno não cadastrado";
  mysqli_close($link);
  echo "<script> 
    alert(" . json_encode($message) . ");
    window.location.href = " . json_encode($url) . "; 
  </script>";
}
$row = mysqli_fetch_row($result);
$nomeAluno = $row[1];

//
// validar livro: consultar tb_acervo
//

$sql="SELECT * FROM tb_acervo WHERE isbn = '$isbn'";
$result = mysqli_query($link, $sql);
$linhas_afetadas = mysqli_affected_rows($link);
$query_erro = mysqli_errno($link);
if ($linhas_afetadas <> 1){
  $message = "Título não cadastrado";
  mysqli_close($link);
  echo "<script> 
    alert(" . json_encode($message) . ");
    window.location.href = " . json_encode($url) . "; 
  </script>";
}
$row = mysqli_fetch_row($result);
$titulo = $row[1];

// verifica livro emprestado

// $row = mysqli_fetch_row($result);
$raLivro = $row[4];
if ($raLivro <> NULL && $acao == "EMPRESTIMO"){
  $message = "Título já emprestado";
  mysqli_close($link);
  echo "<script> 
    alert(" . json_encode($message) . ");
    window.location.href = " . json_encode($url) . "; 
  </script>";
}

// EXECUTAR AÇÃO SOLICITADA NA TABELA

switch ($acao) {
    case "EMPRESTIMO":{
      $sql_acervo = "UPDATE tb_acervo SET raLivro = '$raAluno' WHERE isbn = $isbn"; 
      $dt_retiradaEmprestimo = date("d/m/Y");
      $dt_devolucaoEmprestimo = "0";
      $sql="INSERT INTO tb_emprestimo (raEmprestimo, isbnEmprestimo, dt_retiradaEmprestimo, dt_devolucaoEmprestimo)";
      $sql.=" VALUES ('$raAluno', '$isbn', CURDATE(), '$dt_devolucaoEmprestimo')";
    }
    break;
    case "DEVOLUCAO":{
      $sql_acervo = "UPDATE tb_acervo SET raLivro = NULL WHERE isbn = $isbn";
      $sql = "UPDATE tb_emprestimo SET dt_devolucaoEmprestimo = CURDATE() WHERE raEmprestimo = $raAluno && isbnEmprestimo = $isbn";
    }
    break;
}

// atualiza tb_acervo

try {
  $result = mysqli_query($link, $sql_acervo);
  $linhas_afetadas = mysqli_affected_rows($link);
} catch (mysqli_sql_exception $e) {
  $query_erro = mysqli_errno($link);
}

// atualiza tb_emprestimo

try {
  $result = mysqli_query($link, $sql);
  $linhas_afetadas = mysqli_affected_rows($link);
} catch (mysqli_sql_exception $e) {
  $query_erro = mysqli_errno($link);
}

mysqli_close($link); 

$message = "";
// VOLTAR A PAGINA DO FORMULARIO
if (($acao <> "EMPRESTIMO") && $linhas_afetadas == 0){
   $message = "Empréstimo não localizado";
}  elseif (($acao == "EMPRESTIMO") && ($query_erro == 1062)){
   $message = "Título já emprestado";
   } else { $message = 'Sucesso';
     }

$url = "controle_emprestimo.html";
echo "<script> 
  alert(" . json_encode($message) . "); 
  window.location.href = " . json_encode($url) . ";
</script>";

// header ('Location: controle_emprestimo.html');
// exit;

?>