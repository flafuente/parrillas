<?php defined('_EXE') or die('Restricted access'); ?>

<?php
//Toolbar
Toolbar::addTitle("Entradas", "glyphicon-star", "Listar");
//New button
Toolbar::addButton(
    array(
        "title" => "Nueva",
        "app" => "entradas",
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
        <input type="hidden" name="app" id="app" value="entradas">
        <input type="hidden" name="action" id="action" value="">
        <!-- Results -->
        <?php if (count($results)) { ?>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th><?=Html::sortableLink("id", "Id");?></th>
                            <th><?=Html::sortableLink("nombre", "Nombre");?></th>
                            <th><?=Html::sortableLink("dateInsert", "Fecha creación");?></th>
                            <th><?=Html::sortableLink("dateUpdate", "Fecha actualización");?></th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($results as $entrada) { ?>
                            <tr>
                                <td><?=$entrada->id;?></a></td>
                                <td><?=Helper::sanitize($entrada->nombre);?></td>
                                <td><?=Helper::humanDate($entrada->dateInsert);?></td>
                                <td><?=Helper::humanDate($entrada->dateUpdate);?></td>
                                <td>
                                    <?=HTML::formLink("btn-xs btn-primary", "pencil", Url::site("entradas/edit/".$entrada->id)); ?>
                                    <?=HTML::formLink("btn-xs btn-danger", "remove", Url::site("entradas/delete/".$entrada->id), null, null, "¿Deseas eliminar esta entrada?"); ?>
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
                <p>No se han encontrado entradas</p>
            </blockquote>
        <?php } ?>
    </form>
</div>
