<?php defined('_EXE') or die('Restricted access'); ?>

<?php $user = Registry::getUser(); ?>
<?php $config = Registry::getConfig(); ?>

<div class="navbar navbar-default navbar-inverse navbar-fixed-top" role="navigation">
    <div class="container">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand logo" href="<?=Url::site();?>">
                <?=$config->get("title");?>
            </a>
        </div>
        <div class="navbar-collapse collapse">
            <ul class="nav navbar-nav">
                <?php $url = Registry::getUrl(); ?>
                <?php $active[$url->app][$url->action] = "active"; ?>
                <?php if (!$user->id) { ?>
                    <li class="<?=$active['login']['index']?>">
                        <a href="<?=Url::site("login")?>">
                            <span class="glyphicon glyphicon-log-in"></span>
                            Login
                        </a>
                    </li>
                <?php } else { ?>
                    <li class="<?=$active['entradas']['index']?>">
                        <a href="<?=Url::site("entradas")?>">
                            <span class="glyphicon glyphicon-star"></span>
                            Entradas
                        </a>
                    </li>
                    <li class="<?=$active['tipos']['index']?>">
                        <a href="<?=Url::site("tipos")?>">
                            <span class="glyphicon glyphicon-star"></span>
                            Tipos
                        </a>
                    </li>
                    <li class="<?=$active['moscas']['index']?>">
                        <a href="<?=Url::site("moscas")?>">
                            <span class="glyphicon glyphicon-star"></span>
                            Moscas
                        </a>
                    </li>
                <?php } ?>
            </ul>
            <?php if ($user->id) { ?>
                <ul class="nav navbar-nav navbar-right">
                    <li class="exit">
                        <a href="<?=Url::site("login/doLogout");?>">
                            <span class="glyphicon glyphicon-off"></span>
                            Salir
                            <small><i>(<?=$user->username;?>)</i></small>
                        </a>
                    </li>
                </ul>
            <?php } ?>
        </div>
    </div>
</div>
