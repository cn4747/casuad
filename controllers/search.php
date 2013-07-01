<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Search extends CI_Controller {

	public function index($page = '1')
	{
        $this->load->helper('text');
        $this->load->model('videos');
        $this->load->library(array('input', 'pagination', 'parser'));

        $search_key = $this->input->post('text');
        $data['title'] = 'Поиск';

        $config['base_url'] = $this->config->item('base_url') . 'search/page/';
        $config['per_page'] = 10;

        $search_results = $this->videos->search($search_key, $page, $config['per_page']);

        $config['total_rows'] = $this->videos->get_search_results($search_key);
        $this->pagination->initialize($config);
        $data['pagination'] = $this->pagination->create_links();

        if(is_array($search_results)) foreach($search_results as $i => $item)
            foreach($item as $key => $value)
            {
                if($key == 'thumb')
                {
                    if($value == '1')
                    {
                        $search_results[$i]['picture'] = $this->config->item('base_url') .
                                                         'uploads/video_thumbs/' .
                                                         $search_results[$i]['link'] . '.jpg';
                    }
                    else
                    {
                        $search_results[$i]['picture'] = $this->config->item('base_url') .
                                                         'images/video_loading.jpg';
                    }
                }
                else if($key == 'descr')
                {
                    $search_results[$i][$key] = highlight_phrase($search_results[$i][$key], $search_key, '<b>', '</b>');
                }
            }

        if($search_results === NULL)
        {
            $data['result_box'] = $this->parser->parse('frontend/not_found', array('query' => $key), TRUE);
        }
        else
        {
            $data['result_box'] = $this->parser->parse('frontend/search_results', array('search_results' => $search_results), TRUE);
        }

        $this->template->show('search', $data);
	}

    public function page($page = '1')
    {
        $this->index($page);
    }
}

/* End of file search.php */
/* Location: ./application/controllers/search.php */
