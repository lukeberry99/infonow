<?php
require_once "Alfresco_CMIS_API.php";


    $repoUrl="http://alfresco.luke-berry.co.uk:8080/alfresco/cmisatom";
    $username="admin";
    $password="admin";
    $path="/Sites/burtons-demo/documentLibrary/Burtons/";

    //Load folder
    $folder= new CMISalfObject($repoUrl,$username,$password);
    $folder->goGet();
    //if(!$folder->loaded) die("\nSORRY! cannot open folder!\nThe last HTTP request returned the following status: ".$folder->lastHttpStatus."\n\n");

    //$folder->listContent();

?>
