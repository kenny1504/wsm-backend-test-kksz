
$(document).ready(function () {
    getdata(-1);
});


// Recupera datos segun se indique en el tipo
function getdata(tipo)
{
    var id_account=$('#fin-id-account').val();

    if (id_account=='' && tipo==1)
     return false;

    if (tipo==-1)
    $('#fin-id-account').val('');

    $.ajax({
    type: 'GET',
    url: 'index.php/get_data',
    data:({ tipo:tipo, id_account:id_account }),
    async: true,
    datatype: "json",
    complete: function (data)
        { 
        
            $('#tbodytable').empty()
            if(data.responseJSON.datos.length==0)
             $('#tbodytable').append('<tr><td>No data available for the supplied Account Id.</td><td></td><td></td><td></td><td></td><td></td></tr>');
            else
            $.each(data.responseJSON.datos, function(i, item) {
                $('#tbodytable').append('<tr><td>' + item.accountName + '</td><td>' +
                                                          item.accountId + '</td><td>' +
                                                          item.spend + '</td><td>' +
                                                          item.clicks + '</td><td>' +
                                                          item.impressions + '</td><td>' +
                                                          item.costPerClick + '</td></tr>');

              });
            
        }
    })

}