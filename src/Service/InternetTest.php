<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\String\Slugger\SluggerInterface;

class InternetTest
{
   
    function pingAddress($ip) {
        $pingresult = exec("/bin/ping -n 3 $ip", $outcome, $status);
        if (0 == $status) {
            $status = "alive";
        } else {
            $status = "dead";
        }
        return $status;


    }

function pingGmail(){
 $r = 'dead';

$host = 'smtp.gmail.com'; 
$port = 465; 
$waitTimeoutInSeconds = 1; 
if($fp = fsockopen($host,$port,$errCode,$errStr,$waitTimeoutInSeconds)){   
    $r='alive' ;
} else {
    $r='dead' ;
} 
if($fp !== FALSE ){
    fclose($fp);
}


return $r;

    }

   
}

?>