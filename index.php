<?php
require_once 'common.php';

$docs = array();
if(trim($_GET['query'])!=''){
    $query = '@name '.trim($_GET['query']);
}
$latitude  = deg2rad($_GET['latitude']);
$longitude  = deg2rad($_GET['longitude']);
$maxdistance  = (int)$_GET['maxdistance'];
$state_code = trim($_GET['state_code']);
$indexes = 'geodemo';
$geodist = '';
$where = array();
$order = '';
if(count($_GET)>0) {
    if($state_code!='') {
        $query .= " @state_code ".$state_code;
    }
    if($query!='') {
        $where[] = "MATCH(".$ln_sph->quote($query).")";
    }
    
    if($latitude!=0 && $longitude!=0){
        $geodist = ', GEODIST('.$latitude.', '.$longitude.',latitude,longitude) as distance ';
        $where[] = ' distance < '.$maxdistance;
        $order = 'ORDER BY distance ASC';
    }

    $sql = "SELECT *".$geodist." FROM ".$indexes." WHERE ".implode(' AND ',$where)."  ".$order." LIMIT 0,100";

    $results = $ln_sph->query($sql);
    foreach($results as $r){
        $docs[] = $r;
    }
}else{
    //center on US
    $latitude = deg2rad(37.7750);
    $longitude =deg2rad(-98.4183);
    $maxdistance = 5000000;
}

$meta = $ln_sph->query("SHOW META LIKE 'total_found'")->fetch();
$total_found = $meta['Value'];
$meta = $ln_sph->query("SHOW META LIKE 'time'")->fetch();
$total_time = $meta['Value'];

?>
<?php
$title = 'Demo simple geo search';
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
					<li class="active"><a href="index.php">Simple geo search</a></li>
                    <li ><a href="poly_large.php">Search inside large polygon</a></li>
                    <li ><a href="poly_small.php">Search inside small polygon</a></li>
				</ul>
				<header>
					<h1>Simple geo search</h1>
				</header>
				<div class="row">
					<div class="span9">
						<p>Right click on the map to set your starting pointer</p>
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
									<label class="control-label" for="latitude">latitude</label>
									<div class="controls">
										<input type="text" class="input-large" name="latitude"
											id="latitude" autocomplete="off"
											value="<?=isset($_GET['query'])?htmlentities($_GET['latitude']):'37.7750'?>">
									</div>
								</div>

								<div class="control-group">
									<label class="control-label" for="longitude">longitude</label>
									<div class="controls">
										<input type="text" class="input-large" name="longitude"
											id="longitude" autocomplete="off"
											value="<?=isset($_GET['query'])?htmlentities($_GET['longitude']):'-122.4183'?>">
									</div>
								</div>

								<div class="control-group">
									<label class="control-label" for="maxdistance">Maximum distance</label>
									<div class="controls">
										<select name="maxdistance" id="maxdistance">
											<?php 
											$options = array(500 => '500',
                                                             1000 => '1km',
                                                             10000 => '10km',
                                                             50000 => '50km',
                                                             100000 => '100km',
                                                             250000 => '250km',
                                                             500000 => '500km',
                                                             700000 => '700km',
										                    );
                                                     
										?>
										<?php foreach($options as $value=>$option):?>
										    <option value="<?=$value?>" <?=($value==$maxdistance)?'selected':'';?>><?=$option?></option>
										<?php endforeach;?>
										</select>

									</div>
								</div>
								<div class="control-group">
									<label class="control-label" for="query">State code(optional)</label>
									<div class="controls">
										<input type="text" class="input-large" name="state_code" id="state_code"
											autocomplete="off"
											value="<?=isset($_GET['query'])?htmlentities($_GET['state_code']):''?>">
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
				include 'template/footer.php';
				?>