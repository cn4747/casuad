<?php

class Headers
{
    private $ci;
    
    function __construct()
    {
        $this->ci &= get_instance();
    }
    
    public function nocache()
    {
        header('Cache-Control: no-store, no-cache, must-revalidate');
        header('Cache-Control: post-check=0, pre-check=0', FALSE);
        header('Pragma: no-cache');
    }
}

?>
