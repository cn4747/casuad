<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Template
 *
 * Template loader for automated loading of template blocks
 *
 * @author		Anton Martynenko (http://niggaslife.ru)
 * @version		0.1
 * @license		WTFPL
 */
class Template {
    private $folder = 'frontend';
    private $view_folder = 'frontend';

	function __construct()
	{
		$this->ci =& get_instance();
        $this->ci->load->model('admin/modules');
	}

    /**
     * Set base folder name to load wrapper views from it
     *
     * @param string $folder Subfolder name in views directory
     */
    public function set_folder($folder)
    {
        $this->folder = $folder;
    }

    /**
     * Set folder name to load view from it
     *
     * @param string $folder Subfolder name in views directory
     */
    public function set_view_folder($folder)
    {
        $this->view_folder = $folder;
    }

    /**
     * Shows a view with custom blocks layout
     *
     * @param string $view - view name to be loaded
     * @param array  $data - array with data
     */
    public function show($view, &$data = NULL) {
            //loading module title from db
            if( ! isset($data['title']))
            {
                $this->ci->load->model(array('modules'));
                $data['title'] = $this->ci->modules->get_title($this->ci->uri->segment(2) . '.php');
            }

            //setting message to empty, if it is not set
            if( ! isset($data['message']))
            {
                $data['message'] = '';
            }

            $this->ci->load->model('messages');

            $data['base_url'] = $this->ci->config->item('base_url');
            $data['base_url_encoded'] = urlencode($this->ci->config->item('base_url'));
            $data['current_url'] = $this->ci->config->item('base_url') . $this->ci->uri->uri_string();


            $data['num_messages'] = $this->ci->messages->get_new_messages_count($this->ci->session->userdata('user_id'));

            $this->ci->load->library('parser');
            
            if((isset($data['usercp']) && $data['usercp'] === TRUE) OR $this->ci->session->userdata('user_id'))
            {
                $this->ci->load->helper('text');

                $data['user_email'] = $this->ci->session->userdata('email');
                if(empty($data['user_email']))
                    $data['header_email'] = 'Кабинет';
                else
                    $data['header_email'] = ellipsize($data['user_email'], 17, .15);

                $data['user_nick'] = $this->ci->session->userdata('nick');

                if($this->ci->session->userdata('avatar'))
                {
                    $data['avatar_thumb'] = 'thumb_' . $this->ci->session->userdata('avatar');
                    $data['avatar'] = $this->ci->session->userdata('avatar');
                }
                else
                {
                    $data['avatar_thumb'] = 'thumb_empty.png';
                    $data['avatar'] = 'empty.png';
                }

                $data['header_block'] = $this->ci->parser->parse($this->folder . '/header_usercp', $data, TRUE);
            }
            else
            {
                $data['header_block'] = $this->ci->parser->parse($this->folder . '/header_common', $data, TRUE);
            }

            if( ! isset($data['upload_video_scripts']))
                $data['upload_video_scripts'] = '';

            $this->ci->parser->parse($this->folder . '/header', $data);
            $this->ci->parser->parse($this->folder . '/content_top', $data);

            if( ! isset($data['noadv']))
            {
                $this->ci->load->model('admin/banners');
                $banners_data = array('advert_block' => $this->ci->banners->get_banners());
                $banners_data['base_url'] = $data['base_url'];
                $data['advert_block'] = $this->ci->parser->parse('advert_block', $banners_data, TRUE);
            }

            $this->ci->parser->parse($this->view_folder . '/' . $view, $data);
            $this->ci->parser->parse($this->folder . '/content_bottom', $data);
            $this->ci->parser->parse($this->folder . '/footer', $data);
    }
}
/* End of file template.php */
/* Location: ./application/libraries/template.php */