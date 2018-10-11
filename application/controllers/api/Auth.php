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
		$this->load->model('banco/banco_api_model');
		$this->load->model('banco/tarjeta_api_model');
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
				$access = $this->opciones_api_model->get_app_access($auth['nUsuCodigo']);
				if (!empty($access)) {
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
					$locales = $this->local_api_model->get_local_by_user($auth['nUsuCodigo']);

					//Monedas
					$monedas = $this->moneda_api_model->get_all();

					//Bancos
					$bancos = $this->banco_api_model->get_all();

					//Tarjetas
					$tarjetas = $this->tarjeta_api_model->get_all();

					//Grupos cliente
					$grupos_cliente = $this->cliente_api_model->get_grupos_all();

					//App opciones
					$op_ventas = $this->opciones_api_model->get_opcion_ventas($auth['nUsuCodigo']);
					if (!empty($op_ventas)) {
						$opciones['ventas'] = 1;
					} else {
						$opciones['ventas'] = 0;
					}

					$op_registros = $this->opciones_api_model->get_opcion_registros($auth['nUsuCodigo']);
					if (!empty($op_registros)) {
						$opciones['registros'] = 1;
					} else {
						$opciones['registros'] = 0;
					}

					$op_clientes = $this->opciones_api_model->get_opcion_clientes($auth['nUsuCodigo']);
					if (!empty($op_clientes)) {
						$opciones['clientes'] = 1;
					} else {
						$opciones['clientes'] = 0;
					}

					//App version
					$version = $this->login_api_model->verificar_version();

					//Emp logo
					$img_dir = './recursos/img/logo/' . $config['EMPRESA_LOGO'];
					$image = file_get_contents($img_dir);
					$emp_logo = base64_encode($image);

					// Json Array
					$json = array(
						'status'  => 'success',
						'auth'    => $auth,
						'config'  => $config,
						'api_key' => $apiKey,
						'clientes' => $clientes,
						'locales' => $locales,
						'monedas' => $monedas,
						'bancos' => $bancos,
						'tarjetas' => $tarjetas,
						'grupos_cliente' => $grupos_cliente,
						'opciones' => $opciones,
						'version' => $version,
						'logo' => $emp_logo
					);

					echo json_encode($json);

				} else {
					echo json_encode(array('status' => 'no'));
				}

			} else {
				echo json_encode(array('status' => 'ne'));
			}
		} else {
			echo json_encode(array('status' => 'failed'));
		}
    }
}
