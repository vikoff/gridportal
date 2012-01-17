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
	
	$fromHost = 'thei.org.ua';
	$fromAddress = 'vlad@thei.org.ua';
	$fromName = 'Vadim Khramov';
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
	$mail->AltBody    = strip_tags($body);
	$mail->MsgHTML($body);
	$mail->AddAddress($address, "John Doe");

	return $mail->Send();
}

$db = db::get();

$users = array();
$mailToSend = array();
$mailData = $db->getAll('SELECT * FROM mail WHERE send_date IS NULL');

// получение id всех пользователей
foreach ($mailData as $m)
	if ($m['uid'] && !isset($users[ $m['uid'] ]))
		$users[ $m['uid'] ] = null;

// получение email-адресов всех пользователей		
if (!empty($users)) {
	foreach ($db->getColIndexed('SELECT id, profile FROM users WHERE id IN('.implode(',', array_keys($users)).')') as $uid => $sData){
		if (!empty($sData)) {
			$data = unserialize($sData);
			if (!empty($data['email']))
				$users[$uid] = $data['email'];
		}
	}
}

// сбор почты
foreach ($mailData as $m) {
	
	$email = !empty($m['email'])
		? $m['email']
		: $users[ $m['uid'] ];
	
	if (empty($email))
		continue;
	
	if (!isset($mailToSend[$email]))
		$mailToSend[$email] = array();
	
	$mailToSend[$email][] = array(
		'title' => $m['title'],
		'text' => $m['text'],
	);
}

// echo '<pre>'; print_r($mailToSend);

foreach ($mailToSend as $email => $data) {
	
	$text = '';
	foreach ($data as $msg) {
		$text .= "\n<h3>".$msg['title']."</h3>\n";
		$text .= $msg['text'];
	}
	
	echo "mail to $email\n$text\n\n";
	// send($email, 'title', $text);
}

?>
