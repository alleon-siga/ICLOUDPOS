<?php

// Api Rest
require(APPPATH . '/libraries/REST_Controller.php');

class Monedas extends REST_Controller
{
    protected $uid = null;

    function __construct()
    {
        parent::__construct();

        $this->load->model('monedas/moneda_api_model');
        $this->load->model('api/api_model', 'api');

        $this->very_auth();
    }

    function very_auth()
    {
        // Request Header
        $reqHeader = $this->input->request_headers();

        // Key
        $key = null;
        if (isset($reqHeader['x-api-key'])) {
            $key = $reqHeader['x-api-key'];

        } else if ($key_get = $this->get('x-api-key')) {
            $key = $key_get;

        } else if ($key_post = $this->post('x-api-key')) {
            $key = $key_post;

        } else {
            $key = null;
        }

        // Auth ID
        $auth_id = $this->api->getAuth($key);

        // ID ?
        if (!empty($auth_id)) {
            $this->uid = $auth_id;

        } else {
            $this->uid = null;
        }
    }

    // All
    public function index_get()
    {
        $data = array();

        $data['monedas'] = $this->moneda_api_model->get_all();

        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array(), 200);
        }
    }
}