<?php 
use PhpMvc\View;

$model = '';

View::injectModel($model);
?>
<!--View file: <?=View::getViewFile()?>-->
<header>
  <nav class="navbar navbar-default">
    <div class="container">
      <div class="navbar-header">
        <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
          <span class="sr-only">Toggle navigation</span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
        </button>
        <a class="navbar-brand" href="/">
          <i class="glyphicon glyphicon-tasks"></i>
          <?=$model?>
        </a>
      </div>
    </div>
  </nav>
</header>