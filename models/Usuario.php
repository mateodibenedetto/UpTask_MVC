<?php 

namespace Model;

class Usuario extends ActiveRecord {
    protected static $tabla = 'usuarios';
    protected static $columnasDB = ['id', 'nombre', 'email', 'password', 'token', 'confirmado'];

    public function __construct($args = [])
    {
        $this->id = $args['id'] ?? null;
        $this->nombre = $args['nombre'] ?? '';
        $this->email = $args['email'] ?? '';
        $this->password = $args['password'] ?? '';
        $this->password2 = $args['password2'] ?? '';
        $this->password_actual = $args['password_actual'] ?? '';
        $this->password_nuevo = $args['password_nuevo'] ?? '';
        $this->token = $args['token'] ?? '';
        $this->confirmado = $args['confirmado'] ?? 0;
    }

    // *** Validación del Login *** //
    public function validarLogin() {
        if(!$this->email) {
            self::$alertas['error'][] = 'El email es obligatorio';
        }
        if(!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            self::$alertas['error'][] = 'Email no válido';
        }
        if(!$this->password) {
            self::$alertas['error'][] = 'La contraseña no puede ir vavcia';
        }

        return self::$alertas;
    }

    // *** Validación para cuentas nuevas *** //
    public function validarCuentaNueva() {
        if(!$this->nombre) {
            self::$alertas['error'][] = 'El nombre es obligatorio';
        }
        if(strlen($this->nombre) < 3) {
            self::$alertas['error'][] = 'El nombre debe contener al menos 3 caracteres';
        }
        if(!preg_match("/^[A-Za-zÑñÁáÉéÍíÓóÚúÜü\s]+$/", $this->nombre)) {
            self::$alertas['error'][] = 'El nombre solo puede contener espacios en blanco y letras';
        }
        if(!$this->email) {
            self::$alertas['error'][] = 'El email es obligatorio';
        }
        if(!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            self::$alertas['error'][] = 'Email no válido';
        }
        if(!$this->password) {
            self::$alertas['error'][] = 'La contraseña no puede ir vavcia';
        }
        if(strlen($this->password) < 6) {
            self::$alertas['error'][] = 'La contraseña debe contener al menos 6 caracteres';
        }
        if($this->password !== $this->password2) {
            self::$alertas['error'][] = 'Las contraseñas deben coincidir';
        }


        return self::$alertas;
    }

    // *** Validar Email *** //
    public function validarEmail()
    {
        if(!$this->email) {
            self::$alertas['error'][] = 'El Email es Obligatorio';
        }

        if(!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            self::$alertas['error'][] = 'Email no válido';
        }

        return self::$alertas;
    }

    // *** Validar Contraseña *** //
    public function validarPassword()
    {
        if(!$this->password) {
            self::$alertas['error'][] = 'La contraseña no puede ir vavcia';
        }
        if(strlen($this->password) < 6) {
            self::$alertas['error'][] = 'La contraseña debe contener al menos 6 caracteres';
        }
        
        return self::$alertas;
    }
    
    // *** Validar Perfiles *** //
    public function validar_perfil()
    {
        if(!$this->nombre) self::$alertas['error'][] = 'El Nombre es Obligatorio';
        if(!$this->email) self::$alertas['error'][] = 'El Email es Obligatorio';

        return self::$alertas;
    }


    public function nuevo_password() : array {
        if(!$this->password_actual) self::$alertas['error'][] = 'La contraseña es Obligatorio';
        
        if(!$this->password_nuevo) self::$alertas['error'][] = 'La Nueva Contraseña es Obligatorio';
         
        if($this->password_actual === $this->password_nuevo) self::$alertas['error'][] = 'La nueva contraseña no puede ser igual a la antigua';

        if(strlen($this->password_nuevo) < 6)  self::$alertas['error'][] = 'La Contraseña debe contener al menos 6 caracteres';
    
        return self::$alertas;
    }

    // *** Comprobar el password *** //
    public function comprobar_password() : bool {
        return password_verify($this->password_actual, $this->password);
    }

    // *** Hashea el password *** //
    public function hashPassword() : void {
        $this->password = password_hash($this->password, PASSWORD_BCRYPT);
    }

    // *** Generar Token *** //
    public function crearToken() : void {
        $this->token = uniqid();
    
    }



}