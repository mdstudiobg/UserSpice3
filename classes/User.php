<?php
/*
UserSpice 3
by Dan Hoover at http://UserSpice.com

a modern version of
UserCake Version: 2.0.2
UserCake created by: Adam Davis
UserCake V2.0 designed by: Jonathan Cassels
*/
class User {
	private $_db, $_data, $_sessionName, $_isLoggedIn, $_cookieName;



	public function __construct($user = null){
		$this->_db = DB::getInstance();
		$this->_sessionName = Config::get('session/session_name');
		$this->_cookieName = Config::get('remember/cookie_name');

		if (!$user) {
			if (Session::exists($this->_sessionName)) {
				$user = Session::get($this->_sessionName);

				if ($this->find($user)) {
					$this->_isLoggedIn = true;
				} else {
					//process Logout
				}
			}
		} else {
			$this->find($user);
		}
	}

	public function create($fields = array()){
		if (!$this->_db->insert('users', $fields)) {
			throw new Exception('There was a problem creating an account.');
		}
	}

	public function find($user = null){
		if ($user) {
			$field = (is_numeric($user)) ? 'id' : 'email';
			$data = $this->_db->get('users', array($field, '=', $user));

			if ($data->count()) {
				$this->_data = $data->first();
				if($this->data()->account_id == 0 && $this->data()->account_owner == 1){
					$this->_data->account_id = $this->_data->id;
				}
				return true;
			}
		}
		return false;
	}

	public function login($username = null, $password = null, $remember = false){

		if (!$username && !$password && $this->exists()) {
			Session::put($this->_sessionName, $this->data()->id);
		} else {
			$user = $this->find($username);

			if ($user) {
				if (password_verify($password,$this->data()->password)) {
					Session::put($this->_sessionName, $this->data()->id);

					if ($remember) {
						$hash = Hash::unique();
						$hashCheck = $this->_db->get('users_session' , array('user_id', '=', $this->data()->id));

							$this->_db->insert('users_session', array(
								'user_id' => $this->data()->id,
								'hash' => $hash,
								'uagent' => Session::uagent_no_version()
							));

						Cookie::put($this->_cookieName, $hash, Config::get('remember/cookie_expiry'));
					}

					return true;
				}
			}
		}
		return false;
	}

	public function exists(){
		return (!empty($this->_data)) ? true : false;
	}

	public function data(){
		return $this->_data;
	}

	public function isLoggedIn(){
		return $this->_isLoggedIn;
	}

	public function logout(){
		$this->_db->query("DELETE FROM users_session WHERE user_id = ? AND uagent = ?",array($this->data()->id,Session::uagent_no_version()));

		Session::delete($this->_sessionName);
		Cookie::delete($this->_cookieName);
	}

	public function update($fields = array(), $id=null){

		if (!$id && $this->isLoggedIn()) {
			$id = $this->data()->id;
		}

		if (!$this->_db->update('users', $id, $fields)) {
			throw new Exception('There was a problem updating.');
		}
	}

	public function hasPermission($key){
		$group = $this->_db->get('permissions', array('id', '=', $this->data()->access));
		if ($group->count()) {
			$permissions = json_decode($group->first()->permissions, true);

			if ($permissions[$key] == true) {
				return true;
			}
		}
		return false;
	}
}
