<?php
	$memos = $GLOBALS['SafiRequestVars']['memos'];
	
	if($memos != null && is_array($memos) && count($memos) > 0)
	{
?>
<br/>
<table cellspacing="0" cellpadding="0" class="tablaalertas content" align="center" width="600px">
	<tr>
		<td class="header normalNegroNegrita">Documentos asociados</td>
	</tr>
	<tr>
		<td><table class="tablaalertas content" cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td class="normalNegroNegrita">C&oacute;digo</td>
				<td class="normalNegroNegrita">Descripci&oacute;n</td>
				<td class="normalNegroNegrita">Fecha</td>
			</tr>
			<?php
				foreach ($memos AS $memo)
				{
					echo '
			<tr>
				<td>'.$memo->GetId().'</td>
				<td>'.$memo->GetContenido().'</td>
				<td>'.$memo->GetFechaCreacion().'</td>
			</tr>
					';
				}
			?>
		</table></td>
	</tr>
</table>
<?php
	}
?>