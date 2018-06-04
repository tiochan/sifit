<?php
/**
 * @author Sebastian Gomez (tiochan@gmail.com)
 * For: Politechnical University of Catalonia (UPC), Spain.
 *
 * @package sigvi
 * @subpackage forms
 *
 * lookup_products datawindow class
 *
 * - Must be opened from a lookup field.
 */

	define("SHOW_MENU",false);
	
	include_once "../../include/init.inc.php";

	html_showHeader();

	$get_search= get_http_param("search_full_name","");

	$AUTH_REQUIRED=true;
	$AUTH_LVL=0;			// ADMINS ONLY

	include_once SYSHOME . "/include/forms/forms.inc.php";
	include_once SYSHOME . "/include/forms/field.inc.php";
	include_once SYSHOME . "/include/forms/form_elements/datawindow.inc.php";
	include_once SYSHOME . "/include/forms/form_elements/search_box.inc.php";
	include_once SYSHOME . "/include/lookups/lookup.inc.php";

	$form_name= "form_products";

	html_header($MESSAGES["PRODUCT_MGM_TITLE"]);

	include MY_INC_DIR . "/classes/dw_products.class.php";

	class ext_product extends dw_product {

		function post_show_row($values) {
			echo $this->create_row_action("Ok","select_row",$values["row_id"],ICONS . "/next.png");
		}

		function action_select_row($row_id) {
			global $global_db;

			$prodId= $row_id;

			$query= "select full_name from products where id_product = '$row_id'";
			$res= $global_db->dbms_query($query);

			if(!$res or !(list($prodDesc) = $global_db->dbms_fetch_row($res))) {
				echo "Product not found!";
				return 0;
			}

			$global_db->dbms_free_result($res);

			// The call to the form lookup element:
			$this->parent->lookup_link->set_values($prodId,$prodDesc);
			return 1;
		}
	}

	// Fields
	$fields= array();

	$fields[0]= new field("id_product","id","auto",false,true,true,false);
	$fields[1]= new field("vendor",$MESSAGES["PRODUCT_FIELD_VENDOR"],"string",true,false,true,true);
	$fields[2]= new field("name",$MESSAGES["PRODUCT_FIELD_NAME"],"string",true,false,true,true);
	$fields[3]= new field("version",$MESSAGES["PRODUCT_FIELD_VERSION"],"string",true,false,true,true);
	$fields[4]= new field("full_name",$MESSAGES["PRODUCT_FIELD_FULL"],"string",false,false,false,false);

	$query_adds=" order by vendor";

	// Search box
	$sb_fields=Array($fields[1], $fields[2], $fields[3], $fields[4]);
	$get_search= get_http_param("search_full_name","");
	$sb= new search_box($sb_fields, "sb_search", $MESSAGES["SEARCH"],1,false);
	if($sb->first_time and $get_search!="") $sb->set_field_value("full_name",$get_search);

	// Datawindow
	$dw= new ext_product();

	// Form
	$frm= new form($form_name);
	$frm->form_action="";
	$frm->add_element($dw);

	$frm->form_control();

	html_footer();
?>