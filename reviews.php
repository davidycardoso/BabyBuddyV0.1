<?php
session_start();
include('conexao.php');

// Verifica se a babá está logada
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'babysitter') {
    header("Location: login.php");
    exit();
}

// Verifica os filtros de data ou avaliação
$date_filter = isset($_GET['date']) ? $_GET['date'] : '';
$rating_filter = isset($_GET['rating']) ? $_GET['rating'] : '';

// Consulta as avaliações feitas para a babá logada com filtros aplicados
$sql = "SELECT r.rating, r.comment, r.created_at, g.name AS guardian_name
        FROM reviews r
        JOIN guardians g ON r.guardian_id = g.id
        WHERE r.babysitter_id = ?";

// Aplica filtro de data, se houver
if ($date_filter) {
    $sql .= " AND DATE(r.created_at) = ?";
}

// Aplica filtro de rating, se houver
if ($rating_filter) {
    $sql .= " AND r.rating = ?";
}

$sql .= " ORDER BY r.created_at DESC";
$stmt = $conn->prepare($sql);
if ($date_filter && $rating_filter) {
    $stmt->bind_param("iss", $_SESSION['user_id'], $date_filter, $rating_filter);
} elseif ($date_filter) {
    $stmt->bind_param("is", $_SESSION['user_id'], $date_filter);
} elseif ($rating_filter) {
    $stmt->bind_param("ii", $_SESSION['user_id'], $rating_filter);
} else {
    $stmt->bind_param("i", $_SESSION['user_id']);
}
$stmt->execute();
$result = $stmt->get_result();

// Consulta a média das avaliações
$sql_avg = "SELECT AVG(rating) AS average_rating
            FROM reviews
            WHERE babysitter_id = ?";
$stmt_avg = $conn->prepare($sql_avg);
$stmt_avg->bind_param("i", $_SESSION['user_id']);
$stmt_avg->execute();
$result_avg = $stmt_avg->get_result();
$average_rating = $result_avg->fetch_assoc()['average_rating'];

// Garantir que a média não seja null (no caso de nenhuma avaliação)
if ($average_rating === null) {
    $average_rating = 0;
}

// Função para renderizar as estrelas
function render_stars($rating) {
    $stars = '';
    for ($i = 1; $i <= 5; $i++) {
        $stars .= '<span class="star' . ($i <= $rating ? ' filled' : '') . '">&#9733;</span>';
    }
    return $stars;
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Minhas Avaliações</title>
    <link rel="stylesheet" href="css/reviews.css">
</head>
<body>

<div class="reviews-container">
    <h2>Minhas Avaliações</h2>

    <!-- Filtros -->
    <div class="filters">
        <form method="get" action="">
            <label for="date">Filtrar por Data:</label>
            <input type="date" name="date" value="<?php echo $date_filter; ?>" />

            <label for="rating">Filtrar por Avaliação:</label>
            <select name="rating">
                <option value="">Todas as Avaliações</option>
                <option value="1" <?php echo ($rating_filter == '1') ? 'selected' : ''; ?>>1 Estrela</option>
                <option value="2" <?php echo ($rating_filter == '2') ? 'selected' : ''; ?>>2 Estrelas</option>
                <option value="3" <?php echo ($rating_filter == '3') ? 'selected' : ''; ?>>3 Estrelas</option>
                <option value="4" <?php echo ($rating_filter == '4') ? 'selected' : ''; ?>>4 Estrelas</option>
                <option value="5" <?php echo ($rating_filter == '5') ? 'selected' : ''; ?>>5 Estrelas</option>
            </select>

            <button type="submit">Filtrar</button>
        </form>
    </div>

    <!-- Média das avaliações em estrelas -->
    <div class="average-rating">
        <h3>Média de Avaliação</h3>
        <p>
            <?php echo render_stars(round($average_rating)); ?> <!-- Exibe as estrelas da média -->
            (<?php echo number_format($average_rating, 1); ?>)
        </p>
    </div>
    
    <?php if ($result->num_rows > 0): ?>
        <div class="reviews-list">
            <?php while ($review = $result->fetch_assoc()): ?>
                <div class="review">
                    <p><strong><?php echo $review['guardian_name']; ?></strong> - <?php echo date("d/m/Y H:i", strtotime($review['created_at'])); ?></p>
                    <p><strong>Avaliação:</strong> 
                        <?php echo render_stars($review['rating']); ?> <!-- Exibe as estrelas para cada avaliação individual -->
                    </p>
                    <p><strong>Comentário:</strong> <?php echo htmlspecialchars($review['comment']); ?></p>
                </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <p>Você ainda não tem avaliações.</p>
    <?php endif; ?>

    <!-- Botão de Voltar para o Dashboard -->
    <a href="babysitter_dashboard.php" class="back-btn">Voltar para o Dashboard</a>
</div>

</body>
</html>
