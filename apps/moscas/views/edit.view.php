<?php defined('_EXE') or die('Restricted access'); ?>

<?php
//Toolbar
if ($mosca->id) {
    $subtitle = "Editar mosca";
    $title = "Guardar";
} else {
    $subtitle = "Nuevo mosca";
    $title = "Crear";
}
Toolbar::addTitle("Moscas", "glyphicon-star", $subtitle);
if ($mosca->id) {
    //Delete button
    Toolbar::addButton(
        array(
            "title" => "Eliminar",
            "app" => "moscas",
            "action" => "delete",
            "class" => "danger",
            "spanClass" => "remove",
            "confirmation" => "¿Deseas realmente eliminar esta mosca?",
            "noAjax" => true,
        )
    );
}
//Cancel button
Toolbar::addButton(
    array(
        "title" => "Cancelar",
        "link" => Url::site("moscas"),
        "class" => "primary",
        "spanClass" => "chevron-left",
    )
);
//Save button
Toolbar::addButton(
    array(
        "title" => $title,
        "app" => "moscas",
        "action" => "save",
        "class" => "success",
        "spanClass" => "ok",
    )
);
Toolbar::render();
?>

<form method="post" name="mainForm" id="mainForm" action="<?=Url::site();?>" class="form-horizontal ajax" role="form" autocomplete="off">
    <input type="hidden" name="app" id="app" value="moscas">
    <input type="hidden" name="action" id="action" value="save">
    <input type="hidden" name="id" value="<?=$mosca->id?>">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    Tipo de entrada
                </div>
                <div class="panel-body">
                    <!-- Tipo -->
                    <div class="form-group">
                        <label class="col-sm-3 control-label">
                            Tipo
                        </label>
                        <div class="col-sm-8">
                            <?=Html::select("tipoId", $mosca->tipos, $mosca->tipoId)?>
                        </div>
                    </div>
                    <!-- Código -->
                    <div class="form-group">
                        <label class="col-sm-3 control-label">
                            Código
                        </label>
                        <div class="col-sm-8">
                            <input type="text" id="codigo" name="codigo" class="form-control" value="<?=Helper::sanitize($mosca->codigo);?>">
                        </div>
                    </div>
                    <!-- Nombre -->
                    <div class="form-group">
                        <label class="col-sm-3 control-label">
                            Nombre
                        </label>
                        <div class="col-sm-8">
                            <input type="text" id="nombre" name="nombre" class="form-control" value="<?=Helper::sanitize($mosca->nombre);?>">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
