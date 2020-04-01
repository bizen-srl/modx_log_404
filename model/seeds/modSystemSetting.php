<?php
/*-----------------------------------------------------------------
 * Lexicon keys for System Settings follows this format:
 * Name: setting_ + $key
 * Description: setting_ + $key + _desc
 -----------------------------------------------------------------*/
return array(

    array(
        'key'  		=>     'log404.log_path',
		'value'		=>     '',
		'xtype'		=>     'textfield',
		'namespace' => 'log404',
		'area' 		=> 'log404:default'
	),
	array(
        'key'  		=>     'log404.ignore_ips',
		'value'		=>     '',
		'xtype'		=>     'textfield',
		'namespace' => 'log404',
		'area' 		=> 'log404:default'
	),
	array(
        'key'  		=>     'log404.log_max_lines',
		'value'		=>     '300',
		'xtype'		=>     'textfield',
		'namespace' => 'log404',
		'area' 		=> 'log404:default'
	),
	array(
        'key'  		=>     'log404.header_cols',
		'value'		=>     'Url,Date,IP,User Agent,Hit',
		'xtype'		=>     'textfield',
		'namespace' => 'log404',
		'area' 		=> 'log404:default'
	),
	array(
        'key'  		=>     'log404.useragents',
		'value'		=>     'googlebot,adsbot,apis-google,mediapartners-google,bingbot,adidxbot,bingpreview,msnbot,slurp,duckduckbot,baiduspider,yandexbot,frog,spider,screaming,semrush,ahrefsbot,mj12bot,seznambot,facebot,facebookexternalhit,twitterbot,pinterestbot,yandexbot',
		'xtype'		=>     'textfield',
		'namespace' => 'log404',
		'area' 		=> 'log404:default'
	),
	
);
/*EOF*/