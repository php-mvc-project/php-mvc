<?php
use PhpMvc\Html;
use PhpMvc\View;
?>
<html>
    <head>
        <title><?=Html::getTitle('Default layout')?> - PHP MVC Test project</title>
    </head>
    <body>
        <?php Html::render('header'); ?>

        <div class="container">
            <h1>Default layout</h1>

            <?=Html::validationSummary()?>

            <?php Html::renderBody(); ?>

            <h3>Model</h3>
            <?php var_dump(View::getModel()); ?>

            <h3>ViewData</h3>
            <?php var_dump(View::getData()); ?>

            <h3>ModelState</h3>
            <?php var_dump(View::getModelState()); ?>
        </div>

        <?php Html::render('footer'); ?>
    </body>
</html>