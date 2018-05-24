<?php

/**
 * Created by PhpStorm.
 * User: toni
 * Date: 5/15/2018
 * Time: 6:12 PM
 */
require_once __DIR__ . '/lib/simple_html_dom.php';

class TipoCambioSunat
{
    public function consultarTipoCambio()
    {
        $result = array();
        $html = file_get_html('http://e-consulta.sunat.gob.pe/cl-at-ittipcam/tcS01Alias', false, null, 0);
        $i = -1;
        $n = 0;
        $counter = 0;
        $temp = new stdClass();
        foreach ($html->find('table[class="class="form-table""] > tbody > tr') as $trs) {
            if ($i++ == -1) continue;

            foreach ($trs->find('td') as $td) {
                if ($n == 0) $temp->fecha = trim($td->plaintext).date('/m/Y');
                if ($n == 1) $temp->compra = trim($td->plaintext);
                if ($n == 2) $temp->venta = trim($td->plaintext);

                if ($n++ == 2) {
                    $result[$counter] = $temp;
                    $temp = new stdClass();
                    $n = 0;
                    $counter++;
                }
            }
        }

        return array_reverse($result);
    }

}