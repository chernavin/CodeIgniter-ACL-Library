<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * MY_Controller Class
 *
 * Extends default CodeIgniter controller class
 */
class MY_Controller extends CI_Controller {

	// Guest role name
	const GUEST = 'guest';

	/**
	 * Constructor
	 *
	 * @access	public
	 * @return	void
	 */
	public function __construct()
	{
		parent::__construct();
		
		// Load Session library
		$this->load->library('Session');
		
		// Run access control
		$this->_run_access_control();
	}
	
	/**
	 * Access control
	 *
	 * @access	public
	 * @return	void
	 */
	private function _run_access_control()
	{
		// Load Acl library
		$this->load->library('Acl');
		
		/**
		 * Get user role id.
		 * User gets it after authentication.
		 * The dummy authentication: user/auth
		 */
		
		$user_role_id = $this->session->userdata('role_id') ?
						$this->session->userdata('role_id') :
						self::GUEST;
		
		/**
		 * We will be use controller as resource.
		 * Privilege will be a controller method.
		 */
		
		$cur_resource_id = $this->router->fetch_class();
		$cur_privilege_id = $this->router->fetch_method();
		
		/**
		 * Populate Acl object with static data, for example.
		 * Also, you can use a database and other storage.
		 */
		
		$role_arr = array(self::GUEST, 'member');
		$resource_arr = array('user', 'poll');
		
		// Add roles to Acl object
		foreach ($role_arr as $role_id)
		{
			$this->acl->add_role($role_id);
		}
		
		// Add resources to Acl object
		foreach ($resource_arr as $resource_id)
		{
			$this->acl->add_resource($resource_id);
		}
		
		/**
		 * Set "blacklist" (allow to all).
		 * By default Acl class has a "whitelist" (deny to all).
		 */
		
		$this->acl->allow();
		
		/**
		 * Deny "user/profile" and "user/logout" for a guest.
		 * Deny "poll/*" for a guest.
		 */
		
		$this->acl->deny(self::GUEST, 'user', array('profile', 'logout'));
		$this->acl->deny(self::GUEST, 'poll');
		
		/**
		 * Check user privilege.
		 */
		
		if ( ! $this->acl->is_allowed($user_role_id, $cur_resource_id, $cur_privilege_id))
		{
			show_error('You do not have permission.', 403);
		}
	}

}
// END MY_Controller Class

/* End of file MY_Controller.php */
/* Location: ./application/core/MY_Controller.php */