<?php

/**
 * Spoon Library - Clase para la manipulación de cookies
 *
 * @package     spoon
 * @subpackage  cookie
 * @author      Davy Hellemans <davy@spoon-library.com>
 * @since       0.1.1
 */

class SpoonCookie
{
    /**
     * Elimina una o más cookies.
     */
    public static function delete()
    {
        foreach (func_get_args() as $argument) {
            $keys = is_array($argument) ? $argument : [$argument];

            foreach ($keys as $key) {
                unset($_COOKIE[(string) $key]);
                setcookie((string) $key, '', time() - 3600, '/');
            }
        }
    }

    /**
     * Verifica si la cookie existe.
     *
     * @return bool Devuelve true si existe, false en caso contrario.
     */
    public static function exists()
    {
        foreach (func_get_args() as $argument) {
            $keys = is_array($argument) ? $argument : [$argument];

            foreach ($keys as $key) {
                if (!isset($_COOKIE[(string) $key])) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Obtiene el valor de una cookie.
     *
     * @param string $key Nombre de la cookie.
     * @return mixed El valor almacenado o false si no existe.
     */
    public static function get($key)
    {
        $key = (string) $key;

        if (!self::exists($key)) {
            return false;
        }

        $value = $_COOKIE[$key];

        // Intentar deserializar
        $actualValue = @unserialize($value);

        if ($actualValue === false && serialize(false) !== $value) {
            throw new SpoonCookieException('No se pudo recuperar la cookie "' . $key . '". Puede haber sido manipulada o no haber sido creada con SpoonCookie.');
        }

        return $actualValue;
    }

    /**
     * Guarda un valor en una cookie.
     *
     * @param string $key Nombre de la cookie.
     * @param mixed $value Valor a almacenar (se serializa).
     * @param int $time Tiempo de expiración en segundos (por defecto 1 día).
     * @param string $path Ruta en la que estará disponible.
     * @param string|null $domain Dominio en el que estará disponible.
     * @param bool $secure Solo enviar por HTTPS.
     * @param bool $httpOnly Hacer que solo esté disponible por HTTP.
     * @return bool Devuelve true si se estableció correctamente, false en caso contrario.
     */
    public static function set($key, $value, $time = 86400, $path = '/', $domain = null, $secure = false, $httpOnly = false)
    {
        $key = (string) $key;
        $value = serialize($value);
        $time = time() + (int) $time;

        return setcookie($key, $value, $time, $path, $domain, $secure, $httpOnly);
    }
}

/**
 * Clase de excepción para errores relacionados con cookies.
 */
class SpoonCookieException extends Exception {}
