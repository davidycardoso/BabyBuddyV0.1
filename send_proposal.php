<?php
session_start();
include 'conexao.php';

// Verifica se o responsável está logado
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'guardian') {
    header("Location: login.php");  // Se não for responsável, redireciona para o login
    exit;
}

if (isset($_POST['babysitter_id']) && isset($_POST['proposal_message'])) {
    $babysitter_id = $_POST['babysitter_id'];
    $proposal_message = $_POST['proposal_message'];
    $guardian_id = $_SESSION['user_id']; // ID do responsável

    // Insere a proposta no banco de dados
    $sql = "INSERT INTO proposals (babysitter_id, guardian_id, message) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iis", $babysitter_id, $guardian_id, $proposal_message);

    if ($stmt->execute()) {
        $success_message = "Proposta enviada com sucesso!";
    } else {
        $error_message = "Erro ao enviar a proposta. Tente novamente.";
    }
} else {
    $error_message = "Dados da proposta não foram enviados corretamente.";
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Proposta Enviada</title>
    <link rel="stylesheet" href="css/proposta.css">
    <script>
        // Redireciona após 3 segundos
        setTimeout(function() {
            window.location.href = "babysitter_list.php";  // Redireciona para a lista de babás
        }, 3000);
    </script>
</head>
<body>
    <div class="message-container">
        <?php if (isset($success_message)): ?>
            <div class="success-message">
                <p><?php echo $success_message; ?></p>
            </div>
        <?php elseif (isset($error_message)): ?>
            <div class="error-message">
                <p><?php echo $error_message; ?></p>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>

