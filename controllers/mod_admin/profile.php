<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require(APPPATH . 'core/Admin_Module.php');

class Profile extends Admin_Module
{
    public function  __construct()
    {
        parent::__construct();

        $this->load->model('admin/modules');
        $this->load->model('admin/operate_users');

        $this->base_url = $this->config->item('base_url') .
                    'mod_admin/' .
                    str_replace('.php', '', basename(__FILE__)) . '/';
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
        $this->load->library(array('form_validation'));
        $this->load->model('operate_users');

        if($this->input->post('save'))
        {
            if($this->input->post('edit_login') != $this->input->post('login'))
            {
                $unique = 'is_unique[' . $this->operate_users->get_users_table() . '.login]|';
                $this->form_validation->set_rules('edit_login', 'Ник', 'trim|'.$unique.'xss_clean');
            }
            if($this->input->post('edit_email') != $this->input->post('email'))
            {
                $unique = 'is_unique[' . $this->operate_users->get_users_table() . '.email]|';
                $this->form_validation->set_rules('edit_email', 'E-Mail', 'trim|'.$unique.'valid_email|xss_clean');
            }
            $this->form_validation->set_rules('edit_password1', 'Новый пароль', 'trim|matches[edit_password2]|xss_clean');
            $this->form_validation->set_rules('edit_password2', 'Новый пароль, еще раз', 'trim|xss_clean');

            if($this->form_validation->run() == TRUE)
            {
                $login = $this->input->post('edit_login');
                $password = $this->input->post('edit_password1');
                $email = $this->input->post('edit_email');

                $update = array(
                    'login'     =>  $login,
                    'password'  =>  $password,
                    'email'     =>  $email,
                    'id'        =>  $this->operate_users->get_user_id($this->session->userdata('email'))
                );

                //upload avatar
                $config['upload_path'] = './uploads/avatars/';
                $config['allowed_types'] = 'gif|jpg|png';
                $config['max_size'] = '2048';
                $config['file_name'] = $this->session->userdata('login');

                $this->load->library('upload', $config);

                if( ! empty($_FILES['edit_avatar']['tmp_name']))
                {
                    if ( ! $this->upload->do_upload('edit_avatar')) {
                        $message = $this->upload->display_errors();
                    }
                    else {
                        $filedata = $this->upload->data();

                        //resize avatar
                        $config['image_library'] = 'gd2';
                        $config['source_image'] = $filedata['full_path'];
                        $config['maintain_ratio'] = TRUE;
                        $config['width'] = $this->config->item('ava_width');
                        $config['height'] = $this->config->item('ava_height');

                        $this->load->library('image_lib', $config);

                        $this->image_lib->resize();

                        $update['avatar'] = $filedata['file_name'];

                        $old_avatar = $this->operate_users->get_avatar($this->session->userdata('email'));
                        if($update['avatar'] != $old_avatar)
                        {
                            $this->operate_users->delete_avatar($old_avatar, $update['avatar']);
                        }
                    }
                }

                $this->operate_users->update_user($update);
                $this->auth->update_userdata($update);
            }
            else
            {
                $errors = validation_errors();
                if( ! empty($errors))
                {
                    $message = $errors;
                }
            }
        }
        
        $data = $this->operate_users->get_user_data($this->session->userdata('nick'));

        if(isset($message))
        {
            $data['message'] = $message;
        }

        if (isset($data['avatar']) && !empty($data['avatar']))
        {
            $data['avatar'] = '<img src="' . $this->config->item('base_url') . 'uploads/avatars/' . $data['avatar'] . '" alt="Аватар" />';
        }

        $this->template_admin->show('profile_index', $data);
	}

}

/* End of file profile.php */
/* Location: ./application/controllers/admin/profile.php */
