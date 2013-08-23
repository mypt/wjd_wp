<?php
/*
Plugin Name: wjdTermine
Plugin URI: http://wjd-wp.arsmedia-software.de
Description: Das Plugin WJD Termine verwaltet WJ Termine in einer Form die zur WJD Seite bis 2010 kompatibel war.
Author: Christian Asche (wj@christian-asche.com)
Version: 1.0
Author URI: http://wjd-wp.arsmedia-software.de
*/

add_action('admin_menu', array('wjdTermine', 'admin') );
add_filter('the_content', array('wjdTermine', 'wjTermineInSite') );
register_activation_hook( __FILE__, array('wjdTermine', 'table_install') );
add_action('widgets_init', array('wjdTermine', 'sidebar_Termine'));

class wjdTermine{

	public $id;
	public $kat_id;
	public $ber_id;
	public $schlagzeile;
	public $thema;
	public $ort;
	public $anschrift;
	public $date_start;
	public $date_end;
	public $time_start;
	public $time_end;
	public $link;
	public $link_extern;
	public $author;
	public $email;
	public $kontakt;
	public $kontakt_email;

	public function __construct( array $init = null ){
		foreach( self::getDispatchMap() as $k=>$v ){
			if( isset( $init[$k] ) ) $this->$k = $init[$k];
		}
	}
	
	public static function externTermin(){
		
		$out = '';
		
		$jahr = date("Y"); // TODO Aktuelles Jahr automatisch
		$sortDir = 'ASC';
		$onlyNewer = true;
		$limit = 3;
		$date_start = date("d.m.Y");
		$obj = new wjdTermine;
		$termine = $obj->getTermine( $jahr, $sortDir, $onlyNewer, $limit );
		
//var_dump($termine);exit;
		
		$out .= '
<table>
		';


		foreach( $termine as $k=>$v ){
			
			$zeitpunkt = $v->date_start.' '.$v->time_start;
			$zeitpunkt = strtotime( $zeitpunkt );
			$zeitpunkt = date( 'd.m.Y, H:i', $zeitpunkt );
			
			
			$out .= '
    <tr>
        <td valign="top">&#8226;</td>
        <td style="padding-bottom:10px">
            <p class="wjdext"><i>'.get_option('blogname').'<br /></i>
            <a id="ctl00_repeater_ctl01_hyperLink" href="http://'.$_SERVER['HTTP_HOST'].'/termine/#termindetail_'.(int)$v->id.'">'.htmlspecialchars( $v->schlagzeile ).'</a><br />
            Internationales<br />
            '.htmlspecialchars( $zeitpunkt ).' Uhr - ...</p>
        </td>
    </tr>

			';
			//var_dump( $v )
		}
		
		return $out;
	}

	public static function getDispatchMap(){
		return array(
				'id' => array(
							'required' => false,
							'label' => 'ID',
							'type' => 'int'
						),
				'kat_id' => array(
							'required' => true,
							'label' => 'Kategorie',
							'type' => 'enum',
							'options' => array(
								'13' => 'Ball/Tanzveranstaltung',
								'16' => 'Betriebsbesichtigung',
								'12' => 'Interessenten-Abend',
								'9' => 'Konferenz',
								'7' => 'Mitgliederversammlung',
								'18' => 'Monatstreff',
								'19' => 'Organisationssitzung',
								'25' => 'Party',
								'8' => 'Podiumsdiskussion',
								'6' => 'Pressekonferenz',
								'11' => 'Reise',
								'14' => 'Seminar',
								'21' => 'Sonstiges',
								'20' => 'Vorstand',
								'10' => 'Vortrag',
								'17' => 'Wir über uns',
								'15' => 'Workshop'
							)
						),
				'ber_id' => array(
							'required' => true,
							'label' => 'Bereich',
							'type' => 'enum',
							'options' => array(
								'47' => 'Abendveranstaltung',
								'44' => 'Aktionstag',
								'56' => 'Aktionsteam/Wandel',
								'52' => 'Aktionsteam/Werte',
								'48' => 'Aktionsteam/Wissen',
								'1' => 'Allgemein',
								'43' => 'Ausbildungsmesse',
								'2' => 'Bildung/Wirtschaft',
								'60' => 'Charity',
								'68' => 'Debattierclub',
								'45' => 'Essen + Trinken',
								'9' => 'Existenzgründung/-sicherung',
								'64' => 'Führung',
								'75' => 'Gesellschaft / Soziales',
								'10' => 'Internationale Veranstaltung',
								'3' => 'Internationales',
								'5' => 'Kommunikation',
								'42' => 'Messe',
								'6' => 'Neue Medien/Internet',
								'8' => 'Personal Development',
								'4' => 'Politik/Wirtschaft',
								'11' => 'Sonstiges',
								'46' => 'Twinning',
								'7' => 'Umwelt/Wirtschaft'
							)
						),
				'schlagzeile' => array(
							'required' => true,
							'label' => 'Schlagzeile',
							'type' => 'text',
							'max_length' => 100
						),
				'thema' => array(
							'required' => true,
							'label' => 'Thema',
							'type' => 'text'
						),
				'ort' => array(
							'required' => true,
							'label' => 'Ort',
							'type' => 'text',
							'max_length' => 200
						),
				'anschrift' => array(
							'required' => false,
							'label' => 'Anschrift',
							'type' => 'text'
						),
				'date_start' => array(
							'required' => true,
							'label' => 'Datum Anfang',
							'type' => 'date'
						),
				'date_end' => array(
							'required' => false,
							'label' => 'Datum Ende',
							'type' => 'date'
						),
				'time_start' => array(
							'required' => true,
							'label' => 'Uhrzeit Anfang',
							'type' => 'time'
						),
				'time_end' => array(
							'required' => false,
							'label' => 'Uhrzeit Ende',
							'type' => 'time'
						),
				'link' => array(
							'required' => false,
							'label' => 'Link',
							'type' => 'text',
							'max_length' => 200,
							'help' => 'Link auf der WJD Seite'
						),
				'link_extern' => array(
							'required' => false,
							'label' => 'Link Extern',
							'type' => 'text',
							'max_length' => 200,
							'help' => 'Externe Link, beginnend mit http://'
						),
				'author' => array(
							'required' => true,
							'label' => 'Autor',
							'type' => 'text',
							'max_length' => 200,
							'help' => 'Ansprechpartner'
						),
				'email' => array(
							'required' => true,
							'label' => 'E-Mail',
							'type' => 'text',
							'max_length' => 200,
							'help' => 'Email des Ansprechpartners'
						),
				'kontakt' => array(
							'required' => false,
							'label' => 'Kontakt',
							'type' => 'text',
							'max_length' => 200,
							'help' => 'Name der Person bei man sich anmelden soll'
						),
				'kontakt_email' => array(
							'required' => false,
							'label' => 'Kontakt E-Mail',
							'type' => 'text',
							'max_length' => 200,
							'help' => 'Email der Person die Anmeldungen entgegen nimmt.'
						),
				'kreis' => array(
							'required' => false,
							'label' => 'Kreis'
						)

		);
	}
	
	public static function getOrgIds(){
		return array(
			'249' => 'Altenburg',
			'217' => 'Altmark',
			'49' => 'Altötting',
			'74' => 'Amberg',
			
			'50' => 'Ammer-Lech',
			'31' => 'Ansbach',
			'162' => 'Arnsberg',
			'91' => 'Aschaffenburg',
			'82' => 'Augsburg',
			'92' => 'Bad Kissingen',
			'190' => 'Bad Kreuznach',
			'6' => 'Baden-Württemberg',
			
			'71' => 'Bamberg',
			'239' => 'Barcelona / Spanien',
			'4' => 'Bayern',
			'72' => 'Bayreuth',
			'99' => 'Berlin',
			'98' => 'Berlin-Brandenburg',
			'51' => 'BGL',
			'7' => 'Biberach an der Riß',
			'163' => 'Bochum',
			
			'8' => 'Bodensee-Oberschwaben',
			'164' => 'Bonn',
			'100' => 'Brandenburg an der Havel',
			'109' => 'Braunschweig',
			'116' => 'Bremen',
			'108' => 'Bremerhaven',
			'75' => 'Cham',
			'208' => 'Chemnitz',
			'64' => 'Coburg',
			
			'191' => 'Cochem-Zell',
			'101' => 'Cottbus',
			'52' => 'Dachau',
			'145' => 'Darmstadt',
			'40' => 'Deggendorf',
			'218' => 'Dessau',
			'41' => 'Dingolfing-Landau',
			'117' => 'Dithmarschen',
			'83' => 'Donau-Ries',
			
			'165' => 'Dortmund',
			'209' => 'Dresden',
			'167' => 'Düsseldorf',
			'166' => 'Duisburg',
			'53' => 'Ebersberg',
			'223' => 'Eichsfeldkreis',
			'54' => 'Eichstätt',
			'224' => 'Eisenach',
			
			'110' => 'Emsland',
			'55' => 'Erding',
			'225' => 'Erfurt/Weimar',
			'32' => 'Erlangen',
			'168' => 'Essen',
			'9' => 'Esslingen',
			'65' => 'Fichtelgebirge',
			'111' => 'Flensburg',
			'66' => 'Forchheim',
			
			'147' => 'Frankfurt am Main',
			'102' => 'Frankfurt/Oder',
			'210' => 'Freiberg',
			'10' => 'Freiburg',
			'42' => 'Freyung-Grafenau',
			'148' => 'Friedberg (Hessen)',
			'33' => 'Fürth',
			'149' => 'Fulda',
			
			'56' => 'Garmisch-Partenkirchen',
			'226' => 'Gera',
			'150' => 'Giessen-Vogelsberg',
			'211' => 'Glauchau',
			'11' => 'Göppingen',
			'212' => 'Görlitz',
			'112' => 'Göttingen',
			
			'227' => 'Gotha',
			'84' => 'Günzburg',
			'34' => 'Gunzenhausen',
			'169' => 'Hagen/Ennepe-Ruhr',
			'219' => 'Halle',
			'118' => 'Hamburg',
			'121' => 'Hameln',
			'151' => 'Hanau-Gelnhausen-Schlüchtern',
			
			'114' => 'Hann. Münden',
			'113' => 'Hannover',
			'106' => 'Hanseraum',
			'133' => 'Harz',
			'93' => 'Haßberge',
			'103' => 'Havelland',
			'12' => 'Hegau-westlicher Bodensee',
			'13' => 'Heidelberg',
			
			'14' => 'Heilbronn-Franken',
			'152' => 'Hersfeld-Rotenburg',
			'144' => 'Hessen',
			'119' => 'Hildesheim',
			'15' => 'Hochrhein-Bodensee',
			'67' => 'Hof',
			'122' => 'Holzminden',
			'192' => 'Idar-Oberstein',
			'57' => 'Ingolstadt',
			
			'247' => 'International Friends',
			'170' => 'Iserlohn',
			'228' => 'Jena',
			'240' => 'Johannesburg/Südafrika',
			'193' => 'Kaiserslautern',
			'16' => 'Karlsruhe',
			'153' => 'Kassel',
			'85' => 'Kaufbeuren-Ostallgäu',
			
			'76' => 'Kelheim',
			'86' => 'Kempten/Oberallgäu',
			'125' => 'Kiel',
			'171' => 'Kleve',
			'172' => 'Köln',
			'18' => 'Konstanz',
			'173' => 'Krefeld',
			'68' => 'Kronach',
			
			'69' => 'Kulmbach',
			'146' => 'Lahn-Dill',
			'43' => 'Landshut',
			'213' => 'Leipzig',
			'174' => 'Leverkusen/Rhein-Berg',
			'70' => 'Lichtenfels',
			'194' => 'Limburg-Weilburg-Diez',
			'87' => 'Lindau/Westallgäu',
			
			'175' => 'Lippe-Detmold',
			'241' => 'Lissabon/Portugal',
			'19' => 'Ludwigsburg',
			'120' => 'Lübeck',
			'176' => 'Lüdenscheid',
			'126' => 'Lüneburg-Wolfsburg',
			'242' => 'Madrid/Spanien',
			
			'220' => 'Magdeburg',
			'94' => 'Main-Spessart',
			'199' => 'Mainz (Rheinhessen)',
			'29' => 'Mannheim-Ludwigshafen',
			'154' => 'Marburg',
			'88' => 'Memmingen/Unterallgäu',
			'177' => 'Mittel-Lenne',
			'30' => 'Mittelfranken',
			
			'196' => 'Mittelrhein',
			'178' => 'Mönchengladbach',
			'58' => 'Mühldorf',
			'229' => 'Mühlhausen',
			'59' => 'München',
			'89' => 'Neu-Ulm',
			'127' => 'Neubrandenburg',
			
			'60' => 'Neuburg/Donau',
			'77' => 'Neumarkt',
			'130' => 'Neumünster',
			'180' => 'Neuss',
			'39' => 'Niederbayern',
			'181' => 'Niederberg',
			'129' => 'Nienburg/Weser',
			'179' => 'Nord Westfalen',
			
			'128' => 'Nordfriesland',
			'161' => 'Nordrhein-Westfalen',
			'22' => 'Nordschwarzwald',
			'36' => 'Nürnberg',
			'48' => 'Oberbayern',
			'182' => 'Oberberg',
			'63' => 'Oberfranken',
			'73' => 'Oberpfalz',
			
			'155' => 'Offenbach',
			'134' => 'Oldenburg',
			'20' => 'Ortenau',
			'246' => 'Oslo/Norwegen',
			'135' => 'Osnabrück',
			'131' => 'Ostfriesland und Papenburg',
			'132' => 'Ostholstein',
			'236' => 'Ostprignitz-Ruppin',
			
			'183' => 'Ostwestfalen',
			'21' => 'Ostwürttemberg',
			'184' => 'Paderborn+Höxter',
			'44' => 'Passau',
			'136' => 'Pinneberg',
			'197' => 'Pirmasens-Zweibrücken',
			'214' => 'Plauen-Vogtland',
			
			'243' => 'Porto/Portugal',
			'104' => 'Potsdam',
			'45' => 'Regen',
			'78' => 'Regensburg',
			'23' => 'Rems-Murr',
			'185' => 'Remscheid',
			'137' => 'Rendsburg',
			'24' => 'Reutlingen',
			'198' => 'Rhein-Ahr',
			
			'200' => 'Rhein-Hunsrück',
			'189' => 'Rheinland-Pfalz',
			'95' => 'Rhön-Grabfeld',
			'61' => 'Rosenheim',
			'123' => 'Rostock',
			'235' => 'Rotenburg/Verden',
			'46' => 'Rottal-Inn',
			'138' => 'Rügen',
			
			'248' => 'Rupertiwinkel',
			'231' => 'Saalfeld/Rudolstadt',
			'205' => 'Saarland',
			'206' => 'Saarland',
			'207' => 'Sachsen',
			'216' => 'Sachsen-Anhalt',
			'244' => 'Sao Paulo/Brasilien',
			'139' => 'Schaumburg',
			'140' => 'Schleswig',
			
			'37' => 'Schwabach',
			'81' => 'Schwaben',
			'79' => 'Schwandorf',
			'25' => 'Schwarzwald-Baar-Heuberg',
			'96' => 'Schweinfurt',
			'141' => 'Schwerin',
			'201' => 'Sieg-Westerwald',
			'186' => 'Solingen e.V.',
			'2' => 'Sonneberg-Neuhaus',
			
			'142' => 'Stade',
			'124' => 'Steinburg',
			'115' => 'Stralsund/Nordvorpommern',
			'47' => 'Straubing',
			'26' => 'Stuttgart',
			'237' => 'Südholstein',
			'232' => 'Südthüringen',
			
			'187' => 'Südwestfalen',
			'105' => 'Teltow-Fläming',
			'222' => 'Thüringen',
			'245' => 'Tokyo/Japan',
			'62' => 'Traunstein',
			'202' => 'Trier',
			'27' => 'Ulm',
			
			'90' => 'Unterfranken',
			'157' => 'Waldeck-Frankenberg',
			'80' => 'Weiden',
			'38' => 'Weissenburg',
			'221' => 'Wernigerode',
			'158' => 'Werra-Meißner',
			'203' => 'Westerwald/Lahn',
			'159' => 'Wetzlar',
			
			'160' => 'Wiesbaden',
			'5' => 'WJD',
			'238' => 'WJD ALK',
			'204' => 'Worms',
			'97' => 'Würzburg',
			'188' => 'Wuppertal',
			'215' => 'Zwickau',
			'143' => 'Zwischen Hunte und Weser'
			);
		
	}
	
	public function admin(){
		add_options_page('WJD Termine', 'WJD Termine', 10, 'wjdTermine/'.basename( __FILE__ ) , array('wjdTermine', 'adminPage') );
	}
	
	public static function getOptionPage(){
		return get_option('siteurl') . '/wp-admin/options-general.php?page=wjdTermine/wjdTermine.php';
	}
	
	public function adminPage(){
		$t = new wjdTermine();
		$jahr = isset( $_GET['jahr'] ) ? $_GET['jahr'] : date( 'Y' );
		
		echo '<div class="wrap"><h2>WJD Termine</h2>';
		echo '<p>
				<a href="'.self::getOptionPage().'&edit=0">Neuer Termin</a> |
				Termine anzeigen: 
				<a href="'.self::getOptionPage().'&jahr='.($jahr-1).'">&laquo; '.($jahr-1).'</a> -
				<b><a href="'.self::getOptionPage().'">'.($jahr).'</a></b> -
				<a href="'.self::getOptionPage().'&jahr='.($jahr+1).'">'.($jahr+1).' &raquo;</a> |
			</p>
			<hr />
				';

		
		if( isset( $_GET['edit'] ) ){
			if( $_POST ){
				
				if( get_magic_quotes_gpc() ) $_POST = array_map( 'stripslashes', $_POST );
				
				$t = new wjdTermine( $_POST );
				if( (int) $_GET['edit'] != 0 ){
					$t->id = (int) $_GET['edit'];
				}
				$t->save();
				if( $t->hasErrors() ){
					echo 'Fehler!';
				}else{
					$ok = true;
					echo '<h2>Die Daten wurden gespeichert.</h2>';
				}
			}else{
				if( $_GET['edit'] != 0 ) $_POST = (array) wjdTermine::get( $_GET['edit'] );
			}
			if( !$ok) echo $t->getForm();
		}else if( isset( $_GET['delete'] ) ){
			wjdTermine::delete( $_GET['delete'] );
			echo '<h2>Der Datensatz wurde gelöscht.</h2>';
		}else if( $_POST ){
			self::syncWithWJD( $_POST['user'], $_POST['pwd'], $_POST['org_id'] );
		}else{
			echo $t->getTermineAsTable( $_GET['jahr'] );
		}
		echo '</div>';
	}
	
	public function getTermineAsTable( $jahr = null ){
		if( $jahr == null ) $jahr = date('Y');
		
		$termine = $this->getTermine( $jahr );

		$out .= '<table class="widefat">
					<thead>
						<tr>
							<th>Werkzeuge</th>
							<th>Schlagzeile</th>
							<th>Datum</th>
						</tr>
					</thead>
				';	
		foreach( $termine as $termin ){
			$out .= '
			<tr>
				<td>
					<a href="'.self::getOptionPage().'&edit='.htmlspecialchars($termin->id).'">Bearbeiten</a> | 
					<a href="'.self::getOptionPage().'&delete='.htmlspecialchars($termin->id).'" onclick="return confirm(\'Wirklich löschen? Dies kann nicht rückgängig gemacht werden!\');">Löschen</a>
				</td>
				<td>'.htmlspecialchars( $termin->schlagzeile ).'</td>
				<td>'.htmlspecialchars( $termin->date_start ).'</td>
			</tr>
			';
		}	
		$out .= '</table>';
		return $out;		
	}
	
	public function getTermineAsList( $jahr = null ){

		if( $jahr == null ) $jahr = date('Y');

		$kreise = self::getOrgIds();
		
		$termin = new wjdTermine();
		
		$termine = $this->getTermine( $jahr, 'ASC' );
		
		$now = time();
		
		$dp = self::getDispatchMap();
		
//		$month = (int) date('m');
//		if( $month > 6 ) $termine = array_reverse( $termine );
		

		foreach( $termine as $termin ){
			
			$event = ''."\n";
			$datum = date( 'd.m.Y', strtotime( $termin->date_start.' '.$termin->time_start ) );
			
			$class = strtotime( $termin->date_start ) < $now ? 'past_event' : 'future_event';
			$event .= '<div class="'.$class.'" style="border-top:0px solid #999;margin-bottom: 15px;">'."\n";
			
			$event .= self::showTermin( $termin );
			$event .= "\n";
			
			$event .= '</div>'."\n"; // class past|future_event
			
			if( strtotime( $termin->date_start.' '.$termin->time_start ) < ( $now - 8*60*60 ) ){
				$past .= $event;
			}else{
				$future .= $event;
			}
		}
		
		if( $future ) $out .= '<div class="future_event"><h2>Anstehende Termine</h2></div>';
		$out .= $future;
		if( $past ) $out .= '<div class="past_event"><h2>Vergangene Termine</h2></div>';
		$out .= $past;
		return $out;		
	}

	function showTermin( $termin ){

		$event .= '<p class="eventname">';

			$event .= '<strong>';
			$event .= htmlspecialchars( $termin->schlagzeile );
			$event .= '</strong>';

			$event .= '<br />';
			$event .= 'Start: '.date( 'd.m, H:i', strtotime( $termin->date_start.' '.$termin->time_start ) ).' Uhr';

		$event .= '</p>'."\n";

		$event .= '<div class="termindetail" id="termindetail_'.$termin->id.'">';

			$event .= ''.nl2br( htmlspecialchars( $termin->thema ) ).'<br /><br />';

			$event .= 'Kategorie: '.htmlspecialchars( $dp['kat_id']['options'][$termin->kat_id] . ' / ' . $dp['ber_id']['options'][$termin->ber_id] ).'<br />'.
					  'Anschrift: '.htmlspecialchars($termin->anschrift).'<br />'.
					  'Ort: '.htmlspecialchars($termin->ort).'<br />'
					  ;

			$an = $termin->kontakt ? $termin->kontakt : $termin->author;
			$ae = $termin->kontakt_email ? $termin->kontakt_email : $termin->email;

			if( $termin->link_extern ) {
				$tmp = parse_url( $termin->link_extern, PHP_URL_HOST );
				$target = ( $tmp != $_SERVER['HTTP_HOST'] ) ? '_blank' : '_self';
				$event.= 'Weitere Infos: <a target="'.$target.'" href="'.$termin->link_extern.'">&raquo;'.htmlspecialchars($tmp).'</a><br /><br />';
			}

			if( self::useTermineFromWJD() ){
				
			}else{
				$event .= 'Termin runterladen als <a href="'.htmlspecialchars( self::getiCalUri()).'?getTerminAsICal='.$termin->id.'">iCal (.ics) Datei</a> für Outlook, Apple Mail, etc.';
			}

			if( strtotime( $termin->date_start ) > time() && !self::useTermineFromWJD() ){
				$event .= '<p><a class="more-link" style="width:100px;" href="/termine/?anmelden='.$termin->id.'">Jetzt Anmelden &raquo;</a></p>';
			}

		$event .= '</div>'; // class=termindetail

		return $event;
	}
	
	public static function sendiCalHeader(){
		header("Content-Type: text/Calendar");
		header("Content-Disposition: inline; filename=wjdTermine.ics");
	}
	
	private static function cleanUpForiCal( $str ){
		return str_replace(
			array( '-', ':', '"', chr(13).chr(10) ),
			array( '', '', '', " " ),
			$str
		);
	}
	
	public static function getTermineAsiCal( $id = null ){
		
		$kreise = self::getOrgIds();
		
		if( $id === null ){
			$termine = self::getTermine( null, ASC, true );
		}else{
			try{
				$termine = array( wjdTermine::get( $id ) );
			}catch( Exception $e ){
				$termine = array();
			}
		}
		
		$out .= "BEGIN:VCALENDAR\n";
		$out .= "VERSION:2.0\n";
		$out .= "PRODID:wjdTermine\n";
		$out .= "METHOD:REQUEST\n";
		
		$tz = new DateTimeZone("Europe/Berlin");
    
		$termin = new wjdTermine();
		foreach( $termine as $termin ){
		    
			$anfangO = new DateTime( $termin->date_start . ' ' . $termin->time_start );
			$anfangS = date( 'Ymd\THis', $anfangO->format( 'U' ) - $tz->getOffset( $anfangO ) );
					
			$endeO   = $termin->date_end == '0000-00-00' ? $anfangO : new DateTime( $termin->date_start . ' ' . $termin->time_start );
			$endeS   = date( 'Ymd\THis', $endeO->format( 'U' ) - $tz->getOffset( $endeO ) );
			
			$out .= "BEGIN:VEVENT\n";

		    $out .= "DTSTART:".$anfangS."Z\n";
		    $out .= "DTEND:".$endeS."Z\n";
		
		    $out .= "DESCRIPTION;ENCODING=QUOTED-PRINTABLE: ";
		    if( $useWJD ) $out .= 'Kreis: '.$kreise[ $termin->kreis ].". ";
			// FIXME Bei Daten von WJD stehen in Outlook manchmal = Zeichen vor einzelnen Zeilen
		    $out .= preg_replace( 
						"#\r\n|\n|\r#" , 
						"=0D=0A=", 
						$termin->thema 
					)."\n";
		    
		    $out .= "SUMMARY:".self::cleanUpForiCal( $termin->schlagzeile );
		    $out .= "\n";
		    
		    $out .= "UID:wjdTermin_{$termin->id}\n";
		    $out .= "SEQUENCE:0\n";
		    $out .= "DTSTAMP:".date('Ymd').'T'.date('His')."\n";
		    $out .= "END:VEVENT\n";
		}
		$out .= "END:VCALENDAR\n";
		return $out;
	}
	
	public static function sendiCal( $id = null ){
		if( !headers_sent() ){
			wjdTermine::sendiCalHeader();
		}
		echo wjdTermine::getTermineAsiCal( $id );
	}
	/**
	 * Ermittelt Termine
	 *
	 * @param int $jahr
	 * @param string Sortierrichtung ASC | DESC, default DESC
	 * @param bool Nur neuere Werte anzeigen, default false
	 * @param int Limit, default 0 = ohne Limit 
	 * @return wjdTermine
	 */
	public function getTermine( $jahr = null, $sortDir = 'DESC', $onlyNewer = false, $limit = 0 ){
		global $wpdb;
//		if( $jahr == null || !is_numeric( $jahr )) $jahr = date('Y');
		
		$sql = "SELECT * 
				FROM ".$wpdb->prefix."wjd_termine 
				WHERE 1=1
				".( $jahr == null ? '' : " AND DATE_FORMAT( date_start, '%Y' ) = '".$wpdb->escape($jahr)."'" )."
				".( $onlyNewer ? ' AND date_start >= DATE_SUB( NOW(), INTERVAL 1 DAY ) ' : '' )."
				ORDER BY date_start ".$sortDir." 
				".($limit>0 ? 'LIMIT '.$limit : '' )."
				";
		$rs = $wpdb->get_results( $sql , ARRAY_A);
		if( $rs == null ) return array();
		
		foreach( $rs as $v ){
			$out[] = new wjdTermine( $v );
		}
		return $out;
	}
	public static function useTermineFromWJD(){
		return (bool) file_exists( self::getWJDTermineConfFile() );
	}
	
	private static function getWJDTermineConfFile(){
		return dirname(__FILE__).'/wjd.de.php';
	}
	
	public function getTermineFromWJD( $jahr = null, $sortDir = 'DESC', $onlyNewer = false, $limit = 0 ){
		include( self::getWJDTermineConfFile() );
		
		if( $jahr == null || $onlyNewer ){
			$date = date('d.m.Y', time()-86400 );
			$end  = date('31.12.Y');
		}else{
			$date = '01.01.'.$jahr;
			$end  = '31.12.'.$jahr;
		}
		
		$endUnix = strtotime( $end );
		
		$uri  = 'https://www.wjd.de/intern/schnittstelle/termin.php?';
		$uri .= 'user='.urlencode( $user ).'&pwd='.urlencode( $pass ).'&action=2&';
		$uri .= 'org_id='.urlencode( $kreis ).'&org_sub=j&';
		$uri .= 'date_start='.$date.'&';
		// FIXME date_end wird scheinbar von der Schnittstelle nicht verwendet.
		$uri .= 'date_end='.$end.'&';
		
//		var_dump( $uri );
		$xml = @simplexml_load_string( @file_get_contents( $uri ) );
		
		$out = array();
		$c   = 1;
		
		foreach( (array) $xml->TERMIN as $item ){
			// FIXME date_end wird scheinbar von der Schnittstelle nicht verwendet.
			if( strtotime( (string) $item->date_end ) > $endUnix ) continue;
			
			$tmp = array(
				// Fiktive ID, wird für toggle() im Frontend verwendet.
				'id' => 'e'. (string) $item->extern_ID . '_'.$c, 
				'kat_id' => (string)   $item->kat_ID ,
				'ber_id' => (string)   $item->ber_ID ,
				'schlagzeile' => (string)   $item->schlagzeile ,
				'thema' => (string)   $item->thema ,
				'ort' => (string)   $item->ort ,
				'anschrift' => (string)   $item->anschrift ,
				'date_start' => (string)   $item->date_start ,
				'date_end' => (string)   $item->date_end ,
				'time_start' => (string)   $item->time_start ,
				'time_end' => (string)   $item->time_end ,
				'link' => (string)   $item->link ,
				'link_extern' => (string)   $item->link_extern ,
				'author' => (string)   $item->author ,
				'email' => (string)   $item->email ,
				'kontakt' => (string)   $item->kontakt ,
				'kontakt_email' => (string)   $item->kontakt_email ,
				'kreis' => (string) $item->org_ID
			);
			$out[] = new wjdTermine( $tmp );
			$c++;
		}
		return $out;
	}
	
	public function getFormSelect( $k, $options, $width = '98%' ){
		$out .= '<select style="width: '.$width.';" id="'.$k.'" name="'.$k.'">';
		$out .= '<option value="">-- Bitte wählen --</option>';
		foreach( $options as $sk=>$sv ){
			$out .= '<option value="'.htmlspecialchars($sk).'" '.($_POST[$k]==$sk?'selected="selected"':'').'>'.htmlspecialchars($sv).'</option>';
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
		return array( 'id', 'kreis' );
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
				<th scope="row">Termin speichern</th>
				<td><input type="submit" value="Speichern" /></td>
			</tbody></table></form>';
		$data = get_option( 'wjdTerminAnmeldung'.$_GET['edit'] );

		if( $data ){

			$keys = array_keys( current($data) );

			$out .= '<h2>Anmeldungen</h2>';
			$out .= '<table class="widefat">';
			$out .= '<thead><tr>';
			foreach( $keys as $v ){
				if( $v == 'Anmelden' ) continue;
				$out .= '<th>'.htmlspecialchars( $v ).'</th>';
			}
			$out .= '</tr></thead><tbody>';
			foreach( $data as $v ){
				$out .= '<tr>';
				foreach( $keys as $k ){
					if( $k == 'Anmelden' ) continue;
					$out .= '<td>'.htmlspecialchars($v[$k]).'</td>';
				}
				$out .= '</tr>';
			}
			$out .= '</tbody></table>';
		}
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
		$sql = "SELECT * FROM ".$wpdb->prefix."wjd_termine WHERE id = '".$wpdb->escape($id)."'";
		$rs = $wpdb->get_row( $sql , ARRAY_A);
		if( $rs == null ) throw new Exception( "Unbekannter Datensatz mit der ID " . $id );
		return new wjdTermine( $rs );
	}
	
	public static function delete( $id ){
		global $wpdb;
		$sql = "DELETE FROM ".$wpdb->prefix."wjd_termine WHERE id = '".$wpdb->escape($id)."'";
		$rs = $wpdb->query( $sql , ARRAY_A);
		if( $rs == null ) return false;
		self::logSyncAction( 'delete', $id );
		return true;
	}
	

	
	public static function syncWithWJD( $user, $pwd, $orgId ){

		$host = 'www.wjd.de';
		$path = '/intern/schnittstelle/termin.php';
		$target = 'https://'.$host.$path;
		
		$baseQuery = array(
			'user' => $user,
			'pwd' => $pwd,
			'org_id' => $orgId
		);
		$termin = null;
		
		$sendOnly = array(		
			'user',
			'pwd',
			'action',
			'extern_id',
			'org_id',
			'kat_ID',
			'ber_id',
			'schlagzeile',
			'thema',
			'ort',
			'date_start',
			'date_end',
			'time_start',
			'time_end',
			'link',
			'link_extern',
			'author',
			'email',
			'kontakt',
			'kontakt_email',
			'extern_id'
		);
		
		$actions = self::getSyncAction();
		if( sizeof( $actions ) == 0 ){
			echo '<h2>Keine offenen Aufgaben</h2><p>Synchronisation nicht notwendig.</p>';
			return;
		}
		echo '<h2>Starte Synchronisation</h2>';
		echo '<p>'.sizeof( $actions ).' offene Aufgabe(n).</p>';
		
		foreach( $actions as $item_index=>$item ){
			$query = $baseQuery;
			$query['extern_id'] = $item['id'];
			
			try{
				$termin = wjdTermine::get( $item['id'] );
			}catch( Exception $e ){
				$termin = null;
			}
			
			if( $item['action'] == 'delete' ){
				
				$query['action'] = 4;
				echo 'Lösche Veranstaltung mit der ID '.$item['id'].'<br />';
				
			}else if( $item['action'] == 'add' || $item['action'] == 'edit' ){
				
				$query['action'] = ($item['action'] == 'add') ? 1 : 3;
				
				if( $termin == null ) {
					echo '<p>Hinzufügen/Bearbeiten nicht möglich. Termin existiert nicht mehr. Überspringe Aufgabe.</p>';
					echo '<hr />';
					unset( $actions[$item_index] );
					continue; 
				}
				
				echo ($item['action'] == 'add' ? 'Veröffentliche' : 'Aktualisiere' );
				echo ' #'.$item['id'].': '.$termin->schlagzeile.'<br />';
				$query += array_map( 'utf8_decode', (array) $termin );
				
				// Cleanup
				$query['kat_ID'] = $query['kat_id'];
				unset( $query['kat_id'] );
				unset( $query['id'] );
				
				if( $query['date_end'] == '0000-00-00' ){
					$query['date_end'] = '';
					$query['time_end'] = '';
				}else{
					$query['date_end'] = preg_replace( '#^(\d{4})-(\d{2})-(\d{2})$#', '$3.$2.$1', $query['date_end'] );
				}
				$query['date_start'] = preg_replace( '#^(\d{4})-(\d{2})-(\d{2})$#', '$3.$2.$1', $query['date_start'] );
				
				foreach( $query as $k=>$v ){
					#$v = str_replace(chr(13).chr(10),"  ", $v );
					#$v = strlen( $v ) > 512 ? substr( $v, 0, 512 ).'...' : $v;
					$query[$k] = $v;
				}
				
			}
			
			#$uri = $target . '?' . http_build_query( $query );
			#$rs = file_get_contents( $uri );
			
			
			$uri = $target . '?' . http_build_query( $query );http_build_query( $query );
			$ch = curl_init( $uri );
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			$rs = curl_exec($ch);      
			curl_close($ch);

//			// Problem mit file_get_contents. Mit curl vermutlich gefixt. 
//			if( strlen( $uri ) > 1024 ){
//				echo '<h2>
//						WARNUNG: Die Länge der zu übertragenden Zeichen überschreitet 1024. 
//						Dies kann zu einem Fehler führen. Kürzen Sie bitte ggf. die
//						Texte.
//						</h2>
//					';
//			}

			$code = (int) strip_tags( $rs );
			$msg  = utf8_encode( strip_tags( $rs ) );
			
			// Fehler über 100 sind Statusmeldungen. 5 Bedeutet, dass
			// der Datensatz bei WJD nicht mehr existiert. Für uns ist das 
			// unkritisch und kann als Erfolg gewertet werden.
			if( is_numeric( $code ) && ( $code >= 100 || $code == 5 ) ){
				echo 'Ok! '.strlen( $uri ).' Zeichen gesendet. '.strip_tags( $msg );	
			}else{
				echo '<strong>Fehler beim Senden von '.strlen( $uri ).' Zeichen: '.$code.'</strong> ('.$msg.') ';
				$errors++;
			}

//// Debug
//				echo '<br /><small>'.$uri.'</small><br />Debug-Info: ';
//				foreach( array_map( 'utf8_encode',  $query ) as $k=>$v ){
//					echo htmlspecialchars( $k.': '.$v) .'<br />---<br />';
//				}

			echo '<hr />';
			if( $errors ){
				echo '<p>
						<strong>Es sind Fehler aufgetreten. Die Übertragung wird abgebrochen!</strong>
					  </p>
						';
				break;
			}
			unset( $actions[$item_index] );
		}
		
		// Aufgaben zurücksetzen
		self::resetSyncFile();
		
		echo '<h2>Fertig</h2>';
		
		// Falls Fehler aufgetreten sind, werden die restlichen 
		// Aufgaben zurückgeschrieben
		if( $errors ){
			foreach ($actions as $item) {
				self::logSyncAction( $item['action'], $item['id'] );
			}
		}
	}
	
	private function logSyncAction( $action, $id ){
		file_put_contents( 
			self::getSyncFile(),
			trim( $action ) . ':' . (int) $id . "\n",
			FILE_APPEND 
		);
		chmod( self::getSyncFile(), 0777 );
	}
	
	private function getSyncAction(){
		$actions = file( self::getSyncFile() );
		$rs = array();
		foreach( $actions as $a ){
			if( trim( $a ) == '' ) continue;
			$tmp = explode( ':', $a );
			$rs[] = array( 'action' => $tmp[0], 'id' => $tmp[1] );
		}
		return $rs;
	}
	
	private function getSyncFile(){
		return dirname( __FILE__ ) . '/syn.data';
	}
	
	private function resetSyncFile(){
		file_put_contents( self::getSyncFile(), '' );
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
		
		$table_name = $wpdb->prefix . "wjd_termine";

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
				self::logSyncAction( 'edit', $this->id );
			}else{
				$id = $wpdb->get_var( 'SELECT LAST_INSERT_ID()' );
				self::logSyncAction( 'add', $id );
			}
		}
	}
	
	public function table_install() {
		global $wpdb;

		$table_name = $wpdb->prefix . "wjd_termine";
		if($wpdb->get_var("show tables like '$table_name'") != $table_name) {
		  
			$sql = "CREATE TABLE " . $table_name . " (
				id int NOT NULL AUTO_INCREMENT,
				kat_id int,
				ber_id int,
				schlagzeile varchar(255),
				thema text,
				ort varchar(255),
				anschrift text,
				date_start date,
				date_end date,
				time_start time,
				time_end time,
				link varchar(255),
				link_extern varchar(255),
				author varchar(255),
				email varchar(255),
				kontakt varchar(255),
				kontakt_email varchar(255),
				kreis int,
				PRIMARY KEY id (id)
			)";

			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
			dbDelta($sql);
		}
	}

	private function showTermine(){
		$jahr = $_GET['jahr'] ? $_GET['jahr'] : date('Y');
		$t = new wjdTermine();

		$out .= '
			<p>
				Termine anzeigen:
				<a href="?jahr='.($jahr-1).'">&laquo; '.($jahr-1).'</a> -
				<b><a href="?jahr='.($jahr).'">'.($jahr).'</a></b> -
				<a href="?jahr='.($jahr+1).'">'.($jahr+1).' &raquo;</a>
				|
				Alle Termine als
				<a href="'.self::getiCalUri().'">&raquo;iCal Kalender</a>
				für z.B. Outlook, Lotus, etc.
			</p>
			';

		$out .= $t->getTermineAsList( $jahr);
		return $out;
	}

	private function showForm( $id ){

		try{
			$t = self::get( $id );
		}catch( Exception $e ){
			return '<p style="font-size: 18px;">Unbekannter Termin.</p>';
		}
		
		if ( strtotime( $t->date_start ) < time() ) {
			return '<p style="font-size: 18px;">Eine Anmeldung ist nicht mehr möglich.</p>';
		}
		
		if ( self::useTermineFromWJD() ) {
			return '<p style="font-size: 18px;">Eine Anmeldung ist nur bei dem Kreis möglich.</p>';
		}

		$fields = array(
			'Teilnehmer' =>	'Teilnehmer (Vorname, Nachname)' ,
			'Begleitperson' => 'Begleitperson (Vorname, Nachname)',
			'Firma' => 'Firma',
			'WJKreis' => 'WJ Kreis',
			'Telefon' => 'Telefon',
			'Email' =>	'E-mail'
				);
		if( $_POST ){
			
			$subject = 'WJ Anmeldung - Event '.$t->schlagzeile.' am '.$t->date_start;

			if( $t->email ) $to[] = $t->email;
			if( $t->kontakt_email ) $to[] = $t->kontakt_email;
			if( !$to ) return '<p>Zu dieser Veranstaltung kann leider keine Anmeldung erstellt werden.';

			$to = implode( ',', $to );

			$msg .= "Anmeldung zur Veranstaltung ".$t->schlagzeile." am ".$t->date_start."\n\n";
			foreach( $fields as $k=>$v ){
				$msg .= $v.': '.$_POST[$k]."\n";

				if( $k == 'Begleitperson' ) continue;
				if( !$_POST[$k] ) $errors[ $k ] = 'Bitte füllen Sie das Feld aus.';
			}
			if( !$errors ){
				
				$data = get_option( 'wjdTerminAnmeldung'.$t->id );
				$data[] = $_POST;
				$rs = update_option( 'wjdTerminAnmeldung'.$t->id, $data );
				
				mail( $to, utf8_decode( $subject ), utf8_decode( $msg ) );
				return '<h2>Vielen Dank für Ihre Anmeldung!</h2>';
			}
		}

		$out .= '<form action="" method="post">';
		$out .= '<h2>Anmeldung zur Veranstaltung '.htmlspecialchars($t->schlagzeile).'</h2>';
		foreach( $fields as $k=>$v ){
			if( $errors[$k] ) $out .= '<strong style="color:red;">'.htmlspecialchars( $errors[$k] ).'</strong><br />';
			$out .= '<label for="'.htmlspecialchars( $k).'">'.htmlspecialchars( $v).'</label><br />';
			$out .= '<input type="text" value="'.htmlspecialchars( $_POST[$k] ).'" id="'.htmlspecialchars( $k).'" name="'.htmlspecialchars( $k).'" /><br />';
			$out .= '<br />';
		}

		$out .= '<input type="submit" id="Anmelden" name="Anmelden" /><br />';

		$out .= '';
		$out .= '</form>';
		return $out;
	}

	public function wjTermineInSite( $content ){
		
		$regex = '#{WJD_TERMIN_(\d)}#';

		if( strstr( $content, '{WJD_TERMINE}' ) ) {
			if( $_GET['anmelden'] ){
				$out .= self::showForm( $_GET['anmelden'] );
			}else{
				$out = self::showTermine();
			}
			return str_replace( '{WJD_TERMINE}', $out, $content );
		}else if( preg_match( $regex, $content, $id ) ){
			try{
				$t = self::get( $id[1] );
				$out = self::showTermin( $t );
			}catch( Exception $e ){
				$content = '<p>Dieser Termin existiert nicht.</p>';
			}
			return preg_replace( $regex,$out, $content );
		}else{
			return $content;
		}
	}
	
	private static function getiCalUri(){
		return get_option('siteurl') . '/wp-content/plugins/wjdTermine/iCal.php';
	}
	
	public function sidebar_Termine(){
		register_sidebar_widget(array('WJD', 'widgets'), array('wjdTermine', 'getNextTermineAsWidget'));
	}
	
	public static function getNextTermineAsWidget(){
		$termin = new wjdTermine();
		$anzahl = 5;
		
		$kreise = self::getOrgIds();
		
		$termine = self::getTermine( date('Y'), 'ASC', true, $anzahl );
				
		$target = get_option('siteurl').'/termine/#termindetail_';
		
		$out .= '<h2 class="widgettitle">Die nächsten Termine</h2><ul>';
		if( sizeof( $termine ) == 0 ){ $out .= '<li>Zur Zeit sind keine Termine geplant.</li>'; }
		foreach( $termine as $termin ){
			$c++;
			if( $c > $anzahl ) break;
			
			$out .= '<li><a href="'.$target.$termin->id.'" class="eventname">';
			$out .= $termin->schlagzeile;
			$out .= '</a>';
			$out .= '<br />';
			$out .= '<small>';
			$out .= date('d.m.Y \a\b H:i', strtotime( $termin->date_start.' '.$termin->time_start ) );
			$out .= ' Uhr in '.$termin->ort;
			$out .= '</small><br /><a href="'.$target.$termin->id.'" class="more-link">mehr &raquo;</a>';
			$out .= '</li>';
		}

		$out .= '</ul>';

		$out .= '<h2 class="widgettitle">Nützliches</h2>';
		$out .= '<ul>';
		$out .= '<li><a href="/feed/">News als RSS-Feed</a></li>';
		$out .= '<li><a href="'.self::getiCalUri().'">Termine als iCal Kalender</a></li>';
		$out .= '</ul>';

		
		echo $out;
	}

} // class
?>