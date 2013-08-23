<?php
/*
Plugin Name: wjdNewsletter
Plugin URI: http://wjd-wp.arsmedia-software.de
Description: Der WJD Newsletter erzeugt automatisch zum Monatsanfang einen Newsletter mit den EintrÃ¤gen des letzten Monats, sowie den nÃ¤chsten Terminen. Ausserdem stellt das Plugin ein An- und Abmeldeformular mit dem Double-Opt-In Verfahren bereit. Der Platzhalter f&uuml;r das Formular ist {WJD_NEWSLETTER_ANMELDUNG}. Die Konfiguration erfolgt &uuml;ber das Plugin wjdConfig.
Author: Christian Asche
Version: 1.0
Author URI: http://wjd-wp.arsmedia-software.de
*/

add_action('admin_menu', array('wjdNewsletter', 'admin') );
add_filter('the_content', array( 'wjdNewsletterAnmeldung', 'wjdNewsletterAnmeldungInSite' ) );

class wjdNewsletter{
	
	public function admin(){
		add_options_page('WJD Newsletter', 'WJD Newsletter', 10, 'wjdConfig/'.basename( __FILE__ ) , array('wjdNewsletter', 'adminPage') );
	}
	
	public function adminPage(){
		echo '<div class="wrap"><h2>WJD Newsletter</h2>';
		echo '<p>	
					Der Newsletter benötigt keine besondere Konfiguration.
					Der Quelltext dieser Seite kann als Newsletter übernommen werden:
			  </p>
			  <p>
				<a target="_blank" href="'.htmlspecialchars( self::getCronjobUri() ).'">
					'.htmlspecialchars( self::getCronjobUri() ).'
				</a>
			</p>
			<p>
				Die Seite erzeugt automatisch zum Monatsanfang einen Newsletter mit 
				den EintrÃ¤gen des letzten Monats, sowie den nÃ¤chsten Terminen.
			</p>';
		echo '</div>';
	}
	
	public function getCronjobUri(){
		return get_option('siteurl') . '/wp-content/plugins/wjdNewsletter/cron.php';
	}
	
	public static function getTitle( ){
		ob_start();
		the_title();
		return ob_get_clean();
	}
	
	public static function getExcerpt( ){
		ob_start();
//		the_excerpt();
		the_content('');
		return strip_tags( ob_get_clean() );
	}
	
	public static function getPermalink( ){
		ob_start();
		the_permalink();
		return ob_get_clean();
	}
	
	public static function getID( ){
		return get_the_ID();
	}
	
	public static function getNews( $m ){
		$m = implode( explode( '-', $m ) );
		$my_query = new WP_Query('m='.$m.'&nopaging=1' ); 
		$news = array();
		while ($my_query->have_posts()) : $my_query->the_post(); 
			$item['title'] = wjdNewsletter::getTitle();
			$item['excerpt'] = wjdNewsletter::getExcerpt();
			$item['permalink'] = wjdNewsletter::getPermalink();
			$item['ID'] = wjdNewsletter::getID();
			$news[] = $item;
		endwhile;
		return $news;
	}
	
	public static function nextMonth( $m ){

		if( is_numeric( $m ) ){
			// m=200811
			$year  = substr( $m, 0 , 4 );		
			$month = substr( $m, 4 , 2 );		
		}else{
			// m=2008-11
			$m = explode( '-', $m );
			$month = $m[1];
			$year = $m[0];
		}

		if( (int) $month >= 12 ){
			$month = '01';
			$year++; 
		}else{
			$month = (int) $month + 1;
			$month = sprintf("%02d", $month );
		}
		return $year.'-'.$month;
	}
	
	public static function prevMonth( $m ){
		$m = explode( '-', $m );
		$month = $m[1];
		$year = $m[0];

		if( (int) $month == 1 ){
			$month = '12';
			$year--; 
		}else{
			$month = (int) $month - 1;
			$month = sprintf("%02d", $month );
		}
		return $year.'-'.$month;
	}
	
	public static function getNewsletter( $m ){
		
		$news = wjdNewsletter::getNews( $m );
		
		$m_readable = preg_replace( '#(\d{4})(\d{2})#', '$1-$2-01', wjdNewsletter::nextMonth( $m ) );
		$m_readable = strtotime( $m_readable );
		setlocale(LC_ALL, 'de_DE@euro', 'de_DE', 'de', 'ge');
		$m_readable = strftime( '%B %Y', $m_readable );
		
		$conf = wjdConfig::loadConfig();
		
		$rs = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
   "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="de" xml:lang="de">

		<head>
			<title>Newsletter der WJ</title>
			<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
			<style type="text/css">
				body, th, td{
					line-height: 1.5;
					color:#484848;
					font-family:Arial,sans-serif;
					font-size:12px;
				}
				table{ 
					border-collapse: collapse; 
				}
				h1 a, h2 a, h3 a, h1 a:visited, h2 a:visited, h3 a:visited {
					color:#484848 !important;
				}
				h1{ 
					font-size: 20px;
				}
				h2{
					
					font-size:20px;
					line-height:1.5;
				}
				p{ 
					margin-top: 10px;
					margin-bottom: 10px; 
				}
				a { 
					color:#3399FF;
					text-decoration: none;
				}
				a:hover {
					text-decoration:underline;
				}
				a.eventname {
					color:#484848;
					font-weight:bold;
				}
			</style>
		</head>
		<body>
		<a name="oben" id="oben"></a>
		';
		
		
		$rs .= '<table cellspacing="0" border="0">
					<tr>
						<td id="header" align="right" colspan="3">
							<img align="left" src="'.$conf->logo.'" alt="" />
							<h1>&nbsp;<br />Newsletter f&uuml;r '.$m_readable.'</h1>
						</td>
					</tr>
					<tr>
						<td width="12"><font size="1">&nbsp;</font></td>
						<td valign="top" id="identifier1" style="background-color:#FFFFFF; border-bottom-width:0; border-top-width:0; padding-bottom:0; padding-left:0; padding-right:0; padding-top:0; right:0;">
							<table style="width:100%;">
								<tr>
									<td id="identifier2" style="background-color: #a6ce3a; width: 260px; height: 23px; border: 1px solid #b9b8b3; border-right: 0px solid #FFFFFF; border-left: 0px solid #FFFFFF; border-width:1px 0 1px 1px;	border-bottom: 1px solid #b9b8b3; border-top: 1px solid #b9b8b3;">
									</td>
									<td id="identifier3" style="background-color: #FFFFFF; height: 23px; border: 1px solid #b9b8b3;	border-right: 0px solid #FFFFFF; border-left: 0px solid #FFFFFF; border-width:1px 0 1px 1px;	border-bottom: 1px solid #b9b8b3; border-top: 1px solid #b9b8b3;">
									</td>
								</tr>
							</table>
						<font size="1">&nbsp;</font>
						</td>
						<td valign="top" style="background-color:#FFFFFF; border-bottom-width:0; border-top-width:0; padding-bottom:0; padding-left:0; padding-right:0; padding-top:0; right:0;">
							<font size="1"></font>
								<table>
									<tr>
										<td id="identifier4" style="background-color:#FFFFFF; border: 1px solid #b9b8b3; border-right: 0px solid #FFFFFF; border-left: 0px solid #FFFFFF;	border-width:1px 0 1px 1px;	border-bottom: 1px solid #b9b8b3; border-top: 1px solid #b9b8b3; height:23px; width:270px;">
										</td>
									</tr>
								</table>
							</td>
						</tr>	
						<tr>
						<td>&nbsp;</td>
						<td valign="top" style="padding-bottom:10px; padding-left:10px; padding-right:10px; padding-top:10px;">
							<p>'.$conf->newsletter_header.'</p>
						</td>
						<td valign="top" style="padding-bottom:10px; padding-left:10px; padding-right:10px; padding-top:10px;">
							&nbsp;
						</td>
					</tr>
					<tr id="middle">
						<td>&nbsp;</td>
						<td id="content" valign="top">
						';
		
		$rs .= '<h2 style="border-top:1px solid #A6CE39; padding-top: 15px;">Inhaltsverzeichnis</h2>';
		
		$rs .= '<ol>';
		$c = 1;
		foreach( $news as $item ){
			$rs .= '<li><a href="#sprungmarke_'.$c.'">'.$item['title'].'</a></li>';
			$c++;
		}
		$rs .= '<li><a href="#sprungmarke_termine">Die n&auml;chsten Termine</a></li>';
		$rs .= '<li><a href="#sprungmarke_impressum">Impressum</a></li>';
		$rs .= '</ol>';
		
		$c = 1;
		
		$ct = 'piwik_campaign=NL'.$m;
		
		foreach( $news as $item ){
			$rs .= '<h2 style="border-top:1px solid #A6CE39; padding-top: 15px;"><a id="sprungmarke_'.$c.'" name="sprungmarke_'.$c.'">'.$item['title'].'</a></h2>';
			$rs .= '<p>';
			$rs .= str_replace( '[...]', '', $item['excerpt'] );
			$rs .= '<br /><br />';
			$rs .= '<a class="more-link" style="padding: 2px 4px; margin-top: 5px; width: 90px; background-color: #a6ce39; color: #FFFFFF; text-decoration: none;" href="'.$item['permalink'].'?'.$ct.'">Weiterlesen ...</a>';
			$rs .= ' oder <a href="#oben">nach oben</a><br />';
			$rs .= '</p>';
			$c++;
		}

		
		$rs .= '<h2 style="border-top:1px solid #A6CE39; padding-top: 15px;">Kontakt</h2>';
		$rs .= '<p>';
		$rs .= $conf->newsletter_signatur;
		$rs .= '</p>';
		
		$rs .= '</td>';
		
		$rs .= '<td valign="top" width="250" style="padding-bottom:10px; padding-left:10px; padding-right:10px; padding-top:10px;">';
		
		$rs .= '<div class="context">';
		
		$rs .= '<h2 style="margin-bottom: 0px; padding-bottom: 0px; padding-left: 0px;">
		<table cellpadding="5"><tr><td  style="border:1px; border-color:#CCCCCC; border-style:solid solid none;">
			<a id="sprungmarke_termine" name="sprungmarke_termine">Die n&auml;chsten Termine</a>
		</td></tr></table></h2>';
		
		$rs .= '<table id="context"><tr><td style="border:1px solid #CCCCCC; margin-bottom:25px;">';
		
		$termin = new wjdTermine();
		$termine = $termin->getTermine( date('Y'), 'ASC', true, 5 );
		
		foreach( $termine as $termin ){

			$rs .= '<table><tr><td>';
			$rs .= '<p>';
			$rs .= '<a class="eventname" href="'.get_option('siteurl').'/termine/?'.$ct.'#termin_'.$termin->id.'">&raquo;';
			$rs .= $termin->schlagzeile;
			$rs .= '</a>';
			$rs .= '<br />';
			
			
			$rs .= '<small>';
			$rs .= preg_replace('#(\d+)-(\d+)-(\d+)#', '$3.$2.$1', $termin->date_start ).', '.preg_replace( '#:00$#', '', $termin->time_start ).' Uhr in ';
			$rs .= $termin->ort.' ';
			$rs .= '<a class="morelink" href="'.$item['permalink'].'?'.$ct.'">mehr »</a>';
			$rs .= '</small>';
			
			$rs .= '</p>';
			$rs .= '</td></tr></table>';
			
		}
		$rs .= '<table  cellpadding="5"><tr><td><p>';
		$rs .= 'Alle Infos und weiter Termine unter <a href="'.get_option('siteurl').'/termine/">'.get_option('siteurl').'/termine/</a>';
		$rs .= '</p>';
		$rs .= '</td></tr></table></td></tr></table><table><tr><td><p>&nbsp;</p></td></tr></table>';
		
		$rs .= '<h2 style="margin-bottom: 0px; padding-bottom: 0px; padding-left: 0px;"><table cellpadding="5"><tr><td  style="border:1px; border-color:#CCCCCC; border-style:solid solid none;"><a id="sprungmarke_impressum" name="sprungmarke_impressum">Impressum</a></td></tr></table></h2>';
		
		$rs .= '<table cellpadding="5" style="border:1px solid #CCCCCC; margin-bottom:25px;"><tr><td>';
		
		$rs .= '<p style="border-bottom-color:-moz-use-text-color; border-bottom-style:none; border-bottom-width:medium;">';
		$rs .= nl2br( $conf->getFullImpressum() ); 
		$rs .= '</p></td></tr></table></div>';

		$rs .= '</td>';
		$rs .= '</tr></table>';
		
		$rs .= '</body></html>';
		return ( $rs );
	}
}

class wjdNewsletterAnmeldung{
	
	public $errors = array();
	
	public $Recipient;

	public $EMailSubject;
	
	public $salt;
	
	public $data;
	
	public function __construct( $data ){
		
		$this->getConfig();

		$data = array_map( 'trim', $data );
		if( $data['email'] == '' || $this->isSpam( $data['email'] ) ){
			$this->errors['email'] = 'Sie haben keine oder eine ung&uuml;ltige E-Mail Adresse eingetragen.';
		}
		
		$this->data = $data;
		
	}
	
	public function wjdNewsletterAnmeldungInSite( $content ){
		if( !strstr( $content, '{WJD_NEWSLETTER_ANMELDUNG}' ) ) return $content; 
	
		// BestÃ¤tigung
		if( $_GET['c'] ){
			
			$n = new wjdNewsletterAnmeldung( array() );
			if( $n->subscribe( $_GET['c'] )){
				$form = $n->textSubscribed();
			}else{
				$form = $n->textUnknownId();	
			}
			
		}else if( isset( $_POST['SubmitBtn'] ) ){
			$n = new wjdNewsletterAnmeldung( $_POST );
	
			if( $n->hasErrors() ){
				$form = wjdNewsletterAnmeldung::getForm( $n->getErrors() );
			}else{
				if( $_POST['action'] == 'subscribe'){
					$rs = $n->sendConfirmation();
					if( $rs ){
						$form = $n->textNeedsConfirm();
					}else{
						$form = $n->textCantSendEmail().wjdNewsletterAnmeldung::getForm();
					}
					
				}else if( $_POST['action'] == 'unsubscribe' ){
					$rs = $n->unsubscribe( $_POST['email'] );
					if( $rs ){
						$form = $n->textUnsubscribed();
					}else{
						$form = $n->textCantUnsubscribe();
					}
				}
			}
		}else{
			$form = wjdNewsletterAnmeldung::getForm();
		}
		
		return str_replace( '{WJD_NEWSLETTER_ANMELDUNG}', $form, $content );
	}

	private function textStart(){
		return '<div class="sti_msg">';
	}
	private function textEnd(){
		return '</div>';
	}
	
	public function textSubscribed(){
		return self::textStart().'
				<p><strong>Newsletter aboniert</strong></p>
				<p>Vielen Dank. Ab sofort erhalten Sie unseren Newsletter.</p>
			'.self::textEnd();
	}
	
	public function textUnknownId(){
		return self::textStart().'
				<p>
					Dieser Newsletter-Empf&auml;nger ist uns nicht bekannt?
					<strong>Bitte &uuml;berpr&uuml;fen Sie, ob Sie den gesamten BestÃ¤tigungslink
					im Browser aufgerufen haben.</strong>
				</p>
			'.self::textEnd();
	}
	
	public function textCantUnsubscribe(){
		return self::textStart().'
				<p><strong>Der Newsletter konnte nicht abbestellt werden.</strong></p>
				<p>Bitte &uuml;berpr&uuml;fen Sie Ihre E-Mail Adresse.</p>
				'.self::textEnd();
	}
	
	public function textUnsubscribed(){
		return self::textStart().'
				<p><strong>Newsletter abbestellt</strong></p>
				<p>Sie erhalten ab sofort keinen Newsletter mehr von uns.</p>
				'.self::textEnd();
	}
	
	public function textCantSendEmail(){
		return self::textStart().'
				<p>	
					Wir konnten Ihnen leider keine E-Mail senden. 
					Bitte versuchen Sie es in wenigen Minuten erneut.
				</p>
				<p>
					Vielen Dank f&uuml;r Ihr Verst&auml;ndnis.
				</p>
				'.self::textEnd();
	}
	
	public function textNeedsConfirm(){
		return self::textStart().'
				<p><strong>Newsletter Best&auml;tigung erforderlich</strong></p>
				<p>
					Vielen Dank. Eine E-Mail mit einem Best&auml;tigungslink wurde an Sie gesendet. 
					Damit Sie unseren Newsletter empfangen k&ouml;nnen, m&uuml;ssen Sie zuerst den Link anklicken.
				</p>
				<p>
					<strong>Bitte &uuml;berpr&uuml;fen Sie jetzt Ihr Postfach.</strong>
				</p>
			'.self::textEnd();
	}
	
	public function canSubscribe( $id ){
		return (bool) file_exists( $this->getTmpDir().$id );
	}
	
	public function subscribe( $id ){
		if( !file_exists( $this->getTmpDir().$id ) ) return false;
		$body = file_get_contents( $this->getTmpDir().$id );
		preg_match( '#email:(.+)#', $body, $email );
		$email = trim( $email[1] );
		$rs = mail( $this->Recipient, 'SUBSCRIBE', $body, 'From: '.$email, '-f'.$email );
		if( $rs ){
			unlink( $this->getTmpDir().$id );
		}
		return $rs;
	}
	
	public function unsubscribe( $email ){
		return mail( $this->Recipient, 'UNSUBSCRIBE', 'email: '.$this->data['email']."\n\n".$_SERVER['REMOTE_ADDR']."\n".date('H:i:s'), 'From: '.$this->data['email'], '-f'.$this->data['email'] );
	}
	
	public function isSpam( $str ) {
		return ( eregi("from:",$str) || eregi("to:",$str) || eregi("multipart",$str) || eregi("cc:",$str) || eregi("bcc:",$str) );
	}
	
	public function getTmpDir(){
		$d = dirname(__FILE__).'/tmp/';
		if( !is_dir( $d ) ){
			mkdir( $d );
		}
		return $d;
	}
	
	public function sendConfirmation(){
		
		$conf = wjdConfig::loadConfig();
		
		$id = md5( $this->salt . $this->data['email'] );
	
		$file = '';
		$file .= "anrede:".$this->data['anrede']."\n";
		$file .= "vorname:".$this->data['vorname']."\n";
		$file .= "name:".$this->data['nachname']."\n";
		$file .= "company:".$this->data['company']."\n";
		$file .= "email:".$this->data['email']."\n";
	
		$rs = file_put_contents( $this->getTmpDir().$id, $file );

		if( !$rs ) return false;
		
		$message = 'Sehr geehrte Interessentin, Sehr geehrter Interessent,

vielen Dank für die Anmeldung zu unserem Newsletter. Damit Ihre E-Mail-
Adresse in unserem Newsletterverteiler aufgenommen wird, bestätigen Sie
bitte nochmal Ihre Anmeldung, in dem Sie auf den folgenden Link klicken:

'.get_option('siteurl').'/'.$_SERVER['REQUEST_URI'].'?c='.$id.'

Sollte der Link nicht anklickbar sein, dann kopieren Sie bitte den 
Link-Text und fügen Sie diesen in die Adresszeile Ihres Browsers ein.

Sie möchten nicht in unseren Newsletter aufgenommen werden oder Ihre E-Mail-
Adresse wurde durch eine fremde Person auf unserer Webseite eingetragen?
Kein Problem, löschen Sie ganz einfach diese E-Mail, damit werden Sie nicht
in unserem Newsletterverteiler aufgenommen.

Mit freundlichen Grüßen

'. $conf->getFullImpressum() ;

		return mail( $this->data['email'], $this->EMailSubject, utf8_decode( $message ), 'From: '.$this->Recipient, '-f'.$this->Recipient );
	}
	
	public function getConfig(){
		
		$conf = $this->getConfigFilename();
		if( !file_exists( $conf ) ) $this->createConfig();
		include( $conf );
		
		$wjConf = wjdConfig::loadConfig();

		$this->Recipient = $wjConf->newsletter_subscribe_email;
		$this->EMailSubject = $wjConf->newsletter_subscribe_subject;
		$this->salt = $salt;

		return true;
	}
	
	public function getConfigFilename(){
		return dirname(__FILE__).'/config.php';
	}
	
	public function createConfig(){
		$conf = $this->getConfigFilename();
		if( file_exists( $conf ) ) return false;
		file_put_contents( $conf, '<?php $salt = "'.uniqid().'"; ?>' );
		return true;
	}
	
	public function hasErrors(){
		return (bool) $this->errors;
	}
	
	public function getErrors(){
		return $this->errors;
	}
	
	public static function getForm( array $errors = null ){
		return '
				<p><strong>Newsletter An-/ Abmeldung</strong></p>
				<p>
					Nur das Feld E-Mail-Adresse ist ein Pflichtfeld.
					Wir w&uuml;rden uns aber sehr freuen, Sie beim Namen
					ansprechen zu d&uuml;rfen.
				</p>
			<form method="post" action="">
				<strong>'.( isset( $errors['email'] ) ? $errors['email'] : '' ).'</strong>
				<p>
					Ihre E-Mail-Adresse*:<br />
					<input type="text" name="email" size="37" value="'.htmlspecialchars( $_POST['email']).'" />
				</p>
				<strong>'.( isset( $errors['anrede'] ) ? $errors['anrede'] : '' ).'</strong>
				<p>
					Anrede:<br />
					<input type="text" name="anrede" size="37" value="'.htmlspecialchars( $_POST['anrede']).'" />
				</p>
				<strong>'.( isset( $errors['vorname'] ) ? $errors['vorname'] : '' ).'</strong>
				<p>
					Vorname:<br />
					<input type="text" name="vorname" size="37" value="'.htmlspecialchars( $_POST['vorname']).'" />
				</p>
				<strong>'.( isset( $errors['nachname'] ) ? $errors['nachname'] : '' ).'</strong>
				<p>
					Nachname:<br />
					<input type="text" name="nachname" size="37" value="'.htmlspecialchars( $_POST['nachname']).'" />
				</p>
				<strong>'.( isset( $errors['company'] ) ? $errors['company'] : '' ).'</strong>
				<p>
					Firma:<br />
					<input type="text" name="company" size="37" value="'.htmlspecialchars( $_POST['company']).'" />
				</p>
				<strong>'.( isset( $errors['action'] ) ? $errors['action'] : '' ).'</strong>
				<p>Sie m&ouml;chten sich f&uuml;r unseren Newsletter</p>
				<p>
			  		<input type="radio" value="subscribe" checked name="action" />  anmelden<br />
			  		<input type="radio" name="action" value="unsubscribe" /> abmelden</p>
				<p>&nbsp;</p>
				<p>	
					<input type="submit" value="Abschicken" name="SubmitBtn" />
				</p>
			</form>
		';
	}
}
?>