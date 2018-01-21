<?php

  const OURCLASS = '<regex>';
  const BASEURL = 'https://link/to/vplanroot';
  const SUBFOLDERS = array(
    'f1/',
    'f2/',
  );
  const VPLANCURL = array( // cURL options
      CURLOPT_CUSTOMREQUEST => "GET", //set request type post or get
      CURLOPT_POST => false, //set to GET
      CURLOPT_USERAGENT => "vplan-bot", //set user agent
      CURLOPT_RETURNTRANSFER => true, // return web page
      CURLOPT_HEADER => false, // don't return headers
      CURLOPT_FOLLOWLOCATION => true, // follow redirects
      CURLOPT_ENCODING => "", // handle all encodings
      CURLOPT_AUTOREFERER => true, // set referer on redirect
      CURLOPT_CONNECTTIMEOUT => 120, // timeout on connect
      CURLOPT_TIMEOUT => 120, // timeout on response
      CURLOPT_MAXREDIRS => 10 // stop after 10 redirects
  );
