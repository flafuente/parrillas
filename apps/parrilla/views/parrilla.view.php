<?php defined('_EXE') or die('Restricted access'); ?>

<?php
Toolbar::addTitle("Parrilla", "glyphicon-star");
//Export button
Toolbar::addButton(
    array(
        "title" => "Exportar",
        "app" => "parrilla",
        "action" => "export",
        "class" => "primary",
        "spanClass" => "share-alt",
        "noAjax" => true,
    )
);
Toolbar::render();
?>

<form method="post" name="mainForm" id="mainForm" action="<?=Url::site();?>" class="form-horizontal" role="form" autocomplete="off">
    <input type="hidden" name="app" id="app" value="parrilla">
    <input type="hidden" name="action" id="action" value="export">

    <div class="form-group">
        <label for="fecha" class="col-sm-2 control-label">
            Fecha
        </label>
        <div class="col-sm-3">
            <input type="text" name="fecha" class="form-control" id="fecha" value="<?=date("d-m-Y");?>" placeholder="Fecha">
        </div>
        <div class="col-sm-3">
            <input type="text" name="hour" class="form-control hourMask" id="hour" value="07:00" placeholder="Hora">
        </div>
    </div>
</div>

<div class="container">
    <section>
        <div id="demo">
        <!-- Fecha,Hora,Duracion,House Number,Tipo,Titulo,TC IN, Logo, Segmento -->
        <table id="example" class="display" cellspacing="0" width="100%">
            <thead>
                <tr >
                    <th>Pos</th>
                    <th>Fecha</th>
                    <th>Hora</th>
                    <th>Duracion</th>
                    <th>House Number</th>
                    <th>Tipo</th>
                    <th>Titulo</th>
                    <th>TC IN</th>
                    <th>Logo</th>
                    <th>Segmento</th>
                    <th></th>
                </tr>
            </thead>

            <tfoot>
                <tr>
                    <th>Pos</th>
                    <th>Fecha</th>
                    <th>Hora</th>
                    <th>Duracion</th>
                    <th>House Number</th>
                    <th>Tipo</th>
                    <th>Titulo</th>
                    <th>TC IN</th>
                    <th>Logo</th>
                    <th>Segmento</th>
                    <th></th>
                </tr>
            </tfoot>

            <tbody>

            </tbody>
        </table>

        <br>

        <form class="form-horizontal" role="form">
            <label for="entradaId" class="col-sm-2 control-label">
                Crear entrada
            </label>
            <div class="form-group">
                <div class="col-sm-9">
                    <input type="text" name="entradaId" id="entradaId" class="form-control select2entradas">
                </div>
            </div>
        </form>

    </section>
</div>

<script type="text/javascript" language="javascript" class="init">

    //Hour Mask
    $('input.hourMask').mask("00:00");

    var sum = 0;
    var table;
    var date = $("#fecha").val();
    var hour = $("#hour").val();
    var order = 1;
    var firstTime = true;

    /**** Delete *****/

    //Delte row
    $(document).on('click', '.delete', function (e) {
        id = $(this).closest('tr').attr("id");
        $.ajax('<?=Url::site("parrilla/json");?>?date=' + date + '&action=delete&id=' + id + '&hour=' + hour);
        tableInit();
    });

    /**** Create *****/

    //Create row
    $(document).on('change', '#entradaId', function (e) {
        create($(this).val());
        $(this).select2('data', null);
    });

    //Create row (modal)
    $(document).on('click', '#modalSave', function (e) {
        create($("#entradaIdModal").val(), order);
        $("#entradaIdModal").select2('data', null);
    });

    function create(entradaId, orden)
    {
        $.ajax('<?=Url::site("parrilla/json");?>?date=' + date + '&hour=' + hour + '&action=new&entradaId=' + entradaId + '&order=' + orden);
        tableInit();
        $('#modalEntrada').modal('hide');
    }

    //New row (modal)
    $(document).on('click', '.newModal', function (e) {
        $("#entradaIdModal").select2('data', null);
        $('#modalEntrada').modal('show');
        order = $(this).attr("data-order");
    });

    /**** Import *****/

    //Import (modal)
    $(document).on('click', '.importModal', function (e) {
        $('#modalImportar').modal('show');
        order = $(this).attr("data-order");
    });

    //Import modal date change
    $(document).on('change', '#fechaImportar', function (e) {
        $.ajax({
            url: '<?=Url::site("parrilla/preview");?>',
            data: {fecha: $("#fechaImportar").val()},
            method: 'get',
            dataType: 'json',
            success: function (data) {
                $('#importarParrilla').html(data.data.html);
            },
        })
    });

    //Import action
    $(document).on('click', '.importBtn', function (e) {

        //Get all checked values
        eventosId = $("#importarParrilla input:checkbox:checked").map(function () {
          return $(this).val();
        }).get();

        //Import
        $.ajax({
            url: '<?=Url::site("parrilla/json");?>',
            data: {
                date: date,
                hour: hour,
                action: "import",
                order: order,
                eventosId: eventosId,
            },
            method: 'get',
            dataType: 'json',
        })

        //Reload table
        tableInit();

        //Close modal
        $('#modalImportar').modal('hide');
    });

    /**** Inits *****/

    //DatePickers
    $(document).ready(function () {
        table = tableInit();
        $("#fecha").datepicker({ dateFormat: "dd-mm-yy" });
        $("#fechaImportar").datepicker({ dateFormat: "dd-mm-yy" });
    });

    //Date change
    $(document).on('change', '#fecha', function (e) {
        date = $("#fecha").val();
        tableInit();
        firstTime = true;
    });

    //Hour change
    $(document).on('change', '#hour', function (e) {
        hour = $("#hour").val();
        $.ajax('<?=Url::site("parrilla/json");?>?date=' + date + '&hour=' + hour + '&action=updateHour');
        tableInit();
    });

    //Import TR Click
    $(document).on('click', 'tr.clickable', function (e) {
        checkbox = $(this).find("input");
        checkbox.attr("checked", !checkbox.attr("checked"));
    });

    //DataTable
    function tableInit()
    {
        url = '<?=Url::site("parrilla/json");?>?date=' + date + '&hour=' + hour;
        table = $('#example').DataTable({
            "paging":   false,
            "bPaginate": false,
            "sAjaxSource": url,
            "bFilter": false,
            "bServerSide": true,
            "bDestroy": true,
            "bRetrieve": false,
            "fnRowCallback": function (nRow, aData, iDisplayIndex) {
                //First row
                if (firstTime) {
                    hour = aData[2].substr(0, 5);
                    $("#hour").val(hour);
                    firstTime = false;
                }
                nRow.setAttribute('id', aData.id);  //Initialize row id for every row
                nRow.setAttribute('style',"background-color:" + aData[11] + ";"); //Add color

            }
        });
        table.rowReordering({
            sURL: url,
            sRequestType: "GET",
            fnSuccess: function (response) {
                //console.log("test");
            }
        });

        return table;
    }

    /* Select 2 */
    $(document).ready(function () {
        //Select2 Entradas
        $(".select2entradas").select2({
            placeholder: "Crear entrada",
            minimumInputLength: 1,
            ajax: {
                url: "<?=Url::site('parrilla/entradasJs');?>",
                dataType: 'json',
                data: function (term) {
                    return {
                        q: term,
                    };
                },
                results: function (data) {
                    return {
                        results: $.map(data.data.entradas, function (item) {
                            return {
                                id: item.id,
                                text: item.nombre + " (" + item.houseNumber + ")"
                            }
                        })
                    };
                }
            },
        });
    });
</script>

<!-- Modal Entrada -->
<div class="modal fade" id="modalEntrada" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Cerrar</span></button>
                <h4 class="modal-title" id="myModalLabel">Añadir entrada</h4>
            </div>
            <div class="modal-body">
                <form class="form-horizontal" role="form">
                    <label for="entradaId" class="col-sm-8 control-label">
                        Crear entrada
                    </label>
                    <div class="form-group">
                        <div class="col-sm-8">
                            <input type="text" name="entradaId" id="entradaIdModal" class="form-control select2entradas">
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" id="modalSave">Crear</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Importar -->
<div class="modal fade" id="modalImportar" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Cerrar</span></button>
                <h4 class="modal-title" id="myModalLabel">Importar</h4>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="fecha" class="col-sm-2 control-label">
                        Fecha
                    </label>
                    <div class="col-sm-3">
                        <input type="text" name="fecha" class="form-control" id="fechaImportar" value="<?=date("d-m-Y", strtotime("now -1 day"));?>" placeholder="Fecha">
                    </div>
                </div>
				<div style='clear:both;'><button type="button" class="btn btn-primary" id="importBtn">Importar</button></div>
                <div class="form-group">
                    <div id="importarParrilla"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" id="importBtn">Importar</button>
            </div>
        </div>
    </div>
</div>
