/* 
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Other/javascript.js to edit this template
 */


//***********************************************INICIO funciones generales*****************************************************************
$(document).ready(function() {
    // Ocultar inicialmente la fila del registro de operadora
    $('#registro_operadora_row').hide();

    // Escuchar cambios en el campo 'form_esoperadora'
    $('#formdatosfacturacion-form_esoperadora').change(function() {
        var valorSeleccionado = $(this).val();        
        if (valorSeleccionado == 1) {
            $('#registro_operadora_row').show();
            $('#formdatosfacturacion-form_registro_operadora').prop('required', true);
        } else {
            $('#registro_operadora_row').hide();
            $('#formdatosfacturacion-form_registro_operadora').prop('required', false);
        }
    });
});
$(document).on('click', '.change-status-link', function () {
    var url = $(this).data('url');
    $.get(url, function (data) {
        $('#modalContent').html(data);
    });
});

function poblarTarifario(campo) {
    var valor_campo = campo.value;
    var id_campo = campo.id;
    var separa_campo = id_campo.split('-');
    var item_ix = separa_campo[1];
    var campo_precio = "#formdatosvisitante-" + item_ix + "-form_dvprecio";
    var campo_precio_tot = "#formdatosvisitante-" + item_ix + "-form_dvprecio_total";
    console.log(campo_precio);
    
    $.ajax({
        type: "get", 
        url: "obtenerpreciotarifa" , 
        data: { valorid:valor_campo },
        success: function(result) {
            if (result && result.precio !== undefined) {
                $(campo_precio).val(result.precio);
                $(campo_precio_tot).val(result.precio);
                updateTotal();
            } else {
                alert("Error al obtener el precio");
            }
        },
        error: function(xhr, status, error) {
            console.log(xhr.responseText);
            alert("Error en la solicitud AJAX");
        }
    });
    
};
function poblarHorarios(campo)
{    
    var valor_campo = campo.value;    
    var val_opera= $('#formdatosfacturacion-form_esoperadora').val();
    if(val_opera ==""){
        alert("Debe indicar si es o no operadora");
        $(campo).val("");
    }else{    
        $.ajax({
            type: "get", 
            url: "obtenerhorariosdisponibles" , 
            data: { valorid:valor_campo,
                val_opera:val_opera
            },
            success: function(result) {            
                if (result) {
                    $('#formdatosfacturacion-form_dhora_visita').html(result);
                } else {
                    alert("Error al recuperar horarios");
                }
            },
            error: function(xhr, status, error) {
                console.log(xhr.responseText);
                alert("Error en la solicitud AJAX");
            }
        });
    }
}
function poblarNacionalidad(campo)
{    
    var valor_campo = campo.value;
    var id_campo = campo.id;
    var separa_campo = id_campo.split('-');
    var item_ix = separa_campo[1];
    var campo_nacionalidad = "#formdatosvisitante-" + item_ix + "-form_dvnacionalidad";
    if(valor_campo==1){
        $(campo_nacionalidad).val(62);//es valor de ecuatoriano
    }
    else{
        $(campo_nacionalidad).val("");
    }
        
}
function updateTotal(campo) {
    let total = 0;
    document.querySelectorAll('.precio-total').forEach(function(input) {
        total += parseFloat(input.value) || 0;
    });
    document.getElementById('formdatosfacturacion-form_dtotal').value = total.toFixed(2);
}
function calcularPrecio(campo){
    var val_horario = $('#formdatosfacturacion-form_dhora_visita').val();
    updateCantidad();
    if(val_horario=="")
    {
        alert("Debe seleccionar un horario");
        $(campo).val("");
    }
    else{
        var valor_campo = campo.value;
        var cant_total = $("#formdatosfacturacion-form_dtcantidad").val();
        var selectElement = document.getElementById('formdatosfacturacion-form_dhora_visita');
        // Obtener la opción seleccionada
        var selectedOption = selectElement.options[selectElement.selectedIndex];    
        // Obtener el texto que se muestra en la opción seleccionada
        var displayedText = selectedOption.text;
        var separa_campo_sele = displayedText.split('-');
        var item_tot = separa_campo_sele[2];
        var separa_campo_sele2 = item_tot.split(' ');
        var item_tot2 = parseInt(separa_campo_sele2[1]);
        console.log("item"+item_tot2 + "cantidad"+valor_campo);
        if(cant_total>parseInt(item_tot2)){
            alert("Máximo "+item_tot2+" boletos");
            $(campo).val("");
        }
        else
        {
        var id_campo = campo.id;
        var separa_campo = id_campo.split('-');
        var item_ix = separa_campo[1];
        var campo_precio = "#formdatosvisitante-" + item_ix + "-form_dvprecio";
        var valor_precio = $(campo_precio).val();
        var campo_precio_tot = "#formdatosvisitante-" + item_ix + "-form_dvprecio_total";
        var valor_precio = valor_precio * valor_campo;
        $(campo_precio_tot).val(valor_precio);
        updateTotal();    
        }    
    }
}
function updateCantidad(campo) {
    let total = 0;
    document.querySelectorAll('.cantidad-total').forEach(function(input) {
        total += parseFloat(input.value) || 0;
    });
    document.getElementById('formdatosfacturacion-form_dtcantidad').value = total;
}