<?

session_start();
require_once('setup.php');

?>
<html>
<head>
	<meta http-equiv="Content-type" content="text/html; charset=UTF-8" />
	<title>Структура таблицы БД</title>
	<link rel="stylesheet" type="text/css" href="/data/style.css" />
	<style>
	.menu a{
		font-size: 13px;
		color: blue;
		font-weight: bold;
		text-decoration: none;
	}
	.menu a:hover{
		text-decoration: underline;
	}
	.menu a.active{
		text-decoration: underline;
	}
	</style>
</head>
<body>

<?

require_once('data/actions.php');

$action = isset($_GET['action']) ? $_GET['action'] : 'db-parse-create';

?>


<h2 align="center">Структура таблицы БД</h2>

<div class="menu" align="center">
	<a <?=$action == 'db-parse-create' ? 'class="active"' : '';?> href="?action=db-parse-create">Парсинг CREATE TABLE строки</a> |
	<a <?=$action == 'db-eval-describe' ? 'class="active"' : '';?> href="?action=db-eval-describe">Парсинг DESCRIBE массива</a>
</div>
<br />

<? if($action =='db-parse-create'){ ?>

	<form action="" method="post">
		<input type="hidden" name="action" value="db-parse-create" />
		Строка CREATE TABLE<br />
		<textarea name="create-table-str" style="width: 100%; height: 350px;"></textarea><br />
		<input type="submit" name="" value="Обработать" />
		
	</form>
	
<? }elseif($action == 'db-eval-describe'){ ?>

	<form action="" method="post">
		<input type="hidden" name="action" value="db-eval-describe" />
		Валидный PHP массив, полученный от DESCRIBE<br />
		<textarea name="describe-str" style="width: 100%; height: 350px;"><? var_export(getVar(Storage::get()->data['tableStruct']));?></textarea><br />
		<input type="submit" name="" value="Обработать" />
		
	</form>
	
<? } ?>

</body>
</html>
