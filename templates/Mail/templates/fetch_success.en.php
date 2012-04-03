<?php

return array(
	'title' => 'Task finish',
	'text' => '
		Task '.$this->jobid.' finished with status '.Lng::get()->getLngSnippet($this->lng, $this->task_status).'.
		Task link: '.$this->task_href.'.
	',
);

?>