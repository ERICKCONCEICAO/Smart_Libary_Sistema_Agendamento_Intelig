<?php
/* ativar relatórios de erros*/
//mysqli_report(MYSQLI_REPORT_ALL);
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
date_default_timezone_set("America/Sao_Paulo");

// Verifica se o formulário foi enviado via POST
//if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Validação e sanitização
//    $acao  = isset($_POST['acao']) ? $_POST['acao'] : 'NADA';
//}

// CONEXÃO COM BANCO DE DADOS
// $link = mysqli_connect("localhost", "user", "password", "nome_da_base_de_dados");
$link = mysqli_connect("sql111.infinityfree.com", "if0_41441083", "A2026S1N7", "if0_41441083_db_smartlibrary");

// VERIFICAR CONEXÃO
if($link === false){
  die("ERRO DE CONEXÃO COM BANCO DE DADOS " . mysqli_connect_error());
}
$message = "";
$url = "controle_emprestimo.html";

$sql = "SELECT raEmprestimo, isbnEmprestimo, DATE_FORMAT(dt_retiradaEmprestimo, '%d/%m/%Y') as dtf_retiradaEmprestimo, dt_devolucaoEmprestimo, ";
$sql.=" dt_retiradaEmprestimo FROM tb_emprestimo WHERE dt_devolucaoEmprestimo = 0";
$result = mysqli_query($link, $sql);
$linhas_afetadas = mysqli_affected_rows($link);
$query_erro = mysqli_errno($link);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Univesp</title>
</head>
  <body>
    <!-- <link href = "inserir.css" type = "text/css" rel = "stylesheet" />  -->
    <h2>PJI110 - Projeto Integrador I</h2>
    <h2>* * * Em construção * * *</h2>
    <p>Livros Emprestados</p>
    <ul>
        <?php 
        if (mysqli_num_rows($result) > 0) {
            // Loop pelos dados
            while($row = mysqli_fetch_assoc($result)) {
                $date_row = $row['dt_retiradaEmprestimo'];
                $date_atu = date("Y-m-d");
                $diff = strtotime($date_atu) - strtotime($date_row);
                $intervalo = abs(round($diff / 86400));
                //$intervalo = 0;
                echo "<li>". "RA:" . $row['raEmprestimo'] . " ISBN:" . $row['isbnEmprestimo'] . " retirada: " .
                      $row['dtf_retiradaEmprestimo'] . " dias: " . $intervalo . " </li>";
            }
                                      
        } else {
          echo "0 resultados";
          }
          mysqli_close($link);
        ?>
        <p>
        <p>
        <button onclick="window.location.href='index.html'"> INICIO </button>
    </ul>
  </body>
</html>
