<?php

class Login extends MY_Controller {

    function  __construct() {
        parent::__construct();
    }

	function index()
	{
		$this->load->library(array('parser', 'input', 'auth'));

        $login = $this->input->post('s_login');
        $password = $this->input->post('s_password');

        if($this->auth->login($login, $password, TRUE) == TRUE)
        {
            header('Location: ' . config_item('base_url') . 'mod/usercp');
        }
        else
        {
            $data['the_content'] = $this->parser->parse('mod/login_error', array(), TRUE);

            $data['value_email'] = $login;
            $data['value_password'] = $password;

            $this->template->show('register', $data);
        }
	}

    function quit()
    {
        $this->auth->logout();
        header('Location: ' . config_item('base_url'));
    }

}
?>