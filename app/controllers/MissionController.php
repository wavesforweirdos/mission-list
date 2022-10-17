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
				//añadir mission
				header('Location: newmission');
			} elseif (isset($_POST['delete'])) {
				//eliminar misión
				$title = $_POST['missionName']; //recoger el nombre de la mision desde el input hidden
				$mission->deleteMission($title);
				header('Location: mission');
			} elseif (isset($_POST['complete'])) {
				//misión completada
				$title = $_POST['missionName'];
				$mission->completeMission($title);
				header('Location: mission');
			} elseif (isset($_POST['edit'])) {
				//editar misión
				$title = $_POST['missionName'];
				$_SESSION['title'] = $title;
				header('Location: editmission');
			} elseif (isset($_POST['starred'])) {
				//destacar o no la misión
				$title = $_POST['missionName'];
				$mission->starredMission($title);
				header('Location: mission');
			} elseif (isset($_POST['pendingFilter'])) {
				//filtrar misiones pendientes
				$status = 1;
				$_SESSION['missionArray'] = $mission->filterMission($status);
			} elseif (isset($_POST['starredFilter'])) {
				//filtrar misiones destacadas
				$starred = 'starred';
				$_SESSION['missionArray'] = $mission->filterMission($starred);
			} elseif (isset($_POST['completedFilter'])) {
				//filtrar misiones completadas
				$status = 2;
				$_SESSION['missionArray'] = $mission->filterMission($status);
			} elseif (isset($_POST['trashedFilter'])) {
				//filtrar misiones eliminadas
				$status = 3;
				$_SESSION['missionArray'] = $mission->filterMission($status);
			} elseif (isset($_POST['searchMission'])) {
				//filtrar misiones por nombre
				$title = 'title';
				$query = $_POST['searchName'];
				$_SESSION['searchName'] = $query;
				$_SESSION['missionArray'] = $mission->filterMission($title);
			} elseif (isset($_POST['deleteAll'])) {
				//eliminar todas las misiones de la BD
				$data = '[]';
				$mission->setAllMissions($data);
				header('Location: mission');
			}
		}
	}

	public function newMissionAction()
	{
		//echo 'Esto es newMissionAction';
		$username = $_SESSION['username'];
		$missions = new Mission($username);
		$_SESSION['missionArray'] = $missions->getAllMissions();

		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			if (isset($_POST['newMission']) && !empty($_POST['title']) && !empty($_POST['character']) && !empty($_POST['tag']) && !empty($_POST['end_date'])) {
				//el ususario quiere añadir una mision y todos los campos han sido añadidos
				$title = $_POST['title'];
				$character = $_POST['character'];
				$tag = $_POST['tag'];
				$end_date = $_POST['end_date'];

				$missions->addMission($title, $character, $tag, $end_date);
				header('Location: mission');
			} elseif (isset($_POST['newMission']) && (empty($_POST['title']) || empty($_POST['character']) || empty($_POST['tag']) || empty($_POST['end_date']))) {
				//alguno de los campos no ha sido enviado
				$error = 'Make sure all the fields are filled in.';
				$_SESSION['error'] = $error;
			} elseif (isset($_POST['cancel'])) {
				//el ususario quiere cancelar la operación
				header('Location: mission');
			}
		}
	}

	public function editMissionAction()
	{
		//echo 'Esto es editMissionAction';
		$username = $_SESSION['username'];
		$missions = new Mission($username);

		$title = $_SESSION['title'];
		$_SESSION['mission'] = $missions->getMission($title);
		$mission = $_SESSION['mission'];

		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			if (isset($_POST['editMission'])) {
				//el ususario quiere cambiar el valor de alguno de los inputs
				$modified_title = $_POST['title'];
				$character = $_POST['character'];
				$tag = $_POST['tag'];
				$end_date = $_POST['end_date'];
				$status = $_POST['status'];

				$missions->editMission($title, $modified_title, $character, $tag, $end_date, $status);
				header('Location: mission');
			} elseif (isset($_POST['editMission']) && (empty($_POST['title']) || empty($_POST['character']) || empty($_POST['tag']) || empty($_POST['end_date']))) {
				//alguno de los campos no ha sido enviado
				$error = 'Make sure all the fields are filled in.';
				$_SESSION['error'] = $error;
			} elseif (isset($_POST['cancel'])) {
				//el ususario quiere cancelar la operación
				header('Location: mission');
			}
		}
	}

}
