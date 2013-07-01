<?php
require_once 'common.php';

$fp = fopen('US.txt','r');
while(!feof($fp)){
     $line = fgets($fp);
     $line = explode("\t", $line);
     $values = array();
     $values['id'] = $line[0];
     $values['name'] = "'".$line[1]."'";
     $values['latitude'] = deg2rad($line[4]);
     $values['longitude'] = deg2rad($line[5]);
     $values['latitude_deg'] = $line[4];
     $values['longitude_deg'] = $line[5];
     $values['feature_code'] = "'".$line[7]."'";
     $values['country_code'] = "'".$line[8]."'";
     $values['state_code'] = "'".$line[10]."'";
     $ln_sph->exec("INSERT INTO geodemo(id,name,latitude,longitude,latitude_deg,longitude_deg,feature_code,country_code,state_code) VALUES(".implode(',',$values).")");
  
}
fclose($fp);