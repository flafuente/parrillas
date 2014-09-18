<?php defined('_EXE') or die('Restricted access'); ?>

<?php
//Toolbar
Toolbar::addTitle("Moscas", "glyphicon-star", "Listar");
//New button
Toolbar::addButton(
    array(
        "title" => "Nuevo",
        "app" => "moscas",
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
        <input type="hidden" name="app" id="app" value="moscas">
        <input type="hidden" name="action" id="action" value="">
        <!-- Results -->
        <?php if (count($results)) { ?>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th><?=Html::sortableLink("id", "Id");?></th>
                            <th><?=Html::sortableLink("tipoId", "Tipo");?></th>
                            <th><?=Html::sortableLink("codigo", "Código");?></th>
                            <th><?=Html::sortableLink("nombre", "Nombre");?></th>
                            <th><?=Html::sortableLink("dateInsert", "Fecha creación");?></th>
                            <th><?=Html::sortableLink("dateUpdate", "Fecha actualización");?></th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($results as $mosca) { ?>
                            <tr>
                                <td><?=$mosca->id;?></a></td>
                                <td><?=Helper::sanitize($mosca->getTipoString());?></td>
                                <td><?=Helper::sanitize($mosca->codigo);?></td>
                                <td><?=Helper::sanitize($mosca->nombre);?></td>
                                <td><?=Helper::humanDate($mosca->dateInsert);?></td>
                                <td><?=Helper::humanDate($mosca->dateUpdate);?></td>
                                <td>
                                    <?=HTML::formLink("btn-xs btn-primary", "pencil", Url::site("moscas/edit/".$mosca->id)); ?>
                                    <?=HTML::formLink("btn-xs btn-danger", "remove", Url::site("moscas/delete/".$mosca->id), null, null, "¿Deseas eliminar esta mosca?"); ?>
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
                <p>No se han encontrado moscas</p>
            </blockquote>
        <?php } ?>
    </form>
</div>
