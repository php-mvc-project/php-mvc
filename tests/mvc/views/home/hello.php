<?php 
use PhpMvc\View;
use PhpMvc\Html;

$model = '';

View::setTitle('Hello world');
View::injectModel($model);
?>

<h3>Model</h3>
<?=$model?>