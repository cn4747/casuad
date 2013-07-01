<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Main extends CI_Controller {

    /**
     * Check if user is authenticated and redirect if not
     */
    private function _check_auth()
    {
        if($this->session->userdata('user_id') === FALSE)
        {
            header('Location: ' . $this->config->item('base_url') . 'admin/login');
        }
    }

	/**
	 * Index Page for this controller.
	 *
	 */
	public function index()
	{
        $this->load->helper('form');
        
        $data = array(
            'base_url'  => $this->config->item('base_url')
        );

        if($this->session->userdata('user_id') != FALSE) {
            $data['title'] = 'Панель управления';
            $this->template_admin->show('panel_index', $data);
        }
        else {
            $data['title'] = 'Вход в систему';
            $this->template_admin->show('login', $data);
        }
	}

    /**
     * Main panel page
     */
    public function panel()
    {
        $this->_check_auth();
        
        $data = array(
            'title'     => 'Панель управления'
        );

        $this->template_admin->show('panel_index', $data);
    }

    public function login()
	{
        $data = array(
            'title'     => 'Вход в систему'
        );

        $this->load->helper(array('form', 'url'));
        $this->load->library(array('form_validation', 'input'));

        $this->form_validation->set_rules('login', 'lang:auth_login', 'trim|required|xss_clean');
        $this->form_validation->set_rules('password', 'lang:auth_pass', 'trim|required|xss_clean');

        if($this->form_validation->run() == TRUE)
        {
            //assign to variables to look compact
            $login = $this->input->post('login', TRUE);
            $password = $this->input->post('password', TRUE);
            $remember = $this->input->post('remember');

            //try to login user
            if($this->auth->login($login, $password, $remember) == FALSE)
            {
                $data['errors'] = $this->auth->get_error();
            }
            else
            {
                header('Location: ' . $this->config->item('base_url') . 'admin/panel');
            }
        }
        else
        {
            $validation_errors = validation_errors();
            if( ! empty($validation_errors))
            {
                $data['errors'] = $validation_errors;
            }
        }

        $this->template_admin->show('login', $data);
	}

    public function manage($type = '')
    {
        $this->_check_auth();

        $data = array();

        if($type === '')
        {
            $data['title'] = 'Управление модулями';
            $this->template_admin->show('manage_select', $data);
        }
        else
        {
            $data['type'] = $type;
            
            if($type == 'admin')
            {
                $admin = TRUE;
                $data['title'] = 'Управление модулями админ. панели';
                $mod_dir = 'mod_admin';
                $view = 'manage';
            }
            else
            {
                $admin = FALSE;
                $data['title'] = 'Управление модулями фронтенда';
                $mod_dir = 'mod';
                $view = 'manage_frontend';
            }

            $this->load->helper(array('file', 'form'));
            $this->load->library('input');
            $this->load->model('admin/modules');

            if($this->input->post('delete'))
            {
                $this->modules->delete_module($this->input->post('file'));
            }

            if($this->input->post('update'))
            {
                $module_data = array(
                    'name' => $this->input->post('name'),
                    'file' => $this->input->post('file'),
                    'enabled' => $this->input->post('enabled')
                );
                if($this->modules->update_module($module_data) == TRUE )
                {
                    $data['message'] = "Модуль `{$module_data['file']}` обновлен";
                }
                else
                {
                    $data['message'] = "Ошибка при обновлении модуля `{$module_data['file']}`";
                }
            }

            $list = get_filenames(config_item('controller_path') . $mod_dir);
            $this->modules->update_modules_list($list, intval($admin));

            $data['modules'] = $this->modules->get_modules($admin);

            $this->template_admin->show($view, $data);
        }
    }

    public function permissions($role_name = '')
    {
        $this->_check_auth();

        $data = array();
        $this->load->helper(array('file', 'form'));
        $this->load->library('input');
        $this->load->model('admin/roles');
        $data['base_url'] = $this->config->item('base_url');

        if($role_name === '')
        {
            $data['title'] = 'Права групп для модулей админ. панели';
            $data['roles_list'] = $this->roles->get_roles();

            if(empty($data['roles_list']))
            {
                $this->template_admin->show('permissions_empty', $data);
            }
            else
            {
                $this->template_admin->show('permissions', $data);
            }
        }
        else
        {
            $data['role_name'] = $role_name;
            $this->roles->update_permissions($role_name);

            if($this->input->post('update'))
            {
                $update = array(
                    'role' => $role_name,
                    'file' => $this->input->post('file'),
                    'perm' => $this->input->post('perm')
                );
                $this->roles->update_module_permissions($update);
                $data['message'] = 'Права обновлены';
            }
            
            $data['title'] = 'Права групп для модулей админ. панели';
            $data['module_list'] = $this->roles->get_modules_by_role_name($role_name);

            if($data['module_list'] == NULL)
            {
                $this->template_admin->show('permissions_no_modules', $data);
            }
            else
            {
                $this->template_admin->show('permissions_for_role', $data);
            }
        }
    }

    public function logout()
    {
        $this->auth->logout();

        header('Location: ' . $this->config->item('base_url') . 'admin');
    }
}

/* End of file main.php */
/* Location: ./application/controllers/admin/main.php */