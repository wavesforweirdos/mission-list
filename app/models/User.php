<?php
class User extends Model
{

    public $id;
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
        $db = new Model();
        $db->_setTable('user');

        $this->usersArray = $db->fetchAll();
        $this->usersArray = json_decode(json_encode($this->usersArray), true);
    }

    //-----------------------GETTERS-----------------------
    public function getId()
    {
        foreach ($this->usersArray as $user) {
            if ($this->username === $user['username']) {
                $user_id = $user['id'];
                return $user_id;
            }
        }
    }
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
        $db = new Model();
        $db->_setTable('user');

        if (!$this->usersArray) {
            //si no existe un array con los usuarios, la crea

            $this->usersArray = $db->fetchAll();
            $this->usersArray = json_decode(json_encode($this->usersArray), true);
        }

        if (!$this->CheckUser()) {
            //existe el array de usuarios, pero el username no existe
            $id = count($this->usersArray);
            $newUser = [
                $id,
                $username,
                $name,
                $lastname,
                $mail,
                password_hash($password, PASSWORD_DEFAULT)
            ];

            $db->save($newUser);
        } else {
            //error: el usuario ya existe
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
