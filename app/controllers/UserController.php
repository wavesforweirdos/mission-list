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
                    session_regenerate_id(true);

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

                // Registrarse no inicia sesión: hay que entrar con la contraseña en el login.
                if ($user->registerUser($username, $name, $lastname, $mail, $password)) {
                    header('Location: index');
                } else {
                    header('Location: signin');
                }
                exit;
            } elseif (isset($_POST['login'])) {
                //el ususario quiere acceder

                session_destroy();
                header('Location: index');
            }
        }
    }

    public function logoutAction()
    {
        //el ususario quiere desconectarse
        session_destroy();
        header('Location: index');
    }
}
