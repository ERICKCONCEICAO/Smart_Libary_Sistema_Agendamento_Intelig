<?php
/* ativar relatórios de erros*/
//mysqli_report(MYSQLI_REPORT_ALL);
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
date_default_timezone_set("America/Sao_Paulo");

// CONEXÃO COM BANCO DE DADOS
// $link = mysqli_connect("localhost", "user", "password", "nome_da_base_de_dados");
$link = mysqli_connect("sql111.infinityfree.com", "if0_41441083", "A2026S1N7", "if0_41441083_db_smartlibrary");

// VERIFICAR CONEXÃO
if($link === false){
  die("ERRO DE CONEXÃO COM BANCO DE DADOS " . mysqli_connect_error());
}
$message = "";
$url = "index.php";

// Estatísticas simples
$total_loans = 0;
$distinct_alunos = 0;
$total_books = 0;
$total_late_loans = 0;

$res = mysqli_query($link, "SELECT COUNT(*) as c FROM tb_acervo");
if ($res) { $r = mysqli_fetch_assoc($res); $total_books = (int)$r['c']; mysqli_free_result($res); }

$res = mysqli_query($link, "SELECT COUNT(*) as c FROM tb_emprestimo WHERE dt_devolucaoEmprestimo = 0");
if ($res) { $r = mysqli_fetch_assoc($res); $total_loans = (int)$r['c']; mysqli_free_result($res); }

$res = mysqli_query($link, "SELECT COUNT(DISTINCT raEmprestimo) as c FROM tb_emprestimo WHERE dt_devolucaoEmprestimo = 0");
if ($res) { $r = mysqli_fetch_assoc($res); $distinct_alunos = (int)$r['c']; mysqli_free_result($res); }

$res = mysqli_query($link, "SELECT COUNT(*) as c FROM tb_emprestimo WHERE dt_devolucaoEmprestimo = 0 AND DATEDIFF (CURRENT_DATE(), dt_retiradaEmprestimo) > 30");
if ($res) { $r = mysqli_fetch_assoc($res); $total_late_loans = (int)$r['c']; mysqli_free_result($res); }

// limpeza de variáveis de erro/info
$linhas_afetadas = mysqli_affected_rows($link);
$query_erro = mysqli_errno($link);
?>

<!doctype html>
<html lang="pt-BR">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>SmartLibrary</title>
  <link rel="stylesheet" href="frontend/css/style.css">
</head>
<body>
  <header class="site-header">
    <div class="container">
      <div class="brand">
<!--
        <a href="https://univesp.br/" target="_blank" class="logo_univesp" aria-label="Univesp">
        <img src="img/logo_univesp.png" alt="Univesp">
        </a>
-->
        <a href="index.php" class="logo_smart" aria-label="Smart Library">
        <img src="img/logo_smartlibrary.png" alt="Smart Library">
        </a>
        <h3 style="text-align:left; margin:0 0 1rem;color:var(--accent-dark)">E.E. Deputado Silva Prado</h3>
      </div>
      <nav class="main-nav">
        <ul>
          <li><a class="btn" href="/cadastro_aluno.html">Cadastro de alunos</a></li>
          <li><a class="btn" href="/cadastro_livro.html">Cadastro de livros</a></li>
          <li><a class="btn" href="/controle_emprestimo.html">Empréstimo/Devolução</a></li>
          <li><a class="btn" href="/rel_emprestimos.php">Relatório</a></li>
        </ul>
      </nav>
    </div>
  </header>

<!--
  <main style="margin-top:70px;">
    <section class="hero" id="home">
      <div class="container">
        <h1>Bem-vindo à SmartLibrary</h1>
      </div>
    </section>
-->
  <main style="margin-top:70px;">
    <!-- Estatísticas -->
    <section class="cards" id="controle">
      <div class="card" style="max-width:720px;margin:0 auto 1rem;padding:1rem">
        <div style="display:flex;gap:0.5rem;align-items:center;flex-wrap:wrap">
          <div class="report-stats card">
            <h3 style="margin-top:0">Resumo Geral</h3>
            <ul>
              <li>Livros no acervo: <?php echo $total_books; ?></li>
              <li>Total de empréstimos: <?php echo $total_loans; ?></li>
              <li>Total de alunos com empréstimos registrados: <?php echo $distinct_alunos; ?></li>
              <li>Devoluções em atraso: <?php echo $total_late_loans; ?></li>
            </ul>
          </div>
        </div>
      </div>
    </section>
  </main>
<!--
    <section class="cards" id="controle">
      <div class="container">
        <h2 style="text-align:center; margin:0 0 1rem;color:var(--accent-dark)">Controle de Empréstimo</h2>
        <div class="card" style="max-width:720px;margin:0 auto;padding:1rem">
          <form name="formControle" action="/controle_emprestimo.php" method="POST">
            <div style="display:grid;gap:0.75rem;grid-template-columns:1fr 1fr;align-items:end">
              <label style="display:block">RA
                <input type="text" name="raAluno" id="raAluno_index" />
              </label>
              <label style="display:block">ISBN
                <input type="text" name="isbn" id="isbn_index" />
              </label>
            </div>
            <div style="display:flex;gap:0.5rem;justify-content:center;margin-top:0.75rem">
              <button class="btn" type="submit" name="acao" value="EMPRESTIMO">Empréstimo</button>
              <button class="btn" type="submit" name="acao" value="DEVOLUCAO">Devolução</button>
            </div>
          </form>
        </div>
      </div>
    </section>

    <section class="cards" id="catalogo">
      <div class="container">
        <div class="card" style="text-align:center;padding:2rem;border-radius:10px;box-shadow:0 6px 18px rgba(2,46,51,0.04);">
          <h2 style="margin:0 0 0.5rem;color:var(--accent-dark);font-size:1.35rem">📚 Apresentamos as escolas parceiras:</h2>
          <ul style="list-style:none;padding:0;margin:0;display:flex;flex-direction:column;gap:0.6rem;align-items:center">
            <li style="background:linear-gradient(90deg, rgba(71,163,178,0.06), rgba(2,104,51,0.03));padding:0.6rem 1rem;border-radius:8px;width:100%;max-width:640px;font-weight:600;color:var(--text-primary)">E.E. Deputado Silva Prado</li>
            <li style="background:linear-gradient(90deg, rgba(71,163,178,0.05), rgba(2,104,51,0.02));padding:0.6rem 1rem;border-radius:8px;width:100%;max-width:640px;font-weight:600;color:var(--text-primary)">CEI Casa da Criança</li>
            <li style="background:linear-gradient(90deg, rgba(71,163,178,0.05), rgba(2,104,51,0.02));padding:0.6rem 1rem;border-radius:8px;width:100%;max-width:640px;font-weight:600;color:var(--text-primary)">E.E. Pedro de Alcântara Marcondes Machado</li>
          </ul>
        </div>
      </div>
    </section>
-->

  <footer class="site-footer" id="contato">
    <div class="container">
      <p>© SmartLibrary — Sistema de Agendamento Inteligente</p>
    </div>
  </footer>
</body>
</html>
