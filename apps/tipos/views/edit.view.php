<?php defined('_EXE') or die('Restricted access'); ?>

<?php
//Toolbar
if ($tipo->id) {
    $subtitle = "Editar tipo";
    $title = "Guardar";
} else {
    $subtitle = "Nuevo tipo";
    $title = "Crear";
}
Toolbar::addTitle("Tipos de entrada", "glyphicon-star", $subtitle);
if ($tipo->id) {
    //Delete button
    Toolbar::addButton(
        array(
            "title" => "Eliminar",
            "app" => "tipos",
            "action" => "delete",
            "class" => "danger",
            "spanClass" => "remove",
            "confirmation" => "¿Deseas realmente eliminar este tipo de entrada?",
            "noAjax" => true,
        )
    );
}
//Cancel button
Toolbar::addButton(
    array(
        "title" => "Cancelar",
        "link" => Url::site("tipos"),
        "class" => "primary",
        "spanClass" => "chevron-left",
    )
);
//Save button
Toolbar::addButton(
    array(
        "title" => $title,
        "app" => "tipos",
        "action" => "save",
        "class" => "success",
        "spanClass" => "ok",
    )
);
Toolbar::render();
?>

<form method="post" name="mainForm" id="mainForm" action="<?=Url::site();?>" class="form-horizontal ajax" role="form" autocomplete="off">
    <input type="hidden" name="app" id="app" value="tipos">
    <input type="hidden" name="action" id="action" value="save">
    <input type="hidden" name="id" value="<?=$tipo->id?>">
    <div class="row">
        <div class="col-md-12">
            <div class="col-md-6">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        Tipo de entrada
                    </div>
                    <div class="panel-body">
                        <!-- Nombre -->
                        <div class="form-group">
                            <label class="col-sm-3 control-label">
                                Nombre
                            </label>
                            <div class="col-sm-8">
                                <input type="text" id="nombre" name="nombre" class="form-control" value="<?=Helper::sanitize($tipo->nombre);?>">
                            </div>
                        </div>
                        <!-- Código -->
                        <div class="form-group">
                            <label class="col-sm-3 control-label">
                                Código
                            </label>
                            <div class="col-sm-8">
                                <input type="text" id="codigo" name="codigo" class="form-control" value="<?=Helper::sanitize($tipo->codigo);?>">
                            </div>
                        </div>
                        <!-- Color -->
                        <div class="form-group">
                            <label class="col-sm-3 control-label">
                                Color
                            </label>
                            <div class="col-sm-8">
                                <input type="text" id="color" name="color" class="form-control" value="<?=Helper::sanitize($tipo->color);?>">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
