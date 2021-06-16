<?=$nav?>
<form class="form container <?=count($errors) > 0 ? 'form--invalid' : ''?>" action="sign-up.php" method="post" autocomplete="off"> <!-- form--invalid -->
  <h2>Регистрация нового аккаунта</h2>
  <div class="form__item <?=isset($errors['email']) ? 'form__item--invalid' : ''?>"> <!-- form__item--invalid -->
	<label for="email">E-mail <sup>*</sup></label>
	<input id="email" type="text" name="email"  value="<?=htmlspecialchars($user['email'])?>" placeholder="Введите e-mail">
	<?php if (isset($errors['email'])) : ?>
	<span class="form__error"><?=$errors['email']?></span>
	<?php endif; ?>
  </div>
  <div class="form__item <?=isset($errors['password']) ? 'form__item--invalid' : ''?>">
	<label for="password">Пароль <sup>*</sup></label>
	<input id="password" type="password" name="password"  value="<?=htmlspecialchars($user['password'])?>" placeholder="Введите пароль">
	<?php if (isset($errors['password'])) : ?>
	<span class="form__error"><?=$errors['password']?></span>
	<?php endif; ?>
  </div>
  <div class="form__item <?=isset($errors['name']) ? 'form__item--invalid' : ''?>">
	<label for="name">Имя <sup>*</sup></label>
	<input id="name" type="text" name="name" value="<?=htmlspecialchars($user['name'])?>"  placeholder="Введите имя">
	<?php if (isset($errors['name'])) : ?>
	<span class="form__error"><?=$errors['name']?></span>
	<?php endif; ?>
  </div>
  <div class="form__item <?=isset($errors['message']) ? 'form__item--invalid' : ''?>">
	<label for="message">Контактные данные <sup>*</sup></label>
	<textarea id="message" name="message" placeholder="Напишите как с вами связаться"><?=htmlspecialchars($user['message'])?></textarea>
	<?php if (isset($errors['message'])) : ?>
	<span class="form__error"><?=$errors['message']?></span>
	<?php endif; ?>
  </div>
  <?php if (count($errors) > 0) : ?>
  <span class="form__error form__error--bottom">Пожалуйста, исправьте ошибки в форме.</span>
  <?php endif; ?>
  <button type="submit" class="button">Зарегистрироваться</button>
  <a class="text-link" href="login.php">Уже есть аккаунт</a>
</form>
