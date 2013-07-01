<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require(APPPATH . 'core/Admin_Module.php');

class Manage_Users extends Admin_Module
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
	public function index()
	{
        $data = array(
            'title'      => $this->title,
            'role_list'  => $this->operate_users->get_roles(),
            'base_url'   => $this->base_url
        );

        if(empty($data['role_list']))
        {
            $this->template_admin->show('empty', $data);
        }
        else
        {
            $this->template_admin->show('manage_users_roles', $data);
        }
	}

    /**
     * Show user list by role name
     */
    public function role($role_name)
    {
        $this->load->helper('form');
        $this->load->library('input');

        $data = array(
            'title'           => $this->title,
            'base_url'        => $this->base_url,
            'current_url'     => $this->base_url . 'role/' . $role_name . '/',
            'role_title'      => $this->operate_users->get_role_title($role_name),
            'roles_list'      => $this->operate_users->get_roles_list(),
            'role_name'       => $role_name
        );

        if($this->input->post('update'))
        {
            $update = array(
                'id'        =>  $this->input->post('user_id'),
                'active'    =>  $this->input->post('active'),
                'email'     =>  $this->input->post('email'),
                'password'  =>  $this->input->post('password'),
                'role_id'   =>  $this->input->post('role_id')
            );

            $this->operate_users->update_user($update);
            $data['message'] = 'Данные обновлены';
        }

        if($this->input->post('delete'))
        {
            $this->operate_users->delete_user($this->input->post('user_id'));
            $data['message'] = 'Пользователь удален';
        }

        $data['user_list'] = $this->operate_users->get_users($role_name);

        if(empty($data['user_list']))
        {
            $this->template_admin->show('empty', $data);
        }
        else
        {
            $this->template_admin->show('manage_users_list', $data);
        }
    }

    /**
     * Add user
     */
    public function add()
    {
        $this->load->helper('form');
        $this->load->library('form_validation');

        $data = array(
            'title'         =>  $this->title,
            'base_url'      =>  $this->base_url,
            'roles_list'      => $this->operate_users->get_roles_list()
        );

        if($this->input->post('add'))
        {
            $users = $this->operate_users->get_users_table();
            
            $this->form_validation->set_rules(
                    'ins_nick',
                    'Ник',
                    "trim|is_unique[{$users}.nick]|required|xss_clean"
            );
            $this->form_validation->set_rules(
                    'ins_password',
                    'Пароль',
                    'trim|required|xss_clean'
            );
            $this->form_validation->set_rules(
                    'ins_email',
                    'E-Mail',
                    "trim|valid_email|is_unique[{$users}.email]|required|xss_clean"
            );

            if($this->form_validation->run() == TRUE)
            {
                $add = array(
                    'nick'     =>  $this->input->post('ins_nick'),
                    'password'  =>  $this->input->post('ins_password'),
                    'email'     =>  $this->input->post('ins_email'),
                    'active'    =>  $this->input->post('ins_active'),
                    'role_id'   =>  $this->input->post('ins_role')
                );

                $this->operate_users->add_user($add);
                $data['message'] = 'Пользователь добавлен';
            }
            else
            {
                $data['message'] = validation_errors();
            }
        }
        
        $this->template_admin->show('manage_users_add', $data);
    }
}

/* End of file manage_users.php */
/* Location: ./application/controllers/admin/manage_users.php */
