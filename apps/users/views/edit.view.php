<?php defined('_EXE') or die('Restricted access'); ?>

<?php $currentUser = Registry::getUser();?>

<?php
//Toolbar
if ($user->id) {
    $subtitle = "Editar usuario";
    $title = "Guardar";
} else {
    $subtitle = "Nuevo usuario";
    $title = "Crear";
}
Toolbar::addTitle("Usuarios", "glyphicon-user", $subtitle);
if ($user->id) {
    //Delete button
    Toolbar::addButton(
        array(
            "title" => "Eliminar",
            "app" => "users",
            "action" => "delete",
            "class" => "danger",
            "spanClass" => "remove",
            "confirmation" => "¿Deseas realmente eliminar este usuario?",
            "noAjax" => true,
        )
    );
}
//Cancel button
Toolbar::addButton(
    array(
        "title" => "Cancelar",
        "link" => Url::site("users"),
        "class" => "primary",
        "spanClass" => "chevron-left",
    )
);
//Save button
Toolbar::addButton(
    array(
        "title" => $title,
        "app" => "users",
        "action" => "save",
        "class" => "success",
        "spanClass" => "ok",
    )
);
Toolbar::render();
?>

<form method="post" name="mainForm" id="mainForm" action="<?=Url::site();?>" class="form-horizontal ajax" role="form" autocomplete="off">
    <input type="hidden" name="app" id="app" value="users">
    <input type="hidden" name="action" id="action" value="save">
    <input type="hidden" name="id" value="<?=$user->id?>">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    Detalles
                </div>
                <div class="panel-body">
                    <!-- Estado -->
                    <div class="form-group">
                        <label class="col-sm-3 control-label">
                            Estado
                        </label>
                        <div class="col-sm-8">
                            <input type="hidden" name="statusId" value="0">
                            <input type="checkbox" class="switch" name="statusId" id="statusId" value="1" <?php if($user->statusId) echo "checked";?>>
                        </div>
                    </div>
                    <!-- Username -->
                    <div class="form-group">
                        <label class="col-sm-3 control-label">
                            Username
                        </label>
                        <div class="col-sm-8">
                            <input type="text" id="username" name="username" class="form-control" value="<?=Helper::sanitize($user->username);?>">
                        </div>
                    </div>
                    <!-- Email -->
                    <div class="form-group">
                        <label class="col-sm-3 control-label">
                            Email
                        </label>
                        <div class="col-sm-8">
                            <input type="text" id="email" name="email" class="form-control" value="<?=Helper::sanitize($user->email);?>">
                        </div>
                    </div>
                    <!-- Contraseña -->
                    <div class="form-group">
                        <label class="col-sm-3 control-label">
                            Contraseña
                        </label>
                        <div class="col-sm-8">
                            <input type="password" id="password" name="password" class="form-control">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
