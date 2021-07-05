<?=$nav?>
<form class="form container <?=count($errors) > 0 ? 'form--invalid' : ''?>" action="login.php" method="post"> <!-- form--invalid -->
  <h2>Вход</h2>
  <div class="form__item <?=isset($errors['email']) ? 'form__item--invalid' : ''?>"> <!-- form__item--invalid -->
	<label for="email">E-mail <sup>*</sup></label>
	<input id="email" type="text" name="email" value="<?=htmlspecialchars($user['email'])?>" placeholder="Введите e-mail">
	<?php if (isset($errors['email'])) : ?>
	<span class="form__error"><?=$errors['email']?></span>
	<?php endif; ?>
  </div>
  <div class="form__item form__item--last <?=isset($errors['password']) ? 'form__item--invalid' : ''?>">
	<label for="password">Пароль <sup>*</sup></label>
	<input id="password" type="password" name="password" value="<?=htmlspecialchars($user['password'])?>" placeholder="Введите пароль">
	<?php if (isset($errors['password'])) : ?>
	<span class="form__error"><?=$errors['password']?></span>
	<?php endif; ?>
  </div>
  <?php if (count($errors) > 0) : ?>
  <span class="form__error form__error--bottom">Пожалуйста, исправьте ошибки в форме.</span>
  <?php endif; ?>  
  <button type="submit" class="button">Войти</button>
</form>
