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
    private $deletedMissionArray;
    private $USER_DB_PATCH;


    public function __construct($user)
    {
        $this->username = $user;
        $this->USER_DB_PATCH = CONFIG_PATH . '/database/' . $this->username . '/';
        if (!file_exists($this->USER_DB_PATCH)) {
            $missionFile = json_decode(file_put_contents($this->USER_DB_PATCH . $this->username . '-missions.json', '[]'));
        } else {
            $missionFile = json_decode(file_get_contents($this->USER_DB_PATCH . $this->username . '-missions.json'), true);
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
            $this->missionArray = file_put_contents($this->USER_DB_PATCH . $this->username . '-missions.json', '
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
                file_put_contents($this->USER_DB_PATCH . $this->username . '-missions.json', $json);
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
                    unset($missionFile[$key]);
                    // $deletedMission = $missionFile[$key];
                    // $this->deleteMissionArray = json_decode(file_put_contents($this->USER_DB_PATCH . '-deleted-missions.json', $deletedMission));
                }
            }

            $missionFile = json_encode($missionFile, JSON_PRETTY_PRINT);
            file_put_contents($this->USER_DB_PATCH . $this->username . '-missions.json', true);
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
