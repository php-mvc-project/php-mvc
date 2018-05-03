<?php 
use PhpMvc\View;
use PhpMvc\Html;

$model = new \PhpMvcTest\Models\HomePage();

View::setLayout('_default');
View::setTitle('Home page');
View::injectModel($model);
?>

<!--View file: <?=View::getViewFile()?>-->

<h2>Index</h2>
<p>This is the main page of the most wonderful site in the world!</p>
