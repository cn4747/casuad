<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Test extends CI_Controller {

	public function index()
	{
        $this->load->model('videos');

        $this->videos->rate(1, '198acb31847b56b53f5576cae4ff76ec4791bd07', 1);
	}
}

/* End of file test.php */
/* Location: ./application/controllers/test.php */
