
$().ready(function() {
	
    $('.window .close').click(function (e) { 
		          //Cancel the link behavior 
		          e.preventDefault(); 
		         $('#mask, .window').hide(); 
		      });       
		       
		      //if mask is clicked 
		      $('#mask').click(function (e) { 
		    	  
		          $('#mask, .window').hide('fade',20); 
		    	  
		    
		    	
		    	  
		      });

		      
		      
		      $('.detalleOpcion').click(function(event) {

		    	  detalleOpcion(event,this);

				});

			
		
					
});     

function detalleOpcion(event, obj) {
	//oculta los dialog para solo mostrar la vista que se desea
	$('#vistaDetallePcuanta').hide();
	$('#vistaDetalleCompromiso').hide();
	$('#vistaDetallePmod').hide();
	$('#vistaDetalleErs').hide();
	$('#vistaDetalleNe').hide();
	$('#vistaDetalleEmat').hide();
	$('#vistaDetalleAmat').hide();
	$('#vistaDetalleDesi').hide();

	if (jQuery.browser.msie) {
		event.cancelBubble = true;
	} else {
		event.stopPropagation();
	}

	if ($(obj).attr('tipoDetalle') == undefined) {

		SearchDetallePcta($(obj).attr("docgId"), $(obj).attr("opcion"), $(obj).attr("idCadenaActual"));
		$('#vistaDetallePcuanta').show(100);

	}


	if ($(obj).attr('tipoDetalle') == 'compromiso') {
		
		SearchDetalleComp($(obj).attr("docgId"), $(obj).attr("opcion"));
		$('#vistaDetalleCompromiso').show(100);
	
	}
	
	if ($(obj).attr('tipoDetalle') == 'mpresupuestarias') {
		
		$('#vistaDetallePmod').show(100);
		
		SearchDetallePmod($(obj).attr("docgId"), $(obj).attr("opcion"),$(obj).attr("idCadenaActual"));

	}
	
	if ($(obj).attr('tipoDetalle') == 'respsocial') {
		
		$('#vistaDetalleErs').show(100);
		
		SearchDetalleErs($(obj).attr("docgId"), $(obj).attr("opcion"),$(obj).attr("idCadenaActual"));

	}
	if ($(obj).attr('tipoDetalle') == 'respsocialsal') {
		
		$('#vistaDetalleNe').show(100);
		
		SearchDetalleNe($(obj).attr("docgId"), $(obj).attr("opcion"),$(obj).attr("idCadenaActual"));

	}

	if ($(obj).attr('tipoDetalle') == 'materiales') {
		
		$('#vistaDetalleEmat').show(100);
		
		SearchDetalleEmat($(obj).attr("docgId"), $(obj).attr("opcion"),$(obj).attr("idCadenaActual"));

	}
	
	if ($(obj).attr('tipoDetalle') == 'disminuirmat') {
		
		$('#vistaDetalleAmat').show(100);
		
		SearchDetalleAmat($(obj).attr("docgId"), $(obj).attr("opcion"),$(obj).attr("idCadenaActual"));

	}
	
	if ($(obj).attr('tipoDetalle') == 'desincorporacion') {
		
		$('#vistaDetalleDesi').show(100);
		
		SearchDetalleDesi($(obj).attr("docgId"), $(obj).attr("opcion"),$(obj).attr("idCadenaActual"));

	}
	
	
	
	$("div.pincipalpop").hide();
	$("div.secundpop").hide();
	
	$('div.pincipalpop').show('fade', function() {
	$("div.secundpop").show('fade');
	Detalle(event, obj);
	
	

	  });

	
 };


function  Detalle(e,valor){
    
    //Cancel the link behavior 
    e.preventDefault(); 
    //Get the A tag 
    var id = $(valor).attr('href'); 
 
    //Get the screen height and width 
    var maskHeight = $(document).height(); 
    var maskWidth = $(window).width(); 
    
    
    
    if(($('#tablaBusqueda').width() != null) && (maskWidth < $('#tablaBusqueda').width())){   	  
  	  
  	  var maskWidth  = ($('#tablaBusqueda').width()+16);
    }

 
    //Set height and width to mask to fill up the whole screen 
    $('#mask').css({'width':maskWidth,'height':maskHeight}); 
     
    //transition effect       
   $('#mask').show('fade',100);          
    $('#mask').fadeTo("blind",0.8);
    //Get the window height and width 

  
    var winH = $(window).height(); 
    var winW = $(window).width(); 

           
    //Set the popup window to center 
    $(id).css('top',  winH/2-$(id).height()/2); 
    $(id).css('left', winW/2-$(id).width()/2); 
 
    //transition effect 
    $(id).fadeIn(300);   

    e.cancelBubble = true;
 
};
