<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Home extends AppController
{
    function index()
    {
        $d['page'] = voidClass(['title'=>config('name').' - '.config('description')]);

        $this->render('index', $d);
    }

    function demo()
    {
        $this->setTemplate('default');

        $d['page'] = voidClass(['title'=>'Demo | '.config('name')]);

        $this->render('demo', $d);
    }

    function ajax()
    {
        $json = $this->getJsonResponse();

        $this->jsonRender($json);
    }

    function gamer()
    {
        $this->router->setAccessMethod('get');

        $d['page'] = voidClass(['title'=>'Gamer | '.config('name')]);

        $this->render('demo', $d);
    }
}