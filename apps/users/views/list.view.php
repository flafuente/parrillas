<?php defined('_EXE') or die('Restricted access'); ?>

<?php
//Toolbar
Toolbar::addTitle("Usuarios", "glyphicon-user", "Listar");
//New button
Toolbar::addButton(
    array(
        "title" => "Nuevo",
        "app" => "users",
        "action" => "edit",
        "class" => "success",
        "spanClass" => "plus",
        "noAjax" => true,
    )
);
Toolbar::render();
?>

<div class="main">
    <form method="post" action="<?=Url::site()?>" id="mainForm" name="mainForm" class="form-inline" role="form">
        <input type="hidden" name="router" id="router" value="admin">
        <input type="hidden" name="app" id="app" value="users">
        <input type="hidden" name="action" id="action" value="">
        <!-- Filters -->
        <div class="row filters">
            <!-- Search -->
            <div class="col-sm-3 col-xs-6 filter">
                <?=HTML::search();?>
            </div>
            <!-- Estado -->
            <div class="col-sm-3 col-xs-6 col-md-2 filter">
                <?php $userNull = new User(); ?>
                <?=HTML::select("statusId", $userNull->statuses, $_REQUEST["estadoId"], array("class" => "change-submit"), array("id" => "-1", "display" => "- Estado -")); ?>
            </div>
        </div>
        <!-- Results -->
        <?php if (count($results)) { ?>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th><?=Html::sortableLink("id", "Id");?></th>
                            <th><?=Html::sortableLink("statusId", "Estado");?></th>
                            <th><?=Html::sortableLink("roleId", "Rol");?></th>
                            <th><?=Html::sortableLink("email", "Email");?></th>
                            <th><?=Html::sortableLink("nombre", "Nombre");?></th>
                            <th><?=Html::sortableLink("apellidos", "Apellidos");?></th>
                            <th><?=Html::sortableLink("dateInsert", "Fecha creación");?></th>
                            <th><?=Html::sortableLink("dateUpdate", "Fecha actualización");?></th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($results as $user) { ?>
                            <tr>
                                <td><?=$user->id;?></a></td>
                                <td>
                                    <span class="label label-<?=$user->getStatusCssString();?>">
                                        <?=$user->getStatusString();?>
                                    </span>
                                </td>
                                <td><?=$user->getRoleString()?></td>
                                <td>
                                    <a href="<?=Url::site("admin/users/edit/".$user->id);?>">
                                        <?=Helper::sanitize($user->email);?>
                                    </a>
                                </td>
                                <td><?=$user->nombre;?></td>
                                <td><?=$user->apellidos;?></td>
                                <td><?=Helper::humanDate($user->dateInsert);?></td>
                                <td><?=Helper::humanDate($user->dateUpdate);?></td>
                                <td>
                                    <?=HTML::formLink("btn-xs btn-primary", "pencil", Url::site("admin/users/edit/".$user->id)); ?>
                                    <?=HTML::formLink("btn-xs btn-danger", "remove", Url::site("admin/users/delete/".$user->id), null, null, "¿Deseas eliminar este usuario?"); ?>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
                <?php $controller->setData("pag", $pag); ?>
                <?=$controller->view("modules.pagination");?>
            </div>
        <?php } else { ?>
            <blockquote>
                <p>No se han encontrado usuarios</p>
            </blockquote>
        <?php } ?>
    </form>
</div>
