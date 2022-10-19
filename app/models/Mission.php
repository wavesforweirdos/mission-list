<?php
class Mission extends Model
{
    /*
    private $id;
    private $title;
    private $champ;
    private $tag;
    private $end_date;
    private $status;
    private $starred;
    private $date_record;
    */
    private $user_id;
    private $missionArray;

    public function __construct($id)
    {
        $this->user_id = $id;

        $db = new Model();
        $db->_setTable('mission');

        $this->missionArray = $db->fetchSome($this->user_id);
        $this->missionArray = json_decode(json_encode($this->missionArray), true);

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

    public function getMission($id)
    {
        $missionFile = $this->missionArray;

        if (is_array($missionFile)) {

            foreach ($missionFile as $key => $val) {

                if ($val['id'] == $id) {
                    return $missionFile[$key];
                }
            }
        }
    }

    public function getLastId()
    {
        $db = new Model();
        $db->_setTable('mission');

        $lastId = count($db->fetchAll());
        return $lastId;
    }

    //añadir mission
    public function addMission($id, $title, $champ, $tag, $end_date, $user_id)
    {
        $db = new Model();
        $db->_setTable('mission');

        if (!$this->missionArray) {
            //si no existe un array de misiones, lo crea

            $this->missionArray = $db->fetchSome($this->user_id);
            $this->missionArray = json_decode(json_encode($this->missionArray), true);
        }

        if (!$this->CheckMission()) {
            //existe el array de missiones, pero el nombre de la misión no existe
            $user_id = $this->user_id;
            $newMission = [
                $id,
                $title,
                $champ,
                $tag,
                $end_date,
                1, //pending
                0, //no starred
                date("Y-m-d"),
                $user_id
            ];
            $db->save($newMission);
        } else {
            //error: el nombre de la misión ya existe
        }
    }

    public function deleteMission($id)
    {
        $db = new Model();
        $db->_setTable('mission');

        $missionFile = $this->missionArray;
        if (is_array($missionFile)) {
            foreach ($missionFile as $mission) {
                if ($mission['id'] == $id) {
                    if ($mission['status'] != 3) {
                        //cambiamos el status a 'deleted' == 3
                        $status = 3;
                    } else { //sino lo devolveremos por defecto a pending
                        $status = 1;
                    }
                    $update = [
                        'id' => $mission['id'],
                        'status' => $status
                    ];
                }
            };
        }
        $db->save($update);
    }

    public function completeMission($id)
    {
        $db = new Model();
        $db->_setTable('mission');

        $missionFile = $this->missionArray;
        if (is_array($missionFile)) {
            foreach ($missionFile as $mission) {
                if ($mission['id'] == $id) {
                    if ($mission['status'] != 2) {
                        //cambiamos el status a 'completed' == 2
                        $status = 2;
                    } else { //sino lo devolveremos por defecto a pending
                        $status = 1;
                    }
                    $update = [
                        'id' => $mission['id'],
                        'status' => $status,
                        'end_date' => date("Y-m-d")
                    ];
                }
            };
        }
        $db->save($update);
    }

    public function editMission($id, $title, $champ, $tag, $end_date, $status)
    {
        $db = new Model();
        $db->_setTable('mission');

        $missionFile = $this->missionArray;

        if (is_array($missionFile)) {
            foreach ($missionFile as $mission) {

                if ($mission['id'] == $id) {
                    $update = [
                        'id' => $mission['id'],
                        'title' => $title,
                        'champ' => $champ,
                        'tag' => $tag,
                        'status' => $status,
                        'end_date' => $end_date,
                    ];
                    echo '<pre>';
                    print_r($update);
                    echo '</pre>';
                }
            };
        }
        $db->save($update);
    }

    public function starredMission($id)
    {
        $db = new Model();
        $db->_setTable('mission');

        $missionFile = $this->missionArray;
        if (is_array($missionFile)) {
            foreach ($missionFile as $mission) {
                if ($mission['id'] == $id) {
                    if ($mission['starred'] == 0) {
                        //cambiamos starred al valor contrario 
                        $starred = 1;
                    } else { //sino lo devolveremos por defecto a pending
                        $starred = 0;
                    }
                    $update = [
                        'id' => $mission['id'],
                        'starred' => $starred,
                    ];
                }
            };
        }
        $db->save($update);
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

    public function deleteData($user_id)
    {
        $db = new Model();
        $db->_setTable('mission');
        $db->delete($user_id);
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
