<?php 

//Para definir las rutas del sistema tenemos que hacer coincidir la URL
//y controlador#action-to-call method. E.g.:

/* 
 * '/' => 'index#index',
 * '/calendar' => 'calendar#index'
 */
$routes = array(
	'/test' => 'test#index',
	'/check' => 'test#check',
	
	'/' => 'user#index',
	'/index' => 'user#index',
	'/user' => 'user#index',
	'/login' => 'user#index',
	'/signin' => 'user#signin',
	
	'/mission' => 'mission#index',
	'/newmission' => 'mission#newmission',
	'/editmission' => 'mission#editmission',

	'/logout' => 'user#logout'
);

?>