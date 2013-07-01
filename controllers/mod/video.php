<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Video extends MY_Controller {

	public function index($id = '', $page = '1')
	{
        $this->load->model('videos');
        $this->load->library(array('parser', 'pagination', 'time'));

        $video_item = $this->videos->get_video($id);
        if($video_item === NULL)
        {
            $data['video_item'] = $this->parser->parse('mod/video_page_notfound', array(), TRUE);
        }
        else
        {
            $data['title'] = $video_item['video_title'];
            $this->videos->update_views($id);
            $video_item['user_hash'] = sha1($this->session->userdata('user_id') . $this->session->userdata('email')); //user hash to send ajax queries
            $video_item['video_id'] = $id;

            if($video_item['link'] == '')
            {
                $video_item['video_frame'] = $this->parser->parse('mod/video_page_loading', array(), TRUE);
            }
            else
            {
                $video_item['video_frame'] = $this->parser->parse(
                        'mod/video_page_frame',
                        array('link' => $video_item['link']),
                        TRUE);
            }

            $data['video_item'] = $this->parser->parse('mod/video_page_item', $video_item, TRUE);
        }

        $video_list = $this->videos->get_videos($page, 8, 'fresh');
        foreach ($video_list as $i => $item)
            foreach ($item as $key => $value) {
                if ($key == 'time')
                {
                    $video_list[$i][$key] = $this->time->transform($value);
                }
                else if ($key == 'link')
                {
                    if ($video_list[$i]['thumb'] == '0')
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

        $data['video_list'] = $video_list;

        $config['base_url'] = $this->config->item('base_url') . 'mod/video/page/'.$id;
        $config['total_rows'] = $this->videos->get_videos_count();
        $config['per_page'] = 8;
        $config['uri_segment'] = 4;

        $this->pagination->initialize($config);

        $data['pagination'] = $this->pagination->create_links();

        $this->template->show('video_page', $data);
	}

    public function page($id = NULL, $page = '1')
    {
        $this->index($id, $page);
    }

}

/* End of file video.php */
/* Location: ./application/controllers/video.php */