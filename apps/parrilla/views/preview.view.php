<?php defined('_EXE') or die('Restricted access'); ?>

<?php if (count($eventos)) { ?>

    <table class="table table-condensed">
        <thead>
            <th></th>
            <th>Hora</th>
            <th>Duración</th>
            <th>HouseNumber</th>
            <th>Tipo</th>
            <th>Titulo</th>
        </thead>
        <tbody>
            <?php foreach ($eventos as $evento) { ?>
                <?php $tipo = new Tipo($evento->tipo); ?>

                <tr class="clickable" style="background-color: <?=$tipo->color;?>">
                    <td>
                        <input type="checkbox" value="<?=$evento->id;?>">
                    </td>
                    <td><?=$evento->getHora();?></td>
                    <td><?=$evento->duracion;?></td>
                    <td><?=$evento->houseNumber;?></td>
                    <td><?=$tipo->codigo;?></td>
                    <td><?=$evento->titulo;?></td>
                </tr>

            <?php } ?>
        </tbody>
    </table>

<?php } else { ?>

    <blockquote>
        <p>No se han encontrado eventos</p>
    </blockquote>

<?php } ?>
