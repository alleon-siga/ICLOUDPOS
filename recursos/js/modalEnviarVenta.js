$(document).ready(function(){
	$('#btnEnviar').on('click', function(){
        if($('#txtMail').val() == null){
            mensaje('warning',"<p>Debe ingresar correo electr&oacute;nico</p>");
            return;
        }

        var validar1 = validar2 = false;
        if($('#nv').prop('checked') == false){
            validar1 = true;
        }

        if($('#ce').prop('checked') == false){
            validar2 = true;
        }

        if(validar1 == true && validar2 == true){
            mensaje('warning', "Debe seleccionar al menos un archivo adjunto");
            return;
        }

        $("#msjEnviar").html($("#loading").html());
		$.ajax({
			url: ruta + 'venta/enviarVenta',
			type: 'POST',
			data: $('#form1').serialize(),
			success: function(data){
				$("#msjEnviar").html('');
				mensaje('success', '<p>Venta enviada con exito.</p>');
				$('#correoModal').modal('hide');
			},
			error: function () {
				mensaje('danger', '<p>Error inesperado.</p>');
			}
		});
	});
});

var REGEX_EMAIL = '([a-z0-9!#$%&\'*+/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&\'*+/=?^_`{|}~-]+)*@' +
                  '(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?)';

$('#txtMail').selectize({
    persist: false,
    maxItems: null,
    valueField: 'email',
    labelField: 'name',
    searchField: ['name', 'email'],
    options: [],
    render: {
        item: function(item, escape) {
            return '<div>' +
                (item.name ? '<span class="name">' + escape(item.name) + '</span>' : '') +
                (item.email ? '<span class="email">' + escape(item.email) + '</span>' : '') +
            '</div>';
        },
        option: function(item, escape) {
            var label = item.name || item.email;
            var caption = item.name ? item.email : null;
            return '<div>' +
                '<span class="label">' + escape(label) + '</span>' +
                (caption ? '<span class="caption">' + escape(caption) + '</span>' : '') +
            '</div>';
        }
    },
    createFilter: function(input) {
        var match, regex;

        // email@address.com
        regex = new RegExp('^' + REGEX_EMAIL + '$', 'i');
        match = input.match(regex);
        if (match) return !this.options.hasOwnProperty(match[0]);

        // name <email@address.com>
        regex = new RegExp('^([^<]*)\<' + REGEX_EMAIL + '\>$', 'i');
        match = input.match(regex);
        if (match) return !this.options.hasOwnProperty(match[2]);

        return false;
    },
    create: function(input) {
        if ((new RegExp('^' + REGEX_EMAIL + '$', 'i')).test(input)) {
            return {email: input};
        }
        var match = input.match(new RegExp('^([^<]*)\<' + REGEX_EMAIL + '\>$', 'i'));
        if (match) {
            return {
                email : match[2],
                name  : $.trim(match[1])
            };
        }
        mensaje('warning', '<p>Direcci&oacute;n de correo electr&oacute;nico inv&aacute;lido.</p>');
        return false;
    }
});