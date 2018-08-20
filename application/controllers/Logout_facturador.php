<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class logout_facturador extends  MY_Controller{
	
	function __construct() {
		parent::__construct();
	}
	
	function index(){
		$this->session->unset_userdata('id');
		redirect('facturador', 'refresh');
	}
}