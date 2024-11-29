<?php
session_start();
include('conexao.php');

// Verifica se a babá está logada
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'babysitter') {
    header("Location: login.php");
    exit();
}

$babysitter_id = $_SESSION['user_id'];

// Função para criar compromisso
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['create_appointment'])) {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $appointment_date = $_POST['appointment_date'];
    $notes = $_POST['notes'];

    // Inserir compromisso no banco
    $sql = "INSERT INTO appointments (babysitter_id, title, description, appointment_date, notes) 
            VALUES (?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        echo "Erro na preparação da consulta: " . $conn->error;
    } else {
        $stmt->bind_param("issss", $babysitter_id, $title, $description, $appointment_date, $notes);
        if ($stmt->execute()) {
            echo "Compromisso criado com sucesso!";
        } else {
            echo "Erro ao criar compromisso: " . $stmt->error;
        }
    }
}

// Função para visualizar compromissos
$sql_appointments = "SELECT * FROM appointments WHERE babysitter_id = ? ORDER BY appointment_date DESC";
$stmt_appointments = $conn->prepare($sql_appointments);
$stmt_appointments->bind_param("i", $babysitter_id);
$stmt_appointments->execute();
$result_appointments = $stmt_appointments->get_result();

// Função para excluir compromisso
if (isset($_GET['delete_appointment_id'])) {
    $appointment_id = $_GET['delete_appointment_id'];
    $sql_delete = "DELETE FROM appointments WHERE id = ?";
    $stmt_delete = $conn->prepare($sql_delete);
    $stmt_delete->bind_param("i", $appointment_id);
    if ($stmt_delete->execute()) {
        echo "Compromisso excluído com sucesso!";
    } else {
        echo "Erro ao excluir compromisso: " . $stmt_delete->error;
    }
}

// Função para criar nota
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['create_note'])) {
    $note = $_POST['note'];

    // Inserir nota no banco
    $sql_note = "INSERT INTO notes (babysitter_id, note) VALUES (?, ?)";
    $stmt_note = $conn->prepare($sql_note);
    if ($stmt_note === false) {
        echo "Erro na preparação da consulta de nota: " . $conn->error;
    } else {
        $stmt_note->bind_param("is", $babysitter_id, $note);
        if ($stmt_note->execute()) {
            echo "Nota criada com sucesso!";
        } else {
            echo "Erro ao criar nota: " . $stmt_note->error;
        }
    }
}

// Função para visualizar notas
$sql_notes = "SELECT * FROM notes WHERE babysitter_id = ? ORDER BY created_at DESC";
$stmt_notes = $conn->prepare($sql_notes);
$stmt_notes->bind_param("i", $babysitter_id);
$stmt_notes->execute();
$result_notes = $stmt_notes->get_result();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Agenda da Babá</title>
    <link rel="stylesheet" href="css/agenda.css">
</head>
<body>

<div class="agenda-container">

    <!-- Criar Compromisso -->
    <h2>Criar Compromisso</h2>
    <form action="agenda.php" method="POST">
        <input type="text" name="title" placeholder="Título" required><br>
        <textarea name="description" placeholder="Descrição" required></textarea><br>
        <input type="datetime-local" name="appointment_date" required><br>
        <textarea name="notes" placeholder="Notas"></textarea><br>
        <button type="submit" name="create_appointment">Criar Compromisso</button>
    </form>

    <!-- Visualizar Compromissos -->
    <h3>Compromissos</h3>
    <ul>
        <?php while ($appointment = $result_appointments->fetch_assoc()): ?>
            <li>
                <strong><?php echo $appointment['title']; ?></strong><br>
                <?php echo $appointment['description']; ?><br>
                Data: <?php echo date('d/m/Y H:i', strtotime($appointment['appointment_date'])); ?><br>
                Notas: <?php echo $appointment['notes']; ?><br>
                <a href="agenda.php?delete_appointment_id=<?php echo $appointment['id']; ?>" onclick="return confirm('Tem certeza que deseja excluir este compromisso?')">Excluir</a>
            </li>
        <?php endwhile; ?>
    </ul>

    <!-- Criar Nota -->
    <h2>Criar Nota</h2>
    <form action="agenda.php" method="POST">
        <textarea name="note" placeholder="Escreva sua nota..." required></textarea><br>
        <button type="submit" name="create_note">Criar Nota</button>
    </form>

    <!-- Visualizar Notas -->
    <h3>Notas</h3>
    <ul>
        <?php while ($note = $result_notes->fetch_assoc()): ?>
            <li>
                <?php echo $note['note']; ?><br>
                Criada em: <?php echo date('d/m/Y H:i', strtotime($note['created_at'])); ?>
            </li>
        <?php endwhile; ?>
    </ul>

</div>

<!-- Botão para voltar ao dashboard -->
<a href="babysitter_dashboard.php"><button class="back-button">Voltar ao Dashboard</button></a>


</body>
</html>
