<?php

class Upload_Video extends MY_Controller {

    function  __construct() {
        parent::__construct();
    }

	function index()
	{
		$this->load->library(array('parser', 'input'));

        if($this->input->post('save'))
        {
            $this->load->model('videos');
	    if($this->input->post('stream')) {
		rename('./streams/' . $this->input->post('filename'),'./uploads/videos/' . $this->input->post('filename'));
	    }
            $add = array(
                'title'     =>  $this->input->post('title'),
                'descr'     =>  $this->input->post('descr'),
                'id_user'   =>  $this->session->userdata('user_id'),
                'file'      =>  $this->input->post('filename'),
                'keywords'  =>  $this->input->post('keywords')
            );

            $this->videos->add($add);

            $data['the_content'] = $this->parser->parse('mod/upload_video_success', array(), TRUE);
            $this->template->show('upload_video', $data);
        }
        else
        {
            $data['the_content'] = $this->parser->parse('mod/upload_video_index', array(), TRUE);
            $data['upload_video_scripts'] = $this->parser->parse(
                    'mod/upload_video_scripts',
                    array('base_url' => $this->config->item('base_url')),
                    TRUE
            );

            $this->template->show('upload_video', $data);
        }
	}

}
?>