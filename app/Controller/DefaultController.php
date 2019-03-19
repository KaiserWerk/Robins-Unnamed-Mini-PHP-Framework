<?php

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     * @Method(["GET"])
     */
    public function homepageAction()
    {
        echo 'Hello World!';
    }
    
}