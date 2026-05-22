<?php
/* ativar relatórios de erros*/
//mysqli_report(MYSQLI_REPORT_ALL);
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$acao = $_POST['acao'];
$isbnLivro   = $_POST['isbnLivro'];
$tituloLivro = $_POST['tituloLivro'];
$autorLivro =  $_POST['autorLivro'];
$editoraLivro =  $_POST['editoraLivro'];

// CONEXÃO COM BANCO DE DADOS
// $link = mysqli_connect("localhost", "user", "password", "nome_da_base_de_dados");
$link = mysqli_connect("sql111.infinityfree.com", "if0_41441083", "A2026S1N7", "if0_41441083_db_smartlibrary");

// VERIFICAR CONEXÃO
if($link === false){
  die("ERRO DE CONEXÃO COM BANCO DE DADOS " . mysqli_connect_error());
}

// ESCAPE INPUTS
// $acao = mysqli_real_escape_string($link, $_POST["acao"]);
//$isbnLivro   = mysqli_real_escape_string($link, $_POST["isbnLivro"]);
//$tituloLivro = mysqli_real_escape_string($link, $_POST["tituloLivro"]);
//$autorLivro =  mysqli_real_escape_string($link, $_POST["autorLivro"]);
//$editoraLivro =  mysqli_real_escape_string($link, $_POST["editoraLivro"]);

// EXECUTAR AÇÃO SOLICITADA NA TABELA

switch ($acao) {
    case "INSERT":
    $sql = "INSERT INTO tb_acervo (isbn, titulo, autor, editora) VALUES ('$isbnLivro', '$tituloLivro', '$autorLivro', '$editoraLivro')";
    break;
    case "UPDATE":
    $sql = "UPDATE tb_acervo SET titulo = '$tituloLivro', autor = '$autorLivro', editora = '$editoraLivro' WHERE isbn = $isbnLivro";
    break;
    case "DELETE":
    $sql = "DELETE FROM tb_acervo WHERE isbn = $isbnLivro";
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
   $message = "Titulo não cadastrado";
}  elseif (($acao == "INSERT") && ($query_erro == 1062)){
   $message = "Titulo já existe";
   } else { $message = 'Sucesso';
     }

$url = "cadastro_livro.html";
echo "<script> 
  alert(" . json_encode($message) . "); 
  window.location.href = " . json_encode($url) . "; 
</script>";

// header ('Location: cadastro_livro.html');
// exit;

?>
