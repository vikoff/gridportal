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
	
	// print_r(func_get_args()); // DEBUG
	// return; // DEBUG
	
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
	$mail->CharSet = "UTF-8";
	
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

function getLayout($title, $content) {
	
	$layoutFile = FS_ROOT.'templates/Mail/layout.php';
	
	ob_start();
	include($layoutFile);
	$tplContent = ob_get_clean();
	
	return $tplContent;
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
	
	if (!isset($mailToSend[$email][ $m['template_name'] ]))
		$mailToSend[$email][ $m['template_name'] ] = array();
	
	if (!isset($mailToSend[$email][ $m['template_name'] ][ $m['lng'] ]))
		$mailToSend[$email][ $m['template_name'] ][ $m['lng'] ] = array();
	
	$mailToSend[$email][ $m['template_name'] ][ $m['lng'] ][] = $m['text'];
}

// echo '<pre>'; print_r($mailToSend); die;

if (empty($mailToSend))
	die("no mail to send\n");
	
foreach ($mailToSend as $email => $templates) {
	foreach ($templates as $template => $lngs) {
		foreach ($lngs as $lng => $htmls) {
			
			$tplData = Mail::getTemplateData($template);
			$titleSnippet = count($htmls) > 1 ? $tplData['title_multi_lng'] : $tplData['title_lng'];
			$title = Lng::get()->getLngSnippet($lng, $titleSnippet);
			$text = getLayout($title, implode('<br />', $htmls));
			
			echo "mail to $email ($title)\n\n";
			send($email, $title, $text);
		}
	}
}

?>
