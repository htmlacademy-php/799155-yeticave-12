<?=$nav?>
<section class="lots">
  <h2>Все лоты в категории <span>«<?=htmlspecialchars($cat['name'])?>»</span></h2>
  <ul class="lots__list">
  <?php foreach ($lots as $lot): ?>
    <li class="lots__item lot">
    <?php $bet = $bets[$lot['id']]['price']; 
    $price = intVal($lot['price']) > $bet ? intVal($lot['price']) : $bet;
    ?>
      <div class="lot__image">
        <img src="<?=htmlspecialchars($lot['img_url'])?>" width="350" height="260" alt="Сноуборд">
      </div>
      <div class="lot__info">
        <span class="lot__category"><?=htmlspecialchars($lot['cat_name'])?></span>
        <h3 class="lot__title"><a class="text-link" href="lot.php?id=<?=intVal($lot['id'])?>"><?=htmlspecialchars($lot['name'])?></a></h3>
        <div class="lot__state">
          <div class="lot__rate">
            <?php if ($lot['bets_count'] == 0): ?>
            <span class="lot__amount">Стартовая цена</span>
            <?php else: ?>
            <span class="lot__amount"><?=$lot['bets_count'] . get_noun_plural_form($lot['bets_count'], ' ставка', ' ставки', ' ставок')?></span>
            <?php endif; ?>
            <span class="lot__cost"><?=formatPrice(strip_tags($price))?></span>
          </div>
          <?php $time_left = getTimeLeft(strip_tags($lot['dt_expired'])); ?>
          <?php if ($time_left == false) : ?>
          <div class="lot__timer timer timer--finishing">
              <?='--:--';?>
          <?php else:?>
          <div class="lot__timer timer <?php if ($time_left[0] == 0):?>timer--finishing<?php endif;?>">
              <?=$time_left[2] .':' . $time_left[3]; ?>
          <?php endif; ?>
          </div>
        </div>
      </div>
    </li>
  <?php endforeach; ?>            
  </ul>
</section>
