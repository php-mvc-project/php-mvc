<?php
use PhpMvc\Html;
use PhpMvc\View;
?>
<!--View file: <?=View::getViewFile()?>-->
<html>
    <head>
        <title><?=Html::getTitle('Lite layout')?> - PHP MVC Test project</title>
    </head>
    <body>
        <h1>Lite layout</h1>

        <div class="container">
            <?php Html::renderBody(); ?>
        </div>
    </body>
</html>