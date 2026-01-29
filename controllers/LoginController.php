<?php

namespace Controllers;

use Classes\Email;
use Model\Usuario;
use MVC\Router;

class LoginController 
{
    public static function login(Router $router) {
        $alertas = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $usuario = new Usuario($_POST);

            $alertas = $usuario->validarLogin();

            if (empty($alertas)) {
                //validar que el usuario existe en DB
                $usuario = Usuario::where('email', $usuario->email);
                
                
                if (!$usuario || !$usuario->confirmado) {
                    Usuario::setAlerta('error', 'Correo inválido o pendiente de confirmación');
                }else{
                    //validar passsword
                    if (password_verify($_POST['password'], $usuario->password)) {
                        session_start();
                        $_SESSION['id'] = $usuario->id;
                        $_SESSION['nombre'] = $usuario->nombre;
                        $_SESSION['email'] = $usuario->email;
                        $_SESSION['login'] = true;

                        //redireccionamos a proyectos
                        header('Location: /dashboard');

                    }else{
                        Usuario::setAlerta('error', 'Contraseña incorrecta');
                    }
                }
            }
        }


        $alertas = Usuario::getAlertas();
        $router->render('auth/login',[
            'titulo' => 'Iniciar Sesion',
            'alertas' => $alertas
        ]);
    }

    public static function logout() {
        iniciaSesion();
        $_SESSION = [];
        header('Location: /');

    }

    public static function crear(Router $router) {
        $alertas = [];
        $usuario = new Usuario;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $usuario->sincronizar($_POST);

            $alertas = $usuario->validarNuevaCuenta();

            $existeUsuario = Usuario::where('email', $usuario->email);

            if(empty($alertas)) {
                if($existeUsuario) {
                    Usuario::setAlerta('error', 'El usuario ya existe');
                    $alertas = Usuario::getAlertas();
                }else{
                    //hash password
                    $usuario->hashPassword();

                    //eliminar password2
                    unset($usuario->password2);

                    //generar un token
                    $usuario->crearToken();

                    //enviar email

                    $email = new Email($usuario->email, $usuario->nombre, $usuario->token);
                    $email->enviarConfirmacion();
                    
                    //guarar usuario y redirigir
                    $resultado = $usuario->guardar();
                    if ($resultado) {
                        header('Location: /mensaje');
                    }
                }
            }


        }

        $router->render('auth/crear',[
            'titulo' => 'Crea tu Cuenta en UpTask',
            'usuario' => $usuario,
            'alertas' => $alertas
            
        ]);
    }

    public static function olvide(Router $router) {
        $alertas = [];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $usuario = new Usuario($_POST);
            $alertas = $usuario->validarEmail();

            if (empty($alertas)) {
                //buscar el usuario
                $usuario = Usuario::where('email', $usuario->email);

                if ($usuario && $usuario->confirmado) {
                    //generar un nuevo token
                    $usuario->crearToken();
                    unset($usuario->password2);

                    //actualizar el usuario
                    $usuario->guardar();


                    //enviar el email
                    $email = new Email($usuario->email, $usuario->nombre, $usuario->token);
                    $email->enviarInstrucciones();


                    //imprimir la alerta
                    Usuario::setAlerta('exito', 'Hemos enviado las instrucciones a tu email');

                }else {
                    Usuario::setAlerta('error', "El usuario no existe o no esta confirmado");
                    
                }
            }
        }
        $alertas = Usuario::getAlertas();

        $router->render('auth/olvide', [
            'titulo' => 'Olvide mi Password',
            'alertas' => $alertas
        ]);
    }

    public static function reestablecer(Router $router) {
        $token = s($_GET['token']);
        $mostrar = true;
        
        if (!$token) header('Location: /');

        //buscamos el usuario por el token
        $usuario = Usuario::where('token', $token);
        unset($usuario->password2);

        if (empty($usuario)) {
            Usuario::setAlerta('error', 'Token no válido');
            $mostrar = false;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            //recuperar datos de memoria del usuario
            $usuario->sincronizar($_POST);
            
            //validar nuevo password
            $alertas = $usuario->validarPassword();

            if (empty($alertas)) {
                $usuario->hashPassword();
                $usuario->token = null;
                $resultado = $usuario->guardar();
                if ($resultado) {
                    header('Location: /');
                    exit;
                }
            }
        }

        $alertas = Usuario::getAlertas();
        $router->render('auth/reestablecer', [
            'titulo' => 'Reestablecer contraseña',
            'alertas' => $alertas,
            'mostrar' => $mostrar
        ]);
    }

    public static function mensaje(Router $router) {
        
        $router->render('auth/mensaje', [
            'titulo' => 'Cuenta creada exitosamente'
        ]);
    }

    public static function confirmar(Router $router) {
        $token = s($_GET['token']);

       if(!$token) {
            header('Location: /');
       };
        //encontrar al usuario
        $usuario = Usuario::where('token', $token);

        if (empty($usuario)) {
            Usuario::setAlerta('error', 'Token no Válido');
        }else{
            //encontro el usuario
            $usuario->confirmado = 1;
            $usuario->token = null;
            unset($usuario->password2);

            $usuario->guardar();

            Usuario::setAlerta('exito', 'Cuenta Confirmada');
        }
        

        $alertas = Usuario::getAlertas();

        $router->render('auth/confirmar', [
            'titulo' => 'Confirma tu cuenta UpTask',
            'alertas' => $alertas
        ]);
    }
}
