<?php

class MissionController extends Controller
{
	public function indexAction()
	{
		//echo 'Esto es indexAction <br>';
		$username = $_SESSION['username'];
		$mission = new Mission($username);
		$_SESSION['missionArray'] = $mission->getAllMissions();

		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			if (isset($_POST['addmission'])) {
				//redirigir a formulario de newMission
				header('Location: newmission');
			} elseif (isset($_POST['logout'])) {
				//añadir boton de logout
			} elseif (isset($_POST['delete'])) {
				//recoger el nombre de la mision 
				$title = $_POST['missionName'];
				$mission->deleteMission($title);
				
			}
		}
	}

	public function newMissionAction()
	{
		echo 'Esto es newMissionAction';
		$username = $_SESSION['username'];
		$mission = new Mission($username);
		$_SESSION['missionArray'] = $mission->getAllMissions();

		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			if (isset($_POST['newMission']) && !empty($_POST['title']) && !empty($_POST['character']) && !empty($_POST['tag']) && !empty($_POST['end_date'])) {

				//el ususario quiere añadir una mision
				$title = $_POST['title'];
				$character = $_POST['character'];
				$tag = $_POST['tag'];
				$end_date = $_POST['end_date'];

				$mission->addMission($title, $character, $tag, $end_date);
				header('Location: mission');
			} elseif (isset($_POST['newMission']) && (!empty($_POST['title']) || !empty($_POST['character']) || !empty($_POST['tag']) || !empty($_POST['end_date']))) {
				$error = 'Make sure all the fields are filled in.';
				$_SESSION['error'] = $error;
			} elseif (isset($_POST['cancel'])) {

				//el ususario quiere cancelar la operación
				header('Location: mission');
			}
		}
	}
}
