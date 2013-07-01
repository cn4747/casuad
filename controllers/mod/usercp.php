<?php

class Usercp extends MY_Controller {

    function  __construct() {
        parent::__construct(1);
        $this->lang->load('main', $this->config->item('language'));
    }

	function index($page = '1')
	{
        $this->load->model('videos');
        $this->load->library(array('time', 'parser', 'pagination'));
        
        $data['noadv'] = TRUE;
        $data['usercp'] = TRUE;

        $data['user_nick'] = $this->session->userdata('nick');
        $data['user_email'] = $this->session->userdata('email');
        
        /** videos added by user **/
        $config['base_url'] = $this->config->item('base_url') . 'mod/usercp/page/';
        $config['total_rows'] = $this->videos->get_user_videos_count($this->session->userdata('user_id'));
        $config['per_page'] = 4;
        $config['uri_segment'] = 4;

        $this->pagination->initialize($config);

        $video_list = $this->videos->get_user_videos($this->session->userdata('user_id'), $page, $config['per_page']);
        if($video_list === NULL)
        {
            $data['myvideos'] = $this->parser->parse('mod/usercp_myvideos_notfound', array(), TRUE);
        }
        else
        {
            foreach($video_list as $i => $item)
            {
                foreach ($item as $key => $value) {
                    if ($key == 'time')
                        $video_list[$i][$key] = $this->time->transform($value);
                    else if($key == 'thumb')
                    {
                        if($value == '0')
                            $video_list[$i][$key] = 'images/video_loading.jpg';
                        else
                            $video_list[$i][$key] = 'uploads/video_thumbs/' . $video_list[$i]['link'] . '.jpg';
                    }
                }
            }

            $myvideos_params = array(
                'video_list'    =>  $video_list,
                'pagination'    =>  $this->pagination->create_links()
            );
            $data['myvideos'] = $this->parser->parse('mod/usercp_myvideos_list', $myvideos_params, TRUE);
        }

        /** videos with bonus points **/
        $points_item = $this->videos->get_videos_with_points($this->session->userdata('user_id'), 1);
        if(is_array($points_item))
        {
            $data['points_block'] = $this->parser->parse(
                    'mod/usercp_points_block',
                    array('points_item' => $points_item),
                    TRUE
            );
			$data['points_url'] = 'mod/bonus';
        }
        else
        {
            $data['points_block'] = '';
			$data['points_url'] = '';
        }
        $data['points_sum'] = intval($this->videos->get_user_points($this->session->userdata('user_id')));

        $this->template->show('usercp_index', $data);
	}

    function page($page = '1')
    {
        $this->index($page);
    }

    /**
     * Change user profile data
     */
    function data()
    {
        //if user submitted the form
        if($this->input->post('change'))
        {
            $this->load->helper(array('form'));
            $this->load->library(array('form_validation', 'input'));
            $this->load->model(array('user_data', 'admin/operate_users'));

            //get table name to compare input with data in db
            $table_users = $this->user_data->get_table_users();

            //
            //validation rules
            //
            if($this->input->post('nick') != $this->input->post('old_nick'))
            {
                $this->form_validation->set_rules(
                        'nick',
                        'Ник',
                        "trim|is_unique[{$table_users}.nick]|required|xss_clean"
                );
            }

            if($this->input->post('email') != $this->input->post('old_email'))
            {
                $this->form_validation->set_rules(
                        'email',
                        'E-Mail',
                        "trim|is_unique[{$table_users}.email]|required|valid_email|xss_clean"
                );
            }

            $this->form_validation->set_rules(
                    'password1',
                    'Пароль',
                    'trim|matches[password2]|xss_clean'
            );

            $this->form_validation->set_rules(
                    'password2',
                    'Подтверждение пароля',
                    'trim|xss_clean'
            );
            //
            //end of validation rules
            //

            if($this->form_validation->run() == TRUE)
            {
                $user_id = $this->operate_users->get_user_id($this->session->userdata('email'));
                $update = array(
                    'nick'      =>  $this->input->post('nick'),
                    'password'  =>  $this->input->post('password1'),
                    'id'        =>  $user_id
                );

                if($this->input->post('email'))
                {
                    if($this->session->userdata('email') == FALSE)
                    {
                        $update = array(
                            'email'         =>  $this->input->post('email'),
                            'confirm_email' =>  '',
                            'id'            =>  $user_id
                        );

                        $this->operate_users->update_user($update);

                        //update session
                        $this->auth->update_userdata(array('id' => $user_id));
                    }
                    else
                    {
                        //add new mail to temporary field in database
                        $update['confirm_email'] = $this->input->post('email');
                        //and send email changing confirmation
                        $this->_email_change($this->input->post('email'));
                        //set notification
                        $data['message'] = $this->_get_notification($this->lang->line('message_email_change'));
                    }
                }

                //update database
                $this->operate_users->update_user($update);
                //update session
                $this->auth->update_userdata($update);
            }
            else
            {
                //get errors, if any
                $errors = validation_errors();
                if( ! empty($errors))
                {
                    $data['message'] = $this->_get_message($errors);
                }
            }
        }

        //flag for header view
        $data['usercp'] = TRUE;

        $this->template->show('usercp_data', $data);
        
    }

    /**
     * Change user profile picture
     */
    function picture()
    {
        $this->load->helper('html');

        if( ! empty($_FILES['avatar']['tmp_name']))
        {
            //upload avatar
            $config['upload_path'] = './uploads/avatars/';
            $config['allowed_types'] = 'gif|jpg|png';
            $config['max_size'] = '2048';
            $config['overwrite'] = TRUE;
            $config['file_name'] = $this->session->userdata('login');

            $this->load->library('upload', $config);
                
            if ( ! $this->upload->do_upload('avatar')) {
                $data['message'] = $this->_get_message($this->upload->display_errors());
            }
            else {
                $this->load->model('admin/operate_users');
                
                $filedata = $this->upload->data();

                //resize avatar
                $config['image_library'] = 'gd2';
                $config['source_image'] = $filedata['full_path'];
                $config['maintain_ratio'] = TRUE;
                $config['width'] = $this->config->item('ava_width');
                $config['height'] = $this->config->item('ava_height');

                $this->load->library('image_lib', $config);
                $this->image_lib->resize();

                //create avatar thumb
                $config['image_library'] = 'gd2';
                $config['source_image'] = $filedata['full_path'];
                $config['new_image'] = $filedata['file_path'] . 'thumb_' . $filedata['file_name'];
                $config['maintain_ratio'] = TRUE;
                $config['width'] = $this->config->item('ava_thumb_width');
                $config['height'] = $this->config->item('ava_thumb_height');

                $this->image_lib->initialize($config);
                $this->image_lib->resize();

                $update['avatar'] = $filedata['file_name'];
                $update['id'] = $this->operate_users->get_user_id($this->session->userdata('email'));

                $old_avatar = $this->operate_users->get_avatar($this->session->userdata('email'));
                if ( ! empty($old_avatar) && $update['avatar'] != $old_avatar) {
                    $this->operate_users->delete_avatar($old_avatar, $update['avatar']);
                }
                
                $this->operate_users->update_user($update);
                $this->auth->update_userdata($update);
            }
        }

        //flag for header view
        $data['usercp'] = TRUE;

        $this->template->show('usercp_picture', $data);
    }

    public function messages()
    {
        $this->load->model('messages');

        $data['title'] = 'Сообщения';

        $data['messages'] = $this->messages->get_messages($this->session->userdata('user_id'));

        if(is_array($data['messages'])) foreach($data['messages'] as $i => $item)
        {
            foreach($item as $key => $value)
            {
                if($key == 'date')
                {
                    $data['messages'][$i][$key] = date('d.m.Y - H:i', $value);
                }
                else if($key == 'read')
                {
                    if($value == '0')
                    {
                        $data['messages'][$i]['message_title'] = '<b>' . $data['messages'][$i]['message_title'] . '</b>';
                    }
                    else
                    {
                        $data['messages'][$i]['unread'] = '';
                    }
                }
            }
        }

        if($data['messages'] === NULL)
        {
            $this->template->show('messages_empty', $data);
        }
        else
        {
            $this->template->show('messages_list', $data);
        }
    }

    public function message($id = NULL)
    {
        if($id === NULL)
        {
            show_404();
        }
        else
        {
            $this->load->model('messages');

            $this->messages->set_read($id);
            $data = $this->messages->get_message($id);
            $data['title'] = 'Сообщение';

            $this->template->show('message_single', $data);
        }
    }

    private function _email_change($email)
    {
        $this->load->library(array('email', 'parser'));
        $this->load->model('admin/operate_users');

        $this->email->from(config_item('email_address'), config_item('email_name'));
        $this->email->to($this->input->post('email'));

        $this->email->subject($this->lang->line('email_change'));

        $body = $this->parser->parse_string(
                        $this->lang->line('email_change_body'),
                        array(
                            'user_id' => $this->operate_users->get_user_id($this->session->userdata('email')),
                            'site_url' => $this->config->item('base_url')
                        ),
                        TRUE);
        $this->email->message($body);
        $this->email->send();
    }
}
?>