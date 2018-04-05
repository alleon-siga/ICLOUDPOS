<div class="alertD" style="display: none;">
	<div class="col-xs-12 header">
		<a href="#" class="close">
			<svg version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 159.4 159.3" style="enable-background:new 0 0 159.4 159.3;" xml:space="preserve">
				<g>
					<path class="st0" d="M0,141.7l62-62l-62-62L17.7,0l62,62l62-62l17.7,17.7l-62,62l62,62l-17.7,17.7l-62-62l-62,62L0,141.7z"/>
				</g>
			</svg>
		</a>
		<h5 class="col-xs-12">Tiene cargos pendiente de pago:</h5>
	</div>
	<div class="col-xs-12 content">
		<ul>
			<li>
				<span>Enero</span>
				<span>160.00</span>
			</li>
			<li>
				<span>Febrero</span>
				<span>160.00</span>
			</li>
			<li>
				<span>Marzo</span>
				<span>160.00</span>
			</li>
		</ul>
	</div>
	<div class="col-xs-12 header">
		<h5 class="col-xs-12">Fecha de corte 31/03/2018</h5>
	</div>
</div>
<a href="#" class="notification">
	<svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 146.6 177.9" style="enable-background:new 0 0 146.6 177.9;" xml:space="preserve">
		<g>
			<path d="M73.3,177.9c14.2,0,26.1-10.2,28.6-23.7H44.7C47.2,167.7,59,177.9,73.3,177.9z"/>
			<path d="M143.4,113.4c-7.3,0-13.3-6-13.3-13.3V79.5c0-2.3-0.1-4.6-0.4-6.8c-3,1.1-6.3,1.8-9.8,1.8c-15.3,0-27.7-12.4-27.7-27.7c0-7,2.6-13.5,7-18.4c-4-2.1-8.2-3.7-12.6-4.8c1.4-2.4,2.2-5.1,2.2-8.1C88.9,7,81.9,0,73.3,0S57.7,7,57.7,15.6c0,2.9,0.8,5.7,2.2,8c-25,6-43.5,28.5-43.5,55.3v21.2c0,7.3-6,13.3-13.3,13.3H0v32.8h146.6v-32.8H143.4z"/>
			<circle cx="120" cy="46.8" r="20.5"/>
		</g>
	</svg>
</a>
<script>
$('div.alertD>div.header>a').click(function(e){
	e.preventDefault();
	$('div.alertD').slideUp(150);
	$('a.notification').slideDown(150);
});
$('a.notification').click(function(e){
	e.preventDefault();
	$('a.notification').slideUp(150);
	$('div.alertD').slideDown(150);
});


</script>