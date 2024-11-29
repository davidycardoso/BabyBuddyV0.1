<?php
session_start();
include('conexao.php');

// Verifica se a conexão foi bem-sucedida
if ($conn === false) {
    die("Erro na conexão com o banco de dados: " . $conn->connect_error);
}

// Verifica se a babá está logada
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'babysitter') {
    header("Location: login.php");
    exit();
}

$proposal_id = $_GET['id'] ?? null;
if (!$proposal_id) {
    die('Proposta não encontrada.');
}

// Consulta os detalhes da proposta
$sql = "SELECT p.id, g.name AS guardian_name, p.status, p.babysitter_id, p.guardian_id, p.message AS proposal_message
        FROM proposals p
        JOIN guardians g ON p.guardian_id = g.id
        WHERE p.id = ? AND p.babysitter_id = ?";
$stmt = $conn->prepare($sql);

if ($stmt === false) {
    die("Erro ao preparar a consulta SQL: " . $conn->error);
}

$stmt->bind_param("ii", $proposal_id, $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die('Proposta não encontrada ou você não tem permissão para visualizar essa proposta.');
}

$proposal = $result->fetch_assoc();

// Lógica para aceitar, recusar ou concluir a proposta
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['accept'])) {
        // Aceitar a proposta
        $update_status_sql = "UPDATE proposals SET status = 'aceita' WHERE id = ? AND babysitter_id = ?";
        $update_stmt = $conn->prepare($update_status_sql);
        $update_stmt->bind_param("ii", $proposal_id, $_SESSION['user_id']);
        $update_stmt->execute();
    } elseif (isset($_POST['reject'])) {
        // Recusar a proposta
        $update_status_sql = "UPDATE proposals SET status = 'rejeitada' WHERE id = ? AND babysitter_id = ?";
        $update_stmt = $conn->prepare($update_status_sql);
        $update_stmt->bind_param("ii", $proposal_id, $_SESSION['user_id']);
        $update_stmt->execute();
    } elseif (isset($_POST['complete'])) {
        // Concluir o serviço
        $update_status_sql = "UPDATE proposals SET status = 'concluida' WHERE id = ? AND babysitter_id = ?";
        $update_stmt = $conn->prepare($update_status_sql);
        $update_stmt->bind_param("ii", $proposal_id, $_SESSION['user_id']);
        $update_stmt->execute();
    }

    // Lógica para enviar mensagem
    if (isset($_POST['message'])) {
        $message = $_POST['message'];
        $sender_id = $_SESSION['user_id'];
        $sender_type = 'babysitter'; // Como estamos assumindo que a mensagem é da babá
        $proposal_id = $proposal['id'];

        // Inserir a mensagem no banco de dados
        $message_sql = "INSERT INTO messages (proposal_id, sender_id, sender_type, message, created_at)
                        VALUES (?, ?, ?, ?, NOW())";
        $message_stmt = $conn->prepare($message_sql);

        if ($message_stmt === false) {
            die("Erro ao preparar a consulta SQL para inserir mensagem: " . $conn->error);
        }

        $message_stmt->bind_param("iiss", $proposal_id, $sender_id, $sender_type, $message);
        if ($message_stmt->execute()) {
            // Redireciona para a mesma página após o envio
            header("Location: view_proposal_babysitter.php?id=" . $proposal['id']);
            exit();
        } else {
            die("Erro ao enviar mensagem: " . $conn->error);
        }
    }

    // Redireciona após a ação de aceitar, recusar ou concluir a proposta
    header("Location: view_proposal_babysitter.php?id=" . $proposal_id);
    exit();
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Visualizar Proposta</title>
    <link rel="stylesheet" href="css/babysitter.css">
</head>
<body>

<div class="proposal-container">
    <h2>Detalhes da Proposta</h2>
    
    <p><strong>Responsável:</strong> <?php echo htmlspecialchars($proposal['guardian_name']); ?></p>
    <p><strong>Status:</strong> <?php echo ucfirst($proposal['status']); ?></p>
    <p><strong>Proposta:</strong> <?php echo nl2br(htmlspecialchars($proposal['proposal_message'])); ?></p>

    <!-- Exibição de mensagens -->
    <h3>Mensagens:</h3>
    <div class="messages">
        <?php
        // Consulta as mensagens da proposta
        $message_sql = "SELECT m.message, 
        CASE
            WHEN m.sender_type = 'babysitter' THEN bs.name
            WHEN m.sender_type = 'guardian' THEN g.name
        END AS sender_name,
        m.created_at
        FROM messages m
        LEFT JOIN babysitters bs ON m.sender_id = bs.id
        LEFT JOIN guardians g ON m.sender_id = g.id
        WHERE m.proposal_id = ?
        ORDER BY m.created_at ASC";

        $message_stmt = $conn->prepare($message_sql);

        if ($message_stmt === false) {
            die("Erro ao preparar a consulta SQL para obter mensagens: " . $conn->error);
        }

        $message_stmt->bind_param("i", $proposal_id);
        $message_stmt->execute();
        $message_result = $message_stmt->get_result();

        // Exibe as mensagens
        while ($message = $message_result->fetch_assoc()) {
            echo "<p><strong>" . htmlspecialchars($message['sender_name']) . ":</strong> " . htmlspecialchars($message['message']) . "</p>";
        }
        ?>
    </div>

    <!-- Formulário para enviar uma nova mensagem -->
    <form action="view_proposal_babysitter.php?id=<?php echo $proposal['id']; ?>" method="post">
        <textarea name="message" placeholder="Digite sua mensagem..." required></textarea>
        <button type="submit" class="btn">Enviar Mensagem</button>
    </form>

    <div class="action-buttons">
        <!-- Botões de Aceitar, Recusar e Concluir -->
        <?php if ($proposal['status'] === 'pendente'): ?>
            <form action="view_proposal_babysitter.php?id=<?php echo $proposal['id']; ?>" method="post">
                <button type="submit" name="accept" class="btn accept-btn">Aceitar Proposta</button>
                <button type="submit" name="reject" class="btn reject-btn">Recusar Proposta</button>
            </form>
        <?php elseif ($proposal['status'] === 'aceita' || $proposal['status'] === 'em_andamento'): ?>
            <form action="view_proposal_babysitter.php?id=<?php echo $proposal['id']; ?>" method="post">
                <button type="submit" name="complete" class="btn">Concluir Serviço</button>
            </form>
        <?php endif; ?>
    </div>

    <!-- Botão de voltar -->
    <a href="proposals.php" class="back-btn">Voltar para Propostas Recebidas</a>
</div>

</body>
</html>
