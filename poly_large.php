<?php
require_once 'common.php';

$docs = array();
$query =  trim($_GET['query']);
$function = $_GET['function'];
$geodesic = $_GET['geodesic'];
$indexes = 'geodemo';
$geodist = '';
$where = array();

if($function == '') {
    $function = 'geopoly2d';
}
if($geodesic =='') {
    $geodesic = 'true';
} 

$poldeg = array();
$poldeg[] = array(38.59111377614744,-121.453857421875);
$poldeg[] = array(40.763139630362,  -74.427389474832);
$poldeg[] = array(34.10725639663118,  -118.9599609375);
foreach($poldeg as $c) {
    foreach($c as $p) {
        $polrad[] = $p;
    }
}
// we need to add the first point for gmaps to show the complete polygon
foreach($poldeg as $c) {
    $polstr[] .='['.$c[0].','.$c[1].']';
}
$polstr = '['.implode(',',$polstr).',['.$poldeg[0][0].','.$poldeg[0][1].']'.']';
$where[] = "MATCH('$query')";

$geodist = ', CONTAINS('.$function.'('.implode(',',$polrad).'),latitude_deg,longitude_deg) as distance ';
$where[] = ' distance =1';

$sql = "SELECT *".$geodist." FROM ".$indexes." WHERE ".implode(' AND ',$where)."  ".$order." LIMIT 0,100";

$results = $ln_sph->query($sql);
foreach($results as $r){
    $docs[] = $r;
}

//center on US
$latitude = deg2rad(38.61687046392973);
$longitude = deg2rad(-100.63476562);
$maxdistance = 1000000;

$meta = $ln_sph->query("SHOW META LIKE 'total_found'")->fetch();
$total_found = $meta['Value'];
$meta = $ln_sph->query("SHOW META LIKE 'time'")->fetch();
$total_time = $meta['Value'];

?>
<?php
$title = 'GEOPOLY2D/POLY2D on large polygon';
include 'template/header.php';
?>
<div id="map"></div>
<div class="container">

	<div class="row">
		<div class="span2">
			<div class="sitebar-nav offset1"></div>
		</div>
		<div class="">
			<div class="container">
				<ul class="nav nav-pills">
					<li ><a href="index.php">Simple geo search</a></li>
                    <li class="active"><a href="poly_large.php">Search inside large polygon</a></li>
                    <li ><a href="poly_small.php">Search inside small polygon</a></li>

				</ul>
				<header>
					<h1>GEOPOLY2D/POLY2D on large polygon</h1>
				</header>
				<div class="row">
					<div class="span9">
						<div class="well form-search">
							<form method="GET" action="" id="search_form"
								class="form-horizontal">
								<div class="control-group">
									<label class="control-label" for="query">Text Search</label>
									<div class="controls">
										<input type="text" class="input-large" name="query" id="query"
											autocomplete="off"
											value="<?=isset($_GET['query'])?htmlentities($_GET['query']):'stadium'?>">
									</div>
								</div>

								<div class="control-group">
									<label class="control-label" for="function">Poly function</label>
									<div class="controls">
										<select name="function" id="function">
											<?php 
											$options = array('geopoly2d' => 'geopoly2d',
                                                             'poly2d' => 'poly2d'
										                    );
              
										?>
											<?php foreach($options as $value=>$option):?>
											<option value="<?=$value?>"
											<?=($value==$function)?'selected="selected"':'';?>>
												<?=$option?>
											</option>
											<?php endforeach;?>
										</select>

									</div>
								</div>
								<div class="control-group">
									<label class="control-label" for="geodesic">geodesic on gmaps</label>
									<div class="controls">
						            <select name="geodesic" id="geodesic">
											<?php 
											$options = array('true' => 'true',
                                                             'false' => 'false'
										                    );
              
										?>
											<?php foreach($options as $value=>$option):?>
											<option value="<?=$value?>"
											<?=($value==$geodesic)?'selected="selected"':'';?>>
												<?=$option?>
											</option>
											<?php endforeach;?>
										</select>
									
									</div>
								</div>
								<div class="control-group">
									<div class="controls">
										<input type="submit" class="btn btn-primary" id="send"
											name="send" value="Submit">
										<button type="reset" class="btn " value="Reset">Reset</button>
									</div>
								</div>

							</form>
						</div>
					</div>
				</div>

				<div class="row">
					<?php if(isset($sql)):?>
					<div class="alert alert-success">
						<?=$sql?>
                        <hr>
						Query time: <?=$total_time?>; Total found: <?=$total_found?>
					</div>
					<?php endif;?>
					
				</div>

				<?php 
				include 'template/footer_poly.php';
				?>