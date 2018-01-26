SELECT
	pp.idplantilla_producto,
	pp.nombre_plantilla,
	m.nombre_marca,
	g.nombre_grupo,
	f.nombre_familia,
	l.nombre_linea,
	pcv.campo_valor AS Color,
	pcv1.campo_valor AS Material,
	pcv2.campo_valor AS Planta,
	pcv3.campo_valor AS Aplicaciones,
	pv.proveedor_nombre
FROM
	plantilla_producto pp
LEFT JOIN marcas m ON pp.marca_id = m.id_marca
LEFT JOIN grupos g ON pp.grupo_id = g.id_grupo
LEFT JOIN familia f ON pp.familia_id = f.id_familia
LEFT JOIN lineas l ON pp.linea_id = l.id_linea
LEFT JOIN plantilla_campo_valor pcv ON pp.color_id = pcv.idplantilla_campo_valor
AND pcv.tipo_plantilla = 1
LEFT JOIN plantilla_campo_valor pcv1 ON pp.material_id = pcv1.idplantilla_campo_valor
AND pcv1.tipo_plantilla = 2
LEFT JOIN plantilla_campo_valor pcv2 ON pp.planta_id = pcv2.idplantilla_campo_valor
AND pcv2.tipo_plantilla = 3
LEFT JOIN plantilla_campo_valor pcv3 ON pp.aplicaciones_id = pcv3.idplantilla_campo_valor
AND pcv3.tipo_plantilla = 4
LEFT JOIN proveedor pv ON pp.linea_id = pv.id_proveedor