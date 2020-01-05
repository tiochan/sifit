<?php
/**
 * @author Sebastian Gomez (tiochan@gmail.com)
 * For: Politechnical University of Catalonia (UPC), Spain.
 *
 * @package lib
 * @subpackage lang
 *
 * Spanish version of application messages
 *
 */

global $MESSAGES;

//////////////////////////////////////////////////////////////////////////////
// GENERAL MESSAGES SECTION                                                //
// APPLICATION MESSAGES ARE AT NEXT SECTION                               //
///////////////////////////////////////////////////////////////////////////


// GENERAL APPLICATION MESSAGES, LITERALS
$MESSAGES["APP_HOME"]="inicio";
$MESSAGES["APP_LOGIN_TITLE"]="Identificaci&oacute;n de usuario";
$MESSAGES["APP_LOGIN_USERNAME"]="Usuario";
$MESSAGES["APP_LOGIN_PASSWORD"]="Contrase&ntilde;a";
$MESSAGES["APP_USER_LEVEL"]="Nivel";
$MESSAGES["APP_ACCEPT"]="Aceptar";
$MESSAGES["APP_LOGOUT_TITLE"]="Sesi&oacute;n finalizada";
$MESSAGES["LOADING_DATA"]="Cargando datos...";

$MESSAGES["MAINTENANCE_MESSAGE"]="<center><img src='" . HOME . "/include/images/icons/messagebox_warning.png'><h2>Estamos realizando tareas de mantenimiento (¡o eso espero...!)</h2><h3>Por favor, int&eacute;ntelo m&aacute;s tarde.<br>Disculpe las molestias.</h3></center>";

// AUTH messages
$MESSAGES["AUTH_INVALID_AUTH"]="Usuario/contrase&ntilde;a no v&aacute;lidos.";
$MESSAGES["AUTH_USER_REQUIRED"]="Debe estar registrado para acceder a esta p&aacute;gina.";
$MESSAGES["AUTH_NOT_GRANTED"]="Acceso restringido.";
$MESSAGES["AUTH_LOGOUT"]="Salir";
$MESSAGES["WANT_REGISTER"]="<p>Si no tiene un usuario v&aacute;lido del SIGVI y quiere uno para probar vaya a la <a href='register.php'>p&aacute;gina de registro</a>.</p>";
$MESSAGES["REGISTER_SUCCESS"]=
	"<p><b>Bienvenido %s</b></p>." .
	"<p>Se ha registrado correctamente.</p>" .
	"<p><a href='" . SERVER_URL . HOME . "/index.php'>p&aacute;gina de login</a></p>";

// CHRONOMETER messages
$MESSAGES["CRONO_END_MESSAGE"]="Tiempo de c&aacute;lculo: %.3f segundos";

$MESSAGES["CURRENCY"]="€";

// GENERAL management
$MESSAGES["MAIN_MENU"]="Men&uacute; principal";
$MESSAGES["APP_ADMIN"]="Administraci&oacute;n";
$MESSAGES["APP_CONF"]="Configuraci&oacute;n";
$MESSAGES["ACCEPT"]="Aceptar";
$MESSAGES["NEW"]="+";
$MESSAGES["NEW_INFO"]="Crear un nuevo registro";
$MESSAGES["CREATE"]="Crear";
$MESSAGES["MODIFY"]="Modificar";
$MESSAGES["MODIFY_INFO"]="Modificar el registro";
$MESSAGES["DELETE"]="Borrar";
$MESSAGES["DELETE_INFO"]="Borrar el registro";
$MESSAGES["CANCEL"]="Cancelar";
$MESSAGES["SEARCH"]="Buscar";
$MESSAGES["ADVANCED_SEARCH"]="B&uacute;squeda avanzada";
$MESSAGES["QUERY_ERROR"]="Error en la consulta";
$MESSAGES["CONFIRM_DELETE_REGISTER"]="¿Está seguro de que quiere eliminar este registro?";
$MESSAGES["TRUE"]="Si";
$MESSAGES["FALSE"]="No";
$MESSAGES["NEXT_ROWS"]="Siguientes %d files";
$MESSAGES["PREV_ROWS"]="%d anteriores";
$MESSAGES["FIRST_ROW"]="Inicio";
$MESSAGES["LAST_ROW"]="Fin";
$MESSAGES["INFO_ROWS_SHOWN"]="Mostrando desde la fila %d hasta a la %d, de %d";
$MESSAGES["TOTAL"]="Total";
$MESSAGES["RELOAD"]="Actualizar";
$MESSAGES["RESET"]="Limpiar";
$MESSAGES["NONE"]="Ninguno";
$MESSAGES["CHANGE"]="Cambiar";
$MESSAGES["ENABLED"]="Activado";
$MESSAGES["ABOUT"]="Sobre la aplicaci&oacute;n";
$MESSAGES["SELECT_TITLE"]="Selecciona";
$MESSAGES["SELECT_ALL"]="todo";
$MESSAGES["SELECT_NONE"]="nada";
$MESSAGES["SELECT_INVERT"]="invertir";
$MESSAGES["APPLY_SELECTED"]="Aplicar a la selecci&oacute;n";
$MESSAGES["DIR_NEW"]="Crear directorio";
$MESSAGES["FILE_NEW"]="Agregar un archivo";
$MESSAGES["DIR_CANT_BE_VOID"]="Debe indicar un nombre al directorio";

// Datawindow buttons
$MESSAGES["BUTTON_RELOAD"]="@";
$MESSAGES["BUTTON_SEARCH"]="Buscar";
$MESSAGES["BUTTON_RESET"]="Borrar";
$MESSAGES["BUTTON_NEW"]="+";
$MESSAGES["BUTTON_MODIFY"]="Modificar";
$MESSAGES["BUTTON_DELETE"]="Borrar";
$MESSAGES["BUTTON_ACCEPT"]="Aceptar";
$MESSAGES["BUTTON_SAVE"]="Guardar";
$MESSAGES["BUTTON_CLOSE"]="Cerrar";
$MESSAGES["BUTTON_ACCEPT_AND_CONTIUE"]="Guardar (sin cerrar)";
$MESSAGES["BUTTON_CANCEL"]="Cancelar";
$MESSAGES["BUTTON_BACK"]="<";
$MESSAGES["BUTTON_NEXT"]=">";
$MESSAGES["BUTTON_EXPORT"]="Exportar datos";

// SKILL LEVELS
$MESSAGES["SKILL_0"]="APP ADM";
$MESSAGES["SKILL_3"]="GRP ADM";
$MESSAGES["SKILL_5"]="USER";

// GENERAL checking
$MESSAGES["FIELD_REQUIRED"]="El campo %s es obligatorio";
$MESSAGES["FIELD_EXISTS"]="El valor %s ya existe";
$MESSAGES["FIELD_TYPE_INCORRECT"]="El valor del campo %s no es correcto";

$MESSAGES["MGM_CREATED"]="Creado correctamente";
$MESSAGES["MGM_DELETED"]="Eliminado correctamente";
$MESSAGES["MGM_MODIFIED"]="Modificado correctamente";
$MESSAGES["MGM_ERROR_CREATING"]="Se ha producido un error al darlo de alta";
$MESSAGES["MGM_ERROR_DELETING"]="Se ha producido un error al darlo de baja";
$MESSAGES["MGM_ERROR_MODIFYING"]="Se ha producido un error al modificarlo";
$MESSAGES["MGM_GRANT_ERROR"]="Nivel de acceso no autorizado para esta acci&oacute;n";
$MESSAGES["MGM_CANT_GRANT_SAME_LEVEL"]="El nivel de acceso tiene que ser mayor que el suyo";
$MESSAGES["MGM_GRANT_CANT_CREATE"]="No tiene suficientes privilegios para crear registros";
$MESSAGES["MGM_GRANT_CANT_MODIFY"]="No tiene suficientes privilegios para modificar este registro";
$MESSAGES["MGM_GRANT_CANT_DELETE"]="No tiene suficientes privilegios para borrar este registro";
$MESSAGES["MGM_EXPORTED"]="Los datos se exportaron correctamente";
$MESSAGES["MGM_NOT_EXPORTED"]="Se ha producido un error al exportar los dados";

// Groups and users
$MESSAGES["GROUPS_AND_USERS_MGM_TITLE"]="Grupos y usuarios";

// GROUP management messages
$MESSAGES["GROUPS"]="Grupos";
$MESSAGES["GROUP"]="Grupo";
$MESSAGES["GROUP_MGM_TITLE"]="Gesti&oacute;n de grupos";
$MESSAGES["GROUP_MGM_NOT_OWN_DELETE"]="No puede borrar el grupo al que pertenece";
$MESSAGES["GROUP_MGM_STILL_HAS_USERS"]="Este grupo tiene usuarios asociados. No se puede dar de baja";

$MESSAGES["GROUP_FIELD_NAME"]="Nombre";
$MESSAGES["GROUP_FIELD_DESCRIPTION"]="Descripci&oacute;n";

// APPROVER management messages
$MESSAGES["APPROVERS_MGM_TITLE"]="Gesti&oacuten; de administradores";

// USER management messages
$MESSAGES["USERS"]="Usuarios";
$MESSAGES["CHANGE_MY_DATA"]="Mi usuario";
$MESSAGES["USER_MGM_TITLE"]="Gesti&oacute;n de usuarios";
$MESSAGES["USER_MGM_NOT_OWN_DELETE"]="No puedo permitir el suicidio... aguanta!";

$MESSAGES["USER_FIELD_USERNAME"]="Usuario";
$MESSAGES["USER_FIELD_PASSWORD"]="Clave";
$MESSAGES["CONFIRM_PASSWORD"]="Confirmar";
$MESSAGES["USER_FIELD_EXTERNAL"]="&iquest;Autenticaci&oacute;n externa?";
$MESSAGES["USER_FIELD_NAME"]="Nombre";
$MESSAGES["USER_FIELD_SURNAME"]="Apellidos";
$MESSAGES["USER_FIELD_GROUP"]="Grupo";
$MESSAGES["USER_FIELD_EMAIL"]="email";
$MESSAGES["USER_FIELD_LEVEL"]="Nivel";
$MESSAGES["USER_FIELD_SEND_NOTIFICATIONS"]="&iquest;Recibe notificaciones?";
$MESSAGES["USER_FIELD_HIREDATE"]="Fecha de alta";
$MESSAGES["USER_FIELD_LANG"]="Idioma";
$MESSAGES["USER_FIELD_RECEIVE_ALL"]="&iquest;Quiere recibir las publicaciones diarias de vulnerabilidades?";

$MESSAGES["USER_REGISTER_TITLE"]="P&aacute;gina de registro";

// BUG management messages
$MESSAGES["BUG_REPORT"]="Reportar un problema";
$MESSAGES["BUG_MGM_TITLE"]="Reporte de problemas";
$MESSAGES["BUG_STATUS_OPEN"]="Abierto";
$MESSAGES["BUG_STATUS_CLOSED"]="Cerrado";
$MESSAGES["BUG_STATUS_PENDING"]="Pendiente";
$MESSAGES["BUG_FIELD_STATUS"]="Estado";
$MESSAGES["BUG_FIELD_DESCRIPTION"]="Descripci&oacute;n";
$MESSAGES["BUGS_FIELD_CREATION_DATE"]="Creado el";
$MESSAGES["BUGS_FIELD_CLOSING_DATE"]="Cerrado el";

$MESSAGES["USERNAME_EXISTS"]="EL identificador de usuario ya existe, pruebe otro.";

// Search literals
$MESSAGES["SEARCH_CAN_USE_WILDCARDS"]="<b>Nota:</b> Puede usar comodines para la b&uacute;squeda y los separadores l&oacute;gicos 'or' y 'and', p.e. '%apache% or %mysql%'";

// SEARCH BOXES
$MESSAGES["SHOW_SEARCHBOX"]="Buscar";

// functions.inc.php literals
$MESSAGES["NO_ROWS"]="No hay ning&uacute;n resultado.";
$MESSAGES["NO_ROWS_FOR_ID"]="No hay ninguna fila con este identificador";
$MESSAGES["NUM_ROWS"]="Total: <b>%d</b> registros";
$MESSAGES["NUM_FILES"]="Total: <b>%d</b> ficheros";

// Logs
$MESSAGES["LOGS"]="Logs";
$MESSAGES["LOGS_MGM_TITLE"]="Visualizaci&oacute;n de logs";
$MESSAGES["LOGS_FIELD_DATE"]="Fecha";
$MESSAGES["LOGS_FIELD_USERNAME"]="Username";
$MESSAGES["LOGS_FIELD_USER_ID"]="Id Usuario";
$MESSAGES["LOGS_FIELD_USER_LEVEL"]="Nivel";
$MESSAGES["LOGS_FIELD_HOST"]="Origen";
$MESSAGES["LOGS_FIELD_REG"]="Registro";
$MESSAGES["LOGS_FIELD_MOD"]="M&oacute;dulo";
$MESSAGES["LOGS_OLD_FIRST"]="Los nuevos primero";

// Parameters
$MESSAGES["PARAMETERS_MGM_TITLE"]="Par&aacute;metros globales";
$MESSAGES["PARAMETERS_FIELD_NAME"]="Nombre";
$MESSAGES["PARAMETERS_FIELD_VALUE"]="Valor";
$MESSAGES["PARAMETERS_FIELD_DESCRIPTION"]="Descripci&oacute;n";
$MESSAGES["PARAMETER_CANT_BE_DELETED"]="Este par&aacute;metro no se puede eliminar";

$MESSAGES["TOOLS"]="Herramientas";
$MESSAGES["DDBB"]="Acceso a la base de datos";
$MESSAGES["DDBB_CURRENT"]="BBDD (configuraci&oacute;n actual)";
$MESSAGES["DDBB_GENERIC"]="BBDD (gen&eacute;rico)";
$MESSAGES["DDBB_CURRENT_HEADER"]="Base de datos (configuraci&oacute;n actual)";
$MESSAGES["DDBB_GENERIC_HEADER"]="Base de datos (gen&eacute;rico)";

// Config
$MESSAGES["CONFIG_MGM_TITLE"]="Configuraci&oacute;n";

// Tasks
$MESSAGES["TASKS_PERIODICITY_DAILY"]="Diario";
$MESSAGES["TASKS_PERIODICITY_WORKING_DAILY"]="Diario (de lunes a viernes)";
$MESSAGES["TASKS_PERIODICITY_WEEKLY"]="Semanal";
$MESSAGES["TASKS_PERIODICITY_MONTHLY"]="Mensual";
$MESSAGES["TASKS_PERIODICITY_NEVER"]="Nunca";
$MESSAGES["TASKS_PERIODICITY_HOURLY"]="Cada hora";
$MESSAGES["TASKS_PERIODICITY_HALF_HOURLY"]="Cada media hora";

$MESSAGES["TASKS_MGM_TITLE"]="Administrador de tareas";
$MESSAGES["TASKS_FIELD_NAME"]="Nombre";
$MESSAGES["TASKS_FIELD_SCRIPT"]="Script";
$MESSAGES["TASKS_FIELD_DESCRIPTION"]="Descripci&oacute;n";
$MESSAGES["TASKS_FIELD_PERIODICITY"]="Periodicidad";
$MESSAGES["TASKS_FIELD_HOUR"]="Hora";
$MESSAGES["TASKS_FIELD_SEND_REPORT"]="Enviar informe";

//Subscripciones
$MESSAGES["SUBSCRIPTIONS_MGM_TITLE"]= "Subscripciones a informes";
$MESSAGES["SUBSCRIPTIONS_FIELD_USER"]= "Identificador de usuario";
$MESSAGES["SUBSCRIPTIONS_FIELD_REPORT"]="Identificador de informe";

//Informes
$MESSAGES["REPORT_MGM_TITLE"]= "Informes";
$MESSAGES["REPORT_PREVIEW"]="Previsualizaci&oacute;n de informes";
$MESSAGES["REPORT_FIELD_NAME"]= "Nombre";
$MESSAGES["REPORT_FIELD_GROUP"]="Grupo";
$MESSAGES["REPORT_FIELD_CONTENT"]= "Contenido";
$MESSAGES["REPORT_FIELD_DESCRIPTION"]= "Descripci&oacute;n";
$MESSAGES["REPORT_FIELD_PERIODICITY"]= "Periodicidad";

//Tags
$MESSAGES["TAGS_MGM_TITLE"]= "TAGs";
$MESSAGES["TAGS_FIELD_NAME"]= "Nombre";
$MESSAGES["TAGS_FIELD_CALC_METHOD"]="Metodo a calcular";
$MESSAGES["TAGS_FIELD_VALUE"]= "Valor";
$MESSAGES["TAGS_FIELD_DESCRIPTION"]= "Descripci&oacute;n";
$MESSAGES["TAGS_FIELD_EXTRAINFO"]= "Informaci&oacute;n adicional";
$MESSAGES["TAGS_FIELD_IS_PUBLIC"]= "&iquest;Es p&uacute;blico?";
$MESSAGES["TAGS_CANT_BE_DELETE"]=  "No se puede borrar el TAG porque se usa en:";
$MESSAGES["TAGS_CANT_BE_RENAMED"]= "No se puede cambiar el nombre del TAG porque se usa en:";
$MESSAGES["TAGS_CANT_REFERENCE_ITSELF"]= "Un tag no puede contenerse a sí mismo";
$MESSAGES["TAGS_QUESTION_DUPLICATE"]= "¿Duplicar %s?";

// Dates
$MESSAGES["dd/mm/yy"]="dd/mm/aa";
$MESSAGES["mm/dd/yy"]="mm/dd/aa";
$MESSAGES["yy/mm/dd"]="aa/mm/dd";

// Public references
$MESSAGES["IS_PUBLIC_FOR_ANYBODY"]="P&uacute;blico para todos";
$MESSAGES["IS_PUBLIC_FOR_MY_GROUP"]="P&uacute;blico s&oacute;lo para mi grupo";
$MESSAGES["IS_NOT_PUBLIC"]="No es p&uacute;blico";

$MESSAGES["HELP"]="Ayuda";
?>
