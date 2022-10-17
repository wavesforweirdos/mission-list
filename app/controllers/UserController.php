<?php
class UserController extends Controller
{
    public function indexAction()
    {
        //echo 'Esto es indexAction <br>';

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            if (isset($_POST['login'])) {
                $username = trim($_POST['username']);
                $password = trim($_POST['password']);

                $user = new User($username, $password);

                if (!isset($_SESSION)) {
                    session_start();
                }

                if ($user->loginUser($password)) {

                    $_SESSION['username'] = $user->getUsername();
                    $_SESSION['name'] = $user->getName();
                    $_SESSION['lastname'] = $user->getLastname();
                    $_SESSION['mail'] = $user->getMail();
                    $_SESSION['missions'] = $user->getMissions();

                    //redireccionar hacia otra pagina
                    header('Location: mission');
                } else {
                    // crear alert avisando que el usuario o la contraseña son incorrectos
                }
            } elseif (isset($_POST['signin'])) {
                //el ususario quiere registrarse
                session_destroy();
                header('Location: signin');
            }
        }
    }

    public function signinAction()
    {
        //echo 'Esto es signinAction <br>';

        if (!isset($_SESSION)) {
            session_start();
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            //el ususario quiere registrarse
            if (isset($_POST['signin'])) {
                $username = trim($_POST['username']);
                $name = trim($_POST['name']);
                $lastname = trim($_POST['lastname']);
                $mail = trim($_POST['email']);
                $password = trim($_POST['password']);

                $user = new User($username, $password);

                $user->registerUser($username, $name, $lastname, $mail, $password);

                if (isset($_SESSION)) {
                    $_SESSION['username'] = $user->getUsername();
                    $_SESSION['name'] = $user->getName();
                    $_SESSION['lastname'] = $user->getLastname();
                    $_SESSION['mail'] = $user->getMail();
                    $_SESSION['missions'] = $user->getMissions();

                    //redirigimos a la pagina de login para que inicie sesión (recordamos el username utilizado)
                    header('Location: index');
                }
            } elseif (isset($_POST['login'])) {
                //el ususario quiere acceder

                session_destroy();
                header('Location: index');
            }
        }
    }
}