<?php

namespace Controllers;

use Classes\Email;
use Model\Usuario;
use MVC\Router;


class LoginController {

    // <========= LOGIN =========> //
    public static function login(Router $router) {
        $alertas = [];

        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $usuario = new Usuario($_POST);

            $alertas = $usuario->validarLogin();

            if(empty($alertas)) {
                // Verificar que el usuario exista
                $usuario = Usuario::where('email', $usuario->email);
                
                if(!$usuario || !$usuario->confirmado) {
                    Usuario::setAlerta('error', 'No existe ninguna cuenta con este correo o no está confirmada');
                } else {
                    // El usuario existe
                    if( password_verify($_POST['password'], $usuario->password) ) {
                        // Iniciar sesion
                        session_start();
                        $_SESSION['id'] = $usuario->id;
                        $_SESSION['nombre'] = $usuario->nombre;
                        $_SESSION['email'] = $usuario->email;
                        $_SESSION['login'] = true;
                        
                        // Redireccionar
                        header('Location: /dashboard');
                    } else {
                        Usuario::setAlerta('error', 'Contraseña incorrecta');
                    }
                }
            }
        }

        $alertas = Usuario::getAlertas();
        // Render a la vista
        $router->render('auth/login', [
            'titulo' => 'Iniciar Sesion',
            'alertas' => $alertas
        ]);
    }

    // <========= LOGOUT =========> //
    public static function logout() {
        session_start();
        $_SESSION = [];
        header('Location: /');

    }

    // <========= CREAR =========> //
    public static function crear(Router $router) {
        $alertas = [];
        $usuario = new Usuario;

        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            
            $usuario->sincronizar($_POST);
            $alertas = $usuario->validarCuentaNueva();

            if(empty($alertas)) {
                $existeUsuario = Usuario::where('email', $usuario->email);
            
                if($existeUsuario) {
                    Usuario::setAlerta('error', 'Este correo ya está registrado');
                    $alertas = Usuario::getAlertas();
                } else{ 
                    // *** Hashear Password *** //
                    $usuario->hashPassword(); 

                    // *** Eliminar password2 *** //
                    unset($usuario->password2);

                    // *** Generar Token *** //
                    $usuario->crearToken();

                    $usuario->confirmado = 0;

                    // *** Crear un nuevo usuario *** //
                    $resultado = $usuario->guardar();

                    // *** Enviar email *** //
                    $email = new Email($usuario->nombre, $usuario->email, $usuario->token);
                    $email->enviarConfirmacion();

                    if($resultado) header('Location: /mensaje');  
                    
                }
            }

        }

        // Render a la vista
        $router->render('auth/crear', [
            'titulo' => 'Crear Cuenta',
            'usuario' => $usuario,
            'alertas' => $alertas
        ]);
    }

    // <========= OLVIDE =========> //
    public static function olvide(Router $router) {
        $alertas = [];

        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $usuario = new Usuario($_POST);
            $alertas = $usuario->validarEmail();
            
            if(empty($alertas)) {
                // Buscar el usuario
                $usuario = Usuario::where('email', $usuario->email);

                if($usuario && $usuario->confirmado) {
                    // Generar nuevo token
                    $usuario->crearToken();
                    unset($usuario->password2);

                    // Acutualiza el usuario
                    $usuario->guardar();

                    // Enviar el email
                    $email = new Email($usuario->nombre, $usuario->email, $usuario->token);
                    $email->enviarInstrucciones();

                    // Imprimir la alerta
                    Usuario::setAlerta('exito', 'Hemos enviado las instrucciones a tu emil');
                } else {
                    Usuario::setAlerta('error', 'Este correo no está asociado o no está confirmado');
                }
            }
        }

        $alertas = Usuario::getAlertas();

        // Render a la vista
        $router->render('auth/olvide', [
            'titulo' => 'Reestablecer Contraseña',
            'alertas' => $alertas
        ]);
    }

    // <========= REESTABLECER =========> //
    public static function reestablecer(Router $router) {

        $token = s($_GET['token']);
        $mostrar = true;

        if(!$token) header('Location: /');

        // Identificar el usuario con este token
        $usuario = Usuario::where('token', $token);

        if(empty($usuario)) {
            Usuario::setAlerta('error', 'Token no válido');
            $mostrar = false;
        }

        if($_SERVER['REQUEST_METHOD'] === 'POST') {

            // Añadir la nueva contraseña
            $usuario->sincronizar($_POST);

            // Validar la contraseña
            $alertas = $usuario->validarPassword();

            if(empty($alertas)) {
                // Hashear la nueva contraseña
                $usuario->hashPassword();

                // Eliminar token
                $usuario->token = null;

                // Guardar usuario en la DB
                $resultado = $usuario->guardar();

                // Redireccionar
                if($resultado) header('Location: /');
            }
        }

        $alertas = Usuario::getAlertas();

        // Render a la vista
        $router->render('auth/reestablecer', [
            'titulo' => 'Reestablecer Contraseña',
            'alertas' => $alertas,
            'mostrar' => $mostrar
        ]);
    }

    // <========= MENSAJE =========> //
    public static function mensaje(Router $router) {
       
        // Render a la vista
        $router->render('auth/mensaje', [
            'titulo' => 'Cuenta Creada Exitosamente'
        ]);
    }

    // <========= MENSAJE =========> //
    public static function confirmar(Router $router) {
        
        $token = s($_GET['token']);

        if(!$token) header('Location: /');

        // Encontrar al usuario con este token
        $usuario = Usuario::where('token', $token);

        if(empty($usuario)) {
            // No se encontro ningun usuario con ese token
            Usuario::setAlerta('error', 'Token no válido');   
        } else {
            // Confirmar la cuenta
            $usuario->confirmado = 1;
            $usuario->token = null;
            unset($usuario->password2);
            
            // Guardar en la base de datos
            $usuario->guardar();

            Usuario::setAlerta('exito', 'Cuenta Comprobada Exitosamente');  
        }
        
        $alertas = Usuario::getAlertas();

        // Render a la vista
        $router->render('auth/confirmar', [
            'titulo' => 'Confirma tu cuenta',
            'alertas' => $alertas
        ]);
    }
}