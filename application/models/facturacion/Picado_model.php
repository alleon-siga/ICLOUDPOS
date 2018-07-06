<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class picado_model extends CI_Model
{

    protected $max_importe = 700;

    function __construct()
    {
        parent::__construct();
    }

    function split($productos)
    {
        $validar = $this->validate($productos);
        if ($validar === FALSE) {

            $boletas = array();
            $index = 0;

            while ($this->get_cantidades($productos) > 0) {

                foreach ($productos as $producto) {


                    if (!isset($boletas[$index])) {
                        $boletas[$index] = array();
                    }

                    $boleta_max_importe = 0;
                    foreach ($boletas[$index] as $key => $val) {
                        $boleta_max_importe += ($val['cantidad'] * $val['precio']);
                    }


                    if (($boleta_max_importe + $producto->precio) < $this->max_importe) {

                        if (!isset($boletas[$index][$producto->id . '_' . $producto->um_id])) {
                            $boletas[$index][$producto->id . '_' . $producto->um_id] = array(
                                'id' => $producto->id,
                                'um_id' => $producto->um_id,
                                'precio' => $producto->precio,
                                'cantidad' => 0
                            );
                        }

                        if ($producto->cantidad > 0) {
                            $boletas[$index][$producto->id . '_' . $producto->um_id]['cantidad']++;
                            $producto->cantidad--;
                        } else {
                            continue;
                        }

                    } else {
                        $index++;
                    }

                }
            }

            return array(
                'CODIGO' => '0',
                'MENSAJE' => 'El picado de boletas se ha ejecutado correctamente',
                'BOLETAS' => $boletas
            );
        }
        return $validar;
    }

    protected
    function get_cantidades($productos)
    {
        $cantidades = 0;
        foreach ($productos as $producto)
            $cantidades += $producto->cantidad;

        return $cantidades;
    }

    protected
    function validate($productos)
    {
        foreach ($productos as $producto) {
            if ($producto->precio > $this->max_importe) {
                return array(
                    'CODIGO' => '-2',
                    'MENSAJE' => 'El precio del producto no puede ser mayor al maximo importe permitido',
                    'BOLETAS' => array()
                );
            }
        }

        return FALSE;
    }

}
