<?

session_start();
ini_set('display_errors', 1);

// обозначение текущий папки
define('CUR_PATH', dirname(__FILE__).'/');

// обозначение корня ресурса
define('FS_ROOT', dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR);
define('IS_CLI', TRUE);

// отправка Content-type заголовка
header('Content-Type: text/html; charset=utf-8');

// подключение локального setup файла
require(CUR_PATH.'setup.php');

// подключение файлов CMF
require_once(FS_ROOT.'setup.php');

// подключение файлов PHPMail
require_once(FS_ROOT.'includes/PHPMailer_5.2.1/class.phpmailer.php');

function send($address, $title, $body){
	
	// print_r(func_get_args()); die; // DEBUG
	
	$fromHost = 'localhost';
	$fromAddress = 'vlad@thei.org.ua';
	$fromName = 'CrimeaEcoGrid-portal';
	$fromPassword = 'freedomcom';
	
	$mail = new PHPMailer();

	$mail->IsSMTP(); // telling the class to use SMTP
	$mail->Host       = $fromHost; // SMTP server
	$mail->SMTPDebug  = 2;                     // enables SMTP debug information (for testing)
											   // 1 = errors and messages
											   // 2 = messages only
	$mail->SMTPAuth   = true;                  // enable SMTP authentication
	$mail->Port       = 25;                    // set the SMTP port for the GMAIL server
	$mail->Username   = $fromAddress; 			// SMTP account username
	$mail->Password   = $fromPassword;        // SMTP account password

	$mail->SetFrom($fromAddress, $fromName);

	$mail->Subject    = $title;
	$mail->AltBody    = strip_tags(nl2br($body));
	$mail->MsgHTML($body);
	$mail->AddAddress($address, "e-mail notice");

	$db = db::get();
	if ($mail->Send()) {
		echo "mail sent to $address\n";
		$db->update('mail', array('status' => 1, 'send_date' => time()), 'status=0 AND email='.$db->qe($address));
		return TRUE;
	} else {
		echo "mail sent ERROR to $address\n";
		$db->update('mail', array('status' => -1, 'send_date' => time()), 'status=0 AND email='.$db->qe($address));
		return FALSE;
	}
}

$db = db::get();

$users = array();
$mailToSend = array();
$mailData = $db->getAll('SELECT * FROM mail WHERE status=0');

// сбор почты
foreach ($mailData as $m) {
	
	$email = $m['email'];
	
	if (!isset($mailToSend[$email]))
		$mailToSend[$email] = array();
	
	$mailToSend[$email][] = array(
		'title' => $m['title'],
		'text' => $m['text'],
	);
}

// echo '<pre>'; print_r($mailToSend); die;

foreach ($mailToSend as $email => $data) {
	
	$title = '';
	$text = '';
	if (count ($data == 1)) {
		$title = $data[0]['title'];
		$text = $data[0]['text'];
	} else {
		$title = 'Изменение статуса нескольких задач';
		foreach ($data as $msg) {
			$text .= $msg['text'].'<br />';
		}
	}
	echo "mail to $email\n$title\n\n";
	send($email, $title, $text);
}

?>
