
</div>
<hr>
<footer>

	<p>Copyright &copy; 2001-2013, Sphinx Technologies Inc.</p>
</footer>
</div>
<script
	src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script
	type="text/javascript"
	src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.18/jquery-ui.min.js"></script>
<script
	type="text/javascript" src="js/gmaps.js"></script>
<script type="text/javascript">
$(document).ready(function() {
	latLng = new google.maps.LatLng(<?=rad2deg($latitude)?>, <?=rad2deg($longitude)?>);
    bounds = new google.maps.Circle({center: latLng, radius: <?=$maxdistance/4?>}).getBounds();
	 map = new GMaps({
	        div: '#map',
	        lat: <?=rad2deg($latitude)?>,
	        lng: <?=rad2deg($longitude)?>
	      });
     map.fitBounds(bounds);
     <?php if(isset($_GET['latitude']) && isset($_GET['longitude'])):?>
     map.addMarker({
	        lat: <?=rad2deg($latitude)?>,
	    	lng: <?=rad2deg($longitude)?>,
	    	title: 'Starting point',
	    	animation: google.maps.Animation.BOUNCE,
	    	infoWindow: {content:'<p>Starting point</p>'} 
         });
    <?php endif?>
     map.setContextMenu({
    	  control: 'map',
    	  options: [{
    	    title: 'Set starting point here',
    	    name: 'add_marker',
    	    action: function(e) {
    	      $('#latitude').val(e.latLng.lat());
    	      $('#longitude').val(e.latLng.lng());
    	      $('#search_form').submit();
    	    }
    	  }]
    	});
     <?php foreach($docs as $doc):?>
     map.addMarker({
         lat: <?=rad2deg($doc['latitude'])?>,
         lng: <?=rad2deg($doc['longitude'])?>,
         title: '<?=$doc['name']?>',
         infoWindow: { content:'<p><?=$doc['name']?></p><p>Radians:<?=$doc['latitude']?> <?=$doc['longitude']?><br>Degrees:<?=rad2deg($doc['latitude'])?> <?=rad2deg($doc['longitude'])?></p>'} 
         });
     <?php endforeach;?>
});
</script>

</body>
</html>
