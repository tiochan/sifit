<?php
/**
 * @author Sebastian Gomez (tiochan@gmail.com)
 * For: Politechnical University of Catalonia (UPC), Spain.
 *
 * @package lib
 * @subpackage lang
 *
 * English version of application messages
 * Reviewed from Antoni Matamales
 */

global $MESSAGES;

//////////////////////////////////////////////////////////////////////////////
// GENERAL MESSAGES SECTION                                                //
// APPLICATION MESSAGES ARE AT NEXT SECTION                               //
///////////////////////////////////////////////////////////////////////////


// GENERAL APPLICATION MESSAGES, LITERALS
$MESSAGES["APP_HOME"]="home";
$MESSAGES["APP_LOGIN_TITLE"]="User access";
$MESSAGES["APP_LOGIN_USERNAME"]="Username";
$MESSAGES["APP_LOGIN_PASSWORD"]="Password";
$MESSAGES["APP_USER_LEVEL"]="Level";
$MESSAGES["APP_ACCEPT"]="Accept";
$MESSAGES["APP_LOGOUT_TITLE"]="Session terminated";
$MESSAGES["LOADING_DATA"]="Loading data...";

$MESSAGES["MAINTENANCE_MESSAGE"]="<center><img src='" . HOME . "/include/images/icons/messagebox_warning.png'><h2>We are doing something for maintenance (I hope!)</h2><h3>Please, try later</h3></center>";

// AUTH messages
$MESSAGES["AUTH_INVALID_AUTH"]="Invalid username / password.";
$MESSAGES["AUTH_USER_REQUIRED"]="You need to be registered to access this page.";
$MESSAGES["AUTH_NOT_GRANTED"]="Access denied.";
$MESSAGES["AUTH_LOGOUT"]="Logout";
$MESSAGES["WANT_REGISTER"]="<p>If you aren't a valid SIGVI user but you want one for free testing, go to <a href='register.php'>register page</a>.</p>";
$MESSAGES["REGISTER_SUCCESS"]=
	"<p><b>Welcome %s.</b></p>" .
	"<p>You have been successfully registered.</p>" .
	"<p><a href='" . SERVER_URL . HOME . "/index.php'>login page</a></p>";

// CHRONOMETER messages
$MESSAGES["CRONO_END_MESSAGE"]="Page created in %.3f seconds";

$MESSAGES["CURRENCY"]="€";

// GENERAL management
$MESSAGES["MAIN_MENU"]="Main page";
$MESSAGES["APP_ADMIN"]="Administration";
$MESSAGES["APP_CONF"]="Configuration";
$MESSAGES["ACCEPT"]="Accept";
$MESSAGES["NEW"]="+";
$MESSAGES["NEW_INFO"]="Create new";
$MESSAGES["CREATE"]="Create";
$MESSAGES["MODIFY"]="Modify";
$MESSAGES["MODIFY_INFO"]="Modify";
$MESSAGES["DELETE"]="Delete";
$MESSAGES["DELETE_INFO"]="Delete";
$MESSAGES["CANCEL"]="Cancel";
$MESSAGES["SEARCH"]="Search";
$MESSAGES["ADVANCED_SEARCH"]="Advanced search";
$MESSAGES["QUERY_ERROR"]="Error in query";
$MESSAGES["CONFIRM_DELETE_REGISTER"]="Are you sure that you want to delete this row?";
$MESSAGES["TRUE"]="Yes";
$MESSAGES["FALSE"]="No";
$MESSAGES["NEXT_ROWS"]="Next %d rows";
$MESSAGES["PREV_ROWS"]="%d previous";
$MESSAGES["FIRST_ROW"]="Start";
$MESSAGES["LAST_ROW"]="End";
$MESSAGES["INFO_ROWS_SHOWN"]="Showing from row %d to %d, of %d";
$MESSAGES["TOTAL"]="Total";
$MESSAGES["RELOAD"]="Refresh";
$MESSAGES["RESET"]="Reset";
$MESSAGES["NONE"]="None";
$MESSAGES["CHANGE"]="Change";
$MESSAGES["ENABLED"]="Enabled";
$MESSAGES["ABOUT"]="About";
$MESSAGES["SELECT_TITLE"]="Select";
$MESSAGES["SELECT_ALL"]="all";
$MESSAGES["SELECT_NONE"]="none";
$MESSAGES["SELECT_INVERT"]="invert";
$MESSAGES["APPLY_SELECTED"]="Apply to selected rows";
$MESSAGES["DIR_NEW"]="Add directory";
$MESSAGES["FILE_NEW"]="Add new file";
$MESSAGES["DIR_CANT_BE_VOID"]="A directory name must be given";

// Datawindow buttons
$MESSAGES["BUTTON_RELOAD"]="@";
$MESSAGES["BUTTON_SEARCH"]="Search";
$MESSAGES["BUTTON_RESET"]="Reset";
$MESSAGES["BUTTON_NEW"]="+";
$MESSAGES["BUTTON_MODIFY"]="Modify";
$MESSAGES["BUTTON_DELETE"]="Delete";
$MESSAGES["BUTTON_ACCEPT"]="Accept";
$MESSAGES["BUTTON_SAVE"]="Save";
$MESSAGES["BUTTON_CLOSE"]="Close";
$MESSAGES["BUTTON_ACCEPT_AND_CONTIUE"]="Save (don't close)";
$MESSAGES["BUTTON_CANCEL"]="Cancel";
$MESSAGES["BUTTON_BACK"]="<";
$MESSAGES["BUTTON_NEXT"]=">";
$MESSAGES["BUTTON_EXPORT"]="Export data";

// SKILL LEVELS
$MESSAGES["SKILL_0"]="APP ADM";
$MESSAGES["SKILL_3"]="GRP ADM";
$MESSAGES["SKILL_5"]="USER";

// GENERAL checking
$MESSAGES["FIELD_REQUIRED"]="The field %s is required";
$MESSAGES["FIELD_EXISTS"]="The value %s just exists";
$MESSAGES["FIELD_TYPE_INCORRECT"]="The value of the field %s is incorrect";

$MESSAGES["MGM_CREATED"]="Successfully created";
$MESSAGES["MGM_DELETED"]="Successfully deleted";
$MESSAGES["MGM_MODIFIED"]="Successfully updated";
$MESSAGES["MGM_ERROR_CREATING"]="Error atempting to create";
$MESSAGES["MGM_ERROR_DELETING"]="Error atempting to delete";
$MESSAGES["MGM_ERROR_MODIFYING"]="Error atempting to update";
$MESSAGES["MGM_GRANT_ERROR"]="Unauthorized access level";
$MESSAGES["MGM_CANT_GRANT_SAME_LEVEL"]="The level must be greater than yours";
$MESSAGES["MGM_GRANT_CANT_CREATE"]="You have not enough privileges to create";
$MESSAGES["MGM_GRANT_CANT_MODIFY"]="You have not enough privileges to update";
$MESSAGES["MGM_GRANT_CANT_DELETE"]="You have not enough privileges to delete";
$MESSAGES["MGM_EXPORTED"]="Data exported successfully";
$MESSAGES["MGM_NOT_EXPORTED"]="Error exporting data";

// Groups and users
$MESSAGES["GROUPS_AND_USERS_MGM_TITLE"]="Groups and users";

// GROUP management messages
$MESSAGES["GROUPS"]="Groups";
$MESSAGES["GROUP"]="Group";
$MESSAGES["GROUP_MGM_TITLE"]="Group management";
$MESSAGES["GROUP_MGM_NOT_OWN_DELETE"]="You cant delete your own group";
$MESSAGES["GROUP_MGM_STILL_HAS_USERS"]="There are users associated to this group. Can't be deleted";

$MESSAGES["GROUP_FIELD_NAME"]="Name";
$MESSAGES["GROUP_FIELD_DESCRIPTION"]="Description";

// APPROVER management messages
$MESSAGES["APPROVERS_MGM_TITLE"]="Administrators management";

// USER management messages
$MESSAGES["USERS"]="Users";
$MESSAGES["CHANGE_MY_DATA"]="My user";
$MESSAGES["USER_MGM_TITLE"]="User management";
$MESSAGES["USER_MGM_NOT_OWN_DELETE"]="Can't auto-delete";

$MESSAGES["USER_FIELD_USERNAME"]="Username";
$MESSAGES["USER_FIELD_PASSWORD"]="Password";
$MESSAGES["CONFIRM_PASSWORD"]="Confirm";
$MESSAGES["USER_FIELD_EXTERNAL"]="External authentication?";
$MESSAGES["USER_FIELD_NAME"]="Name";
$MESSAGES["USER_FIELD_SURNAME"]="Surname";
$MESSAGES["USER_FIELD_GROUP"]="Group";
$MESSAGES["USER_FIELD_EMAIL"]="email";
$MESSAGES["USER_FIELD_LEVEL"]="Level";
$MESSAGES["USER_FIELD_SEND_NOTIFICATIONS"]="Receive notifications?";
$MESSAGES["USER_FIELD_HIREDATE"]="Hiredate";
$MESSAGES["USER_FIELD_LANG"]="Lang";
$MESSAGES["USER_FIELD_RECEIVE_ALL"]="Receive daily vuln. publications?";

$MESSAGES["USER_REGISTER_TITLE"]="New user register";

// BUG management messages
$MESSAGES["BUG_REPORT"]="Report a bug";
$MESSAGES["BUG_MGM_TITLE"]="Application bugs";
$MESSAGES["BUG_STATUS_OPEN"]="Open";
$MESSAGES["BUG_STATUS_CLOSED"]="Closed";
$MESSAGES["BUG_STATUS_PENDING"]="Pending";
$MESSAGES["BUG_FIELD_STATUS"]="Status";
$MESSAGES["BUG_FIELD_DESCRIPTION"]="Description";
$MESSAGES["BUGS_FIELD_CREATION_DATE"]="Created";
$MESSAGES["BUGS_FIELD_CLOSING_DATE"]="Closed";

$MESSAGES["USERNAME_EXISTS"]="This username just exists, try other.";

// Search literals
$MESSAGES["SEARCH_CAN_USE_WILDCARDS"]="<b>Note:</b> You can use SQL wildcards and the logic separators 'or' and 'and', p.e. '%apache% or %mysql%'";

// SEARCH BOXES
$MESSAGES["SHOW_SEARCHBOX"]="Show search options";

// functions.inc.php literals
$MESSAGES["NO_ROWS"]="No rows found";
$MESSAGES["NO_ROWS_FOR_ID"]="No rows for this id";
$MESSAGES["NUM_ROWS"]="Total: <b>%d</b> rows";
$MESSAGES["NUM_FILES"]="Total: <b>%d</b> files";

// Logs
$MESSAGES["LOGS"]="Logs";
$MESSAGES["LOGS_MGM_TITLE"]="Log visualizacion";
$MESSAGES["LOGS_FIELD_DATE"]="Date";
$MESSAGES["LOGS_FIELD_USERNAME"]="Username";
$MESSAGES["LOGS_FIELD_USER_ID"]="User Id";
$MESSAGES["LOGS_FIELD_USER_LEVEL"]="Level";
$MESSAGES["LOGS_FIELD_HOST"]="Source";
$MESSAGES["LOGS_FIELD_REG"]="Register";
$MESSAGES["LOGS_FIELD_MOD"]="Module";
$MESSAGES["LOGS_OLD_FIRST"]="Newer first";

// Parameters
$MESSAGES["PARAMETERS_MGM_TITLE"]="Global parameters";
$MESSAGES["PARAMETERS_FIELD_NAME"]="Name";
$MESSAGES["PARAMETERS_FIELD_VALUE"]="Value";
$MESSAGES["PARAMETERS_FIELD_DESCRIPTION"]="Description";
$MESSAGES["PARAMETER_CANT_BE_DELETED"]="This parameter can't be deleted";

$MESSAGES["TOOLS"]="Tools";
$MESSAGES["DDBB"]="Database access";
$MESSAGES["DDBB_CURRENT"]="DDBB (current config)";
$MESSAGES["DDBB_GENERIC"]="DDBB (gen.)";
$MESSAGES["DDBB_CURRENT_HEADER"]="Database (current configuration)";
$MESSAGES["DDBB_GENERIC_HEADER"]="Database (generic)";

// Config
$MESSAGES["CONFIG_MGM_TITLE"]="Configuration";

// Tasks
$MESSAGES["TASKS_PERIODICITY_DAILY"]="Daily";
$MESSAGES["TASKS_PERIODICITY_WORKING_DAILY"]="Working days (monday - friday)";
$MESSAGES["TASKS_PERIODICITY_WEEKLY"]="Weekly";
$MESSAGES["TASKS_PERIODICITY_MONTHLY"]="Monthly";
$MESSAGES["TASKS_PERIODICITY_NEVER"]="Never";
$MESSAGES["TASKS_PERIODICITY_HOURLY"]="Hourly";
$MESSAGES["TASKS_PERIODICITY_HALF_HOURLY"]="Half hourly";

$MESSAGES["TASKS_MGM_TITLE"]="Task manager";
$MESSAGES["TASKS_FIELD_NAME"]="Name";
$MESSAGES["TASKS_FIELD_SCRIPT"]="Script";
$MESSAGES["TASKS_FIELD_DESCRIPTION"]="Description";
$MESSAGES["TASKS_FIELD_PERIODICITY"]="Periodicity";
$MESSAGES["TASKS_FIELD_HOUR"]="Hour";
$MESSAGES["TASKS_FIELD_SEND_REPORT"]="Send report";

//Subscriptions
$MESSAGES["SUBSCRIPTIONS_MGM_TITLE"]= "Subscriptions to reports";
$MESSAGES["SUBSCRIPTIONS_FIELD_USER"]= "User ID";
$MESSAGES["SUBSCRIPTIONS_FIELD_REPORT"]="Report ID";

//Reports
$MESSAGES["REPORT_MGM_TITLE"]= "Reports";
$MESSAGES["REPORT_PREVIEW"]="Report preview";
$MESSAGES["REPORT_FIELD_NAME"]= "Name";
$MESSAGES["REPORT_FIELD_GROUP"]="Group";
$MESSAGES["REPORT_FIELD_CONTENT"]= "Content";
$MESSAGES["REPORT_FIELD_DESCRIPTION"]= "Description";
$MESSAGES["REPORT_FIELD_PERIODICITY"]= "Periodicity";

//Tags
$MESSAGES["TAGS_MGM_TITLE"]= "TAGs";
$MESSAGES["TAGS_FIELD_NAME"]= "Name";
$MESSAGES["TAGS_FIELD_CALC_METHOD"]="Calc method";
$MESSAGES["TAGS_FIELD_VALUE"]= "Value";
$MESSAGES["TAGS_FIELD_DESCRIPTION"]= "Description";
$MESSAGES["TAGS_FIELD_EXTRAINFO"]= "Additional information";
$MESSAGES["TAGS_FIELD_IS_PUBLIC"]= "Is public?";
$MESSAGES["TAGS_CANT_BE_DELETE"]=  "Can't delete this TAG because is being used on:";
$MESSAGES["TAGS_CANT_BE_RENAMED"]= "Can't rename this TAG because is being used on:";
$MESSAGES["TAGS_CANT_REFERENCE_ITSELF"]= "One tag can't reference itlsef";
$MESSAGES["TAGS_QUESTION_DUPLICATE"]= "Duplicate %s?";

// Dates
$MESSAGES["dd/mm/yy"]="dd/mm/yy";
$MESSAGES["mm/dd/yy"]="mm/dd/yy";
$MESSAGES["yy/mm/dd"]="yy/mm/dd";

// Public references
$MESSAGES["IS_PUBLIC_FOR_ANYBODY"]="Public for everybody";
$MESSAGES["IS_PUBLIC_FOR_MY_GROUP"]="Public only for my group";
$MESSAGES["IS_NOT_PUBLIC"]="Not public";

$MESSAGES["HELP"]="Help";
?>
