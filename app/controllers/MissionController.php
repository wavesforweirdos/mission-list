<?php

class MissionController extends Controller
{
	public function indexAction()
	{
		$this->view->message = "hello from mission::index";
	}
	
	public function checkAction()
	{
		echo "hello from mission::check";
	}
}
 