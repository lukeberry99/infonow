<?php
class CMISalfRepo
{
    var $username;
    var $url;
    var $password;
    public $repoId;
    public $rootFolderId;
    public $connected=FALSE;
    public $lastHttp;
    public $lastHttpStatus;
    public $uritemplates;
    function __construct($url, $username = null, $password = null){
        $this->url=$url;
        $this->connect($url, $username, $password);
    }
    function connect($url, $username, $password){
        $this->url=$url;
        $ch=curl_init();
        $reply=$this->getHttp($url,$username,$password);
        $this->lastHttp=$reply;
        if($reply==FALSE)return FALSE;
        $repodata=simplexml_load_string($reply);
        $this->namespaces=$repodata->getNameSpaces(true);
        if(isset($this->namespaces['app']))
            $app=$repodata->children($this->namespaces['app']);
        else 
            $app=$repodata->children();
        $cmisra=$app->children($this->namespaces['cmisra']);	
        $uritemplates=$cmisra->children($this->namespaces['cmis']);
        $cmis=$cmisra->children($this->namespaces['cmis']);
        $this->rootFolderId=$cmis->rootFolderId;
        $this->repoId=$cmis->repositoryId;
        $this->cmisobject=$cmis;
        foreach($cmisra->uritemplate as $template){
            $tempuri=$template->template;
            $type=$template->type;
            $this->uritemplates["$type"]=$tempuri;	
        }
        $this->connected=TRUE;
        return$this->repoId;
    }
    function checkHttpCode($code){
        if($code>=200 && $code < 300) return TRUE;
        else return FALSE;
    }
    function getHttp($url, $username, $password){
        $ch=curl_init();
        curl_setopt($ch, CURLOPT_URL, $url );
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE ); 
        curl_setopt($ch, CURLOPT_USERPWD,"$username:$password");
        $reply=curl_exec($ch);
        $this->lastHttp=$reply;
        $status=curl_getinfo($ch);
        $this->lastHttpStatus=$status['http_code'];
        if($this->checkHttpCode($status['http_code'])) return $reply;
        return FALSE;
    }
    function postHttp($url, $username, $password,$postfields){
        $ch=curl_init($url);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE ); 
        curl_setopt($ch, CURLOPT_POST, TRUE ); 
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/atom+xml;type=entry"));
        curl_setopt($ch, CURLOPT_USERPWD,"$username:$password");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields); 
        $reply=curl_exec($ch);
        $this->lastHttp=$reply;
        $status=curl_getinfo($ch);
        $this->lastHttpStatus=$status['http_code'];
        if($this->checkHttpCode($status['http_code'])) return $reply;
        else return FALSE;
    }
    function putHttp($url, $username, $password,$postfields){
        $fp=fopen("put.xml","wb+");
        if(!$fp){
            echo "CANNOT open file! Please check for folder write permission\n\n";
            return FALSE;
        }
        fwrite($fp,$postfields);
        fclose($fp);
        $fp=fopen("put.xml","rb+");
        $ch=curl_init($url);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_INFILE, $fp);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE ); 
        curl_setopt($ch, CURLOPT_PUT, TRUE ); 
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Length: ' . strlen($postfields),"X-HTTP-Method-Override: PUT","Content-Type: application/atom+xml;type=entry"));
        curl_setopt($ch, CURLOPT_USERPWD,"$username:$password");
        $reply=curl_exec($ch);
        $this->lastHttp=$reply;
        fclose($fp);
        unlink("put.xml");
        $status=curl_getinfo($ch);
        $this->lastHttpStatus=$status['http_code'];
        if($this->checkHttpCode($status['http_code'])) return $reply;
        else return FALSE;
    }
    function deleteHttp($url, $username, $password){
        $ch=curl_init($url);
        curl_setopt($ch, CURLOPT_URL, $url );
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE ); 
        curl_setopt($ch, CURLOPT_USERPWD,"$username:$password");
        $reply=curl_exec($ch);
        $this->lastHttp=$reply;
        $status=curl_getinfo($ch);
        $this->lastHttpStatus=$status['http_code'];
        if($this->checkHttpCode($status['http_code'])) return TRUE;
        else return FALSE;
    }
}
class CMISalfObject extends CMISalfRepo
{
    public $properties=array();
    public $aspects=array();
    public $loaded=FALSE;
    public $objId;
    public $contentUrl;
    public $selfUrl;
    public $childrenUrl;
    public $parentUrl;
    public $editUrl;
    public $containedObjects=array();
    function __construct($url, $username = null, $password = null,$objId = null,$objUrl=null,$objPath=null){
        $this->url=$url;
        $this->connect($url, $username, $password);
        $this->username=$username;
        $this->password=$password;
        if($objUrl){
            $this->loadCMISObject(null,$objUrl);
        }
        else if($objId){
            $this->loadCMISObject($objId);
        }
        else if($objPath){
            $this->loadCMISObject(null,null,$objPath);
        }
        else return FALSE;
    }
    function loadCMISObject($objId=null,$objUrl=null,$objPath=null){
        $this->objId=$objId;
        $this->objUrl=$objUrl;
        if($objUrl){
            $reply=$this->getHttp($objUrl,$this->username,$this->password);
        }
        else if($objPath){
            $urltemplate=$this->uritemplates['objectbypath'];
            $url=str_replace("{path}",urlencode($objPath),$urltemplate);
            $url=str_replace("{","<",$url);
            $url=str_replace("}",">",$url);
            $url=strip_tags($url);
            $reply=$this->getHttp($url,$this->username,$this->password);
        }
        else {
            $urltemplate=$this->uritemplates['objectbyid'];
            $url=str_replace("{id}",urlencode($objId),$urltemplate);
            $url=str_replace("{","<",$url);
            $url=str_replace("}",">",$url);
            $url=strip_tags($url);
            $reply=$this->getHttp($url,$this->username,$this->password);
        }
        if($reply==FALSE){
            $this->loaded=FALSE;
            return FALSE;
        }
        $objdata=simplexml_load_string($reply);
        $atom=$objdata->children($this->namespaces['atom']);
        $this->namespaces=$objdata->getNameSpaces(true);
        $cmisra=$objdata->children($this->namespaces['cmisra']);
        $cmis=$cmisra->children($this->namespaces['cmis']);
        $this->cmisobject=$cmis;
        $this->loaded=TRUE;
        if($atom->content)$this->contentUrl=(string)$atom->content->attributes()->src;
        if($objdata->link)$links=$objdata->link;
        else if($atom->link)$links=$atom->link;
        foreach($links as $link){
            $rel= $link->attributes()->rel;
            $href= $link->attributes()->href;
            $type= $link->attributes()->type;
/*		
        echo "\n================\n";
        echo "REL $rel\n";
        echo "HREF $href\n";
        echo "TYPE $type\n";
 */	
            if($rel=='up')$this->parentUrl=$href;
            if($rel=='down' && $type=="application/atom+xml;type=feed")
                $this->childrenUrl=$href;
            if($rel=='edit')$this->editUrl=$href;
            if($rel=='self')$this->selfUrl=$href;
        }
        for($x=0;$x<count($cmis->properties->propertyString);$x++){
            $propertyDefinitionId=$cmis->properties->propertyString[$x]->attributes()->propertyDefinitionId;
            $value=(string)$cmis->properties->propertyString[$x]->value;
            $this->properties["$propertyDefinitionId"]=$value;
        }			
        for($x=0;$x<count($cmis->properties->propertyId);$x++){
            $propertyDefinitionId=$cmis->properties->propertyId[$x]->attributes()->propertyDefinitionId;
            $value=(string)$cmis->properties->propertyId[$x]->value;
            $this->properties["$propertyDefinitionId"]=$value;
        }			
        for($x=0;$x<count($cmis->properties->propertyDateTime);$x++){
            $propertyDefinitionId=$cmis->properties->propertyDateTime[$x]->attributes()->propertyDefinitionId;
            $value=(string)$cmis->properties->propertyDateTime[$x]->value;
            $this->properties["$propertyDefinitionId"]=$value;
        }			
        if(isset($this->namespaces['aspects']))$aspectsdata=$cmis->children($this->namespaces['aspects'])->aspects;
        else $aspectsdata=$cmis->children($this->namespaces['alf'])->aspects;
        for($x=0;$x<count($aspectsdata->properties);$x++){
            $cmisprop=$aspectsdata->properties[$x]->children($this->namespaces['cmis']);
            if($n=count($cmisprop)){
                for($k=0;$k<$n;$k++){
                    $propertyDefinitionId=$cmisprop[$k]->attributes()->propertyDefinitionId;
                    $value=(string)$cmisprop[$k]->value;
                    if($value)$this->aspects["$propertyDefinitionId"]=$value;
                }	
            }		
        }
        $this->objId=$this->properties['cmis:objectId'];
        return TRUE;
    }
    public function listContent(){
        if($this->properties['cmis:baseTypeId']<>"cmis:folder"){	
            return FALSE;
        }
        $newurl=$this->childrenUrl;
        $reply=$this->getHttp($newurl,$this->username,$this->password);
        $objdata=simplexml_load_string($reply);
/*	if(!$objdata=simplexml_load_string($reply)){
        echo "INVALID XML OBJECT\n\n$reply\n\n";
        die();
        return FALSE;
}*/
        $this->namespaces=$objdata->getNameSpaces(true);
        if(isset($this->namespaces['atom']))$atom=$objdata->children($this->namespaces['atom']);
        else $atom=$objdata->children();
        $entry=$atom->entry;
        for($x=0;$x<count($entry);$x++){

            $ent=$entry[$x];	
            $link=$ent->link;
            foreach($ent->link as $link){
                $rel= $link->attributes()->rel;
                $href= $link->attributes()->href;
                if($rel=="self")$objUrl=$href;		
            }
            $tempdoc[$x]=new CMISalfObject($this->url,$this->username,$this->password,null,$objUrl);
            $this->containedObjects[$x]->objUrl=$objUrl;
            $this->containedObjects[$x]->author=(string)$ent->author->name;
            $this->containedObjects[$x]->title=(string)$ent->title;
            if($ent->content)$this->containedObjects[$x]->content=(string)$ent->content->attributes()->src;
            $this->containedObjects[$x]->properties=$tempdoc[$x]->properties;
            $this->containedObjects[$x]->aspects=$tempdoc[$x]->aspects;
        }
    }
    public function quickListContent(){
        if($this->properties['cmis:baseTypeId']<>"cmis:folder"){	
            return FALSE;
        }
        $newurl=$this->childrenUrl;
        $reply=$this->getHttp($newurl,$this->username,$this->password);
        $objdata=simplexml_load_string($reply);
/*	if(!$objdata=simplexml_load_string($reply)){
        echo "INVALID XML OBJECT\n\n$reply\n\n";
        die();
        return FALSE;
}*/
        $this->namespaces=$objdata->getNameSpaces(true);
        if(isset($this->namespaces['atom']))$atom=$objdata->children($this->namespaces['atom']);
        else $atom=$objdata->children();
        $entry=$atom->entry;
        for($x=0;$x<count($entry);$x++){
            $ent=$entry[$x];	
            $link=$ent->link;
            foreach($ent->link as $link){
                $rel= $link->attributes()->rel;
                $href= $link->attributes()->href;
                if($rel=="self")$objUrl=$href;		
                if($rel=="describedby"){
                    if(strpos($href,"cmis:folder")) 
                        $objType="cmis:folder";	
                    else 
                        $objType="cmis:document";
                }
            }
            $this->containedObjects[$x]->objUrl=(string)$objUrl;
            $this->containedObjects[$x]->author=(string)$ent->author->name;
            $this->containedObjects[$x]->title=(string)$ent->title;
            $this->containedObjects[$x]->type=$objType;
            if($ent->content)$this->containedObjects[$x]->content=(string)$ent->content->attributes()->src;
        }
    }
    public function getContent(){
        $url=$this->contentUrl;
        return $this->getHttp($url, $this->username, $this->password);
    }
    public function download(){
        $url=$this->contentUrl;
        $content=$this->getHttp($url, $this->username, $this->password);
        if(!$content)return FALSE;
        $name=$this->properties['cmis:name'];
        $fp=fopen($name,"wb");
        if(!$fp)return FALSE;
        fwrite($fp,$content);
        fclose($fp);
        return $name;
    }
    public function createFolder($foldername){
        if($this->properties['cmis:baseTypeId']<>"cmis:folder"){	
            return FALSE;
        }
        $foldername=str_replace("&","&#038;",$foldername);
        $inquiry="<?xml version='1.0' encoding='UTF-8'?>
            <atom:entry 
            xmlns:atom=\"http:
            xmlns:cmis=\"http:
            xmlns:cmisra=\"http:
            xmlns:app=\"http:
            <atom:title>$foldername</atom:title>
            <cmisra:object>
            <cmis:properties>
            <cmis:propertyId queryName=\"cmis:objectTypeId\" displayName=\"Object Type Id\" localName=\"objectTypeId\" propertyDefinitionId=\"cmis:objectTypeId\">
            <cmis:value>cmis:folder</cmis:value>
            </cmis:propertyId>
            </cmis:properties>
            </cmisra:object>
            </atom:entry>
            ";	
        $url=$this->childrenUrl;
        $result=$this->postHttp($url,$this->username,$this->password,$inquiry);
        return $this->getObjectId($result);
    }
    public function upload($filename,$mimetype=null){
        if($this->properties['cmis:baseTypeId']<>"cmis:folder"){	
            return FALSE;
        }
        $handle = fopen($filename, "r");
        if(!$handle)return FALSE;
        $contents = fread($handle, filesize($filename));
        if(!$mimetype)$type=mime_content_type($filename);
        else $type=$mimetype;
        fclose($handle);
        $base64_content=base64_encode($contents);
        $fileescname=str_replace("&","&#038;",$filename);
        $inquiry="<?xml version=\"1.0\" encoding=\"UTF-8\" standalone=\"yes\"?>
            <atom:entry xmlns:cmis=\"http:
            xmlns:cmism=\"http:
            xmlns:atom=\"http:
            xmlns:app=\"http:
            xmlns:cmisra=\"http:
            <atom:title>$fileescname</atom:title>
            <cmisra:content>
            <cmisra:mediatype>$type</cmisra:mediatype>
            <cmisra:base64>$base64_content</cmisra:base64>
            </cmisra:content>
            <cmisra:object>
            <cmis:properties>
            <cmis:propertyId propertyDefinitionId=\"cmis:objectTypeId\">
            <cmis:value>cmis:document</cmis:value>
            </cmis:propertyId>
            </cmis:properties>
            </cmisra:object>
            </atom:entry>
            ";
        $url=$this->childrenUrl;
        $result=$this->postHttp($url,$this->username,$this->password,$inquiry);
        if($result)return $this->getObjectId($result);
        else return FALSE;
    }
    public function goGet(){
        $value=str_replace("&","&#038;",$value);
        $inquiry="<?xml version='1.0' encoding='utf-8'?>
 <cmis:query xmlns='http://www.w3.org/2005/Atom' xmlns:cmis='http://docs.oasis-open.org/ns/cmis/core/200908/'>
 <cmis:statement>SELECT * FROM cmis:document</cmis:statement>
 <cmis:maxItems>10</cmis:maxItems>
 <cmis:skipCount>0</cmis:skipCount>
 <cmis:searchAllVersions>true</cmis:searchAllVersions>
 <cmis:includeAllowableActions>true</cmis:includeAllowableActions>
 </cmis:query>";
        $url=$this->selfUrl;
        $result=$this->putHttp($url,$this->username,$this->password,$inquiry);
        print_r($result);
        die();
        //$this->loadCMISObject($this->properties['alfcmis:nodeRef']);
    }


    //public function query($query){

    //}
    public function setAspect($aspect,$value){
        $value=str_replace("&","&#038;",$value);
        $inquiry="<?xml version=\"1.0\" encoding=\"UTF-8\" standalone=\"yes\"?>
            <atom:entry xmlns:cmis=\"http:
            xmlns:cmism=\"http:
            xmlns:atom=\"http:
            xmlns:app=\"http:
            xmlns:aspects=\"http:
            xmlns:cmisra=\"http:
            <cmisra:object>
            <cmis:properties>
            <aspects:setAspects>
            <aspects:properties>
            <cmis:propertyString propertyDefinitionId=\"$aspect\" queryName=\"$aspect\">
            <cmis:value>$value</cmis:value>
            </cmis:propertyString>
            </aspects:properties>
            </aspects:setAspects>
            </cmis:properties>
            </cmisra:object>
            </atom:entry>
            ";
        $url=$this->selfUrl;
        $result=$this->putHttp($url,$this->username,$this->password,$inquiry);
        $this->loadCMISObject($this->properties['alfcmis:nodeRef']);
    }
    public function delete(){
        $url=$this->selfUrl;
        return $this->deleteHttp($url,$this->username,$this->password);
    }
    function getObjectId($node){
        $objdata=simplexml_load_string($node);
        if($objdata==FALSE){
        }
        $namespaces=$objdata->getNameSpaces(true);
        $cmisra=$objdata->children($namespaces['cmisra']);
        $cmis=$cmisra->children($namespaces['cmis']);
        for($x=0;$x<count($cmis->properties->propertyId);$x++){
            $propertyDefinitionId=$cmis->properties->propertyId[$x]->attributes()->propertyDefinitionId;
            $value=(string)$cmis->properties->propertyId[$x]->value;
            if($propertyDefinitionId=="cmis:objectId") return $value;
        }			
        return FALSE;
    }
}
?>
