<?php defined('_EXE') or die('Restricted access'); ?>

<form class="form-horizontal" role="form">
    <div class="form-group">
        <label for="fecha" class="col-sm-2 control-label">
            Fecha
        </label>
        <div class="col-sm-3">
          <input type="text" name="fecha" class="form-control" id="fecha" value="<?=date("d-m-Y");?>" placeholder="Fecha">
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
    $(document).ready(function () {
        var sum = 0;
        var table;

        tableInit();

        $(document).keypress(function (e) {
            if (e.which == 13) {
                //table.fnClearTable();
                //Peticion get del tipo accion=new
                tableInit();
            }
        });

        //Delte
        $(document).on('click', '.delete', function (e) {
            id = $(this).closest('tr').attr("id");
            $.ajax('<?=Url::site("parrilla/json");?>?date=' + $("#fecha").val() + '&action=delete&id=' + id);
            tableInit();
        });

        //Date change
        $("#fecha").datepicker({ dateFormat: "dd-mm-yy" });
        $(document).on('change', '#fecha', function (e) {
            tableInit();
        });

    });

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
    }

</script>
