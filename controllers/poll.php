<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Poll Controller Class
 */
class Poll extends MY_Controller {

	// Question
	private $question = 'What is ACL?';
	// Answer list
	private $answer_list = array(
		1 => 'Access control list.',
		'Access control layer.',
		'Assert control list.'
	);

	/**
	 * Poll
	 *
	 * @access	public
	 * @return	void
	 */
	public function index()
	{
		// Set message to user
		$msg = 'Please, answer the question.';
		
		// View data
		$view_data = array();
		
		// Save user answer in cookie
		if ($this->input->post('answer'))
		{
			$answer_id = intval($this->input->post('answer'));
			$this->input->set_cookie(array(
				'name' => 'poll_answer',
				'value' => $answer_id,
				'expire' => 2592000,
			    'path'   => '/'
			));
			
			$view_data = array('question' => 'Thanks for your vote.');
		}
		else
		{
			$view_data = array(
				'question' => $this->question,
				'answer_list' => $this->answer_list
			);
		}
		
		// Load view
		$this->load->view('layout/header', array('msg' => $msg));
		$this->load->view('poll/index', $view_data);
		$this->load->view('layout/footer');
	}
	
	/**
	 * Last answer
	 *
	 * @access	public
	 * @return	void
	 */
	public function answer()
	{
		// Set message to user
		$msg = 'Your last answer: %s.';
		
		// Get last answer
		$answer_id = intval($this->input->cookie('poll_answer'));
		
		if ($answer_id)
		{
			$msg = 'Your last answer was: <i>' . $this->answer_list[$answer_id] . '</i>';
		}
		else
		{
			$msg = 'You are not participate in the poll.';
		}
		
		// Load view
		$this->load->view('layout/header', array('msg' => $msg));
		$this->load->view('layout/footer');
	}

}
// END Poll Class

/* End of file poll.php */
/* Location: ./application/controllers/poll.php */