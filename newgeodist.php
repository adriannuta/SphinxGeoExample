<?php
require_once 'common.php';

$docs = array();
if(trim($_GET['query'])!=''){
    $query = '@name '.trim($_GET['query']);
}
$latitude  = $_GET['latitude'];
$longitude  = $_GET['longitude'];
$maxdistance  = (int)$_GET['maxdistance'];
$state_code = trim($_GET['state_code']);
$input = $_GET['input'];
$output = $_GET['output'];
$method = $_GET['method'];
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
        if($input!='deg' && $input!='degrees'){
            $geodist = ', GEODIST('.$latitude.', '.$longitude.',latitude,longitude,{in='.$input.',out='.$output.',method='.$method.'}) as distance ';
        }else{
            $geodist = ', GEODIST('.$latitude.', '.$longitude.',latitude_deg,longitude_deg,{in='.$input.',out='.$output.',method='.$method.'}) as distance ';
        }
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
$title = 'New GEODIST() options (2.2+)';
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
					<li ><a href="index.php">Default Geo distance using havesine</a></li>
                    <li><a href="poly_large.php">Search inside large polygon</a></li>
                    <li><a href="poly_small.php">Search inside small polygon</a></li>
                    <li><a href="polar.php">Geo distance with Polar flat-Earth</a></li>
                    <li class="active"><a href="newgeodist.php">New GEODIST() options (2.2+)</a></li>
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
											$options = array(10 => 10,
                                                             50 => 50,
                                                             100 => 100,
                                                             500 => 500,
                                                             1000 => 1000,
                                                             5000 => 5000,
                                                             10000 => 10000,
                                                             25000 => 25000,
                                                             50000 => 50000,
										                    );
                                                     
										?>
										<?php foreach($options as $value=>$option):?>
										    <option value="<?=$value?>" <?=($value==$maxdistance)?'selected':'';?>><?=$option?></option>
										<?php endforeach;?>
										</select>

									</div>
								</div>
								
								<div class="control-group">
									<label class="control-label" for="input">Input type</label>
									<div class="controls">
										<select name="input" id="input">
											<?php 
											$options = array('deg' => 'deg',
                                                             'degrees' => 'degrees',
                                                             'rad' => 'rad',
                                                             'radians' => 'radians',
               										         );
										?>
										<?php foreach($options as $value=>$option):?>
										    <option value="<?=$value?>" <?=($value==$input)?'selected':'';?>><?=$option?></option>
										<?php endforeach;?>
										</select>
									</div>
								</div>
								
								<div class="control-group">
									<label class="control-label" for="output">Output type</label>
									<div class="controls">
										<select name="output" id="output">
											<?php 
											$options = array('m' => 'm',
                                                             'meters' => 'meters',
                                                             'km' => 'km',
                                                             'kilometers' => 'kilometers',
                                                             'ft' => 'ft',
                                                             'feet' => 'feet',
                                                             'mi' => 'mi',
                                                             'miles' => 'miles'
               										           );
										?>
										<?php foreach($options as $value=>$option):?>
										    <option value="<?=$value?>" <?=($value==$output)?'selected':'';?>><?=$option?></option>
										<?php endforeach;?>
										</select>
									</div>
								</div>										
								
								<div class="control-group">
									<label class="control-label" for="method">Method</label>
									<div class="controls">
										<select name="method" id="method">
											<?php 
											$options = array(
                                                             'adaptive' => 'adaptive(faster,default in 2.2+)',
                                                             'haversine' => 'haversine(slower,default in older versions)',
               										           );
										?>
										<?php foreach($options as $value=>$option):?>
										    <option value="<?=$value?>" <?=($value==$method)?'selected':'';?>><?=$option?></option>
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
