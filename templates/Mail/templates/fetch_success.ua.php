<?php

return array(
	'title' => 'Задача успешно выполнена',
	'text' => '
		Задача '.$this->jobid.' выполнена со статусом '.Lng::get()->getLngSnippet($this->lng, $this->task_status).'.
		Ссылка на задачу: '.$this->task_href.'.
	',
);

?>