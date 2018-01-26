<?php

class Auth extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('login/login_api_model');
		$this->load->model('opciones/opciones_api_model');
		$this->load->model('cliente/cliente_api_model');
		$this->load->model('local/local_api_model');
		$this->load->model('usuario/usuario_api_model');
		$this->load->model('monedas/moneda_api_model');
        $this->load->model('api/api_model', 'apiModel');
		$this->load->library('user_agent');
    }

    public function index()
    {
        $username = $this->input->post('username');
        $password = $this->input->post('password');
		
		// Validar
		if (!empty($username) && !empty($password)) 
		{
			$data = array(
				'username' => $username,
				'password' => md5($password)
			);
			
			// Validar Usuario
			$auth = $this->login_api_model->verificar_usuario($data);
			
			if (count($auth) > 0) 
			{
				// Clear Password
				unset($auth['var_usuario_clave']);

				// Config
				$config = array();
				$configuraciones = $this->opciones_api_model->get_opciones();
				if ($configuraciones == TRUE) {
					foreach ($configuraciones as $configuracion) {
						$index          = $configuracion['config_key'];
						$config[$index] = $configuracion['config_value'];
					}
				}

				$config['tipos_documento'] = array('FACTURA', 'BOLETA DE VENTA');

				// Nuevo Api Key
				$apiKey = $this->apiModel->new_api_key($auth['nUsuCodigo'], $level = false, $ignore_limits = false, $is_private_key = false, $ip_addresses = '');

				//Clientes
				$clientes = $this->cliente_api_model->get_all();

				//Locales
				$res = $this->usuario_api_model->get_super_user($auth['nUsuCodigo']);
				if ($res) {
					$id_usuario = null;
				}
				$locales = $this->local_api_model->get_local_by_user($auth['nUsuCodigo']);

				//Monedas
				$monedas = $this->moneda_api_model->get_all();

				// Json Array
				$json = array(
					'status'  => 'success',
					'auth'    => $auth,
					'config'  => $config,
					'api_key' => $apiKey,
					'clientes' => $clientes,
					'locales' => $locales,
					'monedas' => $monedas
				);

				echo json_encode($json);

			} else {
				echo json_encode(array('status' => 'ne'));
			}
		} else {
			echo json_encode(array('status' => 'failed'));
		}
    }
}
