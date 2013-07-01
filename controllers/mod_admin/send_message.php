<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require(APPPATH . 'core/Admin_Module.php');

class Send_Message extends Admin_Module
{
    private $title;
    private $base_url;

    public function  __construct()
    {
        parent::__construct();

        $this->load->model('admin/modules');
        $this->load->model('admin/operate_users');

        $this->base_url = $this->config->item('base_url') .
                    'mod_admin/' .
                    str_replace('.php', '', basename(__FILE__)) . '/';

        $this->title = $this->modules->get_title(basename(__FILE__));
    }

    /**
     * Check if user is permitted to access the module
     *
     * @param string $method Method name to invoke
     */
    public function _remap($method, $params = array())
    {
        if(parent::_is_permitted(basename(__FILE__)) == TRUE)
        {
            if (method_exists($this, $method)) {
                return call_user_func_array(array($this, $method), $params);
            }
            show_404();
        }
    }

	/**
	 * User management index page
	 */
	public function index($page = '1')
	{
        $this->load->model(array('messages', 'admin/operate_users'));
        $this->load->library(array('input', 'pagination'));

        $data = array(
            'title'         =>  $this->title,
            'base_url'      =>  $this->config->item('base_url')
        );

        $config['base_url'] = $this->base_url . 'page/';
        $config['total_rows'] = $this->operate_users->get_users_count('user');
        $config['per_page'] = 20;
        $config['prev_link'] = '&lt;';
        $config['next_link'] = '&gt;';
        $config['uri_segment'] = 4;

        $this->pagination->initialize($config);
        $data['pagination'] = $this->pagination->create_links();

        $data['users'] = $this->operate_users->get_users('user', $page, $config['per_page']);

        $this->template_admin->show('send_message_users', $data);
	}

    public function page($page = '1')
    {
        $this->index($page);
    }

    public function user($id = NULL)
    {
        if($id === NULL)
        {
            show_404();
        }
        else
        {
            $this->load->model(array('messages', 'operate_users'));
            $this->load->library('form_validation');

            if($this->input->post('send'))
            {
                $this->form_validation->set_rules('title', 'Тема', 'trim|required|xss_clean');
                $this->form_validation->set_rules('body', 'Сообщение', 'trim|required|xss_clean');

                if($this->form_validation->run() == TRUE)
                {
                    $input = array(
                        'id_user'   =>  $id,
                        'id_from'   =>  $this->session->userdata('user_id'),
                        'title'     =>  $this->input->post('title'),
                        'body'      =>  $this->input->post('body'),
                        'date'      =>  time(),
                        'read'      =>  '0'
                    );
                    $this->messages->send_message($input);

                    $data['message'] = 'Сообщение отправлено';

					$user_data = $this->operate_users->get_user_data_by_id($id);

					$this->load->library(array('email', 'parser'));
					//send new password to user
					$this->email->from(config_item('email_address'), config_item('email_name'));
					$this->email->to($user_data['email']);

					$this->email->subject($this->lang->line('email_pm_title'));

					$body = $this->parser->parse_string(
							$this->lang->line('email_pm_body'),
							array(
								'site_url'	=>	config_item('base_url')
							),
							TRUE);
					$this->email->message($body);
					$this->email->send();
                }
                else
                {
                    $data['message'] = validation_errors();
                }
            }

            $data['title'] = $this->title;
            $data['base_url'] = $this->base_url;
            $user_data = $this->operate_users->get_user_data_by_id($id);
            $data['username'] = $user_data['nick'];
            $data['user_id'] = $id;

            $data['messages'] = $this->messages->get_messages($id);
            
            $this->template_admin->show('send_message_user', $data);
        }
    }

    public function delete($id, $id_user)
    {
        $this->load->model('messages');

        $this->messages->delete($id);

        $this->user($id_user);
    }
}

/* End of file send_message.php */
/* Location: ./application/controllers/mod_admin/send_message.php */