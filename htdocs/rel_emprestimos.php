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

// Recebe parâmetro de busca (RA) via GET
$ra_busca = isset($_GET['ra']) ? trim($_GET['ra']) : '';

// Consulta principal (usa prepared statements quando houver filtro)
if ($ra_busca !== '') {

//  $stmt = mysqli_prepare($link, "SELECT raEmprestimo, isbnEmprestimo, DATE_FORMAT(dt_retiradaEmprestimo, '%d/%m/%Y') as dtf_retiradaEmprestimo, dt_devolucaoEmprestimo FROM tb_emprestimo WHERE raEmprestimo = ?  && dt_devolucaoEmprestimo = 0 ORDER BY dt_retiradaEmprestimo DESC");
    
$stmt = mysqli_prepare($link, "SELECT e.raEmprestimo, e.isbnEmprestimo, DATE_FORMAT(e.dt_retiradaEmprestimo, '%d/%m/%Y') as dtf_retiradaEmprestimo, e.dt_devolucaoEmprestimo, DATEDIFF (CURRENT_DATE(), e.dt_retiradaEmprestimo) as diasEmprestimo, a.nomeAluno, l.titulo FROM tb_emprestimo e, tb_acervo l, tb_aluno a WHERE raEmprestimo = ? AND dt_devolucaoEmprestimo = 0 AND e.raEmprestimo = a.raAluno AND e.isbnEmprestimo = l.isbn ORDER BY dt_retiradaEmprestimo ASC");
  mysqli_stmt_bind_param($stmt, 's', $ra_busca);
  mysqli_stmt_execute($stmt);
  $result = mysqli_stmt_get_result($stmt);
} else {

//  $sql = "SELECT raEmprestimo, isbnEmprestimo, DATE_FORMAT(dt_retiradaEmprestimo, '%d/%m/%Y') as dtf_retiradaEmprestimo, dt_devolucaoEmprestimo FROM tb_emprestimo WHERE dt_devolucaoEmprestimo = 0 ORDER BY dt_retiradaEmprestimo DESC";

  $sql = "SELECT e.raEmprestimo, e.isbnEmprestimo, DATE_FORMAT(e.dt_retiradaEmprestimo, '%d/%m/%Y') as dtf_retiradaEmprestimo, e.dt_devolucaoEmprestimo, DATEDIFF (CURRENT_DATE(), e.dt_retiradaEmprestimo) as diasEmprestimo, a.nomeAluno, l.titulo FROM tb_emprestimo e, tb_acervo l, tb_aluno a WHERE dt_devolucaoEmprestimo = 0 AND e.raEmprestimo = a.raAluno AND e.isbnEmprestimo = l.isbn ORDER BY dt_retiradaEmprestimo ASC";
  $result = mysqli_query($link, $sql);
}



// limpeza de variáveis de erro/info
$linhas_afetadas = mysqli_affected_rows($link);
$query_erro = mysqli_errno($link);
?>

<!doctype html>
<html lang="pt-BR">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Relatório de Empréstimos — SmartLibrary</title>
  <link rel="stylesheet" href="frontend/css/style.css">
  <style>
    /* pequenas regras locais para ajustar listas no relatório */
    .report-stats{max-width:720px;margin:0 auto 1rem;padding:1rem}
    .report-list{max-width:720px;margin:0 auto;padding:1rem}
    .report-list ul{list-style:none;padding:0;margin:0}
    .report-list li{padding:0.75rem 0;border-bottom:1px solid rgba(2,46,51,0.04)}
    .report-list .loan-main{color:var(--text);}
    .report-list .loan-sub{margin-top:0.25rem;color:rgba(2,46,51,0.6);}
    /* quando o RA mudar, acrescentar um espaçamento maior para separar alunos */
    .report-list li.new-student{margin-top:1rem}
  </style>
</head>
<body>

  <header class="site-header">
    <div class="container">
      <div class="brand">
        <a href="index.php" class="logo_smart" aria-label="Smart Library">
        <img src="img/logo_smartlibrary.png" alt="Smart Library">
        </a>
        <h3 style="text-align:left; margin:0 0 1rem;color:var(--accent-dark)">E.E. Deputado Silva Prado</h3>
      </div>
    </div>
  </header>

  <main style="margin-top:20px;">
    <section class="cards">
      <div class="container">
        <h2 style="text-align:center; margin:0 0 1rem;color:var(--accent-dark)">Relatório de Empréstimos</h2>

        <!-- Formulário de busca por RA -->
        <div class="card" style="max-width:720px;margin:0 auto 1rem;padding:1rem">
          <form method="get" action="rel_emprestimos.php">
            <div style="display:flex;gap:0.5rem;align-items:center;flex-wrap:wrap">
              <label style="flex:1;min-width:180px">Pesquisar por RA:
                <input id="ra" name="ra" type="text" style="width: 40%; padding: 4px; value="<?php echo htmlspecialchars($ra_busca); ?> placeholder="Digite o RA e pressione Enter" />
              </label>
              <div style="display:flex;gap:0.5rem">
                <button class="btn" type="submit">Buscar</button>
                <?php if ($ra_busca !== ''): ?>
                  <button class="btn" type="button" onclick="window.location.href='rel_emprestimos.php'">Limpar</button>
                <?php endif; ?>
              </div>
            </div>
          </form>
        </div>

        

        <!-- Lista de Empréstimos -->
        <div class="report-list card" aria-live="polite">
          <h3 style="margin-top:0">Lista de Empréstimos</h3>
            <ul> 
            <?php 
            $prev_ra = null;
            if ($result && mysqli_num_rows($result) > 0) {
              while($row = mysqli_fetch_assoc($result)) {
                $current_ra = $row['raEmprestimo'];
                $dias = htmlspecialchars($row['diasEmprestimo']);
                if ($dias > 30){
                  $status = ' - ATRASADO';
                } else {
                  $status = '';
                }
                $li_class = ($prev_ra !== null && $current_ra !== $prev_ra) ? 'new-student' : '';
                echo '<li' . ($li_class ? ' class="' . $li_class . '"' : '') . '>';
                echo '<div class="loan-main">RA ' . htmlspecialchars($current_ra) . ' : ' . htmlspecialchars($row['nomeAluno']) . ' — retirada: ' . htmlspecialchars($row['dtf_retiradaEmprestimo']) . ' ( ' . $dias . ' dias )' . $status . '</div>';
                echo '<div class="loan-sub">ISBN ' . htmlspecialchars($row['isbnEmprestimo']) . ' : ' . htmlspecialchars($row['titulo']) . '</div>';
                echo '</li>';
                $prev_ra = $current_ra;
              }
            } else {
              echo "<li>0 resultados</li>";
            }
            // Fecha conexão
            mysqli_close($link);
            ?>
            </ul>
            <div style="text-align:center;margin-top:1rem">
              <a class="btn" href="index.php">Voltar ao Início</a>
            </div>
        </div>

      </div>
    </section>
  </main>

  <footer class="site-footer">
    <div class="container">
      <p>© SmartLibrary — Biblioteca Virtual</p>
    </div>
  </footer>

</body>
</html>
