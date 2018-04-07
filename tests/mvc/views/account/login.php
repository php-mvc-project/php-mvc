<?php 
use PhpMvc\View;
use PhpMvc\Html;

$model = new \PhpMvcTest\Models\Login();

View::setLayout('_default');
View::setTitle('Login');
View::injectModel($model);
?>

<?php
  if (View::getData('success') === true) {
    echo 'It\'s a success! Undeniably!';
  }
  else {
?>
<div class="modal show" tabindex="-1" role="dialog" style="position: static">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Login for all</h4>
      </div>
      <div class="modal-body">
        <form id="loginForm" action="<?=Html::action('Login', 'Account')?>" method="post" enctype="application/x-www-form-urlencoded">
            <div class="form-group">
                <label for="login"><?=Html::displayName('username')?>:</label>
                <input 
                    required 
                    type="text" 
                    class="form-control" 
                    id="login" 
                    name="username" 
                    value="<?=$model->username?>" 
                />
                <?=Html::validationMessage('username')?>
                <?=Html::displayText('username')?>
            </div>
            <div class="form-group">
                <label for="password"><?=Html::displayName('password')?>:</label>
                <input 
                    required
                    type="password" 
                    class="form-control" 
                    id="password" 
                    name="password" 
                    value="<?=$model->password?>" 
                />
                <?=Html::validationMessage('password')?>
                <?=Html::displayText('password')?>
            </div>
        </form>
      </div>
      <div class="modal-footer">
        <a href="<?=Html::action('Index', 'Home')?>" class="btn btn-default">Cancel</a>
        <button type="button" class="btn btn-primary" onclick="$('#loginForm').submit()">Login</button>
      </div>
    </div>
  </div>
</div>
<?php
  }
?>