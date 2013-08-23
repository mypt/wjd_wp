<?php
/*
Plugin Name: wjdMitglieder
Plugin URI: http://wjd-wp.arsmedia-software.de
Description: Das Plugin WJD Mitglieder verwaltet WJ Mitglieder. 
Author: Christian Asche
Version: 1.0
Author URI: http://wjd-wp.arsmedia-software.de
*/

add_action('admin_menu', array('wjdMitglieder', 'admin') );
add_filter('the_content', array('wjdMitglieder', 'wjMitgliederInSite') );
register_activation_hook( __FILE__, array('wjdMitglieder', 'table_install') );


class wjdMitglieder{

	public $id;
	
	public $anrede;
	public $titel;
	public $vorname;
	public $nachname;
	
	public $wjPosition;
	
	public $adresse;
	public $plz;
	public $stadt;
	
	public $telefon;
	public $fax;
	public $email;
	
	public $firma;
	public $url;
	public $branche;
	public $position;
	
	public $bild;
	
	public $aktiv;

	public function __construct( array $init = null ){
		foreach( self::getDispatchMap() as $k=>$v ){
			if( isset( $init[$k] ) ) $this->$k = $init[$k];
		}
	}
	
	public static function getDispatchMap(){
		return array(
				'id' => array(
							'required' => false,
							'label' => 'ID',
							'type' => 'int'
						),
               'anrede' => array(
					'required' => false,
					'label' => 'Anrede',
					'type' => 'text',
					'max_length' => 200
                ),
               'titel' => array(
					'required' => false,
					'label' => 'Titel',
					'type' => 'text',
					'max_length' => 200
                ),

                'vorname' => array(
					'required' => true,
					'label' => 'Vorname',
					'type' => 'text',
					'max_length' => 200
                ),

                'nachname' => array(
					'required' => true,
					'label' => 'Nachname',
					'type' => 'text',
					'max_length' => 200
                ),

                'wjPosition' => array(
					'required' => false,
					'label' => 'WjPosition',
					'type' => 'text',
					'max_length' => 200
                ),

                'adresse' => array(
					'required' => false,
					'label' => 'Adresse',
					'type' => 'text',
					'max_length' => 200
                ),

                'plz' => array(
					'required' => false,
					'label' => 'Plz',
					'type' => 'text',
					'max_length' => 200
                ),

                'stadt' => array(
					'required' => false,
					'label' => 'Stadt',
					'type' => 'text',
					'max_length' => 200
                ),

                'telefon' => array(
					'required' => false,
					'label' => 'Telefon',
					'type' => 'text',
					'max_length' => 200
                ),

                'fax' => array(
					'required' => false,
					'label' => 'Fax',
					'type' => 'text',
					'max_length' => 200
                ),
                'email' => array(
					'required' => false,
					'label' => 'E-Mail',
					'type' => 'text',
					'max_length' => 200
                ),

                'firma' => array(
					'required' => false,
					'label' => 'Firma',
					'type' => 'text',
					'max_length' => 200
                ),

                'url' => array(
					'required' => false,
					'label' => 'Url',
					'type' => 'text',
					'max_length' => 200
                ),

                'branche' => array(
					'required' => false,
					'label' => 'Branche',
					'type' => 'text',
					'max_length' => 200
                ),

                'position' => array(
					'required' => false,
					'label' => 'Position (beruflich)',
					'type' => 'text',
					'max_length' => 200
                ),

                'bild' => array(
					'required' => false,
					'label' => 'Bild',
					'type' => 'text',
					'max_length' => 200
                ),
                'aktiv' => array(
					'required' => true,
					'label' => 'Aktiv',
					'type' => 'enum',
							'options' => array(
								'Ja' => 'Ja',
								'Nein' => 'Nein'
							)
                )
		);
	}
	
	public function admin(){
		add_options_page('WJD Mitglieder', 'WJD Mitglieder', 10, 'wjdMitglieder/'.basename( __FILE__ ) , array('wjdMitglieder', 'adminPage') );
	}
	
	public static function getOptionPage(){
		return get_option('siteurl') . '/wp-admin/options-general.php?page=wjdMitglieder/wjdMitglieder.php';
	}
	
	public function adminPage(){
		$t = new wjdMitglieder();
		
		echo '<div class="wrap"><h2>WJD Mitglieder</h2>';
		echo '<p>
				<a href="'.self::getOptionPage().'&edit=0">Neues Mitglied</a> |
				<a href="'.self::getOptionPage().'">Mitglieder anzeigen</a>
			</p>
			<hr />
				';
		$gesamt = 0;
		foreach( wjdMitglieder::getWJPositionen() as $k=>$v ){
			$tmp[] = htmlspecialchars( $k . ' ('.$v.')' );
			$gesamt += $v;
		}
		echo '<p>'.$gesamt.' Mitglieder. Mitgliederspiegel: '.implode( ', ', $tmp ).'</p>';
		
		if( isset( $_GET['edit'] ) ){
			if( $_POST ){
				
				if( get_magic_quotes_gpc() ) $_POST = array_map( 'stripslashes', $_POST );
				
				$t = new wjdMitglieder( $_POST );
				if( (int) $_GET['edit'] != 0 ){
					$t->id = (int) $_GET['edit'];
				}
				$rs = $t->save();
				if( !$rs || $t->hasErrors() ){
					echo 'Fehler!';
				}else{
					$ok = true;
					echo '<h2>Die Daten wurden gespeichert.</h2>';
				}
			}else{
				if( $_GET['edit'] != 0 ) $_POST = (array) wjdMitglieder::get( $_GET['edit'] );
			}
			if( !$ok) echo $t->getForm();
		}else if( isset( $_GET['delete'] ) ){
			wjdMitglieder::delete( $_GET['delete'] );
			echo '<h2>Der Datensatz wurde gelöscht.</h2>';
		}else{
			echo $t->getMitgliederAsTable( );
		}
		echo '</div>';
	}
	
	public function getMitgliederAsTable(){
		
		$mitglieder = $this->getMitglieder();

		$out .= '<table class="widefat">
					<thead>
						<tr>
							<th>Werkzeuge</th>
							<th>Vorname</th>
							<th>Name</th>
							<th>Aktiv</th>
							<th>WJ Position</th>
						</tr>
					</thead>
				';	
		foreach( $mitglieder as $mitglied ){
			$out .= '
			<tr>
				<td>
					<a href="'.self::getOptionPage().'&edit='.htmlspecialchars($mitglied->id).'">Bearbeiten</a> | 
					<a href="'.self::getOptionPage().'&delete='.htmlspecialchars($mitglied->id).'" onclick="return confirm(\'Wirklich löschen? Dies kann nicht rückgängig gemacht werden!\');">Löschen</a>
				</td>
				<td>'.htmlspecialchars( $mitglied->vorname ).'</td>
				<td>'.htmlspecialchars( $mitglied->nachname ).'</td>
				<td>'.htmlspecialchars( $mitglied->aktiv ).'</td>
				<td>'.htmlspecialchars( $mitglied->wjPosition ).'</td>
			</tr>
			';
		}	
		$out .= '</table>';
		return $out;		
	}
	
	private static function getAZ(){
		foreach( range( 'A', 'Z' ) as $i ){
			$out[] = '<a href="#abschnitt_'.$i.'">'.$i.'</a>';
		}
		return '<p>'.implode( ' | ', $out ).'</p>';
	}
	public function getMitgliederAsList( $aktiv = 'Ja' ){
		
		$mitglied = new wjdMitglieder();
		$mitglieder = $this->getMitglieder( $aktiv );
		
		$dp = self::getDispatchMap();
		
		$out = self::getAZ();
		
		foreach( $mitglieder as $mitglied ){
			
			$abschnitt = $mitglied->nachname{0};
			if( $abschnitt != $lastAbschnitt ){
				$out .= '<p><big id="abschnitt_'.htmlspecialchars($abschnitt).'">'.htmlspecialchars($abschnitt).'</big></p>';
				$lastAbschnitt = $abschnitt;
			}
			
			$bild = $mitglied->bild != '' ? $mitglied->bild : get_option('siteurl').'/wp-content/plugins/wjdMitglieder/default.jpg';
			
			if( $mitglied->email != '' ){
				$crypt = '';
				$ascii = 0;
				for ($i = 0; $i < strlen( $mitglied->email ); $i++) {
					$ascii = ord ( substr ( $mitglied->email, $i ) );
					if (8364 <= $char) {
						$ascii = 128;
					}
					$crypt .= chr($ascii + 1);
				}
				$email = '<a href="javascript:DeCryptX(\'' . $crypt . '\')">E-Mail</a>';
			}else{
				$email = '';
			}
			
			if( $mitglied->url != '' ){
				$url = preg_match('#^(http|https)://#', $mitglied->url ) ? '' : 'http://' ;
				$url .= $mitglied->url;
			}else{
				$url = '';
			}
			
			$out .= '

				<table id="profil_'.htmlspecialchars( $mitglied->id ).'" class="mitglieder" border="0">
				<tbody>
					<tr class="trenner">
						<td class="mitglied_foto" rowspan="7">
							<img 
								class="" 
								title="'.htmlspecialchars( $mitglied->vorname.' '.$mitglied->nachname).'" 
								src="'.htmlspecialchars( $bild ).'" alt="" />
							<br />
							<small>'.htmlspecialchars( $mitglied->wjPosition ).'</small>
						</td>
						<td class="mitglied_key"><em>Name</em>:</td>
						<td><strong>'.htmlspecialchars( $mitglied->vorname ).' '.htmlspecialchars( $mitglied->nachname ).'</strong></td>
					</tr>
					<tr>
						<td class="mitglied_key"><em>Titel</em>:</td>
						<td>'.htmlspecialchars( $mitglied->titel ).'</td>
					</tr>
					<tr>
						<td class="mitglied_key"><em>Position</em>:</td>
						<td>'.htmlspecialchars( $mitglied->position ).'</td>
					</tr>
					<tr>
						<td class="mitglied_key"><em>Telefon</em>:</td>
						<td>'.htmlspecialchars( $mitglied->telefon ).'</td>
					</tr>
					<tr>
						<td class="mitglied_key"><em>Firma</em>:</td>
						<td>
							'.( $url != '' ? '<a href="'.htmlspecialchars( $url ).'" target="_blank">' : '' ).'
								'.htmlspecialchars( $mitglied->firma ).'
							'.( $url != '' ? '</a>' : '' ).'
						</td>
					</tr>
					<tr>
						<td class="mitglied_key"><em>E-Mail</em>:</td>
						<td>'.$email.'</td>
					</tr>
					<tr>
						<td class="mitglied_key"><em>Branche</em>:</td>
						<td>'.htmlspecialchars( $mitglied->branche ).'</td>
					</tr>
				</tbody>
				</table>
			';
		}
		
		$out .= self::getAZ();
		return $out;		
	}
	
	public function getWJPositionen(){
		global $wpdb;
		
		$sql = "SELECT count(wjposition) as `anz`, wjposition 
				FROM ".$wpdb->prefix."wjd_mitglieder 
				WHERE `aktiv` = 'Ja'
				GROUP BY wjposition  
				";
				
		$rs = $wpdb->get_results( $sql , ARRAY_A);
		if( $rs == null ) return array();
		
		foreach( $rs as $v ){
			$out[ $v['wjposition'] ] = $v['anz'];
		}
		return $out;
	}
	
	/**
	 * Ermittelt Termine
	 *
	 * @param unknown_type $jahr
	 * @return wjdMitglieder
	 */
	public function getMitglieder( $aktiv = null ){
		global $wpdb;
		
		$sql = "SELECT * 
				FROM ".$wpdb->prefix."wjd_mitglieder 
				WHERE 1=1
				".( $aktiv != null ? "AND `aktiv` = '".$wpdb->escape($aktiv)."'" : '' )."
				ORDER BY nachname  
				";
				
		$rs = $wpdb->get_results( $sql , ARRAY_A);
		if( $rs == null ) return array();
		
		foreach( $rs as $v ){
			$out[] = new wjdMitglieder( $v );
		}
		return $out;
	}
	
	public function getFormSelect( $k, $options, $width = '98%' ){
		$out .= '<select style="width: '.$width.';" id="'.$k.'" name="'.$k.'">';
		$out .= '<option value="">-- Bitte wählen --</option>';
		foreach( $options as $sk=>$sv ){
			$out .= '<option value="'.htmlspecialchars($sk).'" '.($_POST[$k]==$sk?'selected="selected"':'').'>';
			$out .= htmlspecialchars($sv);
			$out .= '</option>';
		}
		$out .= '</select>';
		return $out;
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
	
	public function ignoreFields(){
		return array( 'id' );
	}
	
	public function getForm(){
		$out = '<form action="" method="post"><table class="form-table"><tbody>';
		$errors = $this->getErrors();

		foreach( self::getDispatchMap() as $k=>$v ) {
			if( in_array( $k, self::ignoreFields()) ) continue;
			
			$out .= '<tr valign="top">';
			$out .= '
				<th scope="row">
					<label style="" for="'.$k.'">'.$v['label'].($v['required']?'*':'').'</label>
				</th>
				<td>';
			$out .= "\n";
			if( $v['type'] == 'text' && !isset( $v['max_length'] ) ) {
				$out .= self::getformTextarea( $k );
			}else if( $v['type'] == 'enum' ) {
				$out .= self::getFormSelect( $k, $v['options']);
			}else{
				$out .= self::getFormInput( $k );
			}
			if( isset( $v['help'] ) ) $out .= '<br />'.$v['help'].'';
			
			if( isset( $errors[$k ] ) ) $out .= '<br /><strong>Fehler: '.htmlspecialchars($errors[$k]).'</strong>';
			$out .= '</td></tr>';
			$out .= "\n";
		}
		$out .= '
				<th scope="row">Daten speichern</th>
				<td><input type="submit" value="Speichern" /></td>
			</tbody></table></form>';
		return $out;
	}
	
	public function validate(){
		$this->_errors = array();
		foreach( self::getDispatchMap() as $k=>$v ){
			
			if( !$this->$k && $v['required'] != true ) continue;
			
			if( $v['required'] == true && $this->$k == '' ) $this->_errors[$k] = "Der Wert ist erforderlich";
			
			if( $v['type'] == 'text' ) {
				if( isset( $v['max_length'] ) && strlen( $this->$k) > $v['max_length'] ){
					$this->_errors[$k] = "Der Text ist zu lang. Erlaubt sind maximal ".$v['max_length']." Zeichen";
				}
			}else if( $v['type'] == 'int' ) {
				if( !is_numeric( $this->$k ) ) $this->_errors[$k] = "Der Wert kann nur eine Zahl sein";
			}else if( $v['type'] == 'enum' ) {
				if( !isset( $v['options'][$this->$k] ) ) $this->_errors[$k] = "Ungültiger Wert.";
			}else if( $v['type'] == 'date' ) {
				if( preg_match( '#^\d{2}\.\d{2}\.\d{4}$#', $this->$k ) ){
					$this->$k = preg_replace( '#^(\d{2})\.(\d{2})\.(\d{4})$#', '$3-$2-$1', $this->$k );
				}
				if( !preg_match( '#^\d{4}-\d{2}-\d{2}$#', $this->$k ) ) $this->_errors[$k] = "Der Wert muss ein Datum sein. Z.B. ".date('Y-m-d');
			}else if( $v['type'] == 'time' ) {
				if( !preg_match( '#^\d{2}(:\d{2}){1,2}$#', $this->$k ) ) $this->_errors[$k] = "Der Wert muss eine Uhrzeit sein. Z.B. ".date('H:i');
			}
		}
		return $this->hasErrors();
	}
	
	public static function get( $id ){
		global $wpdb;
		$sql = "SELECT * FROM ".$wpdb->prefix."wjd_mitglieder WHERE id = '".$wpdb->escape($id)."'";
		$rs = $wpdb->get_row( $sql , ARRAY_A);
		if( $rs == null ) throw new Exception( "Unbekannter Datensatz mit der ID " . $id );
		return new wjdMitglieder( $rs );
	}
	
	public static function delete( $id ){
		global $wpdb;
		$sql = "DELETE FROM ".$wpdb->prefix."wjd_mitglieder WHERE id = '".$wpdb->escape($id)."'";
		$rs = $wpdb->query( $sql , ARRAY_A);
		if( $rs == null ) return false;
		// self::logSyncAction( 'delete', $id );
		return true;
	}
	
	public function hasErrors(){
		return (bool) $this->_errors;
	}
	
	public function getErrors(){
		return $this->_errors;
	}	
	
	public function save(){
		global $wpdb;
		$this->validate();
		
		if( $this->hasErrors() ) return false;
		
		$table_name = $wpdb->prefix . "wjd_mitglieder";

		foreach( self::getDispatchMap() as $k=>$v ){
			$keys[] = '`'.$wpdb->escape( $k ).'`';
			$vals[] = '"'.$wpdb->escape( $this->$k ).'"';
		}
		
		if( isset( $this->id ) ){
			$sql = "UPDATE " . $table_name.' SET ';
			$update = array_combine( $keys, $vals );
			foreach( $update as $k=>$v ){
				$tmp[] = ''.$k.' = '.$v.' ';
			}
			$sql .= ' '.implode( ', ', $tmp ).' ';
			$sql .= 'WHERE id = "'.$wpdb->escape( $this->id ).'" LIMIT 1';
			
			$action = 'edit';
		}else{
			$sql = "INSERT INTO " . $table_name;
			$sql .= ' ('.implode( ',', $keys ).') VALUES ('.implode( ',', $vals ).')';
		
			$action = 'add';
		}

		$results = $wpdb->query( $sql );

		if( $results == 1 ){
			if( $action == 'edit'){
				// self::logSyncAction( 'edit', $this->id );
			}else{
				$id = $wpdb->get_var( 'SELECT LAST_INSERT_ID()' );
				// self::logSyncAction( 'add', $id );
			}
		}
		return $results;
	}
	
	public function table_install() {
		global $wpdb;

		$table_name = $wpdb->prefix . "wjd_mitglieder";
		if($wpdb->get_var("show tables like '$table_name'") != $table_name) {
		  
			$sql = "CREATE TABLE " . $table_name . " (
				id int NOT NULL AUTO_INCREMENT,
				`anrede` text,
				`titel` text,
				`vorname` text,
				`nachname` text,
				`wjPosition` text,
				`adresse` text,
				`plz` text,
				`stadt` text,
				`telefon` text,
				`fax` text,
				`email` text,
				`firma` text,
				`url` text,
				`branche` text,
				`position` text,
				`bild` text,
				`aktiv` varchar(255),
				PRIMARY KEY id (id)
			)";

			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
			dbDelta($sql);
		}
	}
	
	public function wjMitgliederInSite( $content ){

		#if( substr( $_SERVER['REQUEST_URI'], 0, 9) != '/termine/' ) return $content;
		
		if( !strstr( $content, '{WJD_MITGLIEDER}' ) ) return $content; 
		
		$t = new wjdMitglieder();
		
		$out .= '';
		
		$out .= $t->getMitgliederAsList();
		
		return str_replace( '{WJD_MITGLIEDER}', $out, $content );
	}

} // class
?>