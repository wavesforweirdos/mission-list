<?php
class Mission
{
    private $username;
    /*
    private $title;
    private $character;
    private $tag;
    private $end_date;
    private $status;
    private $starred;
    private $date_record;
    */
    private $missionArray;

    public function __construct($user)
    {
        $this->username = $user;

        if (!file_exists(CONFIG_PATH . '/database/' . $this->username . '-missions.json')) {
            $missionFile = json_decode(file_put_contents(CONFIG_PATH . '/database/' . $this->username . '-missions.json', '[]'));
        } else {
            $missionFile = json_decode(file_get_contents(CONFIG_PATH . '/database/' . $this->username . '-missions.json'), true);
        }

        $this->missionArray = $missionFile;

        //ordenar el array por el status (1.pending - 2.completed - 3.deleted)
        $keys = array_column($this->missionArray, 'status');
        array_multisort($keys, SORT_ASC, $this->missionArray);

        // echo '<pre>';
        // print_r($this->missionArray);
        // echo '</pre>';
    }

    //-----------------------GETTERS-----------------------

    public function getAllMissions()
    {
        return $this->missionArray;
    }

    public function getMission($title)
    {
        $missionFile = $this->missionArray;

        if (is_array($missionFile)) {

            foreach ($missionFile as $key => $val) {

                if ($val['title'] === $title) {
                    return $missionFile[$key];
                }
            }
        }
    }

    //-----------------------SETTERS-----------------------
    public function setAllMissions($data)
    {
        $this->missionArray = json_decode(file_put_contents(CONFIG_PATH . '/database/' . $this->username . '-missions.json', $data));

        return $this->missionArray;
    }

    //añadir mission
    public function addMission($title, $character, $tag, $end_date)
    {
        if (!$this->missionArray) {
            //si no existe un array de misiones, lo crea y añade el usuario
            $this->missionArray = file_put_contents(CONFIG_PATH . '/database/' . $this->username . '-missions.json', '
            { "title":"' . $title . '",
            "character": "' . $character . '",
            "tag": "' . $tag . '",
            "end_date": "' . $end_date . '",
            "status": 1, 
            "starred": 0,
            "date_record": "' . date("Y-m-d") . '"
            }');
        } else {
            if (!$this->CheckMission()) {
                //si existe la base de datos y el nombre de la mision no existe
                $newMission = [
                    'title' => $title,
                    'character' => $character,
                    'tag' => $tag,
                    'end_date' => $end_date,
                    'status' => 1, //pending
                    'starred' => 0, //no starred
                    'date_record' => date("Y-m-d")
                ]; //lanzar error si el nombre de la misión ya existe

                $this->missionArray[] = $newMission;
                $json = json_encode($this->missionArray, JSON_PRETTY_PRINT);
                file_put_contents(CONFIG_PATH . '/database/' . $this->username . '-missions.json', $json);
            }
        }
    }

    public function deleteMission($title)
    {
        $missionFile = $this->missionArray;

        if (is_array($missionFile)) {
            foreach ($missionFile as $key => $val) {
                //echo $val['title'];
                if ($val['title'] === $title) {

                    //cambiamos el statos a 'deleted' == 3
                    $missionFile[$key]['status'] = 3;

                    if ($val['status'] != 3) {
                        $missionFile[$key]['status'] = 3;
                    } else { //sino lo devolveremos por defecto a pending
                        $missionFile[$key]['status'] = 1;
                    }
                }
            }

            $missionFile = json_encode($missionFile, JSON_PRETTY_PRINT);
            file_put_contents(CONFIG_PATH . '/database/' . $this->username . '-missions.json', $missionFile);
        }
    }

    public function completeMission($title)
    {
        $missionFile = $this->missionArray;

        if (is_array($missionFile)) {
            foreach ($missionFile as $key => $val) {

                if ($val['title'] === $title) {

                    if ($val['status'] != 2) {
                        $missionFile[$key]['status'] = 2;
                        $missionFile[$key]['end_date'] = date("Y-m-d");
                    } else { //sino lo devolveremos por defecto a pending
                        $missionFile[$key]['status'] = 1;
                    }
                }
            }

            $missionFile = json_encode($missionFile, JSON_PRETTY_PRINT);
            file_put_contents(CONFIG_PATH . '/database/' . $this->username . '-missions.json', $missionFile);
        }
    }

    public function editMission($title, $modified_title, $character, $tag, $end_date, $status)
    {

        $missionFile = $this->missionArray;

        if (is_array($missionFile)) {
            foreach ($missionFile as $key => $val) {

                if ($val['title'] === $title) {

                    //cambiamos los valores por los especificados en los inputs del formulario
                    $missionFile[$key]['title'] = $modified_title;
                    $missionFile[$key]['character'] = $character;
                    $missionFile[$key]['tag'] = $tag;
                    $missionFile[$key]['end_date'] = $end_date;
                    $missionFile[$key]['status'] = $status;
                }
            }

            $missionFile = json_encode($missionFile, JSON_PRETTY_PRINT);
            file_put_contents(CONFIG_PATH . '/database/' . $this->username . '-missions.json', $missionFile);
        }
    }

    public function starredMission($title)
    {
        $missionFile = $this->missionArray;

        if (is_array($missionFile)) {
            foreach ($missionFile as $key => $val) {

                if ($val['title'] === $title) {

                    //cambiamos starred al valor contrario 
                    if ($val['starred'] == 0) {
                        $missionFile[$key]['starred'] = 1;
                    } else {
                        $missionFile[$key]['starred'] = 0;
                    }
                }
            }

            $missionFile = json_encode($missionFile, JSON_PRETTY_PRINT);
            file_put_contents(CONFIG_PATH . '/database/' . $this->username . '-missions.json', $missionFile);
        }
    }

    public function filterMission($filter)
    {
        $missionFile = $this->missionArray;

        //array_filter — Filtra elementos de un array usando una función de devolución de llamada (array, funcion, filtro)

        if (is_array($missionFile)) {
            if ($filter === 'starred') {
                $query = 1;
                $arrayFiltered = array_filter($missionFile, function ($var) use ($query) {
                    return ($var['starred'] == $query);
                });
            } elseif ($filter === 'title') {
                $arrayFiltered = [];
                $query = strtolower($_SESSION['searchName']);

                foreach ($missionFile as $key => $val) {
                    // echo $val['title'];

                    // echo '<pre>';
                    // print_r($missionFile[$key]);
                    // echo '</pre>';

                    if (strpos(strtolower($val['title']), $query) !== false) {
                        array_push($arrayFiltered, $missionFile[$key]);
                    }
                }
                return $arrayFiltered;
            } else {
                $query = $filter;
                $arrayFiltered = array_filter($missionFile, function ($var) use ($query) {
                    return ($var['status'] == $query);
                });
            }
            return $this->missionArray = $arrayFiltered;
        }
    }


    public function CheckMission()
    {

        if (!$this->missionArray) {
            return false;
        } else {
            foreach ($this->missionArray as $mission) {
                //echo 'El nombre de la misión es:' . $mission['title'];
                if ($this->missionArray === $mission['title']) {
                    return true;
                }
            }
        }
    }
}
