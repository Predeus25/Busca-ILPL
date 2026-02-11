<?php
header('Content-Type: application/json; charset=utf-8');

$estado = filter_input(INPUT_GET, 'estado', FILTER_SANITIZE_STRING);
$cidade = filter_input(INPUT_GET, 'cidade', FILTER_SANITIZE_STRING);

if (!$estado || !$cidade) {
    echo json_encode(['places' => []]); // Use 'places' key consistent with Places API (New)
    exit;
}

// ATENÇÃO: Mova a chave de API para uma variável de ambiente ou configuração segura.
// Não a deixe diretamente no código em produção.
$apiKey = 'AIzaSyDXD7LWP09K08PzlkYXCpO-R9ST8awR3cM'; // Substitua pela sua chave de API válida

$query = "casa de repouso em {$cidade}, {$estado}, Brasil";

// Endpoint da Places API (New) Text Search
$url = "https://places.googleapis.com/v1/places:searchText";

// Campos desejados (FieldMask)
// Consulte a documentação para ver os campos disponíveis: https://developers.google.com/maps/documentation/places/web-service/text-search#fields
$fieldMask = 'places.id,places.displayName,places.formattedAddress,places.photos,places.rating,places.userRatingCount,places.location';

// Dados para o corpo da requisição POST
$postData = json_encode([
    'textQuery' => $query,
    'languageCode' => 'pt-BR', // Opcional: Define o idioma dos resultados
    // 'maxResultCount' => 10, // Opcional: Limita o número de resultados (até 20)
    // 'regionCode' => 'BR' // Opcional: Define a região para bias nos resultados
]);

$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'X-Goog-Api-Key: ' . $apiKey,
    'X-Goog-FieldMask: ' . $fieldMask
]);

$response = curl_exec($ch);
$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

// Verifica se a requisição foi bem-sucedida
if ($httpcode >= 200 && $httpcode < 300) {
    echo $response;
} else {
    // Em caso de erro, retorna uma estrutura vazia ou uma mensagem de erro
    // Logar o erro seria importante em um ambiente de produção
    error_log("Erro na API do Google Places: HTTP $httpcode - Response: $response");
    echo json_encode(['places' => [], 'error' => 'Falha ao buscar locais', 'details' => json_decode($response)]);
}

