<?php
session_start();
include('conexao.php');

// Verifica se a babá está logada
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'babysitter') {
    header("Location: login.php");
    exit();
}

// Pega o ID da babá logada
$babysitter_id = $_SESSION['user_id'];

// Verifica se há um filtro de status
$status_filter = isset($_GET['status']) ? $_GET['status'] : '';

// Consulta para pegar as propostas, considerando o filtro de status
$sql = "
    SELECT p.id, g.name AS guardian_name, p.status
    FROM proposals p
    JOIN guardians g ON p.guardian_id = g.id
    WHERE p.babysitter_id = ?
";

// Aplica o filtro de status, se houver
if (!empty($status_filter)) {
    $sql .= " AND p.status = ?";
}

$stmt = $conn->prepare($sql);
if ($stmt === false) {
    die("Erro ao preparar a consulta SQL: " . $conn->error);
}

// Se houver filtro, bind para o status
if (!empty($status_filter)) {
    $stmt->bind_param("is", $babysitter_id, $status_filter);
} else {
    $stmt->bind_param("i", $babysitter_id);
}

if ($stmt->execute() === false) {
    die("Erro ao executar a consulta: " . $stmt->error);
}

$result = $stmt->get_result();
if ($result === false) {
    die("Erro ao obter resultados da consulta: " . $stmt->error);
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Propostas Detalhadas</title>
    <link rel="stylesheet" href="css/proposals.css">
</head>
<body>

<div class="proposals-container">
    <h2>Propostas Detalhadas</h2>

    <!-- Filtro de Status -->
    <form method="get" action="" class="filter-form">
        <label for="status">Filtrar por Status:</label>
        <select name="status" id="status" onchange="this.form.submit()">
            <option value="">Todos</option>
            <option value="pendente" <?php echo ($status_filter == 'pendente') ? 'selected' : ''; ?>>Pendentes</option>
            <option value="aceita" <?php echo ($status_filter == 'aceita') ? 'selected' : ''; ?>>Aceitas</option>
            <option value="rejeitada" <?php echo ($status_filter == 'rejeitada') ? 'selected' : ''; ?>>Rejeitadas</option>
            <option value="em_andamento" <?php echo ($status_filter == 'em_andamento') ? 'selected' : ''; ?>>Em Andamento</option>
            <option value="concluida" <?php echo ($status_filter == 'concluida') ? 'selected' : ''; ?>>Concluídas</option>
        </select>
    </form>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Responsável</th>
                <th>Status</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Verifica se há resultados e exibe as propostas
            if ($result->num_rows > 0) {
                while ($proposal = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $proposal['id'] . "</td>";
                    echo "<td>" . $proposal['guardian_name'] . "</td>";
                    echo "<td>" . ucfirst($proposal['status']) . "</td>";
                    echo "<td>";
                    // Exibe o link para visualizar os detalhes da proposta
                    echo '<a href="view_proposal_babysitter.php?id=' . $proposal['id'] . '">Ver Detalhes</a>';
                    
                    // Verifica o status da proposta e exibe os botões de aceitar ou rejeitar
                    if ($proposal['status'] == 'pendente') {
                        // Mostrar botões de ação se a proposta estiver no status 'pendente'
                        echo ' <a href="accept_proposal.php?id=' . $proposal['id'] . '">Aceitar</a>';
                        echo ' <a href="reject_proposal.php?id=' . $proposal['id'] . '">Rejeitar</a>';
                    }
                    
                    echo "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='4'>Nenhuma proposta encontrada.</td></tr>";
            }
            ?>
        </tbody>
    </table>

    <a href="babysitter_dashboard.php" class="back-btn">Voltar para o Dashboard</a>
</div>

</body>
</html>
