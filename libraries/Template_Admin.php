<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Template
 *
 * Template loader for automated loading of templates
 *
 * @author		Anton Martynenko (http://niggaslife.ru)
 * @version		0.1
 * @license		WTFPL
 */
class Template_Admin {
    private $folder;

	function __construct()
	{
		$this->ci =& get_instance();
        $this->ci->load->model('admin/modules');
        $this->folder = 'admin';
	}

    /**
     * Set folder name to load view from it
     *
     * @param string $folder subfolder name in views directory
     */
    public function set_view_folder($folder)
    {
        $this->folder = $folder;
    }

    /**
     * Shows a view with custom blocks layout
     *
     * @param string $view - view name to be loaded
     * @param array  $data - array with data
     */
    public function show($view, &$data = NULL) {
            if( ! isset($data['title']))
            {
                $this->ci->load->model('modules');
                $data['title'] = $this->ci->modules->get_title($this->ci->uri->segment(2) . '.php');
            }

            if( ! isset($data['base_url']))
            {
                $data['base_url'] = $this->ci->config->item('base_url') . 'mod_admin/';
            }

            $this->ci->load->view('admin/header', $data);
            $data['modules_enabled'] = $this->ci->modules->get_enabled_modules(TRUE);
            $this->ci->load->view('admin/sidebar', $data);
            $this->ci->load->view('admin/content_top', $data);
            $this->ci->load->view($this->folder . '/' . $view, $data);
            $this->ci->load->view('admin/content_bottom', $data);
            $this->ci->load->view('admin/footer', $data);
    }
}
/* End of file template_admin.php */
/* Location: ./application/libraries/template_admin.php */