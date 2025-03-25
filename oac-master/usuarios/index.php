<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión / Registrarse</title>
    <link rel="stylesheet" href="styles.css">
    <script defer src="script.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        /* Estilos generales */
        body {
            background: url('../imagenes/imgan-selfsecurity.jpg') no-repeat center center fixed;
            background-size: cover;
            font-family: 'Poppins', sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
            padding: 20px;
            overflow: hidden;
        }

        /* Contenedor principal */
        .container {
            width: 800px;
            height: 500px;
            border-radius: 15px;
            box-shadow: 0px 8px 20px rgba(0, 0, 0, 0.2);
            display: flex;
            position: relative;
            overflow: hidden;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            transition: transform 0.3s ease-in-out;
        }

        /* Formularios */
        .form-container {
            width: 50%;
            padding: 50px;
            position: absolute;
            transition: 0.5s ease-in-out;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        .login-container {
            left: 0;
        }

        .register-container {
            left: 100%;
        }

        .container.active .login-container {
            left: -100%;
        }

        .container.active .register-container {
            left: 0;
        }

        /* Panel lateral */
        .side-panel {
            width: 50%;
            background: linear-gradient(135deg, #007bff, #0056b3);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: white;
            text-align: center;
            top: 0;
            right: 0;
            padding: 20px;
            transition: transform 0.5s ease-in-out;
        }

        .container.active .side-panel {
            transform: translateX(-100%);
        }

        /* Inputs */
        input {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border: 1px solid #bbb;
            border-radius: 6px;
            font-size: 16px;
            transition: all 0.3s ease-in-out;
        }

        input:focus {
            border-color: #0056b3;
            box-shadow: 0 0 10px rgba(0, 86, 179, 0.4);
            outline: none;
        }

        /* Botones */
        button {
            background: #0056b3;
            color: white;
            border: none;
            padding: 12px;
            width: 100%;
            cursor: pointer;
            margin-top: 10px;
            border-radius: 6px;
            font-size: 16px;
            transition: background 0.3s ease-in-out;
        }

        button:hover {
            background: #003366;
        }

        /* Logos */
        .logo-container {
            position: absolute;
            top: 20px;
            right: 20px;
            display: flex;
            flex-direction: column;
        }

        .logo {
            max-width: 57px;
            cursor: pointer;
            margin-bottom: 10px;
        }

        /* Redes sociales */
        .barra {
            display: flex;
            flex-direction: column;
            gap: 10px;
            margin-top: 20px;
        }

        .barra a {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 50px;
            height: 50px;
            background-color: #333;
            color: white;
            font-size: 24px;
            border-radius: 50%;
            text-decoration: none;
            transition: 0.3s;
        }

        .barra a:hover {
            transform: scale(1.1);
            opacity: 0.8;
        }

        .facebook { background: #3b5998; }
        .twitter { background: #1da1f2; }
        .mail { background: #ff5733; }
        .google { background: #dd4b39; }
        .pinterest { background: #bd081c; }
        .github { background: #171515; }
        

    </style>
</head>
<body>
    <div class="logo-container">
        <img src="../imagenes/SelfSecuritygps.png" alt="SelfSecuritygps Logo" class="logo">
        <div class="barra">
            <a class="facebook" href="#"><i class="fab fa-facebook-f"></i></a>
            <a class="twitter" href="#"><i class="fab fa-twitter"></i></a>
            <a class="mail" href="#"><i class="fas fa-envelope"></i></a>
            <a class="google" href="#"><i class="fab fa-google-plus-g"></i></a>
            <a class="pinterest" href="#"><i class="fab fa-pinterest"></i></a>
            <a class="github" href="#"><i class="fab fa-github"></i></a>
        </div>
    </div>

    <div class="container" id="container">
        <div class="form-container login-container">
            <form action="registroU.php" method="GET">
                <h2>Iniciar Sesión</h2>
                <input type="email" name="correo" placeholder="Correo Electrónico" required>
                <input type="password" name="password" placeholder="Contraseña" required>
                <button type="submit">Entrar</button>
                <p>¿No tienes una cuenta? <a href="#" id="goToRegister">Regístrate</a></p>
            </form>
        </div>

        <div class="form-container register-container">
            <form action="registroU.php" method="GET">
<!-- <img src="../imagenes/Slide.jpg" alt="Slide Image" class="movable-image"> -->
                <h2>Registrarse</h2>
                <input type="text" name="nombre" placeholder="Nombre Completo" required>
                <input type="email" name="correo" placeholder="Correo Electrónico" required>
                <input type="password" name="password" placeholder="Contraseña" required>
                <button type="submit">Registrarse</button>
                <p>¿Ya tienes una cuenta? <a href="#" id="goToLogin">Iniciar Sesión</a></p>
            </form>
        </div>
    </div>

    <script>
        document.getElementById("goToRegister").addEventListener("click", function() {
            document.getElementById("container").classList.add("active");
        });
        document.getElementById("goToLogin").addEventListener("click", function() {
            document.getElementById("container").classList.remove("active");
        });
    </script>
</body>
</html>