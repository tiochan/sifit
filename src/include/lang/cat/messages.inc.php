<?php
/**
 * @author Sebastian Gomez (tiochan@gmail.com)
 * For: Politechnical University of Catalonia (UPC), Spain.
 *
 * @package lib
 * @subpackage lang
 *
 * Catalan version of application messages
 * Reviewed from Iolanda Garcia
 */

global $MESSAGES;

//////////////////////////////////////////////////////////////////////////////
// GENERAL MESSAGES SECTION                                                //
// APPLICATION MESSAGES ARE AT NEXT SECTION                               //
///////////////////////////////////////////////////////////////////////////


// GENERAL APPLICATION MESSAGES, LITERALS
$MESSAGES["APP_HOME"]="inici";
$MESSAGES["APP_LOGIN_TITLE"]="Identificaci&oacute d'usuari";
$MESSAGES["APP_LOGIN_USERNAME"]="Nom d'usuari";
$MESSAGES["APP_LOGIN_PASSWORD"]="Contrasenya";
$MESSAGES["APP_USER_LEVEL"]="Nivell";
$MESSAGES["APP_ACCEPT"]="Acceptar";
$MESSAGES["APP_LOGOUT_TITLE"]="Sessi&oacute finalitzada";
$MESSAGES["LOADING_DATA"]="Carregant les dades...";

$MESSAGES["MAINTENANCE_MESSAGE"]="<center><img src='" . HOME . "/include/images/icons/messagebox_warning.png'><h2>Estem realitzant tasques de manteniment.</h2><h3>Si us plau, torneu a provar m&eacute;s tard.<br>Disculpeu les mol&egrave;sties.</h3></center>";

// AUTH messages
$MESSAGES["AUTH_INVALID_AUTH"]="Usuari/contrasenya no v&agrave;lid.";
$MESSAGES["AUTH_USER_REQUIRED"]="Cal estar registrat per accedir a aquesta p&agrave;gina.";
$MESSAGES["AUTH_NOT_GRANTED"]="Acc&eacute;s restringit.";
$MESSAGES["AUTH_LOGOUT"]="Sortir";
$MESSAGES["WANT_REGISTER"]="<p>Si no teniu un usuari v&agrave;lid del SIGVI i voleu un per provar aneu a a la <a href='register.php'>p&agrave;gina de registre</a>.</p>";
$MESSAGES["REGISTER_SUCCESS"]=
	"<p><b>Benvingut %s</b></p>." .
	"<p>T'has registrat correctament.</p>" .
	"<p><a href='" . SERVER_URL . HOME . "/index.php'>p&agrave;gina de login</a></p>";

// CHRONOMETER messages
$MESSAGES["CRONO_END_MESSAGE"]="Temps de c&agrave;lcul: %.3f segons";

$MESSAGES["CURRENCY"]="€";

// GENERAL management
$MESSAGES["MAIN_MENU"]="Menu principal";
$MESSAGES["APP_ADMIN"]="Administraci&oacute;";
$MESSAGES["APP_CONF"]="Configuraci&oacute;";
$MESSAGES["ACCEPT"]="Acceptar";
$MESSAGES["NEW"]="+";
$MESSAGES["NEW_INFO"]="Crear un nou registre";
$MESSAGES["CREATE"]="Crear";
$MESSAGES["MODIFY"]="Modificar";
$MESSAGES["MODIFY_INFO"]="Modificar el registre";
$MESSAGES["DELETE"]="Esborrar";
$MESSAGES["DELETE_INFO"]="Esborrar el registre";
$MESSAGES["CANCEL"]="Cancel&middot;lar";
$MESSAGES["SEARCH"]="Buscar";
$MESSAGES["ADVANCED_SEARCH"]="Cerca avan&ccedil;ada";
$MESSAGES["QUERY_ERROR"]="Error en la consulta";
$MESSAGES["CONFIRM_DELETE_REGISTER"]="Est&agrave; segur que vol eliminar aquest registre?";
$MESSAGES["TRUE"]="Si";
$MESSAGES["FALSE"]="No";
$MESSAGES["NEXT_ROWS"]="Properes %d files";
$MESSAGES["PREV_ROWS"]="%d anteriors";
$MESSAGES["FIRST_ROW"]="Inici";
$MESSAGES["LAST_ROW"]="Final";
$MESSAGES["INFO_ROWS_SHOWN"]="Mostrant des de la fila %d fins a la %d, de %d";
$MESSAGES["TOTAL"]="Total";
$MESSAGES["RELOAD"]="Actualitzar";
$MESSAGES["RESET"]="Neteja";
$MESSAGES["NONE"]="Cap";
$MESSAGES["CHANGE"]="Canviar";
$MESSAGES["ENABLED"]="Activat";
$MESSAGES["ABOUT"]="Sobre l'aplicaci&oacute;";
$MESSAGES["SELECT_TITLE"]="Selecciona";
$MESSAGES["SELECT_ALL"]="tot";
$MESSAGES["SELECT_NONE"]="res";
$MESSAGES["SELECT_INVERT"]="invertir";
$MESSAGES["APPLY_SELECTED"]="Aplicar a la selecci&oacute;";
$MESSAGES["DIR_NEW"]="Crear una nova carpeta";
$MESSAGES["FILE_NEW"]="Afegir un nou fitxer";
$MESSAGES["DIR_CANT_BE_VOID"]="Cal indicar un nom del directori";

// Datawindow buttons
$MESSAGES["BUTTON_RELOAD"]="@";
$MESSAGES["BUTTON_SEARCH"]="Cerca";
$MESSAGES["BUTTON_RESET"]="Esborra";
$MESSAGES["BUTTON_NEW"]="+";
$MESSAGES["BUTTON_MODIFY"]="Modificar";
$MESSAGES["BUTTON_DELETE"]="Esborrar";
$MESSAGES["BUTTON_ACCEPT"]="Acceptar";
$MESSAGES["BUTTON_SAVE"]="Guardar";
$MESSAGES["BUTTON_CLOSE"]="Tancar";
$MESSAGES["BUTTON_ACCEPT_AND_CONTIUE"]="Guardar (sense tancar)";
$MESSAGES["BUTTON_CANCEL"]="Cancel&middot;lar";
$MESSAGES["BUTTON_BACK"]="<";
$MESSAGES["BUTTON_NEXT"]=">";
$MESSAGES["BUTTON_EXPORT"]="Exportar dades";

// SKILL LEVELS
$MESSAGES["SKILL_0"]="APP ADM";
$MESSAGES["SKILL_3"]="GRP ADM";
$MESSAGES["SKILL_5"]="USER";

// GENERAL checking
$MESSAGES["FIELD_REQUIRED"]="El camp %s &eacute;s obligatori";
$MESSAGES["FIELD_EXISTS"]="El valor %s ja existeix";
$MESSAGES["FIELD_TYPE_INCORRECT"]="El valor del camp %s no &eacute;s correcte";

$MESSAGES["MGM_CREATED"]="Creat correctament";
$MESSAGES["MGM_DELETED"]="Eliminat correctament";
$MESSAGES["MGM_MODIFIED"]="Modificat correctament";
$MESSAGES["MGM_ERROR_CREATING"]="S'ha produit un error al donar-lo d'alta";
$MESSAGES["MGM_ERROR_DELETING"]="S'ha produit un error al donar-lo de baixa";
$MESSAGES["MGM_ERROR_MODIFYING"]="S'ha produit un error al modificar-ho";
$MESSAGES["MGM_GRANT_ERROR"]="El nivell d'acces no autotitzat per aquesta acci&oacute;";
$MESSAGES["MGM_CANT_GRANT_SAME_LEVEL"]="El nivell d'acc&eacute;s ha de ser major que el teu";
$MESSAGES["MGM_GRANT_CANT_CREATE"]="No tens prou privilegis per crear registres";
$MESSAGES["MGM_GRANT_CANT_MODIFY"]="No tens prou privilegis per modificar aquest registre";
$MESSAGES["MGM_GRANT_CANT_DELETE"]="No tens prou privilegis per esborrar aquest registre";
$MESSAGES["MGM_EXPORTED"]="S'ha exportat les dades";
$MESSAGES["MGM_NOT_EXPORTED"]="S'ha produit un error al exportat les dades";

// Groups and users
$MESSAGES["GROUPS_AND_USERS_MGM_TITLE"]="Groups and users";

// GROUP management messages
$MESSAGES["GROUPS"]="Grups";
$MESSAGES["GROUP"]="Grup";
$MESSAGES["GROUP_MGM_TITLE"]="Gesti&oacute; de grups";
$MESSAGES["GROUP_MGM_NOT_OWN_DELETE"]="No pots esborrar el grups al que pertanys";
$MESSAGES["GROUP_MGM_STILL_HAS_USERS"]="Aquest grup te usuaris associats. No es pot donar de baixa";

$MESSAGES["GROUP_FIELD_NAME"]="Nom";
$MESSAGES["GROUP_FIELD_DESCRIPTION"]="Descripcio";

// APPROVER management messages
$MESSAGES["APPROVERS_MGM_TITLE"]="Gesti&oacute; d'administradors";

// USER management messages
$MESSAGES["USERS"]="Usuaris";
$MESSAGES["CHANGE_MY_DATA"]="El meu usuari";
$MESSAGES["USER_MGM_TITLE"]="Gesti&oacute; d'usuaris";
$MESSAGES["USER_MGM_NOT_OWN_DELETE"]="No puc permetre el suicidi... aguanta!";

$MESSAGES["USER_FIELD_USERNAME"]="Username";
$MESSAGES["USER_FIELD_PASSWORD"]="Contrasenya";
$MESSAGES["CONFIRM_PASSWORD"]="Confirmar";
$MESSAGES["USER_FIELD_EXTERNAL"]="Autenticaci&oacute; externa?";
$MESSAGES["USER_FIELD_NAME"]="Nom";
$MESSAGES["USER_FIELD_SURNAME"]="Cognoms";
$MESSAGES["USER_FIELD_GROUP"]="Grup";
$MESSAGES["USER_FIELD_EMAIL"]="eMail";
$MESSAGES["USER_FIELD_LEVEL"]="Nivell";
$MESSAGES["USER_FIELD_SEND_NOTIFICATIONS"]="Rep notificacions?";
$MESSAGES["USER_FIELD_HIREDATE"]="Data d'alta";
$MESSAGES["USER_FIELD_LANG"]="Idioma";
$MESSAGES["USER_FIELD_RECEIVE_ALL"]="Vols rebre les publicacions diaries de vulnerabilitats?";

$MESSAGES["USER_REGISTER_TITLE"]="P&agrave;gina de registre";

// BUG management messages
$MESSAGES["BUG_REPORT"]="Reportar un problema";
$MESSAGES["BUG_MGM_TITLE"]="Reporting de problemes";
$MESSAGES["BUG_STATUS_OPEN"]="Obert";
$MESSAGES["BUG_STATUS_CLOSED"]="Tancat";
$MESSAGES["BUG_STATUS_PENDING"]="Pendent";
$MESSAGES["BUG_FIELD_STATUS"]="Estat";
$MESSAGES["BUG_FIELD_DESCRIPTION"]="Descripci&oacute;";
$MESSAGES["BUGS_FIELD_CREATION_DATE"]="Creat el";
$MESSAGES["BUGS_FIELD_CLOSING_DATE"]="Tancat el";

$MESSAGES["USERNAME_EXISTS"]="L'identificador d'usuari ja existeix, prova un altre.";

// Search literals
$MESSAGES["SEARCH_CAN_USE_WILDCARDS"]="<b>Nota:</b> Pots utilitzar comodins per a la cerca i els separadors l&ograve;gics 'or' i 'and', p.e. '%apache% or %mysql%'";

// SEARCH BOXES
$MESSAGES["SHOW_SEARCHBOX"]="Cercar";

// functions.inc.php literals
$MESSAGES["NO_ROWS"]="No hi ha cap resultat.";
$MESSAGES["NO_ROWS_FOR_ID"]="No hi ha cap fila amb aquest identificador";
$MESSAGES["NUM_ROWS"]="Total: <b>%d</b> registres";
$MESSAGES["NUM_FILES"]="Total: <b>%d</b> fitxers";

// Logs
$MESSAGES["LOGS"]="Logs";
$MESSAGES["LOGS_MGM_TITLE"]="Visualitzaci&oacute; de logs";
$MESSAGES["LOGS_FIELD_DATE"]="Data";
$MESSAGES["LOGS_FIELD_USERNAME"]="Usuari";
$MESSAGES["LOGS_FIELD_USER_ID"]="Id Usuari";
$MESSAGES["LOGS_FIELD_USER_LEVEL"]="Nivell";
$MESSAGES["LOGS_FIELD_HOST"]="Origen";
$MESSAGES["LOGS_FIELD_REG"]="Registre";
$MESSAGES["LOGS_FIELD_MOD"]="M&ograve;dul";
$MESSAGES["LOGS_OLD_FIRST"]="Els nous primer";

// Parameters
$MESSAGES["PARAMETERS_MGM_TITLE"]="Par&agrave;metres globals";
$MESSAGES["PARAMETERS_FIELD_NAME"]="Nom";
$MESSAGES["PARAMETERS_FIELD_VALUE"]="Valor";
$MESSAGES["PARAMETERS_FIELD_DESCRIPTION"]="Descripci&oacute;";
$MESSAGES["PARAMETER_CANT_BE_DELETED"]="Aquest par&agrave;metre no es pot eliminar";

$MESSAGES["TOOLS"]="Eines";
$MESSAGES["DDBB"]="Acc&egrave;s a la base de dades";
$MESSAGES["DDBB_CURRENT"]="BBDD (configuraci&oacute; actual)";
$MESSAGES["DDBB_GENERIC"]="BBDD (gen&egrave;ric)";
$MESSAGES["DDBB_CURRENT_HEADER"]="Base de dades (configuraci&oacute; actual)";
$MESSAGES["DDBB_GENERIC_HEADER"]="Base de dades (gen&egrave;ric)";

// Config
$MESSAGES["CONFIG_MGM_TITLE"]="Configuraci&oacute;";

// Tasks
$MESSAGES["TASKS_PERIODICITY_DAILY"]="Diari";
$MESSAGES["TASKS_PERIODICITY_WORKING_DAILY"]="Diari (de dilluns a divendres)";
$MESSAGES["TASKS_PERIODICITY_WEEKLY"]="Setmanal";
$MESSAGES["TASKS_PERIODICITY_MONTHLY"]="Mensual";
$MESSAGES["TASKS_PERIODICITY_NEVER"]="Mai";
$MESSAGES["TASKS_PERIODICITY_HOURLY"]="Cada hora";
$MESSAGES["TASKS_PERIODICITY_HALF_HOURLY"]="Cada mitja hora";

$MESSAGES["TASKS_MGM_TITLE"]="Administrador de tasques";
$MESSAGES["TASKS_FIELD_NAME"]="Nom";
$MESSAGES["TASKS_FIELD_SCRIPT"]="Script";
$MESSAGES["TASKS_FIELD_DESCRIPTION"]="Descripci&oacute;";
$MESSAGES["TASKS_FIELD_PERIODICITY"]="Periodicitat";
$MESSAGES["TASKS_FIELD_HOUR"]="Hora";
$MESSAGES["TASKS_FIELD_SEND_REPORT"]="Enviar informe";

//Subscripcions
$MESSAGES["SUBSCRIPTIONS_MGM_TITLE"]= "Subscripcions a informes";
$MESSAGES["SUBSCRIPTIONS_FIELD_USER"]= "Identificador d'usuari";
$MESSAGES["SUBSCRIPTIONS_FIELD_REPORT"]="Identificador d'informe";

//Reports
$MESSAGES["REPORT_MGM_TITLE"]= "Informes";
$MESSAGES["REPORT_PREVIEW"]= "Previsualitzaci&oacute; d'informes";
$MESSAGES["REPORT_FIELD_NAME"]= "Nom";
$MESSAGES["REPORT_FIELD_GROUP"]="Grup";
$MESSAGES["REPORT_FIELD_CONTENT"]= "Contingut";
$MESSAGES["REPORT_FIELD_DESCRIPTION"]= "Descripci&oacute;";
$MESSAGES["REPORT_FIELD_PERIODICITY"]= "Periodicitat";

//Tags
$MESSAGES["TAGS_MGM_TITLE"]= "TAGs";
$MESSAGES["TAGS_FIELD_NAME"]= "Nom";
$MESSAGES["TAGS_FIELD_CALC_METHOD"]="M&egrave;tode de c&agrave;lcul";
$MESSAGES["TAGS_FIELD_VALUE"]= "Valor";
$MESSAGES["TAGS_FIELD_DESCRIPTION"]= "Descripci&oacute";
$MESSAGES["TAGS_FIELD_EXTRAINFO"]= "Informaci&oacute; addicional";
$MESSAGES["TAGS_FIELD_IS_PUBLIC"]= "&Eacute;s p&uacute;blic?";
$MESSAGES["TAGS_CANT_BE_DELETE"]= "No es pot esborrar el TAG perque es fa servir en:";
$MESSAGES["TAGS_CANT_BE_RENAMED"]= "No es pot canviar el nom del TAG perque es fa servir en:";
$MESSAGES["TAGS_CANT_REFERENCE_ITSELF"]= "Un tag no pot fer refer&egrave;ncia a ell mateix";
$MESSAGES["TAGS_QUESTION_DUPLICATE"]= "Duplicar %s?";

// Dates
$MESSAGES["dd/mm/yy"]="dd/mm/aa";
$MESSAGES["mm/dd/yy"]="mm/dd/aa";
$MESSAGES["yy/mm/dd"]="aa/mm/dd";

// Public references
$MESSAGES["IS_PUBLIC_FOR_ANYBODY"]="P&uacute;blic per a tothom";
$MESSAGES["IS_PUBLIC_FOR_MY_GROUP"]="P&uacute;blic nom&eacute;s per al meu grup";
$MESSAGES["IS_NOT_PUBLIC"]="No &eacute;s p&uacute;blic";

$MESSAGES["HELP"]="Ajuda";
?>
