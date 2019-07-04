<?php

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     * @Method(["GET"])
     */
    public function homepageAction()
    {
        AuthHelper::init();
        
        TemplateHelper::render('/index', [
            'message' => 'Hello World'
        ], null, null);
    }
    
}
