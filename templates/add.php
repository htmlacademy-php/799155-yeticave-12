<?=$nav?>
<form class="form form--add-lot container <?=count($errors) > 0 ? 'form--invalid' : ''?>" action="add.php" method="post" enctype="multipart/form-data"> <!-- form--invalid -->
  <h2>Добавление лота</h2>
  <div class="form__container-two">
	<div class="form__item <?=isset($errors['lot-name']) ? 'form__item--invalid' : ''?>"> <!-- form__item--invalid -->
	  <label for="lot-name">Наименование <sup>*</sup></label>
	  <input id="lot-name" type="text" name="lot-name" value="<?=htmlspecialchars($lot['lot-name'])?>" placeholder="Введите наименование лота">
		<?php if (isset($errors['lot-name'])) : ?>
	  <span class="form__error"><?=$errors['lot-name']?></span>
		<?php endif; ?>
	</div>
	<div class="form__item <?=isset($errors['category']) ? 'form__item--invalid' : ''?>">
	  <label for="category">Категория <sup>*</sup></label>
	  <select id="category" name="category" value="<?=$lot['category']?>">
		<option vaue="0">Выберите категорию</option>
		<?php $selected = ""; ?>
		<?php foreach($cats as $cat): ?>
		  <?php $selected = ($cat['id'] === $lot['category']) ? "selected" : ""; ?>
		  <option value=<?=$cat['id']?> <?=$selected?>><?=htmlspecialchars($cat['name'])?></option>
		<?php endforeach; ?>
	  </select>
		<?php if (isset($errors['category'])) : ?>
	  <span class="form__error"><?=$errors['category']?></span>
		<?php endif; ?>
	</div>
  </div>
  <div class="form__item form__item--wide <?=isset($errors['message']) ? 'form__item--invalid' : ''?>">
	<label for="message">Описание <sup>*</sup></label>
	<textarea id="message" name="message" placeholder="Напишите описание лота"><?=htmlspecialchars($lot['message'])?></textarea>
	<?php if (isset($errors['message'])) : ?>
	<span class="form__error"><?=$errors['message']?></span>
	<?php endif; ?>
  </div>
  <div class="form__item form__item--file <?=isset($errors['lot-img']) ? 'form__item--invalid' : ''?>">
	<label>Изображение <sup>*</sup></label>
	<div class="form__input-file">
	  <input class="visually-hidden" type="file" name="lot-img" id="lot_img" value="">
		<!-- Сохраним информацию о файлах с изображением лота через механизм $_POST -->
		<input class="visually-hidden" type="text" name="lot_img" value="<?=htmlspecialchars($lot['lot-img'])?>">
		<input class="visually-hidden" type="text" name="new_img" value="<?=$lot['new-img']?>">
	  <label for="lot_img">
		Выбрать файл
	  </label>
		<!-- Покажем имя выбранного файла рядом с кнопкой -->
		<?php if (!empty($lot['lot-img'])) : ?>
		<span class="form__item--info"><?=htmlspecialchars($lot['lot-img'])?></span>
		<?php endif; ?>
		<?php if (isset($errors['lot-img'])) : ?>
	  <span class="form__error"><?=$errors['lot-img']?></span>
		<?php endif; ?>
	</div>
  </div>
  <div class="form__container-three">
	<div class="form__item form__item--small <?=isset($errors['lot-rate']) ? 'form__item--invalid' : ''?>">
	  <label for="lot-rate">Начальная цена <sup>*</sup></label>
	  <input id="lot-rate" type="text" name="lot-rate" value="<?=htmlspecialchars($lot['lot-rate'])?>" placeholder="0">
		<?php if (isset($errors['lot-rate'])) : ?>
	  <span class="form__error"><?=$errors['lot-rate']?></span>
		<?php endif; ?>
	</div>
	<div class="form__item form__item--small <?=isset($errors['lot-step']) ? 'form__item--invalid' : ''?>">
	  <label for="lot-step">Шаг ставки <sup>*</sup></label>
	  <input id="lot-step" type="text" name="lot-step" value="<?=htmlspecialchars($lot['lot-step'])?>" placeholder="0">
		<?php if (isset($errors['lot-step'])) : ?>
	  <span class="form__error"><?=$errors['lot-step']?></span>
		<?php endif; ?>
	</div>
	<div class="form__item <?=isset($errors['lot-date']) ? 'form__item--invalid' : ''?>">
	  <label for="lot-date">Дата окончания торгов <sup>*</sup></label>
	  <input class="form__input-date" id="lot-date" type="text" name="lot-date" value="<?=htmlspecialchars($lot['lot-date'])?>" placeholder="Введите дату в формате ГГГГ-ММ-ДД">
		<?php if (isset($errors['lot-date'])) : ?>
	  <span class="form__error"><?=$errors['lot-date']?></span>
		<?php endif; ?>
	</div>
  </div>
  <button type="submit" class="button">Добавить лот</button>
  <?php if (count($errors) > 0) : ?>
  <span class="form__error form__error--bottom">Пожалуйста, исправьте ошибки в форме.</span>
  <?php endif; ?>
</form> 
