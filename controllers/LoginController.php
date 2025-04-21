<?php

namespace Controllers;
use MVC\Router;
use Model\Usuario;
use Classes\Email;


class LoginController {
    public static function login(Router $router) {
        $alertas = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $usuario = new Usuario($_POST);

            $alertas = $usuario->validarLogin();

            if (empty($alertas)) {
                // Buscar al usuario en la base de datos
                $usuarioExistente = Usuario::where('email', $usuario->email);

                if (
                    !$usuarioExistente ||
                    !$usuarioExistente->confirmado ||
                    !password_verify($_POST['password'], $usuarioExistente->password)
                ) {
                    Usuario::setAlerta('error', 'Credenciales inválidas');
                } else {
                    // Iniciar la sesión
                    session_start();
                    $_SESSION['id'] = $usuarioExistente->id;
                    $_SESSION['nombre'] = $usuarioExistente->nombre;
                    $_SESSION['email'] = $usuarioExistente->email;
                    $_SESSION['login'] = true;

                    header('Location: /dashboard');
                    exit;
                }
            }
        }

        $alertas = Usuario::getAlertas();

        $router->render('auth/login', [
            'titulo' => 'Iniciar Sesión',
            'alertas' => $alertas
        ]);
    }


    public static function logout() {
        session_start();
        $_SESSION = [];

        header('Location: /');

        if($_SERVER['REQUEST_METHOD'] === 'POST') {

        }
    }

    public static function crear(Router $router) {

        $usuario = new Usuario($_POST);
        $alertas = [];

        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $usuario->sincronizar($_POST);
            $alertas = $usuario->validarNuevaCuenta();

                //revisar que alertas este vacio
                if(empty($alertas)) {
                    //verificar que el usuario no este registrado
                    $resultado = $usuario->existeUsuario();
    
                    if($resultado->num_rows) {
                    $alertas = Usuario::getAlertas();
                    }else {
                    //hashear password
                    $usuario->hashPassword();
                    //eliminar password2
                    unset($usuario->password2);
                    //generar un token unico
                    $usuario->crearToken();
                    //enviar el email
                    $email = new Email($usuario->email, $usuario->nombre, $usuario->token);
                    $email->enviarConfirmacion();
    
                    //crear el usuario
                    $resultado = $usuario->guardar();
    
                    if($resultado) {
                        header('Location:/mensaje');
                    }
    
                    }
                }
        }

        $router->render('auth/crear', [
            'usuario' => $usuario,
            'titulo' => 'Crea tu cuenta en UpTask',
            'alertas' => $alertas,

        ]);
    }

    public static function olvide(Router $router) {

        $alertas = [];

        if ($_SERVER['REQUEST_METHOD'] ==='POST') {
            $usuario = new Usuario($_POST);
            $alertas = $usuario->validarEmail();

            if(empty($alertas)) {
                $usuario = Usuario::where('email', $usuario->email);
    
                if($usuario && $usuario->confirmado === "1") {
                    
                    //generar token 
                    $usuario->creartoken();
                    unset($usuario->password2);
                    $usuario->guardar();

                    //Enviar el email
                    $email = new Email($usuario->email, $usuario->nombre, $usuario->token);
                    $email->enviarInstrucciones();
                    //Alerta de exito
                    Usuario::setAlerta('exito', 'Hemos enviado las instrucciones a tu email');

                }else {
                    Usuario::setAlerta('error', 'El usuario no existe o no esta confirmado');

                }
            }
        }

        $alertas = Usuario::getAlertas();

        $router->render('auth/olvide',[
            'alertas' => $alertas
        ]);
    }

    public static function reestablecer(Router $router) {
        $alertas = [];
        $token = s($_GET['token']);
        $mostrar = true;

        if(!$token) header('Location: /');

        //buscar usuario por su token
        $usuario = Usuario::where('token', $token);

        if(empty($usuario)) {
            Usuario::setAlerta('error' ,'Token No Valido');
            $mostrar = false;
        }

        if ($_SERVER['REQUEST_METHOD']==='POST') {
            //leer el nuevo password y guardarlo

            $usuario->sincronizar($_POST);
            $alertas = $usuario->validarPassword();

            if(empty($alertas)) {

                $usuario->hashPassword();
                //token null
                $usuario->token = null;

                $resultado = $usuario->guardar();

    
                if($resultado) {
                    header('Location: /');
                }
            }
        }

        $alertas = Usuario::getAlertas();
        $router->render('auth/reestablecer',[
            'alertas' => $alertas,
            'mostrar' => $mostrar
        ]);
    }

    public static function mensaje(Router $router) {

        $router->render('auth/mensaje', [
            'titulo' => 'Cuenta creada Exitosamente'
        ]);

    }    
    
    public static function confirmar(Router $router) {

        $alertas =[];
        $token = s($_GET['token']);

        if(!$token) header('Location:/');

        $usuario = Usuario::where('token', $token);

        if(empty($usuario)) {
            //mostrar mensaje de error
            Usuario::setAlerta('error', 'Token No Valido');

        }else {
            //Modificar a usuario confirmado
            $usuario->confirmado = "1";
            $usuario->token = null;
            unset($usuario->password2);
            $usuario->guardar();
            
            Usuario::setAlerta('exito', 'Cuenta Comprobada Correctamente');
        }

        //obtener alertas
        $alertas = Usuario::getAlertas();

        //renderizar la vista
        $router->render('auth/confirmar', [
            'alertas' => $alertas,
            'titulo' => 'confirma tu cuenta en UpTask'
        ]);

    }


}