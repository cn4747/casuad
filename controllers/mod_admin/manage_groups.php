<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require(APPPATH . 'core/Admin_Module.php');

class Manage_Groups extends Admin_Module
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
        $this->load->helper('form');
        $this->load->library(array('input', 'form_validation'));

        $data = array(
            'title'      => $this->title,
            'base_url'   => $this->base_url
        );

        if($this->input->post('update'))
        {
            $unique = '';
            //if new name and old name do not match...
            if($this->input->post('name') != $this->input->post('old_name'))
            {
                //must set is_unique validation rule
                $unique = 'is_unique[' . $this->operate_users->get_roles_table() . '.name]|';
            }

            $this->form_validation->set_rules('name', 'Метка', 'trim|'.$unique.'required|xss_clean');
            $this->form_validation->set_rules('title', 'Название', 'trim|required|xss_clean');
            $this->form_validation->set_rules('descr', 'Описание', 'trim|required|xss_clean');

            if($this->form_validation->run() == TRUE)
            {
                $update = array(
                    'name' => $this->input->post('name'),
                    'title' => $this->input->post('title'),
                    'descr' => $this->input->post('descr'),
                    'id' => $this->input->post('id')
                );

                $this->operate_users->update_role($update);
                $data['message'] = 'Данные обновлены';
            }
            else
            {
                $data['message'] = validation_errors();
            }
        }

        if($this->input->post('delete'))
        {
            $this->operate_users->delete_role($this->input->post('id'));
            $data['message'] = 'Группа удалена';
        }

        $data['role_list'] = $this->operate_users->get_roles();
        
        if(empty($data['role_list']))
        {
            $this->template_admin->show('empty', $data);
        }
        else
        {
            $this->template_admin->show('manage_groups_list', $data);
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
            $roles = $this->operate_users->get_roles_table();
            $this->form_validation->set_rules('name', 'Метка', "trim|is_unique[{$roles}.name]|required|xss_clean");
            $this->form_validation->set_rules('title', 'Название', 'trim|required|xss_clean');
            $this->form_validation->set_rules('descr', 'Описание', 'trim|xss_clean');

            if($this->form_validation->run() == TRUE)
            {
                $add = array(
                    'name'      =>  $this->input->post('name'),
                    'title'     =>  $this->input->post('title'),
                    'descr'     =>  $this->input->post('descr')
                );

                $this->operate_users->add_role($add);
                $data['message'] = 'Группа добавлена';
            }
            else
            {
                $data['message'] = validation_errors();
            }
        }
        
        $this->template_admin->show('manage_groups_add', $data);
    }
}

/* End of file manage_users.php */
/* Location: ./application/controllers/admin/manage_users.php */
