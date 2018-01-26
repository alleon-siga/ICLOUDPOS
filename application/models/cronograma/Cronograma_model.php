<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class cronograma_model extends CI_Model
{

    public $FechaInicio=0;
    public $FechaFin=0;
    public $monto_cuota=0;
    public $id_venta=0;
    public $NroCuota=0;
    public $cuota=0;

    function __construct()
    {
        parent::__construct();


    }


    /**
     * @return int
     */
    public function getFechaInicio()
    {
        return $this->FechaInicio;
    }

    /**
     * @param int $FechaInicio
     */
    public function setFechaInicio($FechaInicio)
    {
        $this->FechaInicio = $FechaInicio;
    }

    /**
     * @return int
     */
    public function getFechaFin()
    {
        return $this->FechaFin;
    }

    /**
     * @param int $FechaFin
     */
    public function setFechaFin($FechaFin)
    {
        $this->FechaFin = $FechaFin;
    }

    /**
     * @return int
     */
    public function getMontoCuota()
    {
        return $this->monto_cuota;
    }

    /**
     * @param int $monto_cuota
     */
    public function setMontoCuota($monto_cuota)
    {
        $this->monto_cuota = $monto_cuota;
    }

    /**
     * @return int
     */
    public function getIdventa()
    {
        return $this->id_venta;
    }

    /**
     * @param int $id_venta
     */
    public function setIdventa($id_venta)
    {
        $this->id_venta = $id_venta;
    }

    /**
     * @return int
     */
    public function getNroCuota()
    {
        return $this->NroCuota;
    }

    /**
     * @param int $nrocuota
     */
    public function setNroCuota($NroCuota)
    {
        $this->NroCuota = $NroCuota;
    }

    /**
     * @return int
     */
    public function getCuota()
    {
        return $this->cuota;
    }

    /**
     * @param int $cuota
     */
    public function setCuota($cuota)
    {
        $this->cuota = $cuota;
    }




}
