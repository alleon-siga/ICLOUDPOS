<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class proveedor_model extends CI_Model
{

    function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    function get_cuentas_pagar($data = array())
    {

        $consulta = "
            SELECT 
                ingreso.id_ingreso as ingreso_id,
                ingreso.tipo_documento as documento_nombre,
                ingreso.documento_serie as documento_serie,
                ingreso.documento_numero as documento_numero,
                proveedor.proveedor_nombre as proveedor_nombre,
                ingreso.fecha_emision as fecha_emision,
                ingreso.total_ingreso as monto_venta,
                moneda.simbolo as simbolo, 
                ingreso.total_ingreso as total_ingreso,
                ingreso_credito.monto_cuota as monto_cuota,
                ingreso_credito.monto_debito as monto_debito,
                ingreso_credito.inicial as inicial, 
                DATEDIFF(CURDATE(), ingreso.fecha_emision) as dias_transcurridos,
                l.local_nombre as local_nombre,
                ingreso.tipo_ingreso as tipo_ingreso
            FROM
                (ingreso)
                    JOIN
                proveedor ON ingreso.int_Proveedor_id = proveedor.id_proveedor 
                    JOIN  
                moneda ON moneda.id_moneda = ingreso.id_moneda 
                    JOIN
                ingreso_credito ON ingreso_credito.ingreso_id = ingreso.id_ingreso
                    JOIN
                local l ON ingreso.local_id = l.int_local_id
            WHERE
                ingreso_credito.estado = 'PENDIENTE' AND ingreso.ingreso_status = 'COMPLETADO'
        ";

        if (isset($data['proveedor_id']) && $data['proveedor_id'] != "")
            $consulta .= " AND ingreso.int_Proveedor_id =" . $data['proveedor_id'];

        if (isset($data['documento']))
            $consulta .= " AND ingreso.tipo_documento ='" . $data['documento'] . "'";

        if (isset($data['moneda_id']) && $data['moneda_id'] != "")
            $consulta .= " AND ingreso.id_moneda =" . $data['moneda_id'] . "";
 
        if (isset($data['local_id']) && $data['local_id'] != "")
            $consulta .= " AND ingreso.local_id IN(" . $data['local_id'] . ")";

        if (isset($data['tipo']) && $data['tipo'] != "")
            $consulta .= " AND ingreso.tipo_ingreso ='".$data['tipo']."'";

        $consulta .= " GROUP BY ingreso.id_ingreso";

        return $this->db->query($consulta)->result();
    }

    function get_cuentas_pagar_totales($data = array())
    {

        $consulta = "
            SELECT 
                SUM(ingreso.total_ingreso) as total_monto_venta,
                SUM(ingreso_credito.monto_cuota) AS total_monto_cuota,
                SUM(ingreso_credito.monto_debito) AS total_monto_debito
            FROM
                (ingreso)
            JOIN
              ingreso_credito ON ingreso_credito.ingreso_id = ingreso.id_ingreso
            WHERE
                ingreso_credito.estado = 'PENDIENTE' 
        ";

        if (isset($data['proveedor_id']) && $data['proveedor_id'] != "")
            $consulta .= " AND ingreso.int_Proveedor_id =" . $data['proveedor_id'];

        if (isset($data['documento']))
            $consulta .= " AND ingreso.tipo_documento ='" . $data['documento'] . "'";

        if (isset($data['moneda_id']) && $data['moneda_id'] != "")
            $consulta .= " AND ingreso.id_moneda =" . $data['moneda_id'] . "";

        if (isset($data['local_id']) && $data['local_id'] != "")
            $consulta .= " AND ingreso.local_id IN(" . $data['local_id'] . ")";

        if (isset($data['tipo']) && $data['tipo'] != "")
            $consulta .= " AND ingreso.tipo_ingreso ='".$data['tipo']."'";
        return $this->db->query($consulta)->row();
    }

    function get_all()
    {
        $query = $this->db->where('proveedor_status', '1');
        $this->db->order_by('proveedor_nombre', 'asc');
        $query = $this->db->get('proveedor');
        return $query->result_array();
    }

    function get_by($campo, $valor)
    {
        $this->db->where($campo, $valor);
        $query = $this->db->get('proveedor');
        return $query->row_array();
    }

    function insertar($proveedor)
    {

        $nombre = $this->input->post('proveedor_nombre');
        $validar_nombre = sizeof($this->get_by('proveedor_nombre', $nombre));

        if ($validar_nombre < 1) {
            $this->db->trans_start();
            $this->db->insert('proveedor', $proveedor);
            $id = $this->db->insert_id();
            $this->db->trans_complete();

            if ($this->db->trans_status() === FALSE)
                return FALSE;
            else
                return $id;
        } else {
            return NOMBRE_EXISTE;
        }
    }

    function update($proveedor)
    {
        $produc_exite = $this->get_by('proveedor_nombre', $proveedor['proveedor_nombre']);
        $validar_nombre = sizeof($produc_exite);
        if ($validar_nombre < 1 or ($validar_nombre > 0 and ($produc_exite ['id_proveedor'] == $proveedor ['id_proveedor']))) {
            $this->db->trans_start();
            $this->db->where('id_proveedor', $proveedor['id_proveedor']);
            $this->db->update('proveedor', $proveedor);

            $this->db->trans_complete();

            ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            if ($this->db->trans_status() === FALSE)
                return FALSE;
            else
                return TRUE;
        } else {
            return NOMBRE_EXISTE;
        }
    }

    function select_all_proveedor()
    {
        $this->db->where('proveedor_status !=', '0');
        $query = $this->db->get('proveedor');
        return $query->result();
    }

    function verifProdIngr($proveedor)
    {

        $this->db->where('int_Proveedor_id', $proveedor['id_proveedor']);
        $sql = $this->db->get('ingreso');
        $data = $sql->result();

        if (count($data) > 0) {
            return 'ingreso';
        } else {

            $this->db->where('producto_proveedor', $proveedor['id_proveedor']);
            $sql1 = $this->db->get('producto');
            $data1 = $sql1->result();
            if (count($data1) > 0) {
                return 'producto';
            } else {

                return false;
            }

        }
    }
}
