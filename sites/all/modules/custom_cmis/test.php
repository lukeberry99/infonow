<?php

class CMISMagic {
    
    function __construct() {
    }

    function prepareQueryWithTag($tag) {
        $endpoint = "http://alfresco.luke-berry.co.uk:8080/alfresco/api/-default-/public/cmis/versions/1.1/browser\?cmisselector\=query\&q\=";
        $query = "select cmis:name from cmis:document";
        $type = " WHERE ";
        $tagQuery = "contains('tag:\"$tag\"')";
        $fullQuery = $endpoint . $query . $type . $tagQuery;
        return $fullQuery;
    }

    function call($tag) { 
        $url = $this->prepareQueryWithTag($tag);
        echo $url;
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_USERPWD, 'admin:admin');
        return curl_exec($ch);
        curl_close($ch);
    }
}


$test = new CMISMagic();
echo($test->call('\[health-and-safety\]'));

