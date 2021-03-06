<?php

namespace App\Modules\Web\Dashboard\Users\Controllers;

use App\Core\WebController;

class DefaultController extends WebController
{
    protected $contentIncludes = [
        'datatable',
        'sweetalert',
        'select2',
        'jquery_validation'
    ];

    public function __construct()
    {
        parent::__construct(dirname(__FILE__));
    }
    
    public function index()
    {
        return $this->renderView('index', [
            'page_title'        => 'Data User',
            'page_header_title' => 'Data User',
            'pages_path'        => [
                'data user' => [
                    'url'       => '',
                    'active'    => true
                ]
            ]
        ]);
    }
}
