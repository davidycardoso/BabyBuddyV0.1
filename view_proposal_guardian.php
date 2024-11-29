<?php
session_start();
include('conexao.php');

// Verifica se a conexão foi bem-sucedida
if ($conn === false) {
    die("Erro na conexão com o banco de dados: " . $conn->connect_error);
}

// Verifica se o responsável está logado
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'guardian') {
    header("Location: login.php");
    exit();
}

$proposal_id = $_GET['id'] ?? null;
if (!$proposal_id) {
    die('Proposta não encontrada.');
}

// Consulta os detalhes da proposta, incluindo o babysitter_id
$sql = "SELECT p.id, bs.name AS babysitter_name, p.status, p.message AS proposal_message, p.babysitter_id 
        FROM proposals p
        JOIN babysitters bs ON p.babysitter_id = bs.id
        WHERE p.id = ? AND p.guardian_id = ?";
$stmt = $conn->prepare($sql);

// Verifica se houve erro na preparação da consulta
if ($stmt === false) {
    die("Erro ao preparar a consulta SQL: " . $conn->error);
}

$stmt->bind_param("ii", $proposal_id, $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();

// Verifica se há resultados
if ($result->num_rows > 0) {
    $proposal = $result->fetch_assoc();
} else {
    die('Proposta não encontrada ou você não tem permissão para visualizar essa proposta.');
}

// Envio de mensagem no código do responsável
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['message'])) {
    $message = $_POST['message'];
    $sender_id = $_SESSION['user_id']; // O responsável é quem envia a mensagem

    // Insere a mensagem no banco de dados
    $message_sql = "INSERT INTO messages (proposal_id, sender_id, sender_type, message) VALUES (?, ?, 'guardian', ?)";
    $message_stmt = $conn->prepare($message_sql);
    $message_stmt->bind_param("iis", $proposal_id, $sender_id, $message);
    if ($message_stmt->execute()) {
        header("Location: view_proposal_guardian.php?id=" . $proposal_id);
        exit();
    } else {
        die("Erro ao enviar a mensagem: " . $message_stmt->error);
    }
}

// Avaliação da Babá (caso o status seja 'concluida')
if ($proposal['status'] === 'concluida' && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['rating'], $_POST['comment'])) {
    $rating = (int)$_POST['rating'];
    $comment = trim($_POST['comment']);
    $babysitter_id = $proposal['babysitter_id']; // Atribui o babysitter_id da proposta
    $guardian_id = $_SESSION['user_id'];

    // Insere a avaliação no banco de dados
    $review_sql = "INSERT INTO reviews (babysitter_id, guardian_id, rating, comment) VALUES (?, ?, ?, ?)";
    $review_stmt = $conn->prepare($review_sql);
    $review_stmt->bind_param("iiis", $babysitter_id, $guardian_id, $rating, $comment);
    if ($review_stmt->execute()) {
        // Atualiza o status da proposta para 'avaliada'
        $update_status_sql = "UPDATE proposals SET status = 'avaliada' WHERE id = ?";
        $update_status_stmt = $conn->prepare($update_status_sql);
        $update_status_stmt->bind_param("i", $proposal_id);
        $update_status_stmt->execute();
        header("Location: view_proposal_guardian.php?id=" . $proposal_id);
        exit();
    } else {
        die("Erro ao adicionar a avaliação: " . $review_stmt->error);
    }
}

// Consulta as mensagens da proposta
if (isset($proposal)) {
    $message_sql = "SELECT m.message, 
                           CASE WHEN m.sender_type = 'babysitter' THEN bs.name 
                                WHEN m.sender_type = 'guardian' THEN g.name 
                           END AS sender_name, 
                           m.created_at 
                    FROM messages m 
                    LEFT JOIN babysitters bs ON m.sender_id = bs.id 
                    LEFT JOIN guardians g ON m.sender_id = g.id 
                    WHERE m.proposal_id = ? 
                    ORDER BY m.created_at ASC";
    $message_stmt = $conn->prepare($message_sql);
    $message_stmt->bind_param("i", $proposal_id);
    $message_stmt->execute();
    $message_result = $message_stmt->get_result();
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Visualizar Proposta</title>
    <link rel="stylesheet" href="css/guardian.css">
</head>
<body>

<div class="proposal-container">
    <h2>Detalhes da Proposta</h2>
    
    <p><strong>Babá:</strong> <?php echo htmlspecialchars($proposal['babysitter_name']); ?></p>
    <p><strong>Status:</strong> <?php echo ucfirst($proposal['status']); ?></p>
    <p><strong>Proposta:</strong> <?php echo nl2br(htmlspecialchars($proposal['proposal_message'])); ?></p>

    <!-- Exibição de mensagens -->
    <h3>Mensagens:</h3>
    <div class="messages">
        <?php while ($message = $message_result->fetch_assoc()): ?>
            <p><strong><?php echo htmlspecialchars($message['sender_name']); ?>:</strong> <?php echo htmlspecialchars($message['message']); ?></p>
        <?php endwhile; ?>
    </div>

    <!-- Formulário para enviar uma nova mensagem -->
    <form action="view_proposal_guardian.php?id=<?php echo $proposal['id']; ?>" method="post">
        <textarea name="message" placeholder="Digite sua mensagem..." required></textarea>
        <button type="submit" class="btn">Enviar Mensagem</button>
    </form>

    <!-- Formulário de avaliação -->
    <?php if ($proposal['status'] === 'concluida'): ?>
    <h3>Avaliar a Babá</h3>
    <form action="view_proposal_guardian.php?id=<?php echo $proposal['id']; ?>" method="post">
        <label for="rating">Avaliação (1 a 5):</label>
        <div>
            <input type="radio" id="star1" name="rating" value="1" required>
            <label for="star1">&#9733;</label>
            <input type="radio" id="star2" name="rating" value="2">
            <label for="star2">&#9733;</label>
            <input type="radio" id="star3" name="rating" value="3">
            <label for="star3">&#9733;</label>
            <input type="radio" id="star4" name="rating" value="4">
            <label for="star4">&#9733;</label>
            <input type="radio" id="star5" name="rating" value="5">
            <label for="star5">&#9733;</label>
        </div>

        <label for="comment">Comentário:</label>
        <textarea name="comment" id="comment" required></textarea>
        
        <button type="submit">Enviar Avaliação</button>
    </form>
    <?php endif; ?>
</div>

    <button type="submit"><a href="babysitter_list.php">Ver lista de Babás</a></button>

</body>
</html>
