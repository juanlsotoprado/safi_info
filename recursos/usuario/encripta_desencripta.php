<?
function bytexor($a,$b,$l)  {
   $c="";
   for($i=0;$i<$l;$i++) {
     $c.=$a{$i}^$b{$i};
   }
   return($c);
  }

  function binmd5($val)  {
   return(pack("H*",md5($val)));
  }

  function decrypt_md5($msg,$heslo)  {
   $key=$heslo;$sifra="";
   $key1=binmd5($key);
   while($msg) {
     $m=substr($msg,0,16);
     $msg=substr($msg,16);
     $sifra.=$m=bytexor($m,$key1,16);
     $key1=binmd5($key.$key1.$m);
   }
   echo "\n";
   return($sifra);
  }

  function crypt_md5($msg,$heslo) {
   $key=$heslo;$sifra="";
   $key1=binmd5($key);
   while($msg) {
     $m=substr($msg,0,16);
     $msg=substr($msg,16);
     $sifra.=bytexor($m,$key1,16);
     $key1=binmd5($key.$key1.$m);
   }
   echo "\n";
   return($sifra);
  }
?> 