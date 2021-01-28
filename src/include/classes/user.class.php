<?php

/**
 * @author Sebastian Gomez (tiochan@gmail.com)
 * For: Politechnical University of Catalonia (UPC), Spain.
 *
 * @package lib
 * @subpackage users
 *
 * user class.
 */


class user
{

	public $id_user, $username, $password, $external, $name, $surname;
	public $id_group, $email, $level, $send_notifications, $hiredate, $lang;
	public $deleted;
	public $group_name;
	public $level_name;

	public function user($user_id = -1)
	{

		if ($user_id >= 0) {
			$this->getUserById($user_id);
		}
	}

	public function getUserById($user_id)
	{

		global $global_db, $MESSAGES;

		$query = "select u.*, g.name as group_name from users u, groups g where id_user='" . $user_id . "' and u.id_group = g.id_group";
		$res = $global_db->dbms_query($query);

		if ($global_db->dbms_check_result($res)) {
			$row = $global_db->dbms_fetch_array($res);
			$global_db->dbms_free_result($res);

			foreach ($row as $key => $value) $this->$key = $value;
		}

		if (isset($MESSAGES["SKILL_" . $this->level])) $this->level_name = $MESSAGES["SKILL_" . $this->level];
	}
}
