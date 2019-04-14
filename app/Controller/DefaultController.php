<?php

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     * @Method(["GET"])
     */
    public function homepageAction()
    {
        $message =  'Hello World!';
        TemplateHelper::render('/index', [
            'message' => $message,
        ]);
    }
    
}