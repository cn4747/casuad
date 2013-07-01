<?php

class Pages extends MY_Controller {

    function  __construct() {
        parent::__construct();
    }

	function index()
	{
		//
	}

    function about()
    {
        $data['title'] = 'О проекте';

        $this->template->show('page_about', $data);
    }

    function feedback()
    {
        $data['title'] = 'Обратная связь';

        $this->template->show('page_feedback', $data);
    }

    function rules()
    {
        $data['title'] = 'Правила пользования';

        $this->template->show('page_rules', $data);
    }

    function faq()
    {
        $data['title'] = 'Вопросы и ответы';

        $this->template->show('page_faq', $data);
    }

    function advert()
    {
        $data['title'] = 'Реклама на сайте';

        $this->template->show('page_advert', $data);
    }

}
?>