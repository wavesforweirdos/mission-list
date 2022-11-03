<?php
class User
{

    public $username;
    public $name;
    public $lastname;
    private $mail;
    private $pwd_hashed;

    private $usersArray;

    public function __construct($user, $password)
    {
        $this->username = $user;
        $this->pwd_hashed = password_hash($password, PASSWORD_DEFAULT);
        //devuelve una contraseña encriptada o hash de contraseña tambien podemos usar md5($password) pero no es específico para contraseñas y existen webs que descifran fácilmente las contraseñas md5

        if (!file_exists(CONFIG_PATH . '/database/users.json')) {
            $jsonFile = json_decode(file_put_contents(CONFIG_PATH . '/database/users.json', '[]'));
            // file_put_contents crea, en el caso de no existir el fichero, y añade el contenido que se le indique
        } else {
            $jsonFile = json_decode(file_get_contents(CONFIG_PATH . '/database/users.json'), true);
            // file_get_contents transmite un fichero completo a un string
            // json_decode decodifica un string de JSON en un array
        }
        $this->usersArray = $jsonFile;
    }


    //-----------------------GETTERS-----------------------
    public function getUsername()
    {
        return $this->username;
    }
    public function getName()
    {
        return $this->name;
    }
    public function getLastname()
    {
        return $this->lastname;
    }
    public function getMail()
    {
        return $this->mail;
    }
    public function getMissions()
    {
        return $this->missions;
    }
    public function getUserArray()
    {
        return $this->usersArray;
    }




    //acceso a usuarios
    public function loginUser($password)
    {
        if (!($this->CheckUser())) {
            //echo 'El usuario no existe';
        } else {
            foreach ($this->usersArray as $user) {
                // echo 'El nombre de usuario es <b>' . $user['username'] . '</b> y la contraseña es <b>' . $user['password']. '</b>.<br>';
                if (($this->username == $user['username']) && (password_verify($password, $user['password']))) {
                    //password_verify — Comprueba que la contraseña coincida con un hash(en este caso el hash es la constraseña almacenada en la base de datos)
                    //echo $user['username'] .' y '. $user['password'];
                    return true;
                } else {
                    // $error = 'El usuario existe pero la contraseña es incorrecta.';
                }
            }
        }
    }

    //registrar usuario
    public function registerUser($username, $name, $lastname, $mail, $password)
    {

        if (!$this->usersArray) {
            //si no existe ninguna base de datos, la crea y añade el usuario
            $this->usersArray = file_put_contents(CONFIG_PATH . '/database/users.json', '
            { "username":"' . $username . '",
            "name": "' . $name . '",
            "lastname": "' . $lastname . '",
            "mail": "' . $mail . '",
            "password": "' . password_hash($password, PASSWORD_DEFAULT) . '"
            }');
        } else {
            if (!$this->CheckUser()) {
                //si existe la base de datos, pero el username no existe

                $newUser = [
                    'username' => $username,
                    'name' => $name,
                    'lastname' => $lastname,
                    'mail' => $mail,
                    'password' => password_hash($password, PASSWORD_DEFAULT)
                ];

                $this->usersArray[] = $newUser;
                $json = json_encode($this->usersArray, JSON_PRETTY_PRINT);
                file_put_contents(CONFIG_PATH . '/database/users.json', $json);

                json_decode(file_put_contents(CONFIG_PATH . '/database/' . $this->username . '-missions.json', '[]'));
            }
        }
    }

    //desconectarse
    public function logOut()
    {
        if (isset($_POST['logout'])) {
            session_destroy();
        }
    }

    //comprobar si el usuario existe
    public function CheckUser()
    {
        if (!$this->usersArray) {
            return false;
        } else {
            foreach ($this->usersArray as $user) {
                //echo 'El nombre de usuario es:' . $user['username'];
                if ($this->username === $user['username']) {
                    return true;
                }
            }
        }
    }
}