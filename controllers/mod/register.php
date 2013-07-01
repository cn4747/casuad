<?php

class Register extends MY_Controller {

    function  __construct() {
        parent::__construct();
        
        $this->load->library('email');
    }

	function index()
	{
		$this->load->helper(array('form', 'url'));
        $this->load->model('user_data');
		$this->load->library(array('form_validation', 'parser', 'input'));

        $data = array(
            //'value_nick'        =>  '',
            'value_password1' => '',
            'value_password2' => '',
            'value_email' => ''
        );

        if($this->input->post('register'))
        {
            $table_users = $this->user_data->get_table_users();

            /*$this->form_validation->set_rules(
                    'nick',
                    'Ник',
                    "trim|is_unique[{$table_users}.nick]|required|xss_clean"
            );*/

            $this->form_validation->set_rules(
                    'accept',
                    'Условия сайта',
                    "callback_accept_terms"
            );

            $this->form_validation->set_rules(
                    'password1',
                    'Пароль',
                    'trim|required|matches[password2]|xss_clean'
            );

            $this->form_validation->set_rules(
                    'password2',
                    'Подтверждение пароля',
                    'trim|required|xss_clean'
            );

            $this->form_validation->set_rules(
                    'email',
                    'E-Mail',
                    "trim|is_unique[{$table_users}.email]|required|valid_email|xss_clean"
            );

            if ($this->form_validation->run() == FALSE)
            {
                $data['message'] = parent::_get_message(validation_errors());
                //$data['value_nick'] = set_value('nick');
                $data['value_password1'] = set_value('password1');
                $data['value_password2'] = set_value('password2');
                $data['value_email'] = set_value('email');
                $data['value_accept'] = set_checkbox('accept', '1');
            }
            else
            {
                $insert = array(
                    //'login'     =>  $this->input->post('nick'),
                    'password'  =>  $this->input->post('password1'),
                    'email'     =>  $this->input->post('email')
                );

                $user_hash = $this->user_data->register_user($insert);

                $confirm_url = $this->config->item('base_url') . 'mod/register/confirm/' . $user_hash;
                $body = "Вы зарегистрировались с такими данными:\n";
                //$body .= 'Логин: ' . $this->input->post('nick') . "\n";
                $body .= 'Пароль: ' . $this->input->post('password1') . "\n\n";
                $body .= 'Для подтверждения регистрации пройдите по ссылке ' . $confirm_url;
                
                $this->email->from(config_item('email_address'), config_item('email_name'));
                $this->email->to($this->input->post('email'));

                $this->email->subject('Регистрация на сайте');
                $this->email->message($body);

                $this->email->send();

                $data['the_content'] = $this->parser->parse('mod/register_success', $data, TRUE);
                $this->template->show('register', $data);

                return;
            }
        }

        $data['the_content'] = $this->parser->parse('mod/register_index', $data, TRUE);
        $this->template->show('register', $data);
	}

    public function confirm($hash = '')
    {
        $this->load->library('parser');

        $data = array();
        
        if($hash == '')
        {
            $data['the_content'] = $this->parser->parse('mod/register_confirm_fail', $data, TRUE);
            $this->template->show('register', $data);
        }
        else
        {
            $this->load->model('user_data');
            
            if($this->user_data->activate_account($hash) == FALSE)
            {
                $data['the_content'] = $this->parser->parse('mod/register_confirm_fail', $data, TRUE);
                $this->template->show('register', $data);
            }
            else
            {
                $data['the_content'] = $this->parser->parse('mod/register_confirm_success', $data, TRUE);
                $this->template->show('register', $data);
            }
        }
    }

    public function recover()
    {
        $this->load->helper('form');
        $this->load->library(array('input', 'form_validation', 'parser'));
        $this->load->model('user_data');

        $data = array();
        $data['value_email'] = '';

        $this->form_validation->set_rules(
                'email',
                'E-Mail',
                "trim|required|valid_email|xss_clean"
        );

        if ($this->form_validation->run() == FALSE)
        {
            $data['message'] = $this->_get_message(validation_errors());
            $data['value_email'] = set_value('email');

            $data['the_content'] = $this->parser->parse('mod/register_recover', $data, TRUE);
            $this->template->show('register', $data);
        }
        else {
            if($this->user_data->check_email($this->input->post('email')) == TRUE)
            {
                $password = $this->user_data->get_new_password_for_email($this->input->post('email'));

                //send new password to user
                $this->email->from(config_item('email_address'), config_item('email_name'));
                $this->email->to($this->input->post('email'));

                $this->email->subject($this->lang->line('email_recover'));

                $body = $this->parser->parse_string(
                        $this->lang->line('email_recover_body'),
                        array(
                            'password'      =>  $password,
                            'site_url'      => config_item('base_url')
                        ),
                        TRUE);
                $this->email->message($body);
                $this->email->send();

                $data['the_content'] = $this->parser->parse('mod/register_recover_success', $data, TRUE);
                $this->template->show('register', $data);
            }
            else
            {
                $data['value_email'] = set_value('email');
                $data['message'] = $this->_get_message($this->lang->line('message_email_nonexist'));
                $data['the_content'] = $this->parser->parse('mod/register_recover', $data, TRUE);
                $this->template->show('register', $data);
            }
        }
    }

    /**
     * Set new email for user
     *
     * @param int $id User ID in database
     */
    public function change($id = '')
    {
        $this->load->library(array('parser'));
        
        if($id == '' OR $id == 0)
        {
            $data['the_content'] = $this->parser->parse('mod/register_confirm_fail', array(), TRUE);
            $this->template->show('register', $data);
        }
        else
        {
            $this->load->model('admin/operate_users');

            $email = $this->operate_users->get_confirm_email($id);

            //just in case if we get NULL while getting confirm email
            if($email !== NULL)
            {
                $update = array(
                    'email'         =>  $email,
                    'confirm_email' =>  '',
                    'id'            =>  $id
                );

                $this->operate_users->update_user($update);

                $data['the_content'] = $this->parser->parse('mod/change_success', array(), TRUE);
                $this->template->show('register', $data);

                //update session
                $this->auth->update_userdata(array('id' => $id));
            }
        }
    }

    /**
     * Accept checkbox validation
     *
     * @param string $value Checkbox value. '1' is set in form
     * @return bool
     */
    public function accept_terms($value)
    {
        if($value == '1')
        {
            return TRUE;
        }
        else
        {
            $this->form_validation->set_message('accept_terms', $this->lang->line('message_accept_terms'));
            return FALSE;
        }
    }
}
?>