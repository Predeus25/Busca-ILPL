<?php
// Incluir arquivo de conexão com o banco de dados
include "conexao.php";

// Função para buscar todos os idosos
function buscarIdosos() {
    global $conexao;
    try {
        $sql = "SELECT id, nome, rg, cpf, tipo_sanguineo, doenca, outra_doenca, 
                DATE_FORMAT(data_nascimento, '%d/%m/%Y') as data_nascimento, 
                sexo, foto_perfil, data_cadastro 
                FROM dados_idoso 
                ORDER BY nome ASC";
        $stmt = $conexao->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        return ["erro" => "Erro ao buscar idosos: " . $e->getMessage()];
    }
}

// Função para buscar todos os responsáveis
function buscarResponsaveis() {
    global $conexao;
    try {
        $sql = "SELECT id, nome, email, cell, endereco, bairro, numero, complemento, 
                cep, rg, cpf, tipo_sanguineo, parentesco, outro_parentesco, data_cadastro 
                FROM dados_responsavel 
                ORDER BY nome ASC";
        $stmt = $conexao->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        return ["erro" => "Erro ao buscar responsáveis: " . $e->getMessage()];
    }
}

// Buscar dados
$idosos = buscarIdosos();
$responsaveis = buscarResponsaveis();

// Verificar se houve erro na busca de idosos
$erroIdosos = isset($idosos['erro']) ? $idosos['erro'] : null;

// Verificar se houve erro na busca de responsáveis
$erroResponsaveis = isset($responsaveis['erro']) ? $responsaveis['erro'] : null;
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <title>Dados Cadastrados - ILPL</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="styles_dados.css">
</head>
<body>
  <!-- Header -->
  <header>
    <div class="header-container">
      <img src="Logo-Tipo.png" width="25px" alt="Logo">
      <div class="menu">
        <button id="sidebarToggle" class="sidebar-toggle">
          <span></span>
          <span></span>
          <span></span>
        </button>
      </div>
    </div>
  </header>

  <!-- Barra lateral -->
  <div id="sidebar" class="sidebar">
    <div class="sidebar-header">
      <button id="sidebarClose" class="sidebar-close">&times;</button>
    </div>
    <div class="sidebar-content">
      <nav>
        <ul>         
          <li><a href="Pag_Inicial_2.html">Página Inicial</a></li>
          <li><a href="Pequisa.html">Pesquisa</a></li>
          <li><a href="Dados.php" class="active">Dados</a></li>
          <br/><br/><br/>
          <li><a href="logout.php">Logout</a></li>
        </ul>
      </nav>
    </div>
  </div>

  <!-- Overlay para quando a barra lateral estiver aberta -->
  <div id="overlay" class="overlay"></div>

  <div class="container">
    <fieldset><div class="paragrafo"><h1>Dados Cadastrados</h1></div></fieldset>
    
    <!-- Tabela de Idosos -->
    <div class="idosos"><h2>Idosos Cadastrados</h2></div>

    <div class="table-container">
      <?php if ($erroIdosos): ?>
        <div class="error-message"><?php echo $erroIdosos; ?></div>
      <?php else: ?>
        <table id="tabela-idosos">
          <thead>
            <tr>
              <th>Nome</th>
              <th>CPF</th>
              <th>RG</th>
              <th>Data de Nascimento</th>
              <th>Sexo</th>
              <th>Tipo Sanguíneo</th>
              <th>Doença</th>
              <th>Ações</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($idosos)): ?>
              <tr>
                <td colspan="9" class="no-data">Nenhum idoso cadastrado.</td>
              </tr>
            <?php else: ?>
              <?php foreach ($idosos as $idoso): ?>
                <tr>
                  <td data-label="Nome"><?php echo htmlspecialchars($idoso['nome']); ?></td>
                  <td data-label="CPF"><?php echo htmlspecialchars($idoso['cpf']); ?></td>
                  <td data-label="RG"><?php echo htmlspecialchars($idoso['rg']); ?></td>
                  <td data-label="Data de Nascimento"><?php echo htmlspecialchars($idoso['data_nascimento']); ?></td>
                  <td data-label="Sexo"><?php echo htmlspecialchars($idoso['sexo']); ?></td>
                  <td data-label="Tipo Sanguíneo"><?php echo htmlspecialchars($idoso['tipo_sanguineo']); ?></td>
                  <td data-label="Doença">
                    <?php 
                      echo htmlspecialchars($idoso['doenca']);
                      if ($idoso['outra_doenca']) {
                        echo " - " . htmlspecialchars($idoso['outra_doenca']);
                      }
                    ?>
                  </td>
                  <td data-label="Ações" class="action-buttons">
                    <button class="btn btn-edit" onclick="window.location.replace('Casadastro_2')">Editar</button>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      <?php endif; ?>
    </div>

    <!-- Tabela de Responsáveis -->
    <div class="respon"><h2>Responsáveis Cadastrados</h2></div>
    <div class="table-container">
      <?php if ($erroResponsaveis): ?>
        <div class="error-message"><?php echo $erroResponsaveis; ?></div>
      <?php else: ?>
        <table id="tabela-responsaveis">
          <thead>
            <tr>
              <th>Nome</th>
              <th>Email</th>
              <th>Telefone</th>
              <th>CPF</th>
              <th>RG</th>
              <th>Endereço</th>
              <th>Parentesco</th>
              <th>Tipo Sanguíneo</th>
              <th>Ações</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($responsaveis)): ?>
              <tr>
                <td colspan="9" class="no-data">Nenhum responsável cadastrado.</td>
              </tr>
            <?php else: ?>
              <?php foreach ($responsaveis as $responsavel): ?>
                <tr>
                  <td data-label="Nome"><?php echo htmlspecialchars($responsavel['nome']); ?></td>
                  <td data-label="Email"><?php echo htmlspecialchars($responsavel['email']); ?></td>
                  <td data-label="Telefone"><?php echo htmlspecialchars($responsavel['cell']); ?></td>
                  <td data-label="CPF"><?php echo htmlspecialchars($responsavel['cpf']); ?></td>
                  <td data-label="RG"><?php echo htmlspecialchars($responsavel['rg']); ?></td>
                  <td data-label="Endereço">
                    <?php 
                      echo htmlspecialchars($responsavel['endereco']) . ", " . 
                           htmlspecialchars($responsavel['numero']) . ", " . 
                           htmlspecialchars($responsavel['bairro']) . ", CEP " . 
                           htmlspecialchars($responsavel['cep']);
                      
                      if ($responsavel['complemento']) {
                        echo ", " . htmlspecialchars($responsavel['complemento']);
                      }
                    ?>
                  </td>
                  <td data-label="Parentesco">
                    <?php 
                      echo htmlspecialchars($responsavel['parentesco']);
                      if ($responsavel['outro_parentesco']) {
                        echo " - " . htmlspecialchars($responsavel['outro_parentesco']);
                      }
                    ?>
                  </td>
                  <td data-label="Tipo Sanguíneo"><?php echo htmlspecialchars($responsavel['tipo_sanguineo']); ?></td>
                  <td data-label="Ações" class="action-buttons">
                    <button class="btn btn-edit" onclick="window.location.replace('Casadastro_3')">Editar</button>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      <?php endif; ?>
    </div>
  </div>

  <script>
    // Funcionalidade da barra lateral
    document.addEventListener('DOMContentLoaded', function() {
      const sidebarToggle = document.getElementById('sidebarToggle');
      const sidebarClose = document.getElementById('sidebarClose');
      const sidebar = document.getElementById('sidebar');
      const overlay = document.getElementById('overlay');

      // Abrir a barra lateral
      sidebarToggle.addEventListener('click', function() {
        sidebar.classList.add('active');
        overlay.classList.add('active');
        document.body.style.overflow = 'hidden'; // Impede rolagem do body
      });

      // Fechar a barra lateral (botão X)
      sidebarClose.addEventListener('click', function() {
        sidebar.classList.remove('active');
        overlay.classList.remove('active');
        document.body.style.overflow = ''; // Restaura rolagem do body
      });

      // Fechar a barra lateral (clicando no overlay)
      overlay.addEventListener('click', function() {
        sidebar.classList.remove('active');
        overlay.classList.remove('active');
        document.body.style.overflow = ''; // Restaura rolagem do body
      });
      
      // Funcionalidade de filtro e pesquisa
      const searchBtn = document.querySelector('.search-btn');
      const searchTerm = document.getElementById('search-term');
      const filterType = document.getElementById('filter-type');
      
      searchBtn.addEventListener('click', function() {
        const term = searchTerm.value.toLowerCase();
        const type = filterType.value;
        
        // Filtrar tabela de idosos
        if (type === 'all' || type === 'idoso') {
          filterTable('tabela-idosos', term);
        }
        
        // Filtrar tabela de responsáveis
        if (type === 'all' || type === 'responsavel') {
          filterTable('tabela-responsaveis', term);
        }
      });
      
      function filterTable(tableId, term) {
        const table = document.getElementById(tableId);
        if (!table) return;
        
        const rows = table.getElementsByTagName('tr');
        
        for (let i = 1; i < rows.length; i++) { // Começa em 1 para pular o cabeçalho
          const row = rows[i];
          const cells = row.getElementsByTagName('td');
          
          // Pular linhas sem dados ou mensagens de erro
          if (cells.length <= 1 && cells[0] && cells[0].classList.contains('no-data')) {
            continue;
          }
          
          let found = false;
          
          for (let j = 0; j < cells.length; j++) {
            const cellText = cells[j].textContent.toLowerCase();
            if (cellText.indexOf(term) > -1) {
              found = true;
              break;
            }
          }
          
          if (found) {
            row.style.display = '';
          } else {
            row.style.display = 'none';
          }
        }
      }
    });
    
    // Funções para ações dos botões
    function verDetalhes(tipo, id) {
      alert(`Visualizando detalhes do ${tipo} com ID ${id}`);
      // Aqui você pode implementar a lógica para exibir detalhes em um modal ou redirecionar para uma página de detalhes
    }
    
    function editarRegistro(tipo, id) {
      if (tipo === 'idoso') {
        window.location.href = `editar_idoso.php?id=${id}`;
      } else {
        window.location.href = `editar_responsavel.php?id=${id}`;
      }
    }
    
    function confirmarExclusao(tipo, id) {
      if (confirm(`Tem certeza que deseja excluir este ${tipo}?`)) {
        window.location.href = `excluir.php?tipo=${tipo}&id=${id}`;
      }
    }
  </script>
</body>
</html>
