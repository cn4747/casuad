<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Main extends CI_Controller {

	public function index($page = '1', $type = 'fresh')
	{
            $this->load->model('videos');
            $this->load->library(array('parser', 'pagination', 'time'));

            $video_list = $this->videos->get_videos($page, 8, $type);
            foreach($video_list as $i => $item)
                foreach($item as $key => $value)
                {
                    if($key == 'time')
                    {
                        $video_list[$i][$key] = $this->time->transform($value);
                    }
                    else if($key == 'link')
                    {
                        if($video_list[$i]['thumb'] == '0')
                        {
                            $video_list[$i]['video_thumb'] = $this->config->item('base_url') .
                                                             'images/video_loading.jpg';
                        }
                        else
                        {
                            $video_list[$i]['video_thumb'] = $this->config->item('base_url') .
                                                             'uploads/video_thumbs/' . $value . '.jpg';
                        }
                    }
                }

            $top_video = $this->videos->get_top_video();

            $data = array(
                'title'             =>  'CASUAD',
                'video_list'        =>  $video_list,
                'top_video_title'   =>  $top_video['title'],
                'top_video_descr'   =>  $top_video['descr'],
                'top_video_link'   =>  $top_video['link']
            );

            if($type == 'fresh')
            {
                $data['tab_class_1'] = 'new-active';
                $data['tab_class_2'] = 'recommend-inactive';
            }
            else
            {
                $data['tab_class_1'] = 'new-inactive';
                $data['tab_class_2'] = 'recommend-active';
            }

            $config['base_url'] = $this->config->item('base_url') . 'main/page/';
            $config['total_rows'] = $this->videos->get_videos_count();
            $config['per_page'] = 8;

            $this->pagination->initialize($config);

            $data['pagination'] = $this->pagination->create_links();

            $this->template->show('index_page', $data);
	}

    function page($page = '1')
    {
        $this->index($page);
    }

    function rating($page = '1')
    {
        $this->index($page, 'rating');
    }
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */