<?php

class Bonus extends MY_Controller {

    function  __construct() {
        parent::__construct(1);
        $this->lang->load('main', $this->config->item('language'));
    }

	function index($page = '1')
	{
        $this->load->model('videos');
        $this->load->library(array('time', 'parser', 'pagination'));

        $data['usercp'] = TRUE;

        $config['base_url'] = $this->config->item('base_url') . 'mod/bonus/page/';
        $config['total_rows'] = $this->videos->get_videos_with_points_count($this->session->userdata('user_id'));
        $config['per_page'] = 10;
        $config['uri_segment'] = 4;

        $this->pagination->initialize($config);

        $video_list = $this->videos->get_videos_with_points($this->session->userdata('user_id'), $page, $config['per_page']);
		if($video_list === NULL)
        {
			show_404();
        }
        else
        {
            foreach($video_list as $i => $item)
            {
                foreach ($item as $key => $value) {
					if($key == 'thumb')
                    {
                        if($value == '0')
                            $video_list[$i][$key] = 'images/video_loading.jpg';
                        else
                            $video_list[$i][$key] = 'uploads/video_thumbs/' . $video_list[$i]['link'] . '.jpg';
                    }
                }
            }

            $data['the_content'] = $this->parser->parse(
					'mod/bonus_videos',
					array(
						'video_list'	=>	$video_list,
						'pagination'	=>	$this->pagination->create_links()
					),
					TRUE
			);
        }

        $this->template->show('bonus_index', $data);
	}

	public function page($page = '1')
	{
		$this->index($page);
	}
}
?>