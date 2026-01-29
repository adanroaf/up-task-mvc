<?php

namespace Controllers;

use MVC\Router;
use Model\Usuario;
use Model\Proyecto;

class DashboardController
{
    public static function index(Router $router) {
        iniciaSesion();
        isAuth();

        $id = $_SESSION['id'];
        $proyectos = Proyecto::belongsTo('propietarioId', $id);
        
        
        $router->render('dashboard/index', [
            'titulo' => 'Proyectos',
            'proyectos' => $proyectos
        ]);
    }

    public static function crear_proyecto(Router $router) {
        iniciaSesion();
        isAuth();

        $alertas = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $proyecto = new Proyecto($_POST);
            
            $alertas = $proyecto->validarProyecto();

            if (empty($alertas)) {
                //generar token unico
                $proyecto->url = md5(uniqid());
                //almacenar el creador del proyecto
                $proyecto->propietarioId = $_SESSION['id'];

                //guardar el proyecto
                $resultado = $proyecto->guardar();

                if ($resultado) {
                    header('Location: /proyecto?id=' . $proyecto->url);
                }
            }
            
        }

        $router->render('dashboard/crear-proyecto', [
            'titulo' => 'Crear Proyecto',
            'alertas' => $alertas
        ]);
    }

    public static function proyecto(Router $router) {
        iniciaSesion();
        isAuth();
        $alertas = [];

        //revisar que la persona que accede al proyecto es el dueño
        $token = $_GET['id'];

        if(!$token) header('Location: /dashboard');

        $proyecto = Proyecto::where('url', $token);

        if ($proyecto->propietarioId !== $_SESSION['id']) {
            header('Location: /dashboard');
        }

        $router->render('dashboard/proyecto', [
            'titulo' => $proyecto->proyecto,
            'alertas' => $alertas
        ]);
    }

    public static function perfil(Router $router) {
        iniciaSesion();
        isAuth();
        $alertas = [];

        $usuario = Usuario::find($_SESSION['id']);


        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $usuario->sincronizar($_POST);

            $alertas = $usuario->validar_perfil();

            if (empty($alertas)) {
                //verificar que el usuario no existe en la DB
                $existeUsuario = Usuario::where('email', $usuario->email);

                if ($existeUsuario  && $existeUsuario->id !== $usuario->id) {
                    //mostrar mensaje de error
                    Usuario::setAlerta('error', 'Correo ya registrado');

                    $alertas = $usuario->getAlertas();
                    
                }else{
                    //guardar usuario
                    $usuario->guardar();

                    Usuario::setAlerta('exito', 'Guardado Correctamente');
                    $alertas = $usuario->getAlertas();

                    //asignar el nombre nuevo al sidebar
                    $_SESSION['nombre'] = $usuario->nombre;
                }
            }
        }

        $router->render('dashboard/perfil', [
            'titulo' => 'Perfil',
            'usuario' => $usuario,
            'alertas' => $alertas
        ]);
    }

    public static function cambiar_password(Router $router) {
        iniciaSesion();
        isAuth();
        $alertas = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $usuario = Usuario::find($_SESSION['id']);

            $usuario->sincronizar($_POST);
            $alertas = $usuario->nuevo_password();

            if(empty($alertas)) {
                $resultado = $usuario->comprobar_password();
                if ($resultado) {
                    

                    $usuario->password = $usuario->password_nuevo;
                    unset($usuario->password_actual);
                    unset($usuario->password_nuevo);

                    $usuario->hashPassword();

                    $resultado = $usuario->guardar();

                    if($resultado) {
                        Usuario::setAlerta('exito', 'Contraseña Cambiada correctamente');
                        $alertas = $usuario->getAlertas();
                    }
                }else {
                    Usuario::setAlerta('error', 'Password Incorrecto');
                    $alertas = $usuario->getAlertas();
                }
            }

            //debuguear($usuario);
        }

        $router->render('dashboard/cambiar-password', [
            'titulo' => 'Cambiar Password',
            'alertas' => $alertas
        ]);
    }
}
