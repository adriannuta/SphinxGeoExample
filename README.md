SphinxGeoExample
=========================

These samples illustrate geographic searching using Manticore/Sphinx search.     
Querying is made via SphinxQL using PDO driver.    
Data from geonames.org is used as source.     


Requirements :
-------------------------------------------
LAMP  
Manticore or Sphinx search  **2.1.1-beta** or greater
PHP with PDO mysql  

Installation :
-------------------------------------------
Edit `scripts/sphinx.conf` for setting proper paths.    
Edit common.php for setting Sphinx host and port.   
The code is designed for  US geonames.org data dump.    
Download the dump from http://download.geonames.org/export/dump/. You can use any country dump ( or add more than one), 
but only US has the state code column which is used on geodist() example.    
Unzip the archieve and copy it under Example folder.   
Start searchd first with
 
    $ searchd -c /path/to/sphinx.conf    
   
Edit in filldb.php the name of the dump file.
Generate the RT index
 
    $ php filldb.php
poly_large.php and poly_small.php have the same functionality, except the polygons are different.   

Live demo using Sphinx:
-------------------------------------------
http://demos.sphinxsearch.com/SphinxGeoExample/

License:
-------------------------------------------
Sphinx Samples  is free software, and is released under the terms of the GPL version 2 or (at your option) any later version.
Manticore website : https://manticoresearch.com/
Manticore repository : https://github.com/manticoresoftware/manticore
Sphinx website : http://sphinxsearch.com/  
Sphinx read-only repository : https://github.com/sphinxsearch/sphinx 
