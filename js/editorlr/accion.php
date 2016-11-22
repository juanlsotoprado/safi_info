<script src="js/jquery-1.6.1.min.js" type="text/javascript" charset="utf-8"></script>
<script type="text/javascript" >
 function onLoad(){  
 $('#descrip_sin_tags').children('table').css({'font-size' : '13pt'});
alert ($('#descrip_sin_tags').html());}; 
</script>



 <span id="descrip_sin_tags" style="display:none">
 <? echo $_REQUEST['pcuenta_descripcion']?>
 </span>

 <script>
 onLoad();
</script>
<?php
 $descrip_sin_tags= "<script> document.write($('#descrip_sin_tags').html()) </script>";
 echo $descrip_sin_tags;
?>
 