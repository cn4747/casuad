<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Thumbs extends CI_Controller {

	public function index($id = 0, $link = 0, $counter = 0)
	{
        //if there are some technical problems for about 10 minutes
        if($counter > 10) return;

        if($id !== 0 && $link !== 0)
        {
            $this->load->model('videos');
            $this->load->library(array('image_lib'));

            $thumbs_path = 'uploads/video_thumbs/';
            $saved_image = "{$link}.jpg";
            $fp = fopen($thumbs_path . $saved_image, 'w+');
            $image = @file_get_contents("http://img.youtube.com/vi/{$link}/0.jpg");

            if(empty($image))
            {
                sleep(60);
                $this->index($id, $link, $counter + 1);
            }

            if($fp) {
                fwrite($fp, $image);
                fclose($fp);

                $config['image_library'] = 'gd2';
                $config['source_image'] = $thumbs_path . $saved_image;
                $config['maintain_ratio'] = TRUE;
                $config['width'] = $this->config->item('video_thumb_width');
                $config['height'] = $this->config->item('video_thumb_height');

                $this->image_lib->initialize($config);
                $this->image_lib->resize();

                $config['new_image'] = $thumbs_path . 'mini_' . $saved_image;
                $config['width'] = $this->config->item('video_mini_width');
                $config['height'] = $this->config->item('video_mini_height');

                $this->image_lib->initialize($config);
                $this->image_lib->resize();

                $this->videos->update_thumb($id);
            }
        }
	}
}

/* End of file thumbs.php */
/* Location: ./application/controllers/thumbs.php */
