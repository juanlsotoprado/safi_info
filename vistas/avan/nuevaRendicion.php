<?php
	$form = FormManager::GetForm(FORM_NUEVA_RENDICION_AVANCE);
	$formUTF8 = clone $form;
	$formUTF8->UTF8Encode();
	
	$rendicion = $form->GetRendicionAvance();
	$avance = $form->GetAvance();
	
	$estados = $GLOBALS['SafiRequestVars']['estados'];
	$responsablesDisponibles = $GLOBALS['SafiRequestVars']['responsablesDisponibles'];
	$listaResponsables = $GLOBALS['SafiRequestVars']['listaResponsables'];
	$bancos = $GLOBALS['SafiRequestVars']['bancos'];
	
	$a_oPresupuesto = $_SESSION["an_o_presupuesto"];
	
	/* Parche para que las rendiciones de avance queden en el año anterior */
	//$a_oPresupuesto = 2014;
?>
<html>
	<head>
		<title>.:SAFI:. Ingresar Rendición de Vi&aacute;tico Nacional</title>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
		
		<link href="../../css/plantilla.css" rel="stylesheet" type="text/css" />
		<link href="../../css/safi0.2.css" rel="stylesheet" type="text/css" />
		<link href="../../js/lib/jquery/themes/ui.css" rel="stylesheet" type="text/css" />
		<link type="text/css" href="../../js/lib/calendarPopup/css/calpopup.css" media="screen" rel="stylesheet"/>
		
		<script type="text/javascript" src="../../js/lib/calendarPopup/js/events.js"></script>
		<script type="text/javascript" src="../../js/lib/calendarPopup/js/calpopup.js"></script>
		<script type="text/javascript" src="../../js/lib/calendarPopup/js/dateparse.js"></script>
		<script type="text/javascript" src="../../js/lib/jquery/plugins/jquery.min.js"></script>
		<script type="text/javascript" src="../../js/lib/jquery/plugins/ui.min.js"></script>
		<script type="text/javascript" src="../../js/constantes.js"></script>
		<script type="text/javascript" src="../../js/funciones.js"></script>
		<script>
			<?php
			
				echo "var A_oPresupuesto = " . $a_oPresupuesto;
			
				$estadosJson = array();
				if(is_array($estados))
				{
					foreach ($estados as $estado)
					{
						$estado->UTF8Encode();
						$estadosJson[$estado->Getid()] =  "\"" . $estado->GetId() . "\": ". $estado->ToJson();
					}
				}
				
				$responsablesRendicionAvancePartidasByIdResponsableJson = array();
				if(is_array($listaResponsables))
				{
					foreach ($listaResponsables as $responsableRendicionAvancePartidas)
					{
						$responsableRendicionAvancePartidas->UTF8Encode();
						$responsableRendicionAvance = $responsableRendicionAvancePartidas->GetResponsableRendicionAvance();
						
						$responsablesRendicionAvancePartidasByIdResponsableJson[] = 
							"\"" . $responsableRendicionAvance->GetIdResponsableAvance() .
							"\": " . $responsableRendicionAvancePartidas->ToJson();
					}
				}
				
				$bancosJson = array();
				if(is_array($bancos))
				{
					foreach ($bancos as $banco)
					{
						$banco->UTF8Encode();
						$bancosJson[] = "\"" . $banco->GetId() . "\": " . $banco->ToJson();
					}
				}
				
				echo "
			var formRendicionAvance = " . $formUTF8->ToJson() . ";
			var rendicionAvance = (formRendicionAvance != null)
				? ((formRendicionAvance.rendicionAvance) ? formRendicionAvance.rendicionAvance  : null) : null;
			var estados = { ".implode(", ", $estadosJson)." };
			
			var responsablesRendicionAvancePartidasByIdResponsable = 
				{ ".implode(", ", $responsablesRendicionAvancePartidasByIdResponsableJson)." };
			var bancos = { ".implode(", ", $bancosJson)." };
				";
			?>
			
			var responsableIdSeq = 0;
			var reintegrosDatosIdSeq = 0;
			g_Calendar.setDateFormat('dd/mm/yyyy');
	
			function onLoad()
			{
				location.href = "#mensajes";
				
				if($('#idAvanceBuscado').length > 0)
					establecerFocoInicialCodigoDocumento("idAvanceBuscado");
				
				if(rendicionAvance.responsablesRendicionAvancePartidas != null)
				{
					$.each(
						rendicionAvance.responsablesRendicionAvancePartidas,
						function(indexResponsable, objResponsableRendicionAvancePartidas
						){
							agregarResponsable({objResponsableRendicionAvancePartidas: objResponsableRendicionAvancePartidas});
						}
					);
					/*
					calcularTodosSubtotalesResponsables();
					calcularTotalResponsable();
					*/
				}
			}

			function accionAgregarResponsable(idSelectResponsablesDisponibles)
			{
				objSelect = $('#'+idSelectResponsablesDisponibles);

				if(objSelect.length > 0){
					var indexSelect = objSelect[0].selectedIndex;
					var objOption = $(objSelect[0].options[indexSelect]);
					var idResponsable = objOption.val();

					// Agregar el responsable seleccionado
					if(idResponsable != 0 && responsablesRendicionAvancePartidasByIdResponsable[idResponsable]){
						agregarResponsable({
							objResponsableRendicionAvancePartidas: responsablesRendicionAvancePartidasByIdResponsable[idResponsable]
						});
						objOption.remove();
					}
					// Agregar todos los responsables 
					else if(idResponsable == 0)
					{
						objOptions = objSelect.find("option");
						objOptions.each(function(index, objOption)
						{
							objOption = $(objOption);
							var idResponsableActual = objOption.val();
							if(idResponsableActual != 0 && responsablesRendicionAvancePartidasByIdResponsable[idResponsableActual]){
								agregarResponsable({
									objResponsableRendicionAvancePartidas: 
										responsablesRendicionAvancePartidasByIdResponsable[idResponsableActual]
								});
								objOption.remove();
							}
						});
					}
				}
			}

			function agregarResponsable()
			{
				responsableIdSeq++;

				var params = arguments[0] || null;
				var objResponsableRendicionAvancePartidas = params != null ? (params.objResponsableRendicionAvancePartidas || null) : null;

				var idResponsableAvance = "";
				var cedulaResponsable = "";
				var nombreResponsable = "";
				var tipoResponsable = "";
				var idEstadoSelected = "";
				var montoAnticipo = 0;
				var reintegroIdBanco = 0;
				var reintegroReferencia = "";
				var reintegroFecha = "";
				var objRendicionAvancePartidas = null;
				var objRendicionAvanceReintegros = null;

				var idResponsablesAvanceName = "idResponsablesAvance[]";
				var tiposResponsablesName = "tiposResponsables[]";
				var correlativosResponsablesName = "correlativosResponsables[]";
				var cedulasResponsablesName = "cedulasResponsables[]";
				var selectorCedulasResponsablesName = "cedulasResponsables\\[\\]";
				var estadosResponsablesName = "estadosResponsables[]";
				var partidasName = "partidas[" + responsableIdSeq + "][]";
				var partidasMontosName = "partidasMontos[" + responsableIdSeq + "][]";

				if(objResponsableRendicionAvancePartidas != null)
				{
					if(objResponsableRendicionAvancePartidas.responsableRendicionAvance != null){
						var objResponsableRendicionAvance = objResponsableRendicionAvancePartidas.responsableRendicionAvance;

						idResponsableAvance = objResponsableRendicionAvance.idResponsableAvance;
					
						if(
							objResponsableRendicionAvance.tipoResponsable == '<?php echo EntidadResponsable::TIPO_EMPLEADO?>'
							&& objResponsableRendicionAvance.empleado != null
						){
							tipoResponsable = objResponsableRendicionAvance.tipoResponsable;
							var objEmpleado = objResponsableRendicionAvance.empleado;
							cedulaResponsable = objEmpleado.id;
							nombreResponsable = objEmpleado.nombres.toUpperCase() + ' ' + objEmpleado.apellidos.toUpperCase();
						} else if(
							objResponsableRendicionAvance.tipoResponsable == '<?php echo EntidadResponsable::TIPO_BENEFICIARIO?>'
							&& objResponsableRendicionAvance.beneficiario != null
						){
							tipoResponsable = objResponsableRendicionAvance.tipoResponsable;
							var objBeneficiario = objResponsableRendicionAvance.beneficiario;
							cedulaResponsable = objBeneficiario.id;
							nombreResponsable = objBeneficiario.nombres.toUpperCase() + ' ' + objBeneficiario.apellidos.toUpperCase(); 
						}
						
						if(objResponsableRendicionAvance.estado != null){
							idEstadoSelected = objResponsableRendicionAvance.estado.id; 
						}

						// Obtener los datos de reintegro
						
						if(objResponsableRendicionAvance.reintegroBanco != null){
							reintegroIdBanco = objResponsableRendicionAvance.reintegroBanco.id;
						}
						reintegroReferencia = objResponsableRendicionAvance.reintegroReferencia;
						reintegroFecha = objResponsableRendicionAvance.reintegroFecha;
					}

					// Obtener el monto del anticipo para este responsable
					
					montoAnticipo = parseFloat(objResponsableRendicionAvancePartidas.montoAnticipo);
					montoAnticipo = (isNaN(montoAnticipo)) ? 0 : montoAnticipo;

					if(objResponsableRendicionAvancePartidas.rendicionAvancePartidas != null)
						objRendicionAvancePartidas = objResponsableRendicionAvancePartidas.rendicionAvancePartidas;

					if(objResponsableRendicionAvancePartidas.rendicionAvanceReintegros != null)
						objRendicionAvanceReintegros = objResponsableRendicionAvancePartidas.rendicionAvanceReintegros;
				}

				var tdResponsables = "tdResponsables";
				var objTdResponsables = $('#' + tdResponsables);

				var objTable = document.createElement('table');
				objTable.setAttribute('class', 'wrapperResponsablesAvance');

				var objTbody = document.createElement('tbody');
				objTable.appendChild(objTbody);

				// tr datos del responsable 
				var objTr = document.createElement('tr');
				objTbody.appendChild(objTr);

				var objTd = document.createElement('td');
				objTr.appendChild(objTd);

				// table de datos del responsable
				var objTableSub = document.createElement('table');
				objTableSub.setAttribute('class', 'tableSub');
				objTableSub.setAttribute('cellspacing', '0');
				objTableSub.setAttribute('cellpadding', '0');
				objTd.appendChild(objTableSub);

				var objTbodySub = document.createElement('tbody');
				objTableSub.appendChild(objTbodySub);

				// tr de datos de responsable: nombre, cédula, estado
				var objTrSub = document.createElement('tr');
				objTbodySub.appendChild(objTrSub);

				// td de etiqueta de nombre del responsable
				var objTdSub = document.createElement('td');
				objTdSub.setAttribute('class', 'normalNegrita');
				objTdSub.appendChild(document.createTextNode('Nombre:'));
				objTrSub.appendChild(objTdSub);

				// td de nombre del responsable
				var objTdSub = document.createElement('td');
				objTdSub.setAttribute('class', 'nombreResponsable');
				objTdSub.appendChild(document.createTextNode(nombreResponsable));
				objTrSub.appendChild(objTdSub);

				// td de etiqueta de cédula del responsable
				var objTdSub = document.createElement('td');
				objTdSub.setAttribute('class', 'normalNegrita');
				objTdSub.appendChild(document.createTextNode('C'+eACUTE+'dula:'));
				objTrSub.appendChild(objTdSub);

				// td de cédula del responsable
				var objTdSub = document.createElement('td');
				objTrSub.appendChild(objTdSub);

				var objInputHidden = document.createElement('input');
				objInputHidden.setAttribute('type', 'hidden');
				objInputHidden.setAttribute('name', idResponsablesAvanceName);
				objInputHidden.setAttribute('value', idResponsableAvance);
				objTdSub.appendChild(objInputHidden);

				var objInputHidden = document.createElement('input');
				objInputHidden.setAttribute('type', 'hidden');
				objInputHidden.setAttribute('name', correlativosResponsablesName);
				objInputHidden.setAttribute('value', responsableIdSeq);
				objTdSub.appendChild(objInputHidden);

				var objInputHidden = document.createElement('input');
				objInputHidden.setAttribute('type', 'hidden');
				objInputHidden.setAttribute('name', tiposResponsablesName);
				objInputHidden.setAttribute('value', tipoResponsable);
				objTdSub.appendChild(objInputHidden);

				var objInputHidden = document.createElement('input');
				objInputHidden.setAttribute('type', 'hidden');
				objInputHidden.setAttribute('name', cedulasResponsablesName);
				objInputHidden.setAttribute('value', cedulaResponsable);
				objTdSub.appendChild(objInputHidden);

				var objSpan = document.createElement("span");
				objSpan.setAttribute("class", "cedulaResponsable");
				objSpan.appendChild(document.createTextNode(cedulaResponsable));

				objTdSub.appendChild(objSpan);

				// td de etiqueta de estado del responsable
				var objTdSub = document.createElement('td');
				objTdSub.setAttribute('class', 'normalNegrita');
				objTdSub.appendChild(document.createTextNode('Estado:'));
				objTrSub.appendChild(objTdSub);

				// td de etiqueta de estado del responsable
				var objTdSub = document.createElement('td');
				objTrSub.appendChild(objTdSub);
				
				// span del estado del responsable
				var nombreEstado = (estados[idEstadoSelected]) ? estados[idEstadoSelected].nombre : "No encontrado";
				
				var objSpan = document.createElement("span");
				objSpan.setAttribute("class", "estadoResponsable");
				objSpan.appendChild(document.createTextNode(nombreEstado));

				objTdSub.appendChild(objSpan);
				
				// tr datos datos de las partidas por responsable
				var objTr = document.createElement('tr');
				objTbody.appendChild(objTr);

				var objTd = document.createElement('td');
				objTr.appendChild(objTd);
				
				// table de datos de las partidas por responsable
				var objTableSub = document.createElement('table');
				objTableSub.setAttribute('class', 'tableSub rendicionPartidasyMontos');
				objTableSub.setAttribute('cellspacing', '0');
				objTableSub.setAttribute('cellpadding', '0');
				objTd.appendChild(objTableSub);

				var objTbodySub = document.createElement('tbody');
				objTableSub.appendChild(objTbodySub);

				// tr de etiquetas de partida y monto
				var objTrSub = document.createElement('tr');
				objTbodySub.appendChild(objTrSub);

				// td de etiqueta de partidas de la izquierda
				var objTdSub = document.createElement('td');
				objTdSub.setAttribute('class', 'normalNegrita');
				objTdSub.appendChild(document.createTextNode('Partida'));
				objTrSub.appendChild(objTdSub);

				// td de etiqueta de montos de la izquierda
				var objTdSub = document.createElement('td');
				objTdSub.setAttribute('class', 'normalNegrita');
				objTdSub.appendChild(document.createTextNode('Monto'));
				objTrSub.appendChild(objTdSub);

				// td de etiqueta de partidas de la derecha
				var objTdSub = document.createElement('td');
				objTdSub.setAttribute('class', 'normalNegrita');
				objTdSub.appendChild(document.createTextNode('Partida'));
				objTrSub.appendChild(objTdSub);

				// td de etiqueta de montos de la derecha
				var objTdSub = document.createElement('td');
				objTdSub.setAttribute('class', 'normalNegrita');
				objTdSub.appendChild(document.createTextNode('Monto'));
				objTrSub.appendChild(objTdSub);

				// td de boton de agregar tupla partida/monto
				var objTdSub = document.createElement('td');
				objTdSub.setAttribute('class', 'botonesPartidasYMontos');
				objTrSub.appendChild(objTdSub);

				// Div de boton agregar tupla partida/monto
				var objDiv = document.createElement('div');
				objDiv.setAttribute('class', 'botonAgregarPartidasYMontos');
				objDiv.setAttribute('title', 'Agregar una fila de partidas/montos.');
				objTdSub.appendChild(objDiv);

				objDiv = $(objDiv);
				objDiv.unbind('click');
				objDiv.bind('click', {objTbodySub: objTbodySub},function(event)
				{
					agregarFilaPartidasMontos({
						objTbodySub: event.data.objTbodySub,
						objTable: objTable,
						partidasName: partidasName,
						partidasMontosName: partidasMontosName,
						arrObjRendicionAvancePartida: null
					});
				});

				// Agregar todos los datos de partidas/montos presentes, para el responsable actual de la rendición de avance 
				if(objRendicionAvancePartidas != null)
				{
					var indexElement = 0;
					var lstObjRendicionAvancePartida = null;
					$(objRendicionAvancePartidas).each(function (index, objRendicionAvancePartida)
					{
						if(indexElement % 2 == 0)
						{
							arrObjRendicionAvancePartida = [objRendicionAvancePartida];

							if((objRendicionAvancePartidas.length-1) == indexElement)
							{
								agregarFilaPartidasMontos({
									objTbodySub: objTbodySub,
									objTable: objTable,
									partidasName: partidasName,
									partidasMontosName: partidasMontosName,
									arrObjRendicionAvancePartida: arrObjRendicionAvancePartida
								});
							}
						}
						else
						{
							arrObjRendicionAvancePartida[1] = objRendicionAvancePartida;

							agregarFilaPartidasMontos({
								objTbodySub: objTbodySub,
								objTable: objTable,
								partidasName: partidasName,
								partidasMontosName: partidasMontosName,
								arrObjRendicionAvancePartida: arrObjRendicionAvancePartida
							});
						}
						indexElement++;
					});
				}

				
				/***************************
				*	Datos de reintegro     *
				****************************/

				// tr de la tablas de datos de reintegro
				var objTr = document.createElement('tr');
				objTbody.appendChild(objTr);

				// td de la tabla de datos de reintegro
				var objTd = document.createElement('td');
				objTr.appendChild(objTd);
				
				// table de datos de reintegro
				var objTableSub = document.createElement('table');
				objTableSub.setAttribute('class', 'tableSub');
				objTableSub.setAttribute('cellspacing', '0');
				objTableSub.setAttribute('cellpadding', '0');
				objTd.appendChild(objTableSub);

				var objTbodySub = document.createElement('tbody');
				objTableSub.appendChild(objTbodySub);

				// Tr de etiquetas de datos de reintegro
				var objTrSub = document.createElement('tr');
				objTbodySub.appendChild(objTrSub);

				// Td de etiqueta de banco
				var objTdSub = document.createElement('td');
				objTdSub.setAttribute('class', 'normalNegrita');
				objTdSub.appendChild(document.createTextNode("Banco"));
				objTrSub.appendChild(objTdSub);

				// Td de etiqueta de Nro. de referencia
				var objTdSub = document.createElement("td");
				objTdSub.setAttribute('class', 'normalNegrita');
				objTdSub.appendChild(document.createTextNode("Nro. referencia"));
				objTrSub.appendChild(objTdSub);

				// Td etiqueta de fecha de reintegro
				var objTdSub = document.createElement("td");
				objTdSub.setAttribute('class', 'normalNegrita');
				objTdSub.appendChild(document.createTextNode("Fecha"));
				objTrSub.appendChild(objTdSub);

				// Td etiqueta de monto
				var objTdSub = document.createElement("td");
				objTdSub.setAttribute('class', 'normalNegrita');
				objTdSub.appendChild(document.createTextNode("Monto"));
				objTrSub.appendChild(objTdSub);

				// Td de boton agregar datos de reintegro
				var objTdSub = document.createElement("td");				
				objTdSub.setAttribute('class', 'botonesDatosReintegro');
				objTrSub.appendChild(objTdSub);

				// Div de boton agregar datos de reintegro
				var objDiv = document.createElement('div');
				objDiv.setAttribute('class', 'botonAgregarDatosReintegro');
				objDiv.setAttribute('title', 'Agregar una fila de datos de reintegro.');
				objTdSub.appendChild(objDiv);

				objDiv = $(objDiv);
				objDiv.unbind('click');
				objDiv.bind('click', {objTbodySub: objTbodySub, responsableIdSeq: responsableIdSeq},function(event){
						agregarFilaDatosReintegro({
							objTable: objTable,
							objContainer: event.data.objTbodySub,
							responsableIdSeq: event.data.responsableIdSeq
						});
					}
				);

				var existeReintegro = false;

				if(objRendicionAvanceReintegros != null)
				{
					$(objRendicionAvanceReintegros).each(function(index, objRendicionAvanceReintegro)
					{
						existeReintegro = true;
						// Datos de reintegro
						agregarFilaDatosReintegro({
							objTable: objTable,
							objContainer: objTbodySub,
							responsableIdSeq: responsableIdSeq,
							objRendicionAvanceReintegro: objRendicionAvanceReintegro
						});
					});
				}

				if(!existeReintegro)
				{
					agregarFilaDatosReintegro({
						objTable: objTable,
						objContainer: objTbodySub,
						responsableIdSeq: responsableIdSeq
					});
				}

				/**********************************
				*	Fin de Datos de reintegro     *
				***********************************/


				/***************************
				*   Impresión de totales   *
				****************************/				
				
				// tr totales para cada responsable
				var objTr = document.createElement('tr');
				objTbody.appendChild(objTr);

				var objTd = document.createElement('td');
				objTr.appendChild(objTd);
				
				// table de totales para cada responsable
				var objTableSub = document.createElement('table');
				objTableSub.setAttribute('class', 'tableSub');
				objTableSub.setAttribute('cellspacing', '0');
				objTableSub.setAttribute('cellpadding', '0');
				objTd.appendChild(objTableSub);

				var objTbodySub = document.createElement('tbody');
				objTableSub.appendChild(objTbodySub);

				// Tr Anticipo entregado
				var objTrSub = document.createElement('tr');
				objTbodySub.appendChild(objTrSub);
				
				// Td Anticipo
				var objTdSub = document.createElement('td');
				objTdSub.setAttribute('class', 'anticipo');
				objTrSub.appendChild(objTdSub);

				// Anticipo
				objSpan = document.createElement('span');
				objSpan.setAttribute('class', 'normalNegrita');
				objSpan.appendChild(document.createTextNode('Monto anticipo:'+NON_BREAKING_SPACE));
				objTdSub.appendChild(objSpan);

				objSpanSubtotal = document.createElement('span');
				objSpanSubtotal.setAttribute('class', 'montoAnticipo');
				objSpanSubtotal.appendChild(document.createTextNode(montoAnticipo.toFixed(2).replace(".", ",")));
				objTdSub.appendChild(objSpanSubtotal);

				// Tr de monto gastado
				var objTrSub = document.createElement('tr');
				objTbodySub.appendChild(objTrSub);

				// Td gastado
				var objTdSub = document.createElement('td');
				objTdSub.setAttribute('class', 'gastado');
				objTrSub.appendChild(objTdSub);

				// monto gastado
				objSpan = document.createElement('span');
				objSpan.setAttribute('class', 'normalNegrita');
				objSpan.appendChild(document.createTextNode('Monto gastado:'+NON_BREAKING_SPACE));
				objTdSub.appendChild(objSpan);

				objSpanSubtotal = document.createElement('span');
				objSpanSubtotal.setAttribute('class', 'montoGastado');
				objSpanSubtotal.appendChild(document.createTextNode('0,0'));
				objTdSub.appendChild(objSpanSubtotal);

				// Calcular el monto total gastado para el responsable actual
				calcularMontoGastadoResponsable(objTable);

				// Tr de monto de Reintegro Fundación / Asumido trabajador
				var objTrSub = document.createElement('tr');
				objTbodySub.appendChild(objTrSub);

				// Td Reintegro Fundación / Asumido trabajador
				var objTdSub = document.createElement('td');
				objTdSub.setAttribute('class', 'reintegro');
				objTrSub.appendChild(objTdSub);

				// Reintegro Fundación/Trabajador
				objSpan = document.createElement('span');
				objSpan.setAttribute('class', 'etiquetaMontoReintegro normalNegrita');
				objSpan.appendChild(document.createTextNode('Reintegro:'+NON_BREAKING_SPACE));
				objTdSub.appendChild(objSpan);

				objSpanSubtotal = document.createElement('span');
				objSpanSubtotal.setAttribute('class', 'montoReintegro');
				objSpanSubtotal.appendChild(document.createTextNode("0,0"));
				objTdSub.appendChild(objSpanSubtotal);

				// Calcular el monto total de reintegro a la Fundación o monto asumido por el trabajador
				calcularReintegroResponsable(objTable);				

				// Tr de monto reintegrado
				var objTrSub = document.createElement('tr');
				objTbodySub.appendChild(objTrSub);

				// Td de monto reintegrado
				var objTdSub = document.createElement('td');
				objTdSub.setAttribute('class', 'reintegrado');
				objTrSub.appendChild(objTdSub);

				// monto reintegrado
				objSpan = document.createElement('span');
				objSpan.setAttribute('class', 'normalNegrita');
				objSpan.appendChild(document.createTextNode('Monto reintegrado:'+NON_BREAKING_SPACE));
				objTdSub.appendChild(objSpan);

				objSpanSubtotal = document.createElement('span');
				objSpanSubtotal.setAttribute('class', 'montoReintegrado');
				objSpanSubtotal.appendChild(document.createTextNode('0,0'));
				objTdSub.appendChild(objSpanSubtotal);

				// Calcular el monto total reintegrado a la Fundación
				calcularMontoReintegradoResponsable(objTable);

				// Tr de monto diferencia
				var objTrSub = document.createElement('tr');
				objTbodySub.appendChild(objTrSub);

				// Td de monto diferencia
				var objTdSub = document.createElement('td');
				objTdSub.setAttribute('class', 'diferencia');
				objTrSub.appendChild(objTdSub);

				// monto diferencia
				objSpan = document.createElement('span');
				objSpan.setAttribute('class', 'normalNegrita');
				objSpan.appendChild(document.createTextNode('Monto diferencia:'+NON_BREAKING_SPACE));
				objTdSub.appendChild(objSpan);

				objSpanSubtotal = document.createElement('span');
				objSpanSubtotal.setAttribute('class', 'montoDiferencia');
				objSpanSubtotal.appendChild(document.createTextNode('0,0'));
				objTdSub.appendChild(objSpanSubtotal);

				// Calcular el monto total reintegrado a la Fundación
				calcularMontoDiferenciaResponsable(objTable);

				/**********************************
				*   Fin de Impresión de totales   *
				***********************************/

				
				// Tr link eliminar
				var objTr = document.createElement('tr');
				objTbody.appendChild(objTr);

				var objTd = document.createElement('td');
				objTd.setAttribute('class', 'footer');
				objTr.appendChild(objTd);
				
				// Link eliminar
				var objA = document.createElement('a');
				objA.setAttribute('href', 'javascript:void(0);');
				objA.appendChild(document.createTextNode('Eliminar responsable'));
				objTd.appendChild(objA);

				objA = $(objA);
				objA.unbind('click');
				objA.bind('click', 
					{idResponsable: idResponsableAvance, cedulaResponsable: cedulaResponsable, nombreResponsable: nombreResponsable},
					function(event)
					{					
						$(objTable).remove();
						//calcularTotalResponsable();
	
						if(
							event.data.idResponsable != 0
							&& responsablesRendicionAvancePartidasByIdResponsable[event.data.idResponsable]
						){
							var objOption = document.createElement("option");
							var cedula = event.data.cedulaResponsable;
							if(cedula.length <= 8)
							{
								cedula = (NON_BREAKING_SPACE + event.data.cedulaResponsable).slice(-8);
								cedula = cedula.replace(NON_BREAKING_SPACE, NON_BREAKING_SPACE + NON_BREAKING_SPACE);
							}
							objOption.appendChild(document.createTextNode(cedula+" - "+event.data.nombreResponsable));
							$(objOption).val(event.data.idResponsable);
	
							$("#selectResponsablesDisponibles").append(objOption);
						}
					}
				);
				
				objTdResponsables.append(objTable);

				// Calcular el monto total total  
				//calcularTotalResponsable();
			}

			function agregarFilaDatosReintegro()
			{
				var params = arguments[0] || null;
				var objRendicionAvanceReintegro = params != null ? (params.objRendicionAvanceReintegro || null) : null;
				var objContainer = params != null ? (params.objContainer || null) : null;
				var objTable = params != null ? (params.objTable || null) : null;
				var responsableIdSeq = params != null ? (params.responsableIdSeq || null) : null;

				if(objContainer == null) return;
				if(objTable == null) return;
				if(responsableIdSeq == null) return;

				// Aumentar el número de secuencia de los datos de reintegro
				reintegrosDatosIdSeq++;
				
				var bancosReintegrosName = 'bancosReintegros[' + (responsableIdSeq) + '][]';
				var referenciasReintegrosName = 'referenciasReintegros[' + (responsableIdSeq) + '][]';				
				var fechasReintegrosId = 'fechaReintegro_' + (reintegrosDatosIdSeq);
				var fechasReintegrosName = 'fechasReintegros[' + (responsableIdSeq) + '][]';
				var montosReintegrosName = 'montosReintegros[' + (responsableIdSeq) + '][]';

				var idBancoSelected = "";
				var referencia = "";
				var fecha = "";
				var monto = "";

				if(objRendicionAvanceReintegro != null)
				{
					if(objRendicionAvanceReintegro.banco != null)
						idBancoSelected = objRendicionAvanceReintegro.banco.id;
					referencia = objRendicionAvanceReintegro.referencia;
					fecha = objRendicionAvanceReintegro.fecha;
					monto = objRendicionAvanceReintegro.monto;
				}
				
				// Tr de de datos de reintegro
				var objTrSub = document.createElement('tr');
				objContainer.appendChild(objTrSub);

				// Td de select de banco de reintegro
				var objTdSub = document.createElement('td');
				objTrSub.appendChild(objTdSub);

				var objSelectBancos = document.createElement('select');
				objSelectBancos.setAttribute('name', bancosReintegrosName);
				objSelectBancos.setAttribute('class', 'normalNegro');
				objTdSub.appendChild(objSelectBancos);

				var objOption = document.createElement('option');				
				objOption.setAttribute('value', '0');
				objOption.appendChild(document.createTextNode("Seleccionar..."));
				objSelectBancos.appendChild(objOption);

				if(bancos && bancos != null)
				{
					$.each(bancos, function(idBanco, objBanco){
						objOption = document.createElement('option');
						objOption.setAttribute('value', idBanco);
						objOption.appendChild(document.createTextNode(objBanco.nombre.toUpperCase()));
						
						if(idBanco == idBancoSelected){
							objOption.setAttribute('selected', 'selected');
						}
						
						objSelectBancos.appendChild(objOption);
					});
				}

				// Td de input Nro. de referencia de reintegro 
				var objTdSub = document.createElement("td");
				objTrSub.appendChild(objTdSub);

				// Input de Nro. de referencia de reintegro
				var objInput = document.createElement('input');
				objInput.setAttribute('name', referenciasReintegrosName);
				objInput.setAttribute('value', referencia);
				objInput.setAttribute('type', 'text');
				objInput.setAttribute('class', 'normalNegro');
				objInput.setAttribute('autocomplete', 'off');
				objTdSub.appendChild(objInput);

				// Td de input de fecha de reintegro
				var objTdSub = document.createElement("td");
				objTrSub.appendChild(objTdSub);

				var objInput = document.createElement('input');
				objInput.setAttribute('id', fechasReintegrosId);
				objInput.setAttribute('name', fechasReintegrosName);
				objInput.setAttribute('type', 'text');
				objInput.setAttribute('class', 'dateparse');
				objInput.setAttribute('size', '10');
				objInput.setAttribute("autocomplete", "off");
				objInput.setAttribute('value', fecha);
				objTdSub.appendChild(objInput);

				var objA = document.createElement('a');
				objA.setAttribute('href', 'javascript:void(0);');
				objTdSub.appendChild(objA);

				var objImg = document.createElement('img');
				objImg.setAttribute('src', '../../js/lib/calendarPopup/img/calendar.gif');
				objImg.setAttribute('class', 'cp_img');
				objImg.setAttribute('alt', 'Open popup calendar');
				objA.appendChild(objImg);

				objA = $(objA);
				objA.unbind('click');
				objA.bind('click', {id: fechasReintegrosId}, function(event){
					g_Calendar.show(event, event.data.id);
				});
				
				// Td de monto de reintegro
				objTdSub = document.createElement('td');
				objTrSub.appendChild(objTdSub);

				// Input de monto de reintegro
				var objInput = document.createElement('input');
				objInput.setAttribute('name', montosReintegrosName);
				objInput.setAttribute('value', monto);
				objInput.setAttribute('type', 'text');
				objInput.setAttribute('class', 'normalNegro');
				objInput.setAttribute('autocomplete', 'off');
				objTdSub.appendChild(objInput);

				objInput = $(objInput);
				objInput.unbind('keyup');
				objInput.bind('keyup', {objInput: objInput[0]}, function(event)
				{
					validarDecimal(event.data.objInput);
					calcularMontoReintegradoResponsable(objTable);
					calcularMontoDiferenciaResponsable(objTable);
				});
				
				// Td de div de botón eliminar datos de reintegro
				var objTdSub = document.createElement('td');
				objTdSub.setAttribute('class', 'botonesDatosReintegro');
				objTrSub.appendChild(objTdSub);

				// Div de botón eliminar datos de reintegro
				var objDiv = document.createElement('div');
				objDiv.setAttribute('class', 'botonEliminarDatosReintegro');
				objDiv.setAttribute('title', 'Eliminar esta fila de datos de reintegro.');
				objTdSub.appendChild(objDiv);

				objDiv = $(objDiv);
				objDiv.unbind('click');
				objDiv.bind('click', {objContainer: objContainer, objTrSub: objTrSub},function(event){
					var objTrSub = $(event.data.objTrSub); 
					countDatosReintegro = $(event.data.objContainer).find(">tr").length;
					if(countDatosReintegro > 2){
						objTrSub.remove();
					} else {
						// Limpiar la información de los datos de reintegro de la fila actual
						objTrSub.find(
								'select[name="'+bancosReintegrosName+'"]'
						).val(0);
						
						objTrSub.find(
								'input[name="'+referenciasReintegrosName+'"]'
								+ ', input[name="'+fechasReintegrosName+'"]'
								+ ', input[name="'+montosReintegrosName+'"]'
						).val("");
					}
					calcularMontoReintegradoResponsable(objTable);
					calcularMontoDiferenciaResponsable(objTable);
				});
			}

			function agregarFilaPartidasMontos()
			{
				var params = arguments[0] || null;
				var objTbodySub = params != null ? (params.objTbodySub || null) : null;
				var objTable = params != null ? (params.objTable || null) : null;
				var partidasName = params != null ? (params.partidasName || null) : null;
				var partidasMontosName = params != null ? (params.partidasMontosName || null) : null;
				var arrObjRendicionAvancePartida = params != null ? (params.arrObjRendicionAvancePartida || null) : null;

				if(params == null) return;
				if(objTbodySub == null) return;
				if(objTable == null) return;
				if(partidasName == null) return;
				if(partidasMontosName == null) return;

				// tr de id's de partidas y valores de monto
				var objTrSub = document.createElement('tr');
				objTbodySub.appendChild(objTrSub);

				// Contruir la partida/monto
				agregarPartidaMonto({
					objRendicionAvancePartida: arrObjRendicionAvancePartida != null ? arrObjRendicionAvancePartida[0] : null,
					objTable: objTable,
					objTrSub: objTrSub,
					partidasName: partidasName,
					partidasMontosName: partidasMontosName
				});

				// Contruir la partida/monto
				agregarPartidaMonto({
					objRendicionAvancePartida: arrObjRendicionAvancePartida != null ? arrObjRendicionAvancePartida[1] : null,
					objTable: objTable,
					objTrSub: objTrSub,
					partidasName: partidasName,
					partidasMontosName: partidasMontosName
				});

				// Td de Div de boton eliminar tupla partida/monto
				var objTdSub = document.createElement('td');
				objTdSub.setAttribute('class', 'botonesPartidasYMontos');
				objTrSub.appendChild(objTdSub);

				// Div de boton eliminar tupla partida/monto
				var objDiv = document.createElement('div');
				objDiv.setAttribute('class', 'botonEliminarPartidasYMontos');
				objDiv.setAttribute('title', 'Eliminar esta fila de partidas/montos.');
				objTdSub.appendChild(objDiv);

				objDiv = $(objDiv);
				objDiv.unbind('click');
				objDiv.bind('click', {objContainer: objTbodySub, objTrSub: objTrSub},function(event){
					var objTrSub = $(event.data.objTrSub); 
					countDatosReintegro = $(event.data.objContainer).find(">tr").length;
					if(countDatosReintegro > 2){
						objTrSub.remove();
					} else {
						// Limpiar la información de las tuplas partida/monto de la fila actual
						objTrSub.find(
								'input[name="'+partidasName+'"]'
								+ ', input[name="'+partidasMontosName+'"]'
						).val("");
					}
					calcularMontoGastadoResponsable(objTable);
					calcularReintegroResponsable(objTable);
					calcularMontoDiferenciaResponsable(objTable);
				});
			}

			function agregarPartidaMonto()
			{
				var params = arguments[0] || null;
				var objRendicionAvancePartida = params != null ? (params.objRendicionAvancePartida || null) : null;
				var objTable = params != null ? (params.objTable || null) : null;
				var objTrSub = params != null ? (params.objTrSub || null) : null;
				var partidasName = params != null ? (params.partidasName || null) : null;
				var partidasMontosName = params != null ? (params.partidasMontosName || null) : null;

				if(objTable == null) return;
				if(objTrSub == null) return;
				if(partidasName == null) return;
				if(partidasMontosName == null) return;
				
				// td de id de la partida
				var objTdSub = document.createElement('td');
				objTrSub.appendChild(objTdSub);

				var idPartida = "";
				var montoPartida = "";

				if(objRendicionAvancePartida != null && objRendicionAvancePartida.partida != null){
					idPartida = objRendicionAvancePartida.partida.id;
					montoPartida = objRendicionAvancePartida.monto;
				}

				var objInput = document.createElement('input');
				objInput.setAttribute('class', 'normalNegro');
				objInput.setAttribute('name', partidasName);
				objInput.setAttribute('value', idPartida);
				setAutocompletePartidas($(objInput));
				objTdSub.appendChild(objInput);

				// td de monto de partida
				var objTdSub = document.createElement('td');
				objTrSub.appendChild(objTdSub);

				var objInput = document.createElement('input');
				objInput.setAttribute('class', 'normalNegro');
				objInput.setAttribute('name', partidasMontosName);
				objInput.setAttribute('value', montoPartida);
				objInput.setAttribute('autocomplete', 'off');
				objTdSub.appendChild(objInput);

				objInput = $(objInput);
				objInput.unbind('keyup');
				objInput.bind('keyup', {objInput: objInput[0]}, function(event)
				{
					validarDecimal(event.data.objInput);
					calcularMontoGastadoResponsable(objTable);
					calcularReintegroResponsable(objTable);
					calcularMontoDiferenciaResponsable(objTable);
				});
			}

			function setAutocompletePartidas(objInputPartidas)
			{
				// Configurar autocomplete de partidas
				objInputPartidas.autocomplete({
					source: function(request, response){
						var seleccionados = new Array();
						/*
						$('#ulListaInfocentro input[type="hidden"][name="infocentros\[\]"]').each(function(index, objInput){
							seleccionados[index] = objInput.value;
						});
						*/
						$.ajax({
							url: "../partida.php",
							dataType: "json",
							data: {
								accion: "Search",
								key: request.term,
								tipoRespuesta: 'json',
								anno: A_oPresupuesto/*,
								seleccionados: seleccionados*/
							},
							success: function(json){
								var index = 0;
								var items = new Array();

								$.each(json.listaPartida, function(idPartida, objPartida){

									var label = idPartida + " : " + objPartida.nombre;
									items[index++] = {
											id: idPartida,
											label: label,
											value: idPartida
									};
								});
								response(items);
							}
						});
					},
					minLength: 1,
					select: function(event, ui)
					{
						objInputPartidas.val(ui.item.value);
						return false;
					}
				});
			}

			function calcularMontoGastadoResponsable(objTableResponsable)
			{
				var objTableResponsable = $(objTableResponsable);
				var objListaMontosPartidas = objTableResponsable.find('input[name^="partidasMontos"]');
				var objMontoGastado = objTableResponsable.find(".montoGastado");

				var montoGastado = 0.0;

				objListaMontosPartidas.each(function(index, objMontoPartida){
					var montoPartida = parseFloat(objMontoPartida.value);
					montoPartida = (isNaN(montoPartida)) ? 0 : montoPartida;
					montoGastado += montoPartida;
				});
				
				objMontoGastado.empty();
				objMontoGastado.append(montoGastado.toFixed(2).replace(".", ","));
			}

			function calcularReintegroResponsable(objTableResponsable)
			{
				var objTableResponsable = $(objTableResponsable);
				var objMontoAnticipo = objTableResponsable.find(".montoAnticipo");
				var objMontoGastado = objTableResponsable.find(".montoGastado");
				var objMontoReintegro = objTableResponsable.find(".montoReintegro");
				var objEtiquetaReintegro = objTableResponsable.find(".etiquetaMontoReintegro");
				var textoEtiquetaReintegro = "Reintegro";

				var montoAnticipo = parseFloat(0);
				var montoGastado = parseFloat(0);
				var montoReintegro = parseFloat(0);

				if(objMontoAnticipo.length > 0)
				{
					var montoAnticipo = parseFloat($(objMontoAnticipo).text().replace(",", "."));
					montoAnticipo = (isNaN(montoAnticipo)) ? 0 : montoAnticipo;
				}
				
				if(objMontoGastado.length > 0)
				{
					var montoGastado = parseFloat($(objMontoGastado).text().replace(",", "."));
					montoGastado = (isNaN(montoGastado)) ? 0 : montoGastado;
				}

				montoReintegro = montoAnticipo - montoGastado;

				if(montoAnticipo < montoGastado - 0.000001)
				{
					textoEtiquetaReintegro = "Asumido por el trabajador";
					montoReintegro *= -1;
				}
				else if(montoAnticipo > montoGastado + 0.000001)
				{
					textoEtiquetaReintegro = "Reintegro a la Fundaci"+oACUTE+"n";
				}

				objEtiquetaReintegro.empty();
				objEtiquetaReintegro.append(document.createTextNode(textoEtiquetaReintegro + ":" +NON_BREAKING_SPACE));				
				objMontoReintegro.empty();
				objMontoReintegro.append(montoReintegro.toFixed(2).replace(".", ","));
			}

			function calcularTotalResponsable()
			{
				objTdResponsables = $('#tdResponsables');
				objTotalGastado = $('#totalGastado');
				objListaMontosSubtotales = objTdResponsables.find('.montoGastado');

				var totalGastado = 0.0;
				objListaMontosSubtotales.each(function(index, objMontoGastado){
					var montoGastado = parseFloat($(objMontoGastado).text().replace(",", "."));
					montoGastado = (isNaN(montoGastado)) ? 0 : montoGastado;
					totalGastado += montoGastado;
				});

				objTotalGastado.empty();
				objTotalGastado.append(totalGastado.toFixed(2).replace(".", ","));
			}

			function calcularMontoReintegradoResponsable(objTableResponsable)
			{
				var objTableResponsable = $(objTableResponsable);
				var objListaMontosReintegros = objTableResponsable.find('input[name^="montosReintegros"]');
				var objMontoReintegrado = objTableResponsable.find(".montoReintegrado");

				var montoReintegrado = 0.0;

				objListaMontosReintegros.each(function(index, objMontoReintegro){
					var montoReintegro = parseFloat(objMontoReintegro.value);
					montoReintegro = (isNaN(montoReintegro)) ? 0 : montoReintegro;
					montoReintegrado += montoReintegro;
				});
				
				objMontoReintegrado.empty();
				objMontoReintegrado.append(montoReintegrado.toFixed(2).replace(".", ","));
			}

			
			function calcularMontoDiferenciaResponsable(objTableResponsable)
			{
				var objTableResponsable = $(objTableResponsable);
				
				var objMontoReintegro = objTableResponsable.find(".montoReintegro");
				var objMontoReintegrado = objTableResponsable.find(".montoReintegrado");
				var objMontoDiferencia = objTableResponsable.find(".montoDiferencia");

				var montoReintegro = parseFloat(0);
				var montoReintegrado = parseFloat(0);
				var montoDiferencia = parseFloat(0);

				if(objMontoReintegro.length > 0)
				{
					montoReintegro = parseFloat($(objMontoReintegro).text().replace(",", "."));
					montoReintegro = (isNaN(montoReintegro)) ? 0 : montoReintegro;
				}
				
				if(objMontoReintegrado.length > 0)
				{
					var montoReintegrado = parseFloat($(objMontoReintegrado).text().replace(",", "."));
					montoReintegrado = (isNaN(montoReintegrado)) ? 0 : montoReintegrado;
				}

				montoDiferencia = montoReintegro - montoReintegrado;

				objMontoDiferencia.empty();
				objMontoDiferencia.append(montoDiferencia.toFixed(2).replace(".", ","));
			}

			function desativarBotones()
			{
				$('#btnUnaAccion').attr('disabled', 'disabled');
				$('#btnDosAcciones').attr('disabled', 'disabled');
			}
			
			function accionGuardar()
			{
				if (confirm(pACUTE+"Est"+aACUTE+" seguro que desea registrar esta rendici"+oACUTE+"n de avance? ")) 
				{
					desativarBotones();
					$('#rendicionAvanceForm').submit();
				}
			}

			function accionGuardarYEnviar(objAccion)
			{
				if (confirm(pACUTE+"Est"+aACUTE+" seguro que desea registrar y enviar esta rendici"+oACUTE+"n de avance? ")) 
				{
					desativarBotones();
					$('#' + objAccion).val('GuardarYEnviar');
					$('#rendicionAvanceForm').submit();
				}
			}
			
			function accionActualizar()
			{
				if (confirm(pACUTE+"Est"+aACUTE+" seguro que desea modificar esta rendici"+oACUTE+"n de avance? ")) 
				{
					desativarBotones();
					$('#rendicionAvanceForm').submit();
				}
			}

			function accionActualizarYEnviar(objAccion)
			{
				if (confirm(pACUTE+"Est"+aACUTE+" seguro que desea modificar y enviar esta rendici"+oACUTE+"n de avance? ")) 
				{
					desativarBotones();
					$('#' + objAccion).val('ActualizarYEnviar');
					$('#rendicionAvanceForm').submit();
				}
			}
		</script>
	</head>
	<body class="normal" onload="onLoad();">
		<table align="center">
			<tr>
				<td><?php include(SAFI_VISTA_PATH . '/mensajes.php');?></td>
			</tr>
			<?php
			if ($form->GetTipoOperacion() == NuevaRendicionAvanceForm::TIPO_OPERACION_INSERTAR){
			?>
			<tr>
				<td>
					<form name="avanceBuscarForm" id="avanceBuscarForm" method="post" action="rendicion.php">
						<input type="hidden" name="accion" value="buscarAvance">
						<table cellpadding="0" cellspacing="0" width="640" align="center"
							background="../../imagenes/fondo_tabla.gif" class="tablaalertas"
						>
							<tr> 
			    				<td height="21" colspan="2" class="normalNegroNegrita header" align="left">
			    					Seleccionar avance
			    				</td>
							</tr>
			  				<tr>
								<td height="10" colspan="2"></td>
							</tr>
			  				<tr>
			  					<td class="normalNegrita">C&oacute;digo del avance</td>
			  					<td>
			  						<input
			  							<?php echo "autocomplete=\"off\"" ?>
			  							type="text"
			  							id="idAvanceBuscado"
			  							name="idAvanceBuscado"
			  							class="normalNegro"
			  							value="<?php echo $form->GetIdAvanceBuscado() ?>"
			  						>
			  					</td>
			  				</tr>
						</table>
						<br/>
						<div align="center">
							<input type="submit" value="Buscar" class="normalNegro">
						</div>
					</form>
				</td>
			</tr>
			<?php
			}
			if(
				!empty($avance) && $avance instanceof EntidadAvance
				& !empty($rendicion) && $rendicion instanceof EntidadRendicionAvance
			){
			?>
			<tr><td>
				<form
					name="rendicionAvanceForm"
					id="rendicionAvanceForm"
					method="post"
					action="rendicion.php"
					enctype="multipart/form-data"
				>
					<input
						id="hiddenAccion"
						type="hidden"
						name="accion"
						value="<?php 
							if($form->GetTipoOperacion() == NuevaRendicionAvanceForm::TIPO_OPERACION_INSERTAR){
								echo '
						Guardar
								';
							} else if ($form->GetTipoOperacion() == NuevaRendicionAvanceForm::TIPO_OPERACION_MODIFICAR){
								echo '
						Actualizar
								';
							}
						?>"
					>
					<input
						type="hidden"
						name="idAvance"
						value="<?php echo $avance->GetId() ?>"
					>
					<?php
						if ($form->GetTipoOperacion() == NuevaRendicionAvanceForm::TIPO_OPERACION_MODIFICAR){
							echo '
					<input
						type="hidden"
						name="idRendicion"
						value="'.$rendicion->GetId().'"
					>
							';
						}
					?>
					<table align="center">
						<tr>
							<td><table
								align="center" background="../../imagenes/fondo_tabla.gif" class="tablaalertas content" width="800px;"
								cellpadding="0" cellspacing="0"
								>
								<tr>
									<td colspan="2" class="header normalNegroNegrita documentTitle">
										.: Rendici&oacute;n de avance <?php
										if ($form->GetTipoOperacion() == NuevaRendicionAvanceForm::TIPO_OPERACION_MODIFICAR)
										{
											echo $rendicion->GetId()." ";
										}
									?>:.</td>
								</tr>
								<!-- 
								<tr>
									<td class="normalNegrita">Fecha de registro</td>
									<td class="normalNegro">
										<?php //echo $rendicion->GetFechaRegistro() ?>
									</td>
								</tr>
								 -->
								<tr>
									<td class="normalNegrita">Fecha de la rendici&oacute;n:</td>
									<td class="normalNegro">
										<input
											type="text"
											size="10"
											id="fechaRendicion"
											name="fechaRendicion"
											class="dateparse"
											readonly="readonly"
											value="<?php
												$fecha = explode(' ', $rendicion->GetFechaRendicion());
												echo $fecha[0];
											?>"
										/>
										<!-- 
										<a 
											href="javascript:void(0);" 
											onclick="g_Calendar.show(event, 'fechaRendicion');" 
											title="Show popup calendar"
										><img 
												src="../../js/lib/calendarPopup/img/calendar.gif" 
												class="cp_img" 
												alt="Open popup calendar"
										/></a>
										 -->
									</td>
								</tr>
								<tr>
									<td class="normalNegrita">C&oacute;digo del avance:</td>
									<td class="normalNegro"><?php echo $avance->GetId() ?></td>
								</tr>
								<tr>
									<td class="normalNegrita">Fecha del avance:</td>
									<td class="normalNegro"><?php echo $avance->GetFechaAvance() ?></td>
								</tr>
								<tr>
									<td colspan="2" class="header normalNegroNegrita">Proyecto/Acci&oacute;n centralizada</td>
								</tr>
								<tr>
									<td><span class="normalNegrita"><?php 
										if($avance->GetProyecto() != null){
											echo "Proyecto:";
										} else if($avance->GetAccionCentralizada() != null){
											echo "Acci&oacute;n Centralizada:";
										}
									?></span></td>
									<td><?php
										if($avance->GetProyecto() != null){
											echo $avance->GetProyecto()->GetNombre();
										} else if($avance->GetAccionCentralizada() != null){
											echo $avance->GetAccionCentralizada()->GetNombre();
										}
									?></td>
								</tr>
								<tr>
									<td><span class="normalNegrita">Acci&oacute;n espec&iacute;fica: </span></td>
									<td><?php
										if( $avance->GetProyectoEspecifica() != null){
											$especifica = $avance->GetProyectoEspecifica(); 
											echo '(' .$especifica->GetCentroGestor().'/'.$especifica->GetCentroCosto().') '.$especifica->GetNombre();	
										} else if($avance->GetAccionCentralizadaEspecifica() != null){
											$especifica = $avance->GetAccionCentralizadaEspecifica();
											echo '(' .$especifica->GetCentroGestor().'/'.$especifica->GetCentroCosto().') '.$especifica->GetNombre();
										}
									?></td>
								</tr>
								<tr>
									<td colspan="2" class="header normalNegroNegrita">Datos de la actividad</td>
								</tr>
								<tr>
									<td><span class="normalNegrita">Fecha inicio(*):</span></td>
									<td>
										<?php echo $avance->GetFechaInicioActividad() ?>
										&nbsp;
										&nbsp;
										&nbsp;
										<input
											type="text"
											size="10"
											id="txt_inicio"
											name="fechaInicioActividad"
											class="dateparse"
											readonly="readonly"
											onfocus="javascript: compararFechasYBorrarById('txt_inicio', 'hid_hasta_itin', 'txt_inicio');"
											value="<?php echo $rendicion->GetFechaInicioActividad() ?>"
										/>
										<a 
											href="javascript:void(0);" 
											onclick="g_Calendar.show(event, 'txt_inicio');" 
											title="Show popup calendar"
										><img 
												src="../../js/lib/calendarPopup/img/calendar.gif" 
												class="cp_img" 
												alt="Open popup calendar"
										/></a>
									</td>
								</tr>
								<tr>
									<td><span class="normalNegrita">Fecha fin(*):</span></td>
									<td>
										<?php echo $avance->GetFechaFinActividad()?>
										&nbsp;
										&nbsp;
										&nbsp;
										<input
											type="text"
											size="10"
											id=hid_hasta_itin
											name="fechaFinActividad"
											class="dateparse"
											readonly="readonly"
											onfocus="javascript: compararFechasYBorrarById('txt_inicio', 'hid_hasta_itin', 'hid_hasta_itin');"
											value="<?php echo $rendicion->GetFechaFinActividad() ?>"
										/>
										<a 
											href="javascript:void(0);" 
											onclick="g_Calendar.show(event, 'hid_hasta_itin');" 
											title="Show popup calendar"
										><img 
												src="../../js/lib/calendarPopup/img/calendar.gif" 
												class="cp_img" 
												alt="Open popup calendar"
										/></a>		
									</td>
								</tr>
								<tr>
									<td class="normalNegrita">Objetivos del avance(*):</td>
									<td><?php echo $avance->GetObjetivos() ?></td>
								</tr>
								<tr>
									<td class="normalNegrita">Descripci&oacute;n del avance(*):</td>
									<td><?php echo $avance->GetDescripcion() ?></td>
								</tr>
								<tr>
									<td class="normalNegrita mensajeError">Nota:</td>
									<td class="mensajeError">
										- Los objetivos y la descripci&oacute;n del avance se colocan como ayuda, pero no deben ser 
											iguales a los logros alcanzados ni a la descripci&oacute;n de la actividad en la
											rendici&oacute;n del avance, respectivamente. Por favor no realizar la operaci&oacute;n de copiado
											y pegado sobre estos datos.
									</td>
								</tr>
								<tr>
									<td class="normalNegrita">Logros alcanzados(*):</td>
									<td>
										<textarea
											name="objetivos"
											class="normalNegro"
											rows="2"
											style="width: 500px;"
										><?php echo $rendicion->GetObjetivos() ?></textarea>
									</td>
								</tr>
								<tr>
									<td class="normalNegrita">Descripci&oacute;n de la actividad:</td>
									<td>
										<textarea
											name="descripcion"
											class="normalNegro"
											rows="2"
											style="width: 500px;"
										><?php echo $rendicion->GetDescripcion() ?></textarea>
									</td>
								</tr>
								<tr>
									<td class="normalNegrita">Nro. participantes(*):</td>
									<td>
										<input
											id="nroParticipantes"
											type="text"
											name="nroParticipantes"
											onkeyup="validarNumero(this, true);"
											class="normalNegro"
											style="width: 500px;"
											value="<?php echo $rendicion->GetNroParticipantes() ?>"
										>
									</td>
								</tr>
								<tr>
									<td colspan="2" class="header normalNegroNegrita">Responsables</td>
								</tr>
								<tr>
									<td colspan="2"><hr/></td>
								</tr>
								<tr>
									<td id="tdResponsables" colspan="2"></td>
								</tr>
								<!--
								<tr>
									<td colspan="2"><table class="wrapperResponsablesAvance">
										<tr>
											<td class="normalNegrita total" style="text-align: right;">
												Total gastado: <span id="totalGastado" class="totalGastado">0,0</span>
											</td>
										</tr>
									</table></td>
								</tr>
								-->
								<tr>
									<td colspan="2">
										<select id="selectResponsablesDisponibles">
											<option value="0">Todos</option>
											<?php
											if(is_array($responsablesDisponibles)){
												foreach ($responsablesDisponibles as $responsableDisponible)
												{
													$responsableRendicionAvance = $responsableDisponible->GetResponsableRendicionAvance();
													$idResponsable = $responsableRendicionAvance->GetIdResponsableAvance(); 
													$cedulaResponsable = "";
													$nombreResponsable = "";
													
													if(($empleado=$responsableRendicionAvance->GetEmpleado()) != null)
													{
														$cedulaResponsable = $empleado->GetId();
														$nombreResponsable =  $empleado->GetNombres() ." ". $empleado->GetApellidos();
															
													}
													else if(($beneficiario=$responsableRendicionAvance->GetBeneficiario()) != null)
													{
														$cedulaResponsable = $beneficiario->GetId();
														$nombreResponsable = 
															$beneficiario->GetNombres() ." ". $beneficiario->GetApellidos();
													}
													
												echo "
											<option value=\"".$idResponsable."\">
												" . str_replace(" ", "&nbsp;&nbsp;",str_pad($cedulaResponsable, 8, " ", STR_PAD_LEFT))
												. " - " .mb_strtoupper($nombreResponsable, "ISO-8859-1") . "
											</option>
													";
												}
											}
											?>
										</select>
										<input
											type="button"
											value="Agregar responsable"
											class="normalNegro"
											onclick="javascript:accionAgregarResponsable('selectResponsablesDisponibles');"
										>
									</td>
								</tr>
								<tr>
									<td colspan="2" class="header normalNegroNegrita">Observaciones</td>
								</tr>
								<tr>
									<td colspan="2">
										<textarea
											name="observaciones"
											class="normalNegro"
											rows="3"
											style="width: 785px;"
										><?php echo $rendicion->GetObservaciones() ?></textarea>
									</td>
								</tr>
							</table></td>
						</tr>
						<tr>
							<td class="normal"><div class="ptotal"><span class="peq_naranja">(*)</span> Campo obligatorio</div></td>
						</tr>
					</table>
					<br/>
					<div id="divAcciones" style="text-align: center;">
						<input
							id="btnUnaAccion"
							type="button"
							class="normalNegro"
							<?php 
								if($form->GetTipoOperacion() == NuevaRendicionAvanceForm::TIPO_OPERACION_INSERTAR){
									echo '
							value="Registrar"
							onclick="accionGuardar();"
									';
								} else if ($form->GetTipoOperacion() == NuevaRendicionAvanceForm::TIPO_OPERACION_MODIFICAR){
									echo '
							value="Modificar"
							onclick="accionActualizar();"
									';
								}
							?>
						>
						<?php if($form->GetTipoOperacion() == NuevaRendicionAvanceForm::TIPO_OPERACION_INSERTAR){ ?>
						<input
							id="btnDosAcciones"
							type="button"
							class="normalNegro"
							<?php 
								if($form->GetTipoOperacion() == NuevaRendicionAvanceForm::TIPO_OPERACION_INSERTAR){
									echo '
							value="Registrar y enviar"
							onclick="accionGuardarYEnviar(\'hiddenAccion\');"
									';
								} else if ($form->GetTipoOperacion() == NuevaRendicionAvanceForm::TIPO_OPERACION_MODIFICAR){
									echo '
							value="Modificar y enviar"
							onclick="accionActualizarYEnviar(\'hiddenAccion\');"
									';
								}
							?>
						>
						<?php } ?>
					</div>
				</form>
			</td></tr>
			<?php
			}
			?>
			
		</table>
	</body>
</html>