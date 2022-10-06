<?php

class MissionController extends Controller
{
	public function indexAction()
	{
		//echo 'Esto es indexAction <br>';
		$user_id = $_SESSION['idUser'];
		$mission = new Mission($user_id);
		$_SESSION['missionArray'] = $mission->getAllMissions();

		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			if (isset($_POST['addmission'])) {
				//añadir mission
				header('Location: newmission');
			} elseif (isset($_POST['delete'])) {
				//eliminar misión
				$id = $_POST['missionId']; //recoger el nombre de la mision desde el input hidden
				$mission->deleteMission($id);
				header('Location: mission');
			} elseif (isset($_POST['complete'])) {
				//misión completada
				$id = $_POST['missionId'];
				$mission->completeMission($id);
				header('Location: mission');
			} elseif (isset($_POST['edit'])) {
				//editar misión
				$id = $_POST['missionId'];
				$_SESSION['id'] = $id;
				header('Location: editmission');
			} elseif (isset($_POST['starred'])) {
				//destacar o no la misión
				$id = $_POST['missionId'];
				$mission->starredMission($id);
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
				$mission->deleteData($user_id);
				header('Location: mission');
			}
		}
	}

	public function newMissionAction()
	{
		//echo 'Esto es newMissionAction';
		$user_id = $_SESSION['idUser'];

		$missions = new Mission($user_id);
		$_SESSION['missionArray'] = $missions->getAllMissions();

		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			if (isset($_POST['newMission']) && !empty($_POST['title']) && !empty($_POST['champ']) && !empty($_POST['tag']) && !empty($_POST['end_date'])) {
				//el ususario quiere añadir una mision y todos los campos han sido añadidos
				$id = $missions->getLastId();
				$title = $_POST['title'];
				$champ = $_POST['champ'];
				$tag = $_POST['tag'];
				$end_date = $_POST['end_date'];

				$missions->addMission($id, $title, $champ, $tag, $end_date, $user_id);
				header('Location: mission');
			} elseif (isset($_POST['newMission']) && (empty($_POST['title']) || empty($_POST['champ']) || empty($_POST['tag']) || empty($_POST['end_date']))) {
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
		$user_id = $_SESSION['idUser'];
		$id = $_SESSION['id'];

		$mission = new Mission($user_id);
		$_SESSION['mission'] = $mission->getMission($id);

		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			if (isset($_POST['editMission'])) {
				//el ususario quiere cambiar el valor de alguno de los inputs

				$modified_title = $_POST['title'];
				$modified_champ = $_POST['champ'];
				$modified_tag = $_POST['tag'];
				$modified_end_date = $_POST['end_date'];
				$modified_status = $_POST['status'];

				$mission->editMission($id, $modified_title, $modified_champ, $modified_tag, $modified_end_date, $modified_status);
				header('Location: mission');
			} elseif (isset($_POST['editMission']) && (empty($_POST['title']) || empty($_POST['champ']) || empty($_POST['tag']) || empty($_POST['end_date']))) {
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
