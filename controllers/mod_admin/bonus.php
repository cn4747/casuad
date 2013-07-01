<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require(APPPATH . 'core/Admin_Module.php');

class Bonus extends Admin_Module
{
    private $title;
    private $base_url;

    public function  __construct()
    {
        parent::__construct();

        $this->load->model('admin/modules');
        $this->load->model('admin/operate_users');

        $this->base_url = $this->config->item('base_url') .
                    'mod_admin/' .
                    str_replace('.php', '', basename(__FILE__)) . '/';

        $this->title = $this->modules->get_title(basename(__FILE__));
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
	public function index($page = '1')
	{
        $this->load->model('videos');
        $this->load->library(array('input', 'pagination'));

        $data = array(
            'title'         =>  $this->title,
            'base_url'      =>  $this->config->item('base_url')
        );

        if($this->input->post('add'))
        {
            $data['message'] = 'Баллы добавлены';

            $this->videos->add_points($this->input->post('id'), $this->input->post('points'));
        }

        $config['base_url'] = $this->base_url . 'page/';
        $config['total_rows'] = $this->videos->get_videos_count();
        $config['per_page'] = 20;
        $config['prev_link'] = '&lt;';
        $config['next_link'] = '&gt;';
        $config['uri_segment'] = 4;

        $this->pagination->initialize($config);
        $data['pagination'] = $this->pagination->create_links();

        $data['videos'] = $this->videos->get_videos($page, $config['per_page'], 'fresh');

        $this->template_admin->show('bonus_users', $data);
	}

    public function page($page = '1')
    {
        $this->index($page);
    }
}

/* End of file bonus.php */
/* Location: ./application/controllers/mod_admin/bonus.php */