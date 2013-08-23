<?php
/*
Plugin Name: wjdConfig
Plugin URI: http://wjd-wp.arsmedia-software.de
Description: Konfiguration für die Templates, etc. 
Author: Christian Asche
Version: 1.0
Author URI: http://wjd-wp.arsmedia-software.de
*/

add_action('admin_menu', array('wjdConfig', 'admin') );
add_filter('the_content', array('wjdConfig', 'platzhalterInSite') );

/**
 * Konfigurationsklasse
 */
class wjdConfig{
	
	public $newsletter_header;
	
	public $newsletter_signatur;
	
	public $newsletter_subscribe_email;

	public $newsletter_subscribe_subject;

	public $logo;
	
	public $name;
	
	public $adresse;
	
	public $partner;
	
	public $nachbarkreise;
	
	public $fusszeilen_partner;
	
	public $impressum;
	
	public function __construct( array $init = null ){
		if( !$init !== null ){
			foreach( $init as $k => $v ){
				$this->$k = $v;
			}
		}
	}
	
	public function __set_state( array $init ){
		return new wjdConfig( $init );
	}
	
	public function getFullImpressum(){
		$conf = wjdConfig::loadConfig();
		$out .= $conf->name . "\n";
		$out .= "\n";
		$out .= $conf->adresse . "\n";
		$out .= "\n";
		$out .= $conf->impressum . "\n";
		return $out;
	}
	
	public function platzhalterInSite( $content ){
		if( !strstr( $content, '{WJD_IMPRESSUM}' ) ) return $content; 
		$out = nl2br( self::getFullImpressum() );
		return str_replace( '{WJD_IMPRESSUM}', $out, $content );
	}
	
	public function admin(){
		add_options_page('WJD Config', 'WJD Config', 10, 'wjdConfig/'.basename( __FILE__ ) , array('wjdConfig', 'adminPage') );		
	}
	
	public function adminPage(){
		echo '<div class="wrap"><h2>WJD Config</h2>';
		echo '<p>Hier kannst Du die Konfiguration bearbeiten.</p>';
		
		$_POST = get_magic_quotes_gpc() ? array_map( 'stripslashes', $_POST ) : $_POST;

		$conf = new wjdConfig( $_POST );
		if( $_POST ){
			$conf->saveConfig();
		}else{
			$_POST = (array) $conf->loadConfig(); 
		}
		echo $conf->getForm();
		echo '</div>';
	}
	
	public function getErrors(){
		return (array) $this->errors;
	}
	
	public function getFormTextarea( $k){
		return '
			<textarea rows="10" cols="60" 
			style="width: 98%;" id="'.$k.'" name="'.$k.'"
			>'.htmlspecialchars( $_POST[$k] ).'</textarea>';
	}
	public function getFormInput( $k ){
		return '<input style="width: 98%;" type="text" 
				id="'.$k.'" name="'.$k.'" 
				value="'.htmlspecialchars( $_POST[$k] ).'" 
				/>';
	}
	
	public static function getConfFile( $dist = false ){
		return dirname(__FILE__) .'/config.'.($dist ? 'dist.' : '' ).'php';
	}
	
	/**
	 * Lädt die gespeicherte Konfiguration
	 *
	 * @return wjdConfig
	 */
	public function loadConfig(){
		if( !file_exists( self::getConfFile() ) ){
			copy( self::getConfFile( true ), self::getConfFile() );
		}
		include( self::getConfFile() );
		return $conf;
	}
	
	public function saveConfig(){
		file_put_contents( self::getConfFile(), '<?php $conf = '.var_export( $this, 1 ).'; ?>' );
	}
	
	public function convert( $val ){
		$rs = array();
		$lines = preg_split( '#\r|\n|\r\n#', $val );
		foreach( $lines as $line ){
			$item = explode( '[', $line );
			foreach( $item as $k=>$v ){
				$item[$k] = trim( str_replace( ']', '', $v ) );
			}
			if( !$item[0] ) continue;
			$rs[] = $item;
		}
		return $rs;
	}
	
	public function getForm(){
		$out = '<form action="" method="post"><table class="form-table"><tbody>';
		$errors = $this->getErrors();

		$formular = array(
			'newsletter_header' => array(
								'label' => 'Anrede des Newsletters',
								'required' => true,
								'type' => '',
								'help' => 'Z.B. Hallo [Vorname],&lt;br /&gt;. '
							),
			'newsletter_signatur' => array(
								'label' => 'Signatur des Newsletters',
								'required' => true,
								'type' => 'textarea',
								'help' => 'Bitte beachte die gesetzlichen Vorschriften (z.B. Pflichtangaben in Geschäftsbriefen). HTML ist hier erlaubt. Z.B. Zeilenumbruch &lt;br /&gt;'
							),
			'newsletter_subscribe_email' => array(
								'label' => 'Newsletter-Anmelde-Adresse',
								'required' => true,
								'type' => '',
								'help' => 'An diese E-Mail Adresse werden die An- und Abmelde-E-Mails gesendet. Beispiel: newsletter@wj-KREIS.de'
							),
			'newsletter_subscribe_subject' => array(
								'label' => 'Newsletter-Bestätigungs-Betreff',
								'required' => true,
								'type' => '',
								'help' => 'Dies ist der Betreff der Bestätigungs-E-Mail. Z.B. Ihre Anmeldung zu unserem Newsletter'
							),
			'logo' => array(
								'label' => 'Logo',
								'required' => true,
								'type' => '',
								'help' => 'Z.B. '.htmlspecialchars( get_option('siteurl') ).'/wp-content/themes/wj_de/images/wj-gi-vb-logo.gif'
							),
			'name' => array(
								'label' => 'Name',
								'required' => true,
								'type' => '',
								'help' => 'Z.B. Wirtschaftsjunioren Kreis'
							),
			'adresse' => array(
								'label' => 'Adresse, Telefon, Fax, E-Mail',
								'required' => true,
								'type' => 'textarea',
								'help' => ''
							),
			'impressum' => array(
								'label' => 'Sonstiges für Impressum',
								'required' => true,
								'type' => 'textarea',
								'help' => 'Vertretungsberechtigter Vorstand, Register, etc. '
							),
			'partner' => array(
								'label' => 'Partner',
								'required' => true,
								'type' => 'textarea',
								'help' => 'Zeilenweise Partner z.B. WJD (Deutschland) [http://wjd.de]'
							),
			'nachbarkreise' => array(
								'label' => 'Nachbarkreise',
								'required' => true,
								'type' => 'textarea',
								'help' => 'Zeilenweise Nachbarkreisname [http://www.wj-nachbarkreis.tld]'
							),
			'fusszeilen_partner' => array(
								'label' => 'Partner in der Fußzeilen',
								'required' => true,
								'type' => 'textarea',
								'help' => 'Zeilenweise Internetadressen von Logos die unten angezeigt werden. /wp-content/themes/wj_de/images/jci.gif [www.jci.cc]'
							)
		);
		
		foreach( $formular as $k=>$v ){
			$out .= '<tr valign="top">';
			$out .= '
				<th scope="row">
					<label style="" for="'.$k.'">'.$v['label'].($v['required']?'*':'').'</label>
				</th>
				<td>';
			$out .= "\n";
			if( $v['type'] == 'textarea' ) {
				$out .= self::getFormTextarea( $k );
			}else{
				$out .= self::getFormInput( $k );
			}
			if( isset( $v['help'] ) ) $out .= '<br />'.$v['help'].'';
			
			if( isset( $errors[$k ] ) ) $out .= '<br /><strong>Fehler: '.htmlspecialchars($errors[$k]).'</strong>';
			$out .= '</td></tr>';
			$out .= "\n";
		}
	
		$out .= '
				<th scope="row">Konfiguration speichern</th>
				<td><input type="submit" value="Speichern" /></td>
			</tbody></table></form>';
		return $out;
	}
	
}
?>