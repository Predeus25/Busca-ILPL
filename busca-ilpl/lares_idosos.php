<?php
require 'conexao_API.php';
header('Content-Type: application/json; charset=utf-8');

$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
    echo json_encode(['error' => 'Dados inválidos']);
    exit;
}

$nome = $data['nome'] ?? '';
$endereco = $data['endereco'] ?? '';
$cidade = $data['cidade'] ?? '';
$estado = $data['estado'] ?? '';
// Espera um array de URLs de imagens agora
$imagens = $data['imagens'] ?? []; // Usa array vazio como padrão se não fornecido
$origem = 'google_places'; // Ou determine com base na entrada, se necessário

if (!$nome || !$endereco || !$cidade || !$estado) {
    echo json_encode(['error' => 'Faltam dados obrigatórios para salvar o lar']);
    exit;
}

// Inicia a transação para atomicidade
$pdo->beginTransaction();

try {
    // Verifica se o lar já existe e obtém o ID se existir
    $sql_check = "SELECT id FROM lares WHERE nome = ? AND endereco = ?";
    $stmt_check = $pdo->prepare($sql_check);
    $stmt_check->execute([$nome, $endereco]);
    $lar_existente_id = $stmt_check->fetchColumn();

    if ($lar_existente_id === false) {
        // Lar não existe, insere na tabela lares (sem a coluna imagem)
        // Certifique-se que a coluna 'imagem' foi removida ou não está sendo usada aqui
        $sql_insert_lar = "INSERT INTO lares (nome, endereco, cidade, estado, origem) VALUES (?, ?, ?, ?, ?)";
        $stmt_insert_lar = $pdo->prepare($sql_insert_lar);
        $stmt_insert_lar->execute([$nome, $endereco, $cidade, $estado, $origem]);

        // Pega o ID do lar recém-inserido
        $lar_id = $pdo->lastInsertId();

        // Insere as imagens na tabela imagens_lares
        if (!empty($imagens) && is_array($imagens)) {
            $sql_insert_img = "INSERT INTO imagens_lares (lar_id, url_imagem) VALUES (?, ?)";
            $stmt_insert_img = $pdo->prepare($sql_insert_img);

            foreach ($imagens as $url_imagem) {
                // Adiciona uma verificação básica se a URL é válida e não está vazia
                if (!empty($url_imagem) && filter_var($url_imagem, FILTER_VALIDATE_URL)) {
                    $stmt_insert_img->execute([$lar_id, $url_imagem]);
                } else {
                    // Opcional: Logar ou tratar URLs inválidas
                    error_log("URL de imagem inválida ou vazia para o lar ID $lar_id: " . print_r($url_imagem, true));
                }
            }
        }

        // Confirma a transação
        $pdo->commit();
        echo json_encode(['success' => true, 'message' => 'Lar e imagens salvos com sucesso', 'lar_id' => $lar_id]);

    } else {
        // Lar já existe. Desfaz a transação iniciada, pois não faremos inserção.
        // Se fosse necessário atualizar imagens de lares existentes, a lógica seria aqui.
        $pdo->rollBack();
        echo json_encode(['message' => 'Lar já cadastrado', 'lar_id' => $lar_existente_id]);
    }

} catch (PDOException $e) {
    // Em caso de erro no banco de dados, desfaz a transação
    $pdo->rollBack();
    error_log("Erro no banco de dados ao salvar lar/imagens: " . $e->getMessage()); // Log do erro no servidor
    // Não envie detalhes do erro PDO para o cliente em produção por segurança
    echo json_encode(['error' => 'Erro ao salvar no banco de dados']);
    exit;
} catch (Exception $e) {
    // Captura outros erros inesperados
     $pdo->rollBack();
     error_log("Erro inesperado no script lares_idosos: " . $e->getMessage());
     echo json_encode(['error' => 'Ocorreu um erro inesperado']);
     exit;
}

?>

