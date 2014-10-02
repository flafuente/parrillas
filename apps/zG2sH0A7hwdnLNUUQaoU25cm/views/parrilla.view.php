<?php defined('_EXE') or die('Restricted access'); ?>

<form method="post" name="mainForm" id="mainForm" action="<?=Url::site();?>" class="form-horizontal" role="form" autocomplete="off">
    <input type="hidden" name="app" id="app" value="zG2sH0A7hwdnLNUUQaoU25cm">

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
                </tr>
            </tfoot>

            <tbody>

            </tbody>
        </table>

        <br>

    </section>
</div>

<script type="text/javascript" language="javascript" class="init">

    var table;
    var date = $("#fecha").val();

    $(document).ready(function () {
        table = tableInit();
        $("#fecha").datepicker({ dateFormat: "dd-mm-yy" });
    });

    //Date change
    $(document).on('change', '#fecha', function (e) {
        date = $("#fecha").val();
        tableInit();
    });

    function tableInit()
    {
        url = '<?=Url::site("zG2sH0A7hwdnLNUUQaoU/json");?>?date=' + date;
        table = $('#example').DataTable({
            "paging":   false,
            "bPaginate": false,
            "sAjaxSource": url,
            "bFilter": false,
            "bServerSide": true,
            "bDestroy": true,
            "bRetrieve": false,
            "fnRowCallback": function (nRow, aData, iDisplayIndex) {
                nRow.setAttribute('id', aData.id);  //Initialize row id for every row
                nRow.setAttribute('style',"background-color:" + aData[11] + ";"); //Add color

            }
        });

        return table;
    }
</script>
