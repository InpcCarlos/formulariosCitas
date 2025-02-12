/* 
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Other/javascript.js to edit this template
 */


//***********************************************INICIO funciones generales*****************************************************************

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
    $.ajax({
        type: "get", 
        url: "obtenerhorariosdisponibles" , 
        data: { valorid:valor_campo },
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
function updateTotal(campo) {
    let total = 0;
    document.querySelectorAll('.precio-total').forEach(function(input) {
        total += parseFloat(input.value) || 0;
    });
    document.getElementById('formdatosfacturacion-form_dtotal').value = total.toFixed(2);
}
function calcularPrecio(campo){
    var valor_campo = campo.value;
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