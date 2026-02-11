<?php
session_start(); // Inicia a sessão em todas as páginas que precisam de autenticação

// Verifica se o usuário está logado
if (!isset($_SESSION["logged_in"]) || $_SESSION["logged_in"] !== true) {
    // Se não estiver logado, redireciona para a página de login
    header("Location: login.html");
    exit(); // Garante que o script pare de executar após o redirecionamento
}

// Se chegou até aqui, o usuário está logado.
// Você pode exibir o nome do usuário, por exemplo:
$nomeUsuario = $_SESSION["usuario_nome"] ?? "Usuário"; // Pega o nome da sessão

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <title>Busca ILPL - Bem-vindo, <?php echo htmlspecialchars($nomeUsuario); ?>!</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <!-- Mantém o link para o CSS original, mas talvez precise ajustar caminhos se mover arquivos -->
  <link rel="stylesheet" href="styles_pesquisa.css"> 

  <script async
    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDXD7LWP09K08PzlkYXCpO-R9ST8awR3cM&loading=async&libraries=places,marker&callback=initMap">
  </script>

  <style>
    /* Estilos gerais do modal (baseados no seu styles.css, mas garantindo rolagem) */
    #modalBox {
        display: none; /* Hidden by default */
        position: fixed; /* Stay in place */
        z-index: 1000; /* Sit on top */
        left: 0;
        top: 0;
        width: 100%; /* Full width */
        height: 100%; /* Full height */
        overflow: auto; /* Enable scroll if needed */
        background-color: rgba(0,0,0,0.6); /* Black w/ opacity */
        align-items: center; /* Center vertically */
        justify-content: center; /* Center horizontally */
    }
    .modal-content {
        background-color: #fefefe;
        margin: auto; /* Auto margin for centering */
        padding: 25px;
        border: 1px solid #888;
        width: 80%; /* Could be more specific */
        max-width: 700px; /* Max width */
        border-radius: 10px;
        position: relative;
        /* --- Correção para Rolagem --- */
        max-height: 85vh; /* Altura máxima antes de rolar */
        overflow-y: auto; /* Habilita rolagem vertical */
        /* --- Fim da Correção --- */
    }
    .modal-close {
        color: #aaa;
        position: absolute; /* Position relative to modal-content */
        top: 10px;
        right: 20px;
        font-size: 28px;
        font-weight: bold;
    }
    .modal-close:hover,
    .modal-close:focus {
        color: black;
        text-decoration: none;
        cursor: pointer;
    }

    /* Estilos adicionais para a galeria de imagens no modal */
    #modalImageContainer {
      display: flex;
      overflow-x: auto;
      gap: 10px;
      margin-bottom: 1rem;
      padding-bottom: 10px;
      max-height: 300px;
      background-color: #f0f0f0;
      border-radius: 8px;
      padding: 10px;
    }
    #modalImageContainer img {
      max-height: 280px;
      width: auto;
      border-radius: 4px;
      object-fit: cover;
      cursor: pointer;
      border: 1px solid #ccc;
    }
    #modalImage {
        display: none;
    }
    /* Estilo para a seção de contato */
    #modalContato {
        margin-top: 1rem;
        padding-top: 1rem;
        border-top: 1px solid #eee;
        display: none; /* Começa escondido */
    }
    #modalContato p {
        margin: 0.5rem 0;
        font-size: 0.95rem;
    }
    #modalContato strong {
        color: #333;
    }
    .loading-spinner {
        border: 4px solid #f3f3f3; /* Light grey */
        border-top: 4px solid #3498db; /* Blue */
        border-radius: 50%;
        width: 20px;
        height: 20px;
        animation: spin 1s linear infinite;
        display: inline-block;
        margin-left: 10px;
        vertical-align: middle;
    }
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    /* Estilo para o botão de logout */
    .logout-button {
        position: absolute;
        top: 15px;
        right: 15px;
        padding: 8px 15px;
        background-color: #f44336; /* Red */
        color: white;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        text-decoration: none;
        font-size: 0.9em;
    }
    .logout-button:hover {
        background-color: #d32f2f;
    }
  </style>

</head>
<body>

  <div class="container">
    <!-- Botão de Logout -->
    <a href="logout.php" class="logout-button">Sair</a>

    <h1>Busca ILPL</h1>
    <p>Bem-vindo, <?php echo htmlspecialchars($nomeUsuario); ?>!</p>

    <label for="estado">Selecione o estado:</label>
    <select id="estado" onchange="carregarCidades()">
      <option value="">Escolha um estado</option>
      <option value="SP">São Paulo</option>
      <option value="MG">Minas Gerais</option>
      <!-- Adicionar mais estados conforme necessário -->
    </select>
    <label for="cidade">Selecione a cidade:</label>
    <select id="cidade" onchange="mostrarLocais()">
      <option value="">Escolha uma cidade</option>
    </select>
    <div id="resultado">
      <br/>
      <h2>Locais encontrados:</h2>
      <table id="tabela-locais">
        <thead>
          <tr>
            <th>Foto</th>
            <th>Nome do Local</th>
            <th>Endereço</th>
            <th>Ação</th>
          </tr>
        </thead>
        <tbody><!-- Os locais serão inseridos aqui via JavaScript --></tbody>
      </table>
    </div>
  </div>

  <!-- Modal -->
  <div id="modalBox" role="dialog" aria-modal="true">
    <div class="modal-content">
      <span class="modal-close" onclick="fecharModal()" aria-label="Fechar modal">&times;</span>
      <div id="modalImageContainer"></div>
      <img id="modalImage" src="" alt="Imagem do local" style="display: none;" />
      <h2 id="modalNome"></h2>
      <p id="modalEndereco" class="modal-info"></p>
      <p class="modal-info">⭐ Avaliação: <span id="modalAvaliacao">N/A</span></p>
      <p class="modal-info">📊 Total de Avaliações: <span id="modalTotalAvaliacoes">N/A</span></p>
      <p class="modal-descricao" id="descricaoLocal"></p>
      <div id="modalMap" style="height: 250px; width: 100%; margin-top: 1rem; border-radius: 8px;"></div>

      <!-- Botão Ver Contato e Seção de Contato -->
      <button class="btn-agendar" id="btnAgendarModal">Ver Contato</button>
      <div id="modalContato">
        <h3>Informações de Contato:</h3>
        <p><strong>Telefone:</strong> <span id="modalTelefone">Buscando...</span></p>
        <p><strong>Email:</strong> <span id="modalEmail">Buscando...</span></p>
        <p id="contatoErro" style="color: red; display: none;"></p>
      </div>
    </div>
  </div>

  <script>
    // Chave da API do Google (Mantenha segura, idealmente não no frontend)
    const apiKey = 'AIzaSyDXD7LWP09K08PzlkYXCpO-R9ST8awR3cM'; 
    let map;
    let infoWindow;
    let advancedMarkerElement;
    let currentPlaceId = null;
    let placesService; // Serviço para buscar detalhes

    async function initMap() {
      try {
          const { Map } = await google.maps.importLibrary("maps");
          const { AdvancedMarkerElement } = await google.maps.importLibrary("marker");
          const { Place } = await google.maps.importLibrary("places"); // Importar Place
          
          map = new Map(document.getElementById("modalMap"), {
            center: { lat: -14.235, lng: -51.925 }, // Centro do Brasil
            zoom: 4,
            mapId: 'DEMO_MAP_ID' // Use seu Map ID se tiver um
          });
          
          advancedMarkerElement = new AdvancedMarkerElement({ map: map, position: null });
          infoWindow = new google.maps.InfoWindow();
          placesService = new google.maps.places.PlacesService(map); // Inicializa o PlacesService
          
          console.log("Mapa e serviços inicializados.");

          // Adiciona listener ao botão após o DOM estar pronto
          document.getElementById('btnAgendarModal').addEventListener('click', buscarEMostrarContato);
      } catch (error) {
          console.error("Erro ao inicializar o Google Maps:", error);
          alert("Não foi possível carregar o mapa. Verifique sua conexão e a chave da API.");
      }
    }

    const cidadesPorEstado = {
      SP: [ "São Paulo", "Guarulhos", "Campinas", "São Bernardo do Campo", "Santo André", "Osasco", "Sorocaba", "Ribeirão Preto", "São José dos Campos", "São José do Rio Preto", "Santos" ],
      MG: [ "Belo Horizonte", "Uberlândia", "Contagem", "Juiz de Fora", "Montes Claros", "Betim", "Uberaba", "Ribeirão das Neves", "Governador Valadares", "Divinópolis", "Ipatinga" ]
      // Adicionar mais estados e cidades aqui
    };

    function carregarCidades() {
      const estado = document.getElementById("estado").value;
      const cidadeSelect = document.getElementById("cidade");
      cidadeSelect.innerHTML = '<option value="">Escolha uma cidade</option>'; // Limpa opções anteriores
      document.querySelector("#tabela-locais tbody").innerHTML = ''; // Limpa resultados anteriores
      
      if (estado && cidadesPorEstado[estado]) {
        cidadesPorEstado[estado].forEach(cidade => {
          const option = document.createElement("option");
          option.value = cidade;
          option.textContent = cidade;
          cidadeSelect.appendChild(option);
        });
      }
    }

    // Função para buscar locais usando um script PHP intermediário (como estava antes)
    // Esta função parece usar um endpoint PHP que interage com a API do Google Places.
    // Mantendo essa estrutura, mas adicionando tratamento de erro.
    async function mostrarLocais() {
      const estado = document.getElementById("estado").value;
      const cidade = document.getElementById("cidade").value;
      if (!estado || !cidade) return;

      // **Atenção:** O endpoint 'buscar_google_places_updated.php' precisa existir e funcionar.
      // Este script PHP seria responsável por chamar a API do Google Places (Text Search ou Nearby Search)
      // e retornar os resultados em JSON.
      const url = `buscar_google_places_updated.php?estado=${encodeURIComponent(estado)}&cidade=${encodeURIComponent(cidade)}`;
      const tbody = document.querySelector("#tabela-locais tbody");
      tbody.innerHTML = '<tr><td colspan="4" style="text-align:center;">Buscando locais... <span class="loading-spinner"></span></td></tr>';

      try {
        const response = await fetch(url);
        if (!response.ok) {
            throw new Error(`Erro na requisição: ${response.status} ${response.statusText}`);
        }
        const data = await response.json();
        tbody.innerHTML = ''; // Limpa o 'Buscando...'

        if (data.error) {
          console.error("Erro retornado pelo backend PHP:", data.error, data.details);
          tbody.innerHTML = `<tr><td colspan="4" style="text-align:center; color: red;">Erro ao buscar locais: ${data.error}</td></tr>`;
          return;
        }

        // Assumindo que o PHP retorna um array 'places' com dados da API do Google
        if (!data.places || data.places.length === 0) {
          tbody.innerHTML = '<tr><td colspan="4" style="text-align:center;">Nenhum lar encontrado para esta cidade.</td></tr>';
          return;
        }

        // Processa cada local retornado pelo PHP
        for (const place of data.places) {
          const nome = place.displayName?.text || place.name || 'Nome não disponível'; // Compatibilidade
          const endereco = place.formattedAddress || place.vicinity || 'Endereço não disponível';
          const placeId = place.id || place.place_id; // Compatibilidade com diferentes versões da API
          const location = place.location || (place.geometry ? place.geometry.location : null);

          if (!placeId) {
              console.warn("Local sem Place ID encontrado:", place);
              continue; // Pula locais sem ID
          }

          let imagensUrls = [];
          let imagemPrincipalUrl = 'https://via.placeholder.com/100x75?text=Sem+Foto'; // Placeholder
          
          // Verifica se há fotos e constrói URLs usando a API Key
          if (place.photos && place.photos.length > 0) {
              // A API Places (New) retorna 'name' da foto, não 'photo_reference'
              const photoName = place.photos[0].name;
              if (photoName) {
                  // URL para a API Places (New) Photo Media
                  imagemPrincipalUrl = `https://places.googleapis.com/v1/${photoName}/media?key=${apiKey}&maxWidthPx=100&maxHeightPx=75`;
                  imagensUrls = place.photos.map(photo =>
                      `https://places.googleapis.com/v1/${photo.name}/media?key=${apiKey}&maxWidthPx=800`
                  );
              } else if (place.photos[0].photo_reference) { // Fallback para API antiga (se o PHP retornar assim)
                   imagemPrincipalUrl = `https://maps.googleapis.com/maps/api/place/photo?maxwidth=100&photoreference=${place.photos[0].photo_reference}&key=${apiKey}`;
                   imagensUrls = place.photos.map(p => `https://maps.googleapis.com/maps/api/place/photo?maxwidth=800&photoreference=${p.photo_reference}&key=${apiKey}`);
              }
          }

          const tr = document.createElement("tr");
          tr.innerHTML = `
            <td data-label="Foto"><img src="${imagemPrincipalUrl}" alt="Foto de ${nome}" loading="lazy" style="width:100px; height:75px; object-fit:cover; border-radius:4px;" /></td>
            <td data-label="Nome">${nome}</td>
            <td data-label="Endereço">${endereco}</td>
            <td data-label="Ação"><button class="btn-conhecer" onclick='abrirModal(${JSON.stringify(place)}, ${JSON.stringify(imagensUrls)})'>Conhecer</button></td>
          `;
          tbody.appendChild(tr);

          // Salva/Atualiza no banco de dados local (via PHP)
          // **Atenção:** O endpoint 'lares_idosos.php' precisa existir e funcionar.
          fetch('lares_idosos.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ 
                nome: nome,
                endereco: endereco,
                cidade: cidade,
                estado: estado,
                imagens: imagensUrls, // Salva URLs das imagens grandes
                google_place_id: placeId,
                latitude: location?.latitude,
                longitude: location?.longitude
             })
          })
          .then(res => res.json())
          .then(saveResult => {
              if(saveResult.success){
                  console.log(`Lar '${nome}' salvo/atualizado com ID local: ${saveResult.lar_id}`);
              } else {
                  console.warn(`Problema ao salvar/atualizar lar '${nome}': ${saveResult.message || saveResult.error}`);
              }
          })
          .catch(err => console.error("Erro crítico ao tentar salvar no DB local:", err));
        }
      } catch (error) {
        tbody.innerHTML = '<tr><td colspan="4" style="text-align:center; color: red;">Erro ao buscar ou processar locais. Verifique o console.</td></tr>';
        console.error("Erro na função mostrarLocais:", error);
      }
    }

    // Função para abrir o modal com detalhes do local
    async function abrirModal(placeData, imagensUrls) {
      if (!map || !advancedMarkerElement) {
        console.error("Mapa ou marcador não inicializado.");
        alert("Erro ao carregar detalhes do mapa. Tente recarregar a página.");
        return;
      }

      currentPlaceId = placeData.id || placeData.place_id;
      if (!currentPlaceId) {
          console.error("Erro: Google Place ID não encontrado nos dados do local para o modal.");
          alert("Não foi possível carregar os detalhes deste local (ID ausente).");
          return;
      }

      // Reseta e esconde a seção de contato
      const contatoDiv = document.getElementById('modalContato');
      const telefoneSpan = document.getElementById('modalTelefone');
      const emailSpan = document.getElementById('modalEmail');
      const erroSpan = document.getElementById('contatoErro');
      contatoDiv.style.display = 'none';
      telefoneSpan.innerHTML = 'Buscando...'; // Reset text
      emailSpan.innerHTML = 'Buscando...'; // Reset text
      erroSpan.style.display = 'none';
      erroSpan.textContent = '';

      // Mostra o modal
      document.getElementById("modalBox").style.display = "flex";

      // Preenche informações básicas do modal
      const nome = placeData.displayName?.text || placeData.name || 'Nome não disponível';
      const endereco = placeData.formattedAddress || placeData.vicinity || 'Endereço não disponível';
      const avaliacao = placeData.rating ? placeData.rating.toFixed(1) : 'N/A';
      const totalAvaliacoes = placeData.userRatingCount || placeData.user_ratings_total || 0;
      const location = placeData.location || (placeData.geometry ? placeData.geometry.location : null);

      document.getElementById("modalNome").innerText = nome;
      document.getElementById("modalEndereco").innerText = endereco;
      document.getElementById("modalAvaliacao").innerText = avaliacao;
      document.getElementById("modalTotalAvaliacoes").innerText = totalAvaliacoes;
      // Descrição simples (pode ser melhorada)
      document.getElementById("descricaoLocal").innerText =
        `Conheça o lar ${nome}, localizado em ${endereco}. Avaliado com ${avaliacao}/5 por ${totalAvaliacoes} usuários.`;

      // Preenche a galeria de imagens
      const imageContainer = document.getElementById("modalImageContainer");
      imageContainer.innerHTML = ''; // Limpa imagens anteriores
      if (imagensUrls && imagensUrls.length > 0) {
        imageContainer.style.display = 'flex';
        imagensUrls.forEach(url => {
          const img = document.createElement('img');
          img.src = url;
          img.alt = `Imagem de ${nome}`;
          img.loading = 'lazy';
          img.onerror = () => { img.src = 'https://via.placeholder.com/200x150?text=Erro+Img'; }; // Placeholder em caso de erro
          imageContainer.appendChild(img);
        });
      } else {
        imageContainer.innerHTML = '<p style="text-align:center; width:100%;">Nenhuma imagem disponível.</p>';
        imageContainer.style.display = 'block'; // Mudar para block se for texto
      }

      // Atualiza o mapa
      const mapDiv = document.getElementById("modalMap");
      if (location && (location.latitude || location.lat) && (location.longitude || location.lng)) {
        const position = { lat: location.latitude || location.lat, lng: location.longitude || location.lng };
        map.setCenter(position);
        map.setZoom(16);
        advancedMarkerElement.position = position;
        advancedMarkerElement.title = nome;
        mapDiv.style.display = 'block';
      } else {
        mapDiv.style.display = 'none';
        console.warn("Coordenadas não encontradas para:", nome);
        advancedMarkerElement.position = null;
      }
    }

    // Função para buscar detalhes de contato usando Place Details API via PHP
    async function buscarEMostrarContato() {
        if (!currentPlaceId) {
            console.error("Não foi possível buscar contato: Google Place ID não definido.");
            const erroSpan = document.getElementById('contatoErro');
            erroSpan.textContent = 'Erro interno: ID do local não encontrado.';
            erroSpan.style.display = 'block';
            document.getElementById('modalContato').style.display = 'block';
            return;
        }

        const contatoDiv = document.getElementById('modalContato');
        const telefoneSpan = document.getElementById('modalTelefone');
        const emailSpan = document.getElementById('modalEmail');
        const erroSpan = document.getElementById('contatoErro');

        // Mostra a seção de contato e indicadores de carregamento
        contatoDiv.style.display = 'block';
        telefoneSpan.innerHTML = 'Buscando... <span class="loading-spinner"></span>';
        emailSpan.innerHTML = 'Buscando... <span class="loading-spinner"></span>';
        erroSpan.style.display = 'none';
        erroSpan.textContent = '';

        try {
            console.log(`Buscando detalhes (contato) para place_id: ${currentPlaceId}`);
            // **Atenção:** O endpoint 'buscar_contato_lar.php' precisa existir e funcionar.
            // Este script PHP deve usar o Place ID para chamar a API Place Details do Google
            // solicitando os campos 'formatted_phone_number' e 'website' (ou 'email' se disponível, raro).
            const response = await fetch(`buscar_contato_lar.php?place_id=${encodeURIComponent(currentPlaceId)}`);
            if (!response.ok) {
                 throw new Error(`Erro na requisição de contato: ${response.status} ${response.statusText}`);
            }
            const data = await response.json();
            console.log('Resposta do servidor (contato):', data);

            if (data.success) {
                // Assume que o PHP retorna 'telefone' e 'email' (ou 'website')
                telefoneSpan.textContent = data.telefone || 'Não informado';
                // A API do Google raramente retorna email. Website é mais comum.
                emailSpan.textContent = data.email || data.website || 'Não informado'; 
                if (data.website) {
                    // Se for um website, transforma em link
                    emailSpan.innerHTML = `<a href="${data.website}" target="_blank" rel="noopener noreferrer">${data.website}</a>`;
                }
            } else {
                console.error("Erro retornado pelo backend PHP (contato):", data);
                telefoneSpan.textContent = '-'; // Indicar que não foi possível buscar
                emailSpan.textContent = '-';
                erroSpan.textContent = data.message || data.error || 'Não foi possível carregar os contatos.';
                erroSpan.style.display = 'block';
            }
        } catch (error) {
            console.error("Falha na requisição de contato (fetch catch):", error);
            telefoneSpan.textContent = 'Erro';
            emailSpan.textContent = 'Erro';
            erroSpan.textContent = 'Falha ao conectar com o servidor para buscar contatos.';
            erroSpan.style.display = 'block';
        }
    }

    function fecharModal() {
      document.getElementById("modalBox").style.display = "none";
      currentPlaceId = null; // Limpa o ID ao fechar
      // Opcional: Parar qualquer carregamento pendente de contato
    }

    // Fecha o modal se clicar fora do conteúdo
    window.onclick = function(event) {
      const modal = document.getElementById("modalBox");
      if (event.target == modal) {
        fecharModal();
      }
    }

    // Inicializa o mapa quando a API do Google estiver pronta
    // A função initMap é chamada pelo callback na URL da API do Google Maps

  </script>

</body>
</html>

