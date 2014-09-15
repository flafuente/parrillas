<?php defined('_EXE') or die('Restricted access'); ?>

<div class="row">
    <div class="col-xs-offset-3 col-xs-6">
        <div class="well">
            <fieldset>
                <legend>
                    Login
                </legend>
                <?php $user = Registry::getUser(); ?>
                <?php if (!$user->id) { ?>
                    <form class="form-horizontal ajax" role="form" method="post">
                        <!-- Username -->
                        <div class="form-group">
                            <label for="login" class="col-sm-4 control-label">
                                Email
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" id="login" name="login">
                            </div>
                        </div>
                        <!-- Password -->
                        <div class="form-group">
                            <label for="password" class="col-sm-4 control-label">
                                Password
                            </label>
                            <div class="col-sm-8">
                                <input type="password" class="form-control" id="password" name="password">
                            </div>
                        </div>
                        <!-- Buttons -->
                        <div class="form-group">
                            <div class="col-sm-offset-4 col-sm-8">
                                <?=Html::formButton("btn-primary", null, "Acceder", array(
                                        "data-app" => "login",
                                        "data-action" => "doLogin"
                                    )
                                );?>
                            </div>
                        </div>
                    </form>
                <?php } else { ?>
                    <h3>Hi there <?=$user->username?>! :)</h3>
                    <?=Html::formLink("btn-primary", "off", Url::site("login/doLogout"), "Salir");?>
                <?php } ?>
            </fieldset>
        </div>
    </div>
</div>
