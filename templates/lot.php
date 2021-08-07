<?=$nav?>
<?php $time_left = getTimeLeft(strip_tags($lot['dt_expired']));
      $price = $max_bet['price'] > intVal($lot['price']) ? $max_bet['price'] : intVal($lot['price']);
      $bet_min = $price + intVal($lot['bet_step']);
      $hide_form = ($is_auth === 1 and $time_left !== false and  $lot['author_id'] !== $user_id and
      $user_id !== $max_bet['author']) ? false : true;?>
<section class="lot-item container">
  <h2><?=htmlspecialchars($lot['name'])?></h2>
  <div class="lot-item__content">
    <div class="lot-item__left">
      <div class="lot-item__image">
        <img src="<?=htmlspecialchars($lot['img_url'])?>" width="730" height="548"
        alt="<?=htmlspecialchars(explode(" ", $lot['name'])[0])?>">
      </div>
      <p class="lot-item__category">Категория: <span><?=htmlspecialchars($lot['cat_name'])?></span></p>
      <p class="lot-item__description"><?=htmlspecialchars($lot['descr'])?></p>
    </div>
    <div class="lot-item__right">
      <div class="lot-item__state">
        <?php if ($time_left === false) : ?>
        <div class="lot__timer timer timer--end">
            <?='Торги окончены';?>
        <?php else :?>
        <div class="lot__timer timer <?=($time_left[0] < 1) ? 'timer--finishing' : ''?>">
            <?=$time_left[2] .':' . $time_left[3]; ?>
        <?php endif; ?>
        </div>
        <div class="lot-item__cost-state">
          <div class="lot-item__rate">
            <span class="lot-item__amount">Текущая цена</span>
            <span class="lot-item__cost"><?=formatPrice(strip_tags($price))?></span>
          </div>
          <div class="lot-item__min-cost">
            Мин. ставка <span>
            <?=formatPrice($bet_min)?>
            </span>
          </div>
        </div>
        <form class="lot-item__form <?=$hide_form ? 'visually-hidden' : '';?>" action="lot.php"
        method="post" autocomplete="off">
          <p class="lot-item__form-item form__item <?=isset($errors['cost']) ? 'form__item--invalid' : ''?>">
            <label for="cost">Ваша ставка</label>
            <input id="cost" type="text" name="cost" placeholder="Ваша ставка"
            value="<?=(intVal($bet['cost']) === 0) ? $bet_min : htmlspecialchars($bet['cost'])?>">
            <?php if (isset($errors['cost'])) : ?>
            <span class="form__error"><?=$errors['cost']?></span>
            <?php endif; ?>
          </p>
          <!-- Запомним id лота для верификации формы -->
          <input class="visually-hidden" type="text" name="lot-id" value="<?=$lot['id']?>">
          <button type="submit" class="button">Сделать ставку</button>
        </form>
      </div>
      <div class="history <?=$is_auth?'':'visually-hidden';?>">
        <h3>История ставок (<span><?=count($bet_history)?></span>)</h3>
        <table class="history__list">
        <?php foreach ($bet_history as $item) :?>
          <tr class="history__item">
            <td class="history__name"><?=$item['name']?></td>
            <td class="history__price"><?=formatPrice($item['price']);?></td>
            <td class="history__time"><?=getTimeStr($item['dt_add'], $lot['dt_expired'])?></td>
          </tr>
        <?php endforeach; ?>
        </table>
      </div>
    </div>
  </div>
</section>
