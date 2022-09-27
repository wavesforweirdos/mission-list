<?php
class Mission
{
    private $username;
    private $title;
    private $character;
    private $tag;
    private $end_date;
    private $status;
    private $starred;
    private $date_record;

    private $missionArray;
    private $deletedMissionArray;


    public function __construct($user)
    {
        $this->username = $user;

        if (!file_exists(CONFIG_PATH . '/database/' . $this->username . '/' . $this->username . '-missions.json')) {
            $missionFile = json_decode(file_put_contents(CONFIG_PATH . '/database/' . $this->username . '/' . $this->username . '-missions.json', '[]'));
        } else {
            $missionFile = json_decode(file_get_contents(CONFIG_PATH . '/database/' . $this->username . '/' . $this->username . '-missions.json'), true);
        }
        $this->missionArray = $missionFile;
    }

    //-----------------------GETTERS-----------------------

    public function getAllMissions()
    {
        return $this->missionArray;
    }

    //añadir mission
    public function addMission($title, $character, $tag, $end_date)
    {
        if (!$this->missionArray) {
            //si no existe un array de misiones, lo crea y añade el usuario
            $this->missionArray = file_put_contents(CONFIG_PATH . '/database/' . $this->username . '/' . $this->username . '-missions.json', '
            { "title":"' . $title . '",
            "character": "' . $character . '",
            "tag": "' . $tag . '",
            "end_date": "' . $end_date . '",
            "status": "pending",
            "starred": "0",
            "date_record": "' . date("Y-m-d H:i:s") . '"
            }');
        } else {
            if (!$this->CheckMission()) {
                //si existe la base de datos y el nombre de la mision no existe
                $newMission = [
                    'title' => $title,
                    'character' => $character,
                    'tag' => $tag,
                    'end_date' => $end_date,
                    'status' => 'pending',
                    'starred' => '0',
                    'date_record' => date("Y-m-d H:i:s")
                ];

                $this->missionArray[] = $newMission;
                $json = json_encode($this->missionArray, JSON_PRETTY_PRINT);
                file_put_contents(CONFIG_PATH . '/database/' . $this->username . '/' . $this->username . '-missions.json', $json, FILE_APPEND | LOCK_EX);
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

                    //añadimos las tareas eliminadas en un archivo aparte
                    $this->deletedMissionArray[] = $missionFile[$key];
                    $deletedJson = json_encode($this->deletedMissionArray, JSON_PRETTY_PRINT);
                    file_put_contents(CONFIG_PATH . 'database/' . $this->username . '/' . $this->username . '-deleted-missions.json', $deletedJson, FILE_APPEND | LOCK_EX);

                    //eliminamos la variable del array inicial
                    unset($missionFile[$key]);
                }
            }
            $missionFile = json_encode($missionFile, JSON_PRETTY_PRINT);
            file_put_contents(CONFIG_PATH . '/database/' . $this->username . '/' . $this->username . '-missions.json', $missionFile);
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
