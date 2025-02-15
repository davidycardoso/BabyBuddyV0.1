<?php
// Inclui a conexão com o banco de dados
include 'conexao.php';
session_start();

// Verifica se o usuário está logado e se é responsável
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'guardian') {
    header("Location: login.php");  // Se não for responsável, redireciona para o login
    exit;
}

// Consulta para buscar as babás
$sql = "SELECT * FROM babysitters";
$result = $conn->query($sql);

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Babás Próximas</title>
    <link rel="stylesheet" href="css/list.css"> <!-- Aqui você inclui o arquivo CSS -->
</head>
<body>
    <?php
    if ($result->num_rows > 0) {
        echo "<h2>Babás Próximas</h2>";

        // Exibe a lista de babás
        while ($row = $result->fetch_assoc()) {
            $babysitter_id = $row['id'];
            $name = $row['name'];
            $photo = $row['photo'];

            echo "<div class='babysitter-item'>";

            // Cria um link para o perfil completo
            echo "<a href='babysitter_profile.php?babysitter_id=$babysitter_id'>";

            // Exibe a foto da babá
            if ($photo) {
                echo "<img src='$photo' alt='$name' width='100' height='100'>";
            } else {
                echo "<img src='default-avatar.jpg' alt='$name' width='100' height='100'>";
            }

            // Exibe o nome da babá
            echo "<h3>$name</h3>";

            echo "</a>";
            echo "</div>";
        }
    } else {
        echo "<p class='no-babysitters'>Nenhuma babá encontrada.</p>";
    }

    // Fecha a conexão
    $conn->close();
    ?>

    <!-- Botão para voltar ao dashboard -->
    <div class="back-button-container">
        <a href="guardian_dashboard.php" class="back-button">Voltar ao Dashboard</a>
    </div>
</body>
</html>
