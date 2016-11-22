<script language="javascript" src="js/func_montletra.js"></script>
<script language="javascript">
//-->
var numero = 0;
var valor = 0;
var archivos = new Array();
var archivoss = new Array();
var fisico = new Array();
formulario=document.getElementById('form1');

// Funciones comunes
c= function (tag) { // Crea un elemento
   return document.createElement(tag);
}
d = function (id) { // Retorna un elemento en base al id
   return document.getElementById(id);
}
e = function (evt) { // Retorna el evento
   return (!evt) ? event : evt;
}
f = function (evt) { // Retorna el objeto que genera el evento
   return evt.srcElement ?  evt.srcElement : evt.target;
}
addField = function () {
   container = d('files');
   
   span = c('SPAN');
   span.className = 'file';
   span.id = 'file' + (++numero);
  
   field = c('INPUT');   
   field.name = 'archivos[]';
   field.type = 'file';
   field.className = 'peq';
   
   a = c('A');
   a.name = span.id;
   a.href = '#';
   a.className='link';
   a.onclick = removeField;
   a.innerHTML = 'Eliminar';

   span.appendChild(field);
   span.appendChild(a);
   container.appendChild(span);
   formulario.largo_dig.value = numero;
   archivos.length=formulario.largo_dig.value ;
   //alert(archivos.length);
   	
   }
removeField = function (evt) {
   lnk = f(e(evt));
   span = d(lnk.name);
   span.parentNode.removeChild(span);
   valor=(--archivos.length);
   formulario.largo_dig.value = valor;
    
}
//RESPALDOS FISICOS
function add_fisico(id,tipo)
{   
	if(tipo==0){
		if(formulario.descripcion.value==""){
			alert("Debe indicar el nombre del respaldo fisico");
			return
		}
		var registro1 = new Array(1);
		registro1[0] = formulario.descripcion.value;
		for(i=0;i<fisico.length;i++)
		  { 
		  if (fisico[i][0]==registro1[0])
			{
			alert("Este nombre ya ha sido asignado");
			formulario.descripcion.value=""
			return;
			}
		  }
		fisico[fisico.length]=registro1;
	}
	
	var tbody = document.getElementById('body1');
	var tbody2 = document.getElementById(id);
	
	for(i=0;i<fisico.length-1;i++)
	{
		tbody2.deleteRow(2);
	}
	if(tipo!=0)
	{
		tbody2.deleteRow(2);
		for(i=tipo;i<fisico.length;i++)
		{
			fisico[i-1]=fisico[i];
		}
		
		fisico.pop();
	}
	
	var subtotal = 0;
	for(i=0;i<fisico.length;i++)
	{

		var row = document.createElement("tr")
				
		if((i%2)==0)
			row.className = "reci2"
		else
			row.className = "reci"
            
			//creamos una columna
		   	var td1 = document.createElement("td");
			td1.setAttribute("align","Center");
			td1.className = 'normal';
			//creamos una variable oculta
			var hid_1 = document.createElement("INPUT");
			hid_1.setAttribute("type","text");
			var name="descripcion"+i;
	        hid_1.setAttribute("name",name);
			hid_1.className = "normal";
	        hid_1.setAttribute("value",fisico[i]);
			hid_1.setAttribute("id",name);
	       	hid_1_text = document.createTextNode(fisico[i]);
					
			
			//td1.appendChild(hid_1);			
		    td1.appendChild(hid_1_text);	
			
			//Se crea la tercera columna
			var td3 = document.createElement("td");				
			td3.setAttribute("align","left");
			td3.className = 'link';
			
			editLink = document.createElement("a");
			linkText = document.createTextNode("Eliminar");
			editLink.setAttribute("href", "javascript:add_fisico('"+id+"','"+(i+1)+"')");
			editLink.className='link';
			editLink.appendChild(linkText);
			td3.appendChild (editLink);
			row.appendChild(td1);
			row.appendChild(td3);
			tbody.appendChild(row);
	}	
	formulario.descripcion.value = "";
	formulario.largo_fis.value = fisico.length;
	
}

function eliminar (id,tipo)
{

   	if(tipo==0)
   	{
	var registro2 = new Array(1);
	archivoss[archivoss.length]=registro2;
	}

	var tbody2 = document.getElementById(id);
	var tbody = document.getElementById('body2');
	
	
	if(tipo!=0)
	{
		for(i=0;i<archivoss.length;i++)
		tbody2.deleteRow(1);		
		//alert(archivos);
		for(i=tipo;i<archivoss.length;i++)
		{			
		 archivoss[i-1]=archivoss[i];			
		}
		
		archivoss.pop();
		
	}


        var cont=parseInt(archivoss.length);
	indice=0;
	i=0;
	while(i<cont)
	{
		var row = document.createElement("tr");
		var td4 = document.createElement("td");
		td4.setAttribute("align","Center");
		td4.appendChild (document.createTextNode(trim(archivoss[i])));
		
		//td4.appendChild (document.createTextNode(trim(archivoss[i][0])));
		var td26 = document.createElement("td");
		td26.setAttribute("align","left");
		td26.className = 'link';
		
		editLink = document.createElement("a");
		linkText = document.createTextNode("Eliminar");
		editLink.setAttribute("href","javascript:eliminar('"+id+"','"+(indice+1)+"')");
		//editLink.setAttribute("href","javascript:eliminar('" +codigo+"','" +descrip+"','"+id+"','"+(i+1)+"')");
		editLink.className='link';
		editLink.appendChild(linkText);
		td26.appendChild (editLink);
		row.appendChild(td4);
		row.appendChild(td26);
		tbody.appendChild(row);
		indice=indice+1;
		i=indice;
	}
	
		
}

function mostrar_digital(codigo,descrip,id,i)
{
	
		var registro = new Array(2);
		var tbody2 = document.getElementById(id);
		var tbody = document.getElementById('body2');
		registro[0] = descrip;
		archivoss[archivoss.length]=registro[0];

		var row = document.createElement("tr");
		var td4 = document.createElement("td");
		td4.setAttribute("align","Center");
		td4.appendChild (document.createTextNode(trim(registro[0])));
		var td26 = document.createElement("td");
		td26.setAttribute("align","left");
		td26.className = 'link';
		
		editLink = document.createElement("a");
		linkText = document.createTextNode("Eliminar");
		editLink.setAttribute("href","javascript:eliminar('"+id+"','"+(i+1)+"')");
		//editLink.setAttribute("href","javascript:eliminar('" +codigo+"','" +descrip+"','"+id+"','"+(i+1)+"')");
		editLink.className='link';
		editLink.appendChild(linkText);
				
		td26.appendChild (editLink);
		row.appendChild(td4);
		row.appendChild(td26);
		tbody.appendChild(row);
		
}

function mostrar_fisicos(codigo,descrip,id,i)
{
		var registro = new Array(2);
		var tbody2 = document.getElementById(id);
		var tbody = document.getElementById('body1');
		registro[0] = descrip;
		fisico[fisico.length]=registro[0];
		//alert(archivos[);
		var row = document.createElement("tr");
		var td4 = document.createElement("td");
		td4.setAttribute("align","Center");
		td4.appendChild (document.createTextNode(trim(registro[0])));
		var td26 = document.createElement("td");
		td26.setAttribute("align","left");
		td26.className = 'link';
		
		editLink = document.createElement("a");
		linkText = document.createTextNode("Eliminar");
		editLink.setAttribute("href","javascript:add_fisico('"+id+"','"+(i+1)+"')");
		//editLink.setAttribute("href","javascript:eliminar('" +codigo+"','" +descrip+"','"+id+"','"+(i+1)+"')");
		editLink.className='link';
		editLink.appendChild(linkText);
				
		td26.appendChild (editLink);
		row.appendChild(td4);
		row.appendChild(td26);
		tbody.appendChild(row);
		
}

function crear()
{
	formulario.txt_arreglo_f.value=''
	for(i=0;i<fisico.length;i++)
		{	//formulario.txt_arreglo_f.value+=fisico[i][0];
			formulario.txt_arreglo_f.value+=fisico[i];
			if(i!=(fisico.length-1))
				formulario.txt_arreglo_f.value+="/";
			else
				formulario.txt_arreglo_f.value;
		}
	return formulario.txt_arreglo_f.value
}

function crear_digital()
{
	formulario.txt_arreglo_d.value=''
	//alert(archivos.length);
	for(i=0;i<archivoss.length;i++)
		{
			//formulario.txt_arreglo_d.value+=archivoss[i][0];
			formulario.txt_arreglo_d.value+=archivoss[i];
			if(i!=(archivoss.length-1))
				formulario.txt_arreglo_d.value+="/";
			else
				formulario.txt_arreglo_d.value;
		}
	return formulario.txt_arreglo_d.value
}

</script>

</head>

<!--<body>-->

<?
$codig=$cod_doc;

		$sql_sopor="SELECT * FROM sai_buscar_doc_soporte('$codig') resultado_set(doso_doc_soport varchar)"; 
		$resultado_sopor=pg_query($conexion,$sql_sopor) or die("Error al mostrar lista de documentos relacionados");
		$total=pg_num_rows($resultado_sopor);
		if ($total>0) 
		{
		?>
		<table width="50%" align="center" background="../imagenes/fondo_tabla.gif" class="tablaalertas">
	  	<tr class="normal">
            	 <td height="15" colspan="2" valign="middle" class="td_gray"><div align="left" class="normalNegroNegrita">
			DOCUMENTOS ASOCIADOS </div>
            	 </td>
          	</tr>
		<tr>
            	<td width="21" align="center" class="normal">C&oacute;digo</td>
            	<td height="50%" align="center" class="normal">Descripci&oacute;n</td>
          	</tr>
		  	<?
			while($row_sopor=pg_fetch_array($resultado_sopor))
				{
		  	?>
			<tr>
				<td align="center" class="normal style4">
				
<?php 
	if(isset($tipoDocumento)){
		switch($tipoDocumento){
			case 'vnac':
			case GetConfig("preCodigoRendicionViaticoNacional"):
			case GetConfig("preCodigoAvance"):
			case GetConfig("preCodigoRendicionAvance"):
				$initialPath = '../../';
				break;
			default:
				$initialPath = '';
				break;
		}
		
	} else {
		$initialPath = ''; 
	}
?>
<a href="javascript:abrir_ventana('<?php echo $initialPath?>documentos/memo/memo_detalle.php?codigo=<? echo $row_sopor['doso_doc_soport'];?>')" class="copyright">
	
				<?php echo($row_sopor['doso_doc_soport']);?>
				</a>

				</td>
				<td class="normal style4" align="center">Devoluci&oacute;n</td></tr><?}?></table>




<?
}
if($request_id_objeto==1 || $request_id_objeto==2)
{
?>
<tr class="td_gray">
	  <td height="18" colspan="2" class="normalNegroNegrita">RESPALDOS DIGITALES </td>
    </tr>
	<tr>
	  <td height="18" colspan="2"><dl class="normal">
	   <table width="410" border="0" align="center" class="tablaCentral" id="tbl_digital">
          
          </tbody>
          <tr>
            <td height="50%" align="center"><dl><dt>
                  <label class="normal"><strong>Archivos Adjuntos:</strong></label>
                  &nbsp;<a href="javascript:addField()" accesskey="5" class="normal">Agregar</a><br>
              </dt>
              <dd><br>
                    <div id="files">
                      <input type="hidden" name="largo_dig" id="largo_dig" />
					  <input type="hidden" name="txt_arreglo_d" value="" /></td>
</div>
                    <br>
                </dd>
              <dd>&nbsp;</dd>
              <dt></dt>
            </dl></td>
          </tr>
		  <tbody id="body2">
          </tbody>
		  <tr>
            <td height="17" align="center"><?php 
			
			$tipo=Digital;
		
	$sql_resp="SELECT * FROM sai_any_tabla('sai_respaldo','docg_id,resp_nombre,resp_tipo','docg_id=''$codig'' and resp_tipo=''$tipo''')resultado_set(docg_id varchar, resp_nombre varchar,resp_tipo varchar)"; 

 $resultado_resp=pg_query($conexion,$sql_resp) or die("Error al mostrar lista de respaldos");
		$i = 0;
		while($row=pg_fetch_array($resultado_resp))
		{  ?>
              <script language='JavaScript' type='text/JavaScript'>
	mostrar_digital('<?=$row['usua_login'];?>','<?=$row['resp_nombre'];?>','tbl_digital',<?=$i;?>); 
	          </script>
              <?php $i = $i+1;}?></td>
          </tr>
        </table>
	    <dt></dt>
	    <dt></dt>
	    </dl></td>
    </tr>
	<tr>
	  <td height="18" colspan="2" class="td_gray"><span class="normalNegroNegrita"><strong>RESPALDOS F&#205;SICOS </strong></span></td>
    </tr>
	<tr>
	  <td height="18" colspan="2"><dl class="normal">
	    <table width="410" border="0" align="center" class="tablaCentral" id="tbl_fisico">
          
          <tr>            </tr>
          <tr>
            <td height="15" valign="middle" class="normal" >
              <div align="right"><strong>Nombre:</strong>
                  <input name="descripcion" id="descripcion" type="text" class="normal" />
                  </div></td><td valign="middle" class="normal"><div align="left"><a href="javascript: add_fisico('tbl_fisico','0')" class="normal" >Agregar</a></div></td>
          </tr>
          <tbody id="body1"  class="normal">
          </tbody>
		  <tr>
            <td height="50%" colspan="2" align="center">
			<?php 
			$codig=$cod_doc;
			$tipo=Fisico;
		
	$sql_resp="SELECT * FROM sai_seleccionar_campo('sai_respaldo','docg_id,resp_nombre,resp_tipo','docg_id=''$codig'' and resp_tipo=''$tipo''','',2)resultado_set(docg_id varchar, resp_nombre varchar,resp_tipo varchar)"; 
	$resultado_resp=pg_query($conexion,$sql_resp) or die("Error al mostrar lista de respaldos");
		$i = 0;
		while($row=pg_fetch_array($resultado_resp))
		{ //echo("Respaldo".$row['resp_nombre']."<br>"); ?>
              <script language='JavaScript' type='text/JavaScript'>
	mostrar_fisicos('<?=$row['usua_login'];?>','<?=$row['resp_nombre'];?>','tbl_fisico',<?=$i;?>)
	</script>
    <?php $i = $i+1;}?>
			&nbsp;
			</td>
          </tr>
          <tr>
            <td align="center"><input type="hidden" name="largo_fis" id="largo_fis" />
			<input type="hidden" name="txt_arreglo_f" value="" /></td>
            <td width="143" height="50%" align="center" class="normal">&nbsp;</td>
          </tr>
        </table>
	    <dt></dt>
	    </dl>	  </td>
    </tr>
<?
}
?>
