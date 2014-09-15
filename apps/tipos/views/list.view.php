<?php defined('_EXE') or die('Restricted access'); ?>

<?php
//Toolbar
Toolbar::addTitle("Tipos de entradas", "glyphicon-star", "Listar");
//New button
Toolbar::addButton(
    array(
        "title" => "Nuevo",
        "app" => "tipos",
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
        <input type="hidden" name="app" id="app" value="tipos">
        <input type="hidden" name="action" id="action" value="">
        <!-- Filters -->
        <div class="row filters">
            <!-- Search -->
            <div class="col-sm-3 col-xs-6 filter">
                <?=HTML::search();?>
            </div>
        </div>
        <!-- Results -->
        <?php if (count($results)) { ?>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th><?=Html::sortableLink("id", "Id");?></th>
                            <th><?=Html::sortableLink("codigo", "Código");?></th>
                            <th><?=Html::sortableLink("nombre", "Nombre");?></th>
                            <th><?=Html::sortableLink("mascara", "Máscara");?></th>
                            <th><?=Html::sortableLink("color", "Color");?></th>
                            <th><?=Html::sortableLink("dateInsert", "Fecha creación");?></th>
                            <th><?=Html::sortableLink("dateUpdate", "Fecha actualización");?></th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($results as $tipo) { ?>
                            <tr>
                                <td><?=$tipo->id;?></a></td>
                                <td><?=Helper::sanitize($tipo->codigo);?></td>
                                <td><?=Helper::sanitize($tipo->nombre);?></td>
                                <td><?=Helper::sanitize($tipo->mascara);?></td>
                                <td><?=Helper::sanitize($tipo->color);?></td>
                                <td><?=Helper::humanDate($tipo->dateInsert);?></td>
                                <td><?=Helper::humanDate($tipo->dateUpdate);?></td>
                                <td>
                                    <?=HTML::formLink("btn-xs btn-primary", "pencil", Url::site("tipos/edit/".$tipo->id)); ?>
                                    <?=HTML::formLink("btn-xs btn-danger", "remove", Url::site("tipos/delete/".$tipo->id), null, null, "¿Deseas eliminar este tipo de entrada?"); ?>
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
                <p>No se han encontrado tipos de entrada</p>
            </blockquote>
        <?php } ?>
    </form>
</div>
