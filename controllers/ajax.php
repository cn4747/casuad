<?php

class Ajax extends MY_Controller {

    function  __construct() {
        parent::__construct();
    }

	function index()
	{
        
	}

    function rate()
    {
        $this->load->model('videos');
        $this->load->library(array('input'));

        if($this->input->post('hash') != sha1(''))
        {
            if($this->input->post('rating') == '1')
            {
                $this->videos->rate($this->input->post('id'), $this->input->post('hash'), 1);
            }
            else if($this->input->post('rating') == '-1')
            {
                $this->videos->rate($this->input->post('id'), $this->input->post('hash'), -1);
            }

            echo 'true';
        }
    }

	function hints()
	{
		$this->load->model('videos');
		$this->load->library('input');

		$result = $this->videos->search_hints($this->input->post('word'));

		if($result === NULL)
		{
			echo json_encode(array('error' => 'Null result'));
		}
		else
		{
			echo json_encode(array(
				'error'		=>	'',
				'list'		=>	$result
			));
		}
	}

}
?>