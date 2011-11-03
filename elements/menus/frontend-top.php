<?php

$r = Request::get();
$user = CurUser::get();

return array (

	'name' => 'frontend-top',
	
	'items' => array(
	
		array(
			'title' => 'top-menu.main',
			'href' => href(''),
			'active' => $r->getParts(array(0)) == '',
		),
	
		array(
			'title' => 'top-menu.projects',
			'href' => href('project/list'),
			'active' => $r->getParts(array(0, 1)) == 'project/list',
		),
	
		array(
			'title' => 'top-menu.tasks',
			'href' => href('task-set'),
			'active' => $r->getParts(array(0)) == 'task-set',
		),
	
		array(
			'title' => 'top-menu.analyze',
			'href' => href('task-submit/analyze'),
			'active' => $r->getParts(array(0, 1)) == 'task-submit/analyze',
		),
	
		array(
			'title' => 'top-menu.results',
			'href' => href('task-submit/statistics'),
			'active' => $r->getParts(array(0, 1)) == 'task-submit/statistics',
		),
	
	)
);

?>