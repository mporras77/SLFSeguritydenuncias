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
    position: fixed;
    bottom: 20px;
    right: 20px;
    display: flex;
    flex-direction: column;
    align-items: center; /* Centra los íconos */
    gap: 10px; /* Espacio uniforme entre elementos */
}

.barra a {
    position: relative;
    display: flex;
    justify-content: center; /* Centra el ícono */
    align-items: center;
    width: 50px; /* Tamaño uniforme */
    height: 50px;
    font-size: 24px;
    text-decoration: none;
    border-radius: 50%; /* Hace los botones circulares */
    background-color: white; /* Fondo para mejor visibilidad */
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
}

.barra a:hover {
    transform: scale(1.1); /* Efecto de crecimiento */
}

/* Tooltip (Texto emergente al pasar el mouse) */
.barra a span {
    position: absolute;
    right: 60px; /* Espaciado adecuado */
    background: rgba(0, 0, 0, 0.7);
    color: white;
    font-size: 14px;
    padding: 5px 10px;
    border-radius: 5px;
    white-space: nowrap;
    opacity: 0;
    transform: translateX(10px);
    transition: opacity 0.3s, transform 0.3s;
}

.barra a:hover span {
    opacity: 1;
    transform: translateX(0);
}

/* Colores específicos */
.facebook { color: #1877F2; }
.twitter { color: #1DA1F2; }
.kick { color:green; }
.youtube { color: #DB4437; }
.tiktok { color:black }
.whatsapp { color: green; }


    </style>
</head>
<body>
    <div class="logo-container">
    <a href="https://selfsecurity.com.co">
    <img src="../imagenes/SelfSecuritygps.png" alt="SelfSecuritygps Logo" class="logo">
</a>

        <div class="barra">
            <a class="facebook" href="https://www.facebook.com/selfsecur1tygps"><i class="fab fa-facebook-f"></i></a>
            <a class="twitter" href="https://x.com/Self_Security_?t=Y_Mw40mqbBj3xGLHmoA02w&s=09"><i class="fab fa-twitter"></i></a>
            <a class="kick" href="https://kick.com/selfsecuritygps" target="_blank">
    <i class="fas fa-tv"></i>
</a>
            <a class="youtube" href="https://www.youtube.com/@selfsecuritygps"><i class="fab fa-youtube"></i></a>
            <a class="tiktok" href="https://www.tiktok.com/@selfsecurity.gps"><i class="fab fa-tiktok"></i></a>
            <a class="whatsapp" href="#"><i class="fab fa-whatsapp"></i></a>
        </div>
    </div>

    <div class="container" id="container">
    <!-- FORMULARIO DE LOGIN -->
    <div class="form-container login-container">
        <form action="login.php" method="POST">
            <h2>Iniciar Sesión</h2>
            <input type="email" name="correo" placeholder="Correo Electrónico" required>
            <input type="password" name="password" placeholder="Contraseña" required>
            <button type="submit">Entrar</button>
            <p>¿No tienes una cuenta? <a href="#" id="goToRegister">Regístrate</a></p>
        </form>
    </div>

    <!-- FORMULARIO DE REGISTRO -->
    <div class="form-container register-container">
    <form action="./registoU.php" method="POST">
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