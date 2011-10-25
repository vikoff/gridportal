<?

// $str = 'gsiftp://arc.univ.kiev.ua:21/job/172013174747531266208357 asdfa sdf';
$str = 'gsiftp://arc.univ.kiev.ua:21/job/44301317392049336680053/CrimeaEco.err asdf asdfa';

echo preg_match('/(gsiftp:\/\/\S+\d+)/', $str, $matches).'<br />';

echo '<pre>'; print_r($matches);

?>