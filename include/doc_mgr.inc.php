<?php
/**
 * @author Sebastian Gomez (tiochan@gmail.com)
 * For: Politechnical University of Catalonia (UPC), Spain.
 *
 * @package lib
 * @subpackage doc manager
 *
 */

	include_once SYSHOME . "/include/forms/forms.inc.php";
	include_once SYSHOME . "/objects/classes/component.class.php";

	$global_index= 0;

	class doc_form extends form {
		var $doc_id;

		function doc_form($doc_id, $form_name) {
			$this->doc_id= $doc_id;

			parent::form($form_name);
		}

		function show_hidden() {
			echo "<input type='hidden' name='doc_id' value='" . $this->doc_id . "'>\n";
		}
	}


	function get_new_doc_id() {
		global $global_db;


		$query="select doc_id from doc_id";
		$res= $global_db->dbms_query($query);

		if(!$res or !(list($last_doc_id)= $global_db->dbms_fetch_row($res))) {
			die("ERROR: Cant obtain new doc_id.");
		}

		$global_db->dbms_free_result($res);
		$last_doc_id++;

		$query= "update doc_id set doc_id = '$last_doc_id'";

		$global_db->dbms_query($query);
		if($global_db->dbms_affected_rows() < 1) {
			die("ERROR: Couldn't update doc_id counter.");
		}

		return $last_doc_id;
	}

	function add_new_doc($doc_id, $doc_name, $class_name) {
		global $global_db;

		$query="insert into documents (doc_id,doc_name,doc_class) values ('$doc_id','$doc_name','$class_name')";
		$global_db->dbms_query($query);
		return ($global_db->dbms_affected_rows() == 1);
	}

	function exists_doc_name($doc_name) {
		global $global_db;

		$query="select count(*) from documents where doc_name='$doc_name'";
		$res= $global_db->dbms_query($query);

		list($cont)= $global_db->dbms_fetch_row($res);

		$global_db->dbms_free_result($res);

		return $cont > 0;
	}

	function update_doc($doc_id, $new_doc_name) {
		global $global_db;

		$query="update documents set doc_name = '$new_doc_name' where doc_id = '$doc_id'";
		$global_db->dbms_query($query);
		if($global_db->dbms_affected_rows() < 1) {
			die("ERROR: Couldn't update document $doc_id with name $new_doc_name at documents index.<br>" . $global_db->dbms_error());
		}
	}

	function delete_doc($doc_id) {
		global $global_db;

		$query="delete from documents where doc_id = '$doc_id'";
		$global_db->dbms_query($query);
		return ($global_db->dbms_affected_rows() == 1);
	}

	function delete_acl_doc($doc_id) {
		global $global_db;

		$query="delete from acl_documents where doc_id = '$doc_id'";
		$global_db->dbms_query($query);
		return ($global_db->dbms_affected_rows() == 1);
	}


	//----------------------------
	// Realitza l'insert a la taula que defineix el grup i la tiplogia d'un objecte
	// quan l'objecte depent d'un altre
	//----------------------------
	function insert_acl_extends($doc_id, $parent_doc_id) {
		global $global_db;

		$query = "insert into acl_documents	select '$doc_id',a2.id_grup, a2.id_tipologia from acl_documents a2 where a2.doc_id='$parent_doc_id'";

		if (!($res = $global_db->dbms_query($query))) {
			html_showError($global_db->dbms_error());
			return false;
		}
		return true;
	}


	function doc_rebuild_index() {
		global $global_db;

		// Rebuild the documents table.
		$classes=array();
		$docs=array();

		// First, get classes
		$tablas=$global_db->dbms_get_tables();
		foreach ($tablas as $tabla) {
			if(strpos($tabla,"class_")===false) continue;
			list($prefijo, $clase) = explode("_",$tabla);
			$classes[]="$clase";
		}

		foreach($classes as $class) {
			$query="select doc_id, doc_name,'" . $class . "' as \"doc_class\" from class_$class";
			$res= $global_db->dbms_query($query);

			if(!$res) continue;

			while($row= $global_db->dbms_fetch_array($res)) {
				$docs[]= $row;
			}

			$global_db->dbms_free_result($res);
		}

		$query= "delete from documents";
		$global_db->dbms_query($query);

		$i=0;
		foreach ($docs as $doc) {
			$i++;
			$query= "insert into documents values('" . $doc["doc_id"] . "','" . $doc["doc_name"] . "','" . $doc["doc_class"] . "')";
			$global_db->dbms_query($query);
		}
		echo "S'han afegit $i documents<br>";
	}

	function get_doc_id($doc_name) {
		global $global_db;

		// doc_id can contains the doc_id or doc_name.

		$query="select doc_id from documents where doc_name = '$doc_name'";
		$res=$global_db->dbms_query($query);

		if(!$res or !(list($doc_id)= $global_db->dbms_fetch_row($res))) {
			return false;
		}

		$global_db->dbms_free_result($res);
		return $doc_id;
	}

	function get_doc_name($doc_id) {
		global $global_db;

		// doc_id can contains the doc_id or doc_name.

		$query="select doc_name from documents where doc_id = '$doc_id'";
		$res=$global_db->dbms_query($query);

		if(!$res or !(list($doc_name)= $global_db->dbms_fetch_row($res))) {
			return false;
		}

		$global_db->dbms_free_result($res);
		return $doc_name;
	}

	function get_doc($doc_reference, &$doc_id, &$doc_name, &$class) {
		global $global_db;

		if(is_numeric($doc_reference)) {
			$where="(documents.doc_id = '$doc_reference' or documents.doc_name = '$doc_reference')";
		} else {
			$where="documents.doc_name = '$doc_reference'";
		}

		$query="
				SELECT documents.doc_id,
					   documents.doc_name,
					   documents.doc_class
				FROM documents
				WHERE $where
		";

		$res=$global_db->dbms_query($query);

		if(!$res or !(list($doc_id, $doc_name, $class)= $global_db->dbms_fetch_row($res))) {
			return 0;
		}

		$global_db->dbms_free_result($res);

		return 1;
	}

	/* Esta funcion sera la encargada de indicar si el usuario actual puede acceder
	 * o no al documento que pide.
	 */
	function can_access_doc($doc_id) {

		return 1;
	}

	function create_class(&$component, $doc_id, $doc_name, $class, $template_id) {
		global $global_index;

		$class_file= SYSHOME . "/objects/classes/" . $class . ".class.php";

		if(!file_exists($class_file)) {
			html_showError("The class $class is not defined.<br>");
			return 0;
		}
		include_once $class_file;

		$doc_ref= get_unique_object_ref($doc_id);
		//$doc_ref= $global_index++;
		$component= new $class($doc_id, $doc_name, $doc_ref, $template_id);

		return 1;
	}

	/**
	 * Returns a unique object identifier using the object name, like doc_id, etc.
	 * The returned value is an integer idexing the objects of the application.
	 * If an object is new, its value is set to 1, if other with the same id is set
	 * before, the index is incremented and returned (2, 3, ...)
	 *
	 * @param string $obj_id
	 * @return integer
	 */
	function get_unique_object_ref($obj_id) {
		global $object_array;
		global $global_index;

		if(!is_array($object_array)) $object_array= array();

		if(!isset($object_array[$obj_id])) $object_array[$obj_id]=0;
		$object_array[$obj_id]++;

		return $object_array[$obj_id];
	}

	/**
	 * Instantiates an existent document.
	 *
	 * @param string $doc_reference, the reference to the document (doc name or doc id)
	 * @param unknown_type $template_reference
	 * @return unknown
	 */
	function &instantiate_doc($doc_reference, $template_reference) {
		global $global_auth;

		global $MESSAGES;

		// Get document information...
		if(!get_doc($doc_reference, $doc_id, $doc_name, $doc_class)) {

			$error_doc_reference= "dummy";
			$template_reference= "document_not_found";
			!get_doc($error_doc_reference, $doc_id, $doc_name, $doc_class) and die("show_page::instantiate_class: document $doc_reference not found");
			!create_class($component, $doc_id, $doc_name, $doc_class, $template_reference) and die("show_page::instantiate_class: error instancing document $doc_reference");
			$component->template->set_tag("DOC_REFERENCE",$doc_reference);
		} else {
			/*
			echo " - doc_id: $doc_id<br>" .
				 " - doc_name: $doc_name<br>" .
				 " - doc_class: $doc_class<br>";
			 */
			!create_class($component, $doc_id, $doc_name, $doc_class, $template_reference) and die("show_page::instantiate_class: error instancing document $doc_reference");
		}
		return $component;
	}

	function show_page($doc_reference, $template_reference) {
		global $form_name;
		global $form;

		$component=null;

		$form_name= "form_base";
		$form= new doc_form($doc_reference, $form_name);

		if($component= & instantiate_doc($doc_reference, $template_reference)) {
			//echo "<table><th>Antes de DETAIL</th><tr><td>" . print_object($form) . "</td></tr></table>";
			// If any element is needed to be present into DETAIL, and no detail is defined.. redefine it:
			if(!$component->exists_element("DETAIL", $ret)) {
				include_once SYSHOME . "/objects/classes/detail.class.php";

				$detail= new detail($doc_reference, $template_reference);
				$form->add_element($detail);
			} else {
				$form->add_element($component);
			}
			//echo "<table><th>Despues de DETAIL</th><tr><td>" . print_object($form) . "</td></tr></table>";

			return $form->form_control();
		}
	}

?>
