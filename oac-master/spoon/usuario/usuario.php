<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

/**
 * Clase Usuario para la gestión de usuarios en la base de datos.
 * Extiende de DBConexion para manejar la conexión con la base de datos.
 */
class Usuario extends DBConexion
{
    private $nomUsu;  // Nombre de usuario
    private $emaUsu;  // Correo electrónico
    private $canEmaEncontrados; // Cantidad de emails encontrados
    private $passwdUsu; // Contraseña (encriptada)
    private $perfil; // Perfil de usuario
    private $nombre; // Nombre completo

    /**
     * Obtiene el perfil del usuario.
     * @return int Perfil del usuario.
     */
    public function getPerfil()
    {
        return $this->perfil;
    }

    /**
     * Establece el nombre del usuario con validación.
     * @param string $nombre Nombre del usuario.
     */
    public function setNombre($nombre)
    {
        if (is_string($nombre) && strlen($nombre) < 60) {
            $this->nombre = utf8_decode($nombre);
        } else {
            throw new Exception("Error: Nombre inválido.");
        }
    }

    /**
     * Establece el nombre de usuario con validación.
     * @param string $nombreUsuario Nombre de usuario.
     */
    public function setNomUsu($nombreUsuario)
    {
        if (is_string($nombreUsuario) && strlen($nombreUsuario) < 50) {
            $this->nomUsu = utf8_decode($nombreUsuario);
        } else {
            throw new Exception("Error: Nombre de usuario inválido.");
        }
    }

    /**
     * Establece el correo electrónico con validación.
     * @param string $emailUsuario Correo electrónico del usuario.
     */
    public function setEmaUsu($emailUsuario)
    {
        if (filter_var($emailUsuario, FILTER_VALIDATE_EMAIL) && strlen($emailUsuario) < 50) {
            $this->emaUsu = $emailUsuario;
        } else {
            throw new Exception("Error: Correo electrónico inválido.");
        }
    }

    /**
     * Establece la contraseña del usuario con validación y encriptación segura.
     * @param string $passwordUsuario Contraseña del usuario.
     */
    public function setPasUsu($passwordUsuario)
    {
        if (strlen($passwordUsuario) < 40 && ctype_alnum($passwordUsuario)) {
            $this->passwdUsu = password_hash($passwordUsuario, PASSWORD_BCRYPT);
        } else {
            throw new Exception("Error: Contraseña inválida. Use solo números y letras.");
        }
    }

    /**
     * Verifica si el usuario ya existe en la base de datos.
     * @return bool Verdadero si el usuario existe, falso si no.
     */
    private function verifyUserExist()
    {
        $this->canEmaEncontrados = $this->getNumRows('SELECT usuario FROM usuarios WHERE usuario = ?', [$this->nomUsu]);

        return $this->canEmaEncontrados === 1;
    }

    /**
     * Verifica si el correo electrónico ya está registrado en la base de datos.
     * @return bool Verdadero si el correo existe, falso si no.
     */
    private function verifyEmailExist()
    {
        $emailCount = $this->getNumRows('SELECT correo FROM usuarios WHERE correo = ?', [$this->emaUsu]);

        return $emailCount === 1;
    }

    /**
     * Registra un nuevo usuario en la base de datos.
     * @return bool Verdadero si el usuario fue registrado correctamente, falso si hubo un error.
     */
    public function addUser()
    {
        if ($this->verifyUserExist()) {
            throw new Exception("Error: Este usuario ya está registrado.");
        } elseif ($this->verifyEmailExist()) {
            throw new Exception("Error: Este correo electrónico ya está registrado.");
        }

        $aUsers = [
            'idusu' => null,
            'nomusu' => $this->nomUsu,
            'corusu' => $this->emaUsu,
            'pasusu' => $this->passwdUsu,
            'nombre' => $this->nombre,
            'fecregusu' => date('Y-m-d'),
        ];

        try {
            $this->insert('usuarios', $aUsers);
            return true;
        } catch (Exception $e) {
            throw new Exception("Error al registrar usuario: " . $e->getMessage());
        }
    }

    /**
     * Inicia sesión verificando credenciales en la base de datos.
     * @return bool Verdadero si el inicio de sesión es exitoso, falso si falla.
     */
    public function logInUser()
    {
        if (!$this->verifyUserExist()) {
            throw new Exception("Error: Usuario no registrado.");
        }

        $rs = $this->getRecord(
            'SELECT id_usuario, usuario, nombre, perfil, password FROM usuarios WHERE correo = ?',
            [$this->emaUsu]
        );

        if ($rs !== null && password_verify($this->passwdUsu, $rs['password'])) {
            session_start();
            $_SESSION['nomUsuario'] = $rs['usuario'];
            $_SESSION['idUsuario'] = $rs['id_usuario'];
            $_SESSION['nombre'] = $rs['nombre'];
            $_SESSION['logged'] = true;
            $_SESSION['perfil'] = $rs['perfil'];
            $this->perfil = $rs['perfil'];

            return true;
        } else {
            throw new Exception("Error: Credenciales incorrectas.");
        }
    }
}
?>
