<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require(APPPATH . 'core/Admin_Module.php');

class Advert extends Admin_Module
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
	public function index()
	{
        $this->load->model('admin/banners');
        $this->load->library(array('input', 'pagination', 'form_validation'));

        $data = array(
            'title'         =>  $this->title,
            'current_url'   =>  $this->base_url,
            'base_url'      =>  $this->config->item('base_url')
        );

        $data['banners'] = $this->banners->get_banners();

        $this->template_admin->show('advert_index', $data);
	}

    public function upload()
    {
        $this->load->model('admin/banners');

        $data = array(
            'a_text1'   =>  '',
            'a_text2'   =>  '',
            'a_url'     =>  ''
        );
        
        if($this->input->post('add'))
        {
            $this->load->library('form_validation');

            $this->form_validation->set_rules('text1', 'Текст 1', 'trim|required|xss_clean');
            $this->form_validation->set_rules('text2', 'Текст 2', 'trim|xss_clean');
			$this->form_validation->set_rules('url', 'Ссылка', 'trim|required|xss_clean');

            if($this->form_validation->run() == TRUE)
            {
                $data['a_text1'] = $this->input->post('text1');
                $data['a_text2'] = $this->input->post('text2');
                $data['a_url'] = $this->input->post('url');
				$data['a_id'] = '';

                $banner_data = array(
                    'text1'     =>  $this->input->post('text1'),
                    'text2'     =>  $this->input->post('text2'),
                    'url'       =>  $this->input->post('url'),
                    'date'      => time()
                );

                $config['upload_path'] = './uploads/banners/';
                $config['allowed_types'] = '*';

                $this->load->library('upload', $config);

                if ( ! $this->upload->do_upload('image'))
                {
                    $data['message'] = $this->upload->display_errors();
                }
                else
                {
                    $filedata = $this->upload->data();
                    $banner_data['image'] = $filedata['file_name'];

                    $data['message'] = 'Баннер добавлен';

                    $data['a_text1'] = '';
                    $data['a_text2'] = '';
                    $data['a_url'] = '';

                    $this->banners->add($banner_data);
                }
            }
            else
            {
                $data['message'] = validation_errors();
            }
        }

        $this->template_admin->show('advert_form', $data);
    }

    public function delete($id)
    {
        $this->load->model('admin/banners');

        $this->banners->delete($id);

        $this->index();
    }

	public function edit($id = NULL)
	{
		if($id === NULL)
		{
			show_404();
		}
		else
		{
			$this->load->model('admin/banners');
			$this->load->library('form_validation');

            $this->form_validation->set_rules('text1', 'Текст 1', 'trim|required|xss_clean');
            $this->form_validation->set_rules('text2', 'Текст 2', 'trim|xss_clean');
			$this->form_validation->set_rules('url', 'Ссылка', 'trim|required|xss_clean');

			$get_banner_data = $this->banners->get_banner($id);
			$data['a_text1'] = $get_banner_data['text1'];
            $data['a_text2'] = $get_banner_data['text2'];
            $data['a_url'] = $get_banner_data['url'];
			$data['a_id'] = $get_banner_data['id'];

            if($this->form_validation->run() == TRUE)
            {
                $data['a_text1'] = $this->input->post('text1');
                $data['a_text2'] = $this->input->post('text2');
                $data['a_url'] = $this->input->post('url');

                $banner_data = array(
                    'text1'     =>  $this->input->post('text1'),
                    'text2'     =>  $this->input->post('text2'),
                    'url'       =>  $this->input->post('url'),
                    'date'      =>  time()
                );

                $data['message'] = 'Баннер обновлен';

				$this->banners->update($banner_data, $id);
            }
            else
            {
                $data['message'] = validation_errors();
            }

			$this->template_admin->show('advert_form_edit', $data);
        }
        
	}
    

}

/* End of file advert.php */
/* Location: ./application/controllers/mod_admin/advert.php */