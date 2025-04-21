<?php

namespace Model;

class Usuario extends ActiveRecord {
    protected static $tabla = 'usuarios';
    protected static $columnasDB = ['id', 'nombre', 'email', 'password','token', 'confirmado'];


    public function __construct($args = []) {
        $this->id =$args['id'] ?? null;
        $this->nombre =$args['nombre'] ?? '';
        $this->email =$args['email'] ?? '';
        $this->password =$args['password'] ?? '';
        $this->password2 =$args['password2'] ?? '';
        $this->password_actual =$args['password_actual'] ?? '';
        $this->password_nuevo =$args['password_nuevo'] ?? '';
        $this->token =$args['token'] ?? '';
        $this->confirmado =$args['confirmado'] ?? 0;
    }

    //mensajes de validacion para la creacion de una cuenta

    public function validarNuevaCuenta() {
        if(!$this->nombre) {
            self::$alertas['error'][] ='El Nombre es Obligatorio';
        }
        if(!$this->email) {
            self::$alertas['error'][] ='El Email es Obligatorio';

        }
        if(!$this->password) {
            self::$alertas['error'][] ='El Password es Obligatorio';
        }
        if(strlen($this->password) < 6) {
            self::$alertas['error'][] ='El password debe contener al menos 6 caracteres';

        }
        if(($this->password !== $this->password2)) {
            self::$alertas['error'][] ='Los passwords son diferentes';

        }

        return self::$alertas;
    }

    public function validarLogin(){
        if(!$this->email) {
            self::$alertas['error'][] ='El Email es Obligatorio';
        }

        if(!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            self::$alertas['error'][] ='Email no Valido';
        }

        if(!$this->password) {
            self::$alertas['error'][] ='El Password es Obligatorio';
        }

        return self::$alertas;
    }

    public function validarEmail() {
        if(!$this->email) {
            self::$alertas['error'][] ='El Email es Obligatorio';
        }
        if(!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            self::$alertas['error'][] ='Email no Valido';
        }


        return self::$alertas;
    }

    public function validarPassword() {
        if(!$this->password) {
            self::$alertas['error'][] = 'El password es obligatorio';
        }

        if(strlen($this->password) <6) {
            self::$alertas ['error'][] = 'El password es debe tener al menos 6 caracteres';
        }
        return self::$alertas;
    }

    public function existeUsuario() {
        $email = self::$db->real_escape_string($this->email);
        $query = "SELECT * FROM " . self::$tabla . " WHERE email = '$email' LIMIT 1";
            
        $resultado = self::$db->query($query);

        if($resultado->num_rows) {
            self::$alertas['error'][] = 'El usuario ya esta registrado';
        }else {
            //no esta registrado
        }

        return $resultado;
    }

    public function validar_perfil() : array{
        if(!$this->nombre) {
            self::$alertas['error'][] = 'El Nombre es obligatorio';
        }
        if(!$this->email) {
            self::$alertas['error'][] = 'El Email es obligatorio';
        }
        return self::$alertas;
    }

    
    public function nuevo_password() : array{
        if(!$this->password_actual) {
            self::$alertas['error'][] = 'El Password no puede ir vacio';
        }

        if(!$this->password_nuevo) {
            self::$alertas['error'][] = 'El Password nuevo no puede ir vacio';
        }

        if(strlen($this->password_actual)<6) {
            self::$alertas['error'][] = 'El Password debe contener al menos 6 caracteres';
        }

        return self::$alertas;
    }

    public function comprobar_password() :bool {
        return password_verify($this->password_actual, $this->password );
    }

    public function hashPassword() :void {
        $this->password = password_hash($this->password, PASSWORD_BCRYPT);
    }
    
    public function crearToken() :void {
        $this->token = md5(uniqid());
    }

    

}