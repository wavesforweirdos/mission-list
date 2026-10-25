<?php
class Test
{
    public function __construct()
    {
        echo "This is main controller";
        $this->view = new View();
    }
}