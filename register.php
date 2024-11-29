<?php
// Conexão com o banco de dados
include('conexao.php');

if (isset($_POST['register'])) {
    // Recebe dados do formulário
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $user_type = isset($_POST['user_type']) ? $_POST['user_type'] : ''; // Garante que o tipo de usuário seja obtido corretamente

    // Verifica se o tipo de usuário foi selecionado
    if (empty($user_type)) {
        echo "Erro: Tipo de usuário não selecionado.";
        exit; // Interrompe a execução se não houver tipo de usuário selecionado
    }

    // Latitude e Longitude
    $latitude = $_POST['latitude']; // Recebe a latitude capturada pelo JavaScript
    $longitude = $_POST['longitude']; // Recebe a longitude capturada pelo JavaScript

    // Criptografa a senha
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Processa a foto
    $photo = '';
    if ($user_type == 'babysitter' && isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $photo_name = $_FILES['photo']['name'];
        $photo_tmp_name = $_FILES['photo']['tmp_name'];
        $photo_size = $_FILES['photo']['size'];
        $photo_ext = strtolower(pathinfo($photo_name, PATHINFO_EXTENSION));

        // Verifica se a extensão da imagem é válida
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
        if (in_array($photo_ext, $allowed_extensions)) {
            // Gera um nome único para a foto e move para o diretório de uploads
            $photo_new_name = uniqid('', true) . '.' . $photo_ext;
            $photo_target = 'uploads/' . $photo_new_name;

            if (move_uploaded_file($photo_tmp_name, $photo_target)) {
                $photo = $photo_target; // Caminho da foto
            }
        }
    }

    // Insere os dados no banco
    if ($user_type == 'babysitter') {
        $hourly_rate = $_POST['hourly_rate']; // Taxa por hora
        $qualifications = $_POST['qualifications']; // Qualificações
        $experience = $_POST['experience']; // Experiência

        $sql = "INSERT INTO babysitters (name, email, password, latitude, longitude, photo, hourly_rate, qualifications, experience) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssssss", $name, $email, $hashed_password, $latitude, $longitude, $photo, $hourly_rate, $qualifications, $experience);
    } else {
        $sql = "INSERT INTO guardians (name, email, password) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $name, $email, $hashed_password);
    }

    if ($stmt->execute()) {
        echo "Cadastro realizado com sucesso!";
    } else {
        echo "Erro ao cadastrar: " . $stmt->error;
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro - BabyBuddy</title>
    <link rel="stylesheet" href="css/register.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css"> <!-- Font Awesome -->
</head>
<body>
    <div class="login-container">
        <i class="fa fa-baby icon"></i>  <!-- Ícone de bebê -->
        <h1>BabyBuddy</h1>
        <h2>Cadastre-se</h2>
        <form method="POST" action="register.php" enctype="multipart/form-data">
            <input type="text" name="name" placeholder="Nome" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Senha" required>

            <!-- Selecione o tipo de usuário -->
            <label for="babysitter">Babá</label>
            <input type="radio" name="user_type" value="babysitter" required>
            
            <label for="guardian">Responsável</label>
            <input type="radio" name="user_type" value="guardian" required>

            <!-- Campos adicionais para a babá -->
            <div id="babysitter_fields" style="display: none;">
                <input type="file" name="photo" accept="image/*">
                <input type="number" name="hourly_rate" placeholder="Taxa por hora" required>
                <textarea name="qualifications" placeholder="Qualificações" required></textarea>
                <textarea name="experience" placeholder="Experiência anterior" required></textarea>

                <!-- Latitude e Longitude serão preenchidos automaticamente -->
                <input type="text" id="latitude" name="latitude" placeholder="Latitude" readonly required>
                <input type="text" id="longitude" name="longitude" placeholder="Longitude" readonly required>
                
                <!-- Botão para capturar a localização -->
                <button type="button" onclick="getLocation()">Obter Localização</button>
            </div>

            <button type="submit" name="register" class="btn">Cadastrar</button>
        </form>
        <div class="options">
            <p><a href="login.php">Já tem uma conta? Faça login</a></p>
        </div>
    </div>

    <script>
        // Exibe os campos adicionais para babá quando a opção for escolhida
        document.querySelectorAll('input[name="user_type"]').forEach((radio) => {
            radio.addEventListener('change', function() {
                const babysitterFields = document.getElementById('babysitter_fields');
                const babysitterInputs = babysitterFields.querySelectorAll('input, textarea');

                if (this.value === 'babysitter') {
                    babysitterFields.style.display = 'block';
                    babysitterInputs.forEach(input => input.setAttribute('required', 'required'));
                } else {
                    babysitterFields.style.display = 'none';
                    babysitterInputs.forEach(input => input.removeAttribute('required'));
                }
            });
        });

        // Função para obter a localização do navegador
        function getLocation() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(showPosition, showError);
            } else {
                alert("Geolocalização não é suportada por este navegador.");
            }
        }

        // Função para exibir a posição
        function showPosition(position) {
            document.getElementById("latitude").value = position.coords.latitude;
            document.getElementById("longitude").value = position.coords.longitude;
        }

        // Função para lidar com erros
        function showError(error) {
            switch(error.code) {
                case error.PERMISSION_DENIED:
                    alert("Usuário negou a solicitação de geolocalização.");
                    break;
                case error.POSITION_UNAVAILABLE:
                    alert("A localização não está disponível.");
                    break;
                case error.TIMEOUT:
                    alert("A solicitação para obter a localização expirou.");
                    break;
                case error.UNKNOWN_ERROR:
                    alert("Ocorreu um erro desconhecido.");
                    break;
            }
        }
    </script>
</body>
</html>
