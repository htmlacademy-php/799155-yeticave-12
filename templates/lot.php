<?=$nav?>
<?php $price = $max_bet > intVal($lot['price']) ? $max_bet : intVal($lot['price']); ?>
<section class="lot-item container">
  <h2><?=htmlspecialchars($lot['name'])?></h2>
  <div class="lot-item__content">
    <div class="lot-item__left">
      <div class="lot-item__image">
        <img src="<?=htmlspecialchars($lot['img_url'])?>" width="730" height="548" alt="<?=htmlspecialchars($lot['name'])?>">
      </div>
      <p class="lot-item__category">Категория: <span><?=htmlspecialchars($lot['cat_name'])?></span></p>
      <p class="lot-item__description"><?=strip_tags($lot['descr'])?></p>
    </div>
    <div class="lot-item__right">
      <div class="lot-item__state">
        <?php $time_left = getTimeLeft(strip_tags($lot['dt_expired'])); ?>
        <?php if ($time_left == false) : ?>
        <div class="lot__timer timer timer--finishing">
            <?='--:--';?>
        <?php else:?>
        <div class="lot__timer timer <?php if ($time_left[0] == 0):?>timer--finishing<?php endif;?>">
            <?=$time_left[2] .':' . $time_left[3]; ?>
        <?php endif; ?>
        </div>
        <div class="lot-item__cost-state">
          <div class="lot-item__rate">
            <span class="lot-item__amount">Текущая цена</span>
            <span class="lot-item__cost"><?=formatPrice(strip_tags($price))?></span>
          </div>
          <div class="lot-item__min-cost">
            Мин. ставка <span><?=formatPrice(strip_tags($price + htmlspecialchars($lot['bet_step'])))?></span>
          </div>
        </div>
        <form class="lot-item__form <?=$is_auth?'':'visually-hidden';?>" action="lot.php" method="post" autocomplete="off">
          <p class="lot-item__form-item form__item <?=isset($errors['cost']) ? 'form__item--invalid' : ''?>">
            <label for="cost">Ваша ставка</label>
            <input id="cost" type="text" name="cost" placeholder="Ваша ставка" value="<?=htmlspecialchars($bet['cost'])?>">
            <span class="form__error"><?=$errors['cost']?></span>
          </p>
          <!-- Запомним id лота для верификации формы -->
          <input class="visually-hidden" type="text" name="lot-id" value="<?=$lot['id']?>">
          <button type="submit" class="button">Сделать ставку</button>
        </form>
      </div>
      <div class="history <?=$is_auth?'':'visually-hidden';?>">
        <h3>История ставок (<span>10</span>)</h3>
        <table class="history__list">
          <tr class="history__item">
            <td class="history__name">Иван</td>
            <td class="history__price">10 999 р</td>
            <td class="history__time">5 минут назад</td>
          </tr>
          <tr class="history__item">
            <td class="history__name">Константин</td>
            <td class="history__price">10 999 р</td>
            <td class="history__time">20 минут назад</td>
          </tr>
          <tr class="history__item">
            <td class="history__name">Евгений</td>
            <td class="history__price">10 999 р</td>
            <td class="history__time">Час назад</td>
          </tr>
          <tr class="history__item">
            <td class="history__name">Игорь</td>
            <td class="history__price">10 999 р</td>
            <td class="history__time">19.03.17 в 08:21</td>
          </tr>
          <tr class="history__item">
            <td class="history__name">Енакентий</td>
            <td class="history__price">10 999 р</td>
            <td class="history__time">19.03.17 в 13:20</td>
          </tr>
          <tr class="history__item">
            <td class="history__name">Семён</td>
            <td class="history__price">10 999 р</td>
            <td class="history__time">19.03.17 в 12:20</td>
          </tr>
          <tr class="history__item">
            <td class="history__name">Илья</td>
            <td class="history__price">10 999 р</td>
            <td class="history__time">19.03.17 в 10:20</td>
          </tr>
          <tr class="history__item">
            <td class="history__name">Енакентий</td>
            <td class="history__price">10 999 р</td>
            <td class="history__time">19.03.17 в 13:20</td>
          </tr>
          <tr class="history__item">
            <td class="history__name">Семён</td>
            <td class="history__price">10 999 р</td>
            <td class="history__time">19.03.17 в 12:20</td>
          </tr>
          <tr class="history__item">
            <td class="history__name">Илья</td>
            <td class="history__price">10 999 р</td>
            <td class="history__time">19.03.17 в 10:20</td>
          </tr>
        </table>
      </div>
    </div>
  </div>
</section>
