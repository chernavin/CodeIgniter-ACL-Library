<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * User Controller Class
 */
class User extends MY_Controller {

	/**
	 * Home
	 *
	 * @access	public
	 * @return	void
	 */
	public function index()
	{
		// Set message to user
		$msg = 'Please, use above menu to test work of ACL library.';
		
		// Load view
		$this->load->view('layout/header', array('msg' => $msg));
		$this->load->view('layout/footer');
	}

	/**
	 * Login
	 *
	 * @access	public
	 * @return	void
	 */
	public function login()
	{
		// Set message to user
		$msg = 'Click the button to log in.';

		// The dummy authentication
		if ($this->input->post())
		{
			$this->session->set_userdata('role_id', 'member');
			$msg = 'You successfully logged in.';
		}
		
		// Load view
		$this->load->view('layout/header', array('msg' => $msg));
		$this->load->view('user/login');
		$this->load->view('layout/footer');
	}
	
	/**
	 * Logout
	 *
	 * @access	public
	 * @return	void
	 */
	public function logout()
	{
		// Set message to user
		$msg = 'Click the button to log out.';
		
		// Process log out
		if ($this->input->post())
		{
			$this->session->unset_userdata('role_id');
			$msg = 'You successfully logged out.';
		}
		
		// Load view
		$this->load->view('layout/header', array('msg' => $msg));
		$this->load->view('user/logout');
		$this->load->view('layout/footer');
	}
	
	/**
	 * Profile
	 */
	public function profile()
	{
		// Set message to user
		$msg = 'The dummy user profile.';
		
		// Load view
		$this->load->view('layout/header', array('msg' => $msg));
		$this->load->view('layout/footer');
	}

}
// END User Class

/* End of file user.php */
/* Location: ./application/controllers/user.php */