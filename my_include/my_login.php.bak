<?php
/**
 * @author Sebastian Gomez (tiochan@gmail.com)
 * For: Politechnical University of Catalonia (UPC), Spain.
 *
 * @package admin
 *
 * Log-in page.
 */

	include_once "include/init.inc.php";

	global $GLOBAL_HEADERS;
	$GLOBAL_HEADERS["cas_css"]="<link rel='stylesheet' type='text/css' media='all' href='". HOME . "/my_include/styles/tot.css' />";


	/**
	 * Look for this user as internal user in our sigvi.user table.
	 */
	function is_userid_registered($username, $id_user, &$name, &$external, &$level, &$group, &$group_name, &$def_lang, &$email) {

		global $global_db;

		$ok=false;	

		$query="
			select u.id_user, u.name, u.external, u.level, u.id_group, u.lang, g.name, u.email
			from users u, groups g
			where username='$username' and
				u.id_group = g.id_group";

		$res=$global_db->dbms_query($query);

		if($global_db->dbms_check_result($res)) {
			$row=$global_db->dbms_fetch_row($res);
			$id_user=$row[0];
			$name=$row[1];
			$external=$row[2];
			$level=$row[3];
			$group=$row[4];
			$def_lang=$row[5];
			$group_name=$row[6];
			$email=$row[7];

			$global_db->dbms_free_result($res);
			$ok=true;
		}

		return $ok;
	}


	// MAIN
	function authenticate_id($unique_key) {

		global $global_db;

		$ok= false;

		$query="SELECT m.user_id, u.username from mark_keys m, users u where m.user_key='$unique_key' and m.user_id=u.id_user";
		$res= $global_db->dbms_query($query);
		if(!$global_db->dbms_check_result($res)) {
			html_showError("Key not found");
			return false;
		}

		$row= $global_db->dbms_fetch_row($res);
		$global_db->dbms_free_result($res);

		$id_user= $row[0];
		$username= $row[1];

		if(is_userid_registered($username, $id_user, $name, $external, $level, $group, $group_name, $def_lang, $email)) {
			$_SESSION[APP_NAME . '_realname']=$name;
			$_SESSION[APP_NAME . '_username']=$username;
			$_SESSION[APP_NAME . '_level']=$level;
			$_SESSION[APP_NAME . '_group']=$group;
			$_SESSION[APP_NAME . '_group_name']=$group_name;
			$_SESSION[APP_NAME . '_id_user']=$id_user;
			$_SESSION[APP_NAME . '_def_lang']=$def_lang;
			$_SESSION[APP_NAME . '_logged']=true;
			$_SESSION[APP_NAME . '_remote_addr']= $_SERVER['REMOTE_ADDR'];
			$_SESSION[APP_NAME . '_user_email']= $email;

			$ok= true;
		}

		if($ok) {
			log_write("AUTH","User logged in from ID: $username",0);
		} else {
			log_write("AUTH","User ID authentication failed (username: $username)",0);
		}

		return $ok;
	}


	/////////////////// BEGIN /////////////

	$login_result=false;

	if(isset($_POST["next_page"])) {
		$next_page=stripslashes($_POST["next_page"]);
	} else {
		if(isset($_GET["next_page"])) {
			$next_page=stripslashes($_GET["next_page"]);
		} else {
			$next_page= HOME . "/index.php";
		}
	}

	function showMyForm($username, $error) {

		global $MESSAGES;
		global $next_page;

?>
		<form id="fm1" name="login" action=<?php echo $_SERVER["PHP_SELF"]; ?> method="post">

			<div class="all">
				<div class="header">
		    		<div id="container">
			    		<div id="content"><a href="http://www.upcnet.es" target="_blank"><img src="<?php echo HOME; ?>/include/images/logo1.png" border="0" alt="UPCnet"></a></div>
<!--  					<div id="content" align="right"><a href="http://www.upc.edu" target="_blank"><img height='55' src="<?php echo HOME; ?>/include/images/logo2.png" border="0" alt="UPC"></a></div>-->
						<div id="content" align="right"><a href="http://www.upc.edu" target="_blank"><img height='55' src="<?php echo HOME . APP_LOGO;?>" border="0" alt="UPC"></a></div>
					</div>
			    </div>
			    <!--[if lte IE 6]>
			    <div class="message">
				    <div class="portalMessage error">
			    		<h2>Navegador massa antic</h2>
			    		<p>Ja no donem suport al teu navegador.</p>
					</div>
			    </div>
			    <div style="display:none !important;">
			    <![endif]-->
				<div class="section">
			    	<div class="dalt">
			    	    <h2>Inicieu la sessi&oacute;</h2>
			            <p class="descripcio">Identifiqueu-vos amb el nom d'usuari de la Intranet UPC.</p>

		            	<div class="formE">
		            		<input type="hidden" name="next_page" value="<?php echo $next_page; ?>">
		            		<input type="hidden" name="first_time" value="0">
				        	<label for="usuari">Usuari</label>
		    			    <input type="text" id="username" name="username" value="<?php echo $username; ?>" />
		    		    	<label for="contrasenya">Contrasenya</label>
				    	    <input type="password" id="password" name="password" />

				    	    <input type="checkbox" id="warn" name="warn" value="true" tabindex="3" />
		        			<input type="hidden" name="lt" value="e2s1" />
		        			<input type="hidden" name="_eventId" value="submit" />

			            </div>

						<div class="formB">
		    				<input type="submit" class="submit" value="Entra" />
				       </div>
				    </div>
			        <div class="baix">
			    	    <h2>No podeu entrar?</h2>

				        <p>Ajuda per al  <a href="http://www.upcnet.es/CanviContrasenyaUPC"> canvi i oblit de contrasenya</a> de la UPC.</p>
			        </div>
			    </div>

			    <!--[if lte IE 6]>
				</div>
			    <![endif]-->
			    <div class="footer">
			    	<p>&copy; <a href="http://www.upc.edu/">UPC <img src="<?php echo MY_IMAGES; ?>/icon_blank.gif" alt="(obriu en finestra nova)" /></a>. Universitat Polit&egrave;cnica de Catalunya. BarcelonaTech.</p>
			    </div>
			</div>



		    <script language="JavaScript" type="text/javascript">
		     <!--

		     document.forms[0].elements["username"].focus();
		     document.forms[0].elements["warn"].style.position = "absolute";
		     document.forms[0].elements["warn"].style.left = "-9000px";

		     // -->
		     </script>
<?php
		if(defined("DEMO_VERSION") && DEMO_VERSION) {
?>
			<table border=0 align="center">
				<tr><td><?php echo $MESSAGES["WANT_REGISTER"]; ?></td></tr>
			</table>
<?php
		}
?>
		</form>
		<script language="JavaScript" type="text/javascript">
		   document.forms.login.elements[0].focus();
		</script>
<?php
	}



	// Is already logged?
	if(isset($_SESSION['logged']) and ($_SESSION['logged']==true)) {
		//echo "user already logged. Redirecting to... $next_page\n";
		header("Location: $next_page");
		exit;
	}

	////////////////////////
	// NOT LOGGED.
	$unique_key= get_http_param("id",0);
	if($unique_key != 0) {
		if(authenticate_id($unique_key)) {
			html_showInfo("User logged, redirecting...\n");
			echo "<script>self.location.href='$next_page'</script>";
			exit;
		}
	}

	// If first time, just show the login form
	if(!isset($_POST["first_time"])) {
		html_header("");
		showMyForm("", "");
		html_showSimpleFooter();
		exit;
	}

	// Not first time, Are vars defined?
	if(isset($_POST["username"]) and isset($_POST["password"])) {
		$login_result=authenticate($_POST["username"], $_POST["password"]);
	}

	// Ok, authentication succesful, redirect to previous page if defined, else Home.
	if($login_result) {
		//header("Location: $next_page", false);
		html_showInfo("User logged, redirecting...\n");
		echo "<script>self.location.href='$next_page'</script>";
		exit;
	}

	// Something wrong... try again

	global $MESSAGES;
	html_header("");
	showMyForm($_POST["username"], $MESSAGES["AUTH_INVALID_AUTH"]);
	html_showSimpleFooter();
?>
