<?php defined('_EXE') or die('Restricted access'); ?>

<?php
//Toolbar
if ($entrada->id) {
    $subtitle = "Editar entrada";
    $title = "Guardar";
} else {
    $subtitle = "Nueva entrada";
    $title = "Crear";
}
Toolbar::addTitle("Entradas", "glyphicon-star", $subtitle);
if ($entrada->id) {
    //Delete button
    Toolbar::addButton(
        array(
            "title" => "Eliminar",
            "app" => "entradas",
            "action" => "delete",
            "class" => "danger",
            "spanClass" => "remove",
            "confirmation" => "¿Deseas realmente eliminar esta entrada?",
            "noAjax" => true,
        )
    );
}
//Cancel button
Toolbar::addButton(
    array(
        "title" => "Cancelar",
        "link" => Url::site("entradas"),
        "class" => "primary",
        "spanClass" => "chevron-left",
    )
);
//Save button
Toolbar::addButton(
    array(
        "title" => $title,
        "app" => "entradas",
        "action" => "save",
        "class" => "success",
        "spanClass" => "ok",
    )
);
//Save & new button
Toolbar::addButton(
    array(
        "title" => $title." y nueva",
        "app" => "entradas",
        "action" => "saveNew",
        "class" => "success",
        "spanClass" => "ok",
    )
);
Toolbar::render();
?>

<form method="post" name="mainForm" id="mainForm" action="<?=Url::site();?>" class="form-horizontal ajax" role="form" autocomplete="off">
    <input type="hidden" name="app" id="app" value="entradas">
    <input type="hidden" name="action" id="action" value="save">
    <input type="hidden" name="id" value="<?=$entrada->id?>">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    Entrada
                </div>
                <div class="panel-body">
                    <?php if (!empty($tipos)) { ?>
                        <!-- Tipo -->
                        <div class="form-group">
                            <label class="col-sm-3 control-label">
                                Tipo
                            </label>
                            <div class="col-sm-8">
                                <?=HTML::select("tipoId", $tipos, $entrada->tipoId, array("id" => "tipoId", "class" => "select2"), null, array("display" => "nombre")); ?>
                            </div>
                        </div>
                    <?php } ?>
                    <!-- Nombre -->
                    <div class="form-group">
                        <label class="col-sm-3 control-label">
                            Nombre
                        </label>
                        <div class="col-sm-8">
                            <input type="text" id="nombre" name="nombre" class="form-control" value="<?=Helper::sanitize($entrada->nombre);?>">
                        </div>
                    </div>
                    <!-- House Number -->
                    <div class="form-group">
                        <label class="col-sm-3 control-label">
                            House Number
                        </label>
                        <div class="col-sm-8">
                            <input type="text" id="houseNumber" name="houseNumber" class="form-control validate" value="<?=Helper::sanitize($entrada->houseNumber);?>">
                        </div>
                    </div>
                    <!-- Segmento -->
                    <div class="form-group">
                        <label class="col-sm-3 control-label">
                            Segmento
                        </label>
                        <div class="col-sm-8">
                            <input type="hidden" name="segmento" value="0">
                            <input type="checkbox" class="switch" name="segmento" id="segmento" value="1" <?php if($entrada->segmento) echo "checked";?>>
                        </div>
                    </div>
                    <?php if (!empty($moscas1)) { ?>
                        <!-- Mosca -->
                        <div class="form-group">
                            <label class="col-sm-3 control-label">
                                Mosca
                            </label>
                            <div class="col-sm-8">
                                <?=HTML::select("moscaId", $moscas1, $entrada->moscaId, array("id" => "moscaId", "class" => "select2"), null, array("display" => "nombre")); ?>
                            </div>
                        </div>
                    <?php } ?>
                    <?php if (!empty($moscas2)) { ?>
                        <!-- Mosca2 -->
                        <div class="form-group">
                            <label class="col-sm-3 control-label">
                                Mosca 2
                            </label>
                            <div class="col-sm-8">
                                <?=HTML::select("moscaId2", $moscas2, $entrada->moscaId, array("id" => "moscaId", "class" => "select2"), null, array("display" => "nombre")); ?>
                            </div>
                        </div>
                    <?php } ?>
                    <!-- TC IN -->
                    <div class="form-group">
                        <label class="col-sm-3 control-label">
                            TC IN
                        </label>
                        <div class="col-sm-8">
                            <input type="text" id="tcIn" name="tcIn" class="form-control dateMask" value="<?=Helper::sanitize($entrada->tcIn);?>" placeholder="HH:MM:SS:FR">
                        </div>
                    </div>
                    <!-- TC OUT -->
                    <div class="form-group">
                        <label class="col-sm-3 control-label">
                            TC OUT
                        </label>
                        <div class="col-sm-8">
                            <input type="text" id="tcOut" name="tcOut" class="form-control dateMask" value="<?=Helper::sanitize($entrada->tcOut);?>" placeholder="HH:MM:SS:FR">
                        </div>
                    </div>
                    <!-- Duración -->
                    <div class="form-group">
                        <label class="col-sm-3 control-label">
                            Duración
                        </label>
                        <div class="col-sm-8">
                            <input type="text" readonly="true" id="duracion" name="duracion" class="form-control validate" value="<?=Helper::sanitize($entrada->duracion);?>" placeholder="HH:MM:SS:FR">
                        </div>
                    </div>
                    <?php if (!empty($entradas)) { ?>
                        <!-- Entrada ED -->
                        <div class="form-group edfin">
                            <label class="col-sm-3 control-label">
                                ED
                            </label>
                            <div class="col-sm-8">
                                <?=HTML::select("entradaIdEd", $entradas, $entrada->entradaIdEd, array("id" => "entradaIdEd", "class" => "select2"), null, array("display" => "nombre")); ?>
                            </div>
                        </div>
                        <!-- Entrada FIN -->
                        <div class="form-group edfin">
                            <label class="col-sm-3 control-label">
                                FIN
                            </label>
                            <div class="col-sm-8">
                                <?=HTML::select("entradaIdFin", $entradas, $entrada->entradaIdFin, array("id" => "entradaIdFin", "class" => "select2"), null, array("display" => "nombre")); ?>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</form>

<script>

    //Date Mask
    $('input.dateMask').mask("00:00:00:00");

    <?php if (!empty($tipos)) { ?>
        //Tipo change
        var tipos = Array();

        <?php foreach ($tipos as $tipo) { ?>
            tipos[<?=$tipo->id?>] = {mascara: "<?=$tipo->mascara;?>", codigo: "<?=$tipo->codigo;?>"};
        <?php } ?>

        $(document).on('change', '#tipoId', function (e) {
            //House Number helper
            $("#houseNumber").attr("placeholder", tipos[$(this).val()].mascara);
            //ED/FIN
            if (tipos[$(this).val()].codigo.toUpperCase() == "P") {
                $(".edfin").show();
            } else {
                $(".edfin").hide();
            }
        });
        $("#tipoId").change();

    <?php } ?>

    //House Number autocomplete
    $(document).on('input', '#houseNumber', function (e) {

        //Elements
        var form = $("#mainForm");
        var field = $(this);

        //To Upper
        //field.val(field.val().toUpperCase());

        //Remove previous errors
        $(this).addClass("is-autocheck-loading");
        $(this).removeClass("is-autocheck-faliure");
        $(this).removeClass("is-autocheck-successful");
        field.parent().find("span.help-block").remove();
        field.parent().parent().removeClass("has-error");

        //Ajax
        $.ajax({
            type: "POST",
            url: "<?=Url::site('entradas/ajaxCheckHouseNumber')?>",
            data: {
                "houseNumber": $(this).val(),
                "tipoId": $("#tipoId").val()
            },
            dataType: "json"
        }).done(function (data) {
            field.removeClass("is-autocheck-loading");
            //Errors?
            if (data.data.status != "ok") {
                field.addClass("is-autocheck-faliure");
                processMessages(data.messages, form)
            } else {
                field.addClass("is-autocheck-successful");
            }
        });
    });

    //Duración autocomplete
    $(document).on('input', '.dateMask', function (e) {

        var field = $("#duracion");
        //Remove previous errors
        field.addClass("is-autocheck-loading");
        field.removeClass("is-autocheck-faliure");
        field.removeClass("is-autocheck-successful");

        //Ajax
        $.ajax({
            type: "POST",
            url: "<?=Url::site('entradas/ajaxTcDiff')?>",
            data: {
                "tcIn": $("#tcIn").val(),
                "tcOut": $("#tcOut").val()
            },
            dataType: "json"
        }).done(function (data) {
            field.removeClass("is-autocheck-loading");
            //Errors?
            if (data.data.status != "ok") {
                field.addClass("is-autocheck-faliure");
            } else {
                field.val(data.data.diff);
            }
        });
    });
</script>
