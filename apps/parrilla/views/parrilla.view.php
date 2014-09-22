<?php defined('_EXE') or die('Restricted access'); ?>

<form class="form-horizontal" role="form">
    <div class="form-group">
        <label for="fecha" class="col-sm-2 control-label">
            Fecha
        </label>
        <div class="col-sm-3">
            <input type="text" name="fecha" class="form-control" id="fecha" value="<?=date("d-m-Y");?>" placeholder="Fecha">
        </div>
        <div class="col-sm-3">
            <input type="text" name="entradaId" class="form-control select2entradas">
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
    </section>
</div>

<script type="text/javascript" language="javascript" class="init">
    var sum = 0;
    var table;

    $(document).ready(function () {
        table = tableInit();
        $("#fecha").datepicker({ dateFormat: "dd-mm-yy" });
    });

    //Delte row
    $(document).on('click', '.delete', function (e) {
        id = $(this).closest('tr').attr("id");
        $.ajax('<?=Url::site("parrilla/json");?>?date=' + $("#fecha").val() + '&action=delete&id=' + id);
        tableInit();
    });

    //Date change
    $(document).on('change', '#fecha', function (e) {
        tableInit();
    });

    //New row
    $(document).keypress(function (e) {
        if (e.which == 13) {
            //table.fnClearTable();
            //Peticion get del tipo accion=new

            table.row.add( [
                    counter +'.1',
                    counter +'.2',
                    counter +'.3',
                    counter +'.4',
                    counter +'.5'
                ] ).draw();
        }
    });

    //Create row
    $(document).on('change', '.select2entradas', function (e) {
        create($(this).val());
        $(this).select2('data', null);
    });

    function create(entradaId)
    {
        $.ajax('<?=Url::site("parrilla/json");?>?date=' + $("#fecha").val() + '&action=new&entradaId=' + entradaId);
        tableInit();
    }

    function tableInit()
    {
        url = '<?=Url::site("parrilla/json");?>?date=' + $("#fecha").val();
        table = $('#example').DataTable({
            "paging":   false,
            "bPaginate": false,
            "sAjaxSource": url,
            "bFilter": false,
            "bServerSide": true,
            "bDestroy": true,
            "bRetrieve": true,
            "fnRowCallback": function (nRow, aData, iDisplayIndex) {
                nRow.setAttribute('id', aData.id);  //Initialize row id for every row
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

    $(".select2entradas").select2({
        placeholder: "Buscar entrada",
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

</script>
