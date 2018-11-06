<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class credito_model extends CI_Model
{

    function __construct()
    {
        parent::__construct();
    }

    /*dec_credito_montodebito ES EL TOTAL PAGADO HASTA LOS MOMENTOS*/

    /*EL CAMPO dec_credito_montocuota ES EL TOTAL DE LA VENTA MENOS EL INICIAL.
                ESA SERIA LO QUE LE QUEDA POR PAGAR AL CLIENTE*/

    /*el campo pago_anticipado es para saber si es el pago de todas las cuotas a la vez*/

    /*el campo fecha_cancelado es la fecha en la que se paga toda la deuda*/

    public function update($where, $data)
    {
        $this->db->trans_start();
        $this->db->where($where);
        $this->db->update('credito', $data);

        $this->db->trans_complete();


        if ($this->db->trans_status() === FALSE) {
            return false;
        } else {

            return true;
        }

        $this->db->trans_off();
    }
}