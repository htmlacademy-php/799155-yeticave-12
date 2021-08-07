
<?=$nav?>
<section class="rates container">
  <h2>Мои ставки</h2>
  <table class="rates__list">
  <?php foreach ($bets as $bet) :?>
      <?php $time_left = getTimeLeft(strip_tags($bet['dt_expired']));?>
      <?php $rates_class = '';?>
      <?php if ($bet['winner_id'] === $user_id) :?>
            <?php $rates_class = 'rates__item--win'?>
      <?php elseif ($time_left === false) :?>
            <?php $rates_class = 'rates__item--end'?>     
      <?php endif;?>
    <tr class="rates__item <?=$rates_class?>">
      <td class="rates__info">
        <div class="rates__img">
          <img src="<?=htmlspecialchars($bet['img_url'])?>" width="54" height="40"
               alt="<?=htmlspecialchars(str_getcsv($bet['lot_name'], " ")[0])?>">
        </div>
        <div>
            <h3 class="rates__title">
            <a href="lot.php?id=<?=intVal($bet['lot_id'])?>"><?=htmlspecialchars($bet['lot_name'])?></a>
            </h3>
            <?php if ($bet['winner_id'] === $user_id) :?>
            <p><?=htmlspecialchars($bet['contact'])?></p>
            <?php endif;?>
        </div>
      </td>
      <td class="rates__category">
        <?=htmlspecialchars($bet['cat_name']);?>
      </td>
      <td class="rates__timer">
        <?php if ($bet['winner_id'] === $user_id) :?>
        <div class="timer timer--win">
            <?='Ставка выиграла'?>
        <?php elseif ($time_left === false) :?>
        <div class="timer timer--end">
            <?='Торги окончены';?>
        <?php else :?>
        <div class="timer <?=($time_left[0] < 1) ? 'timer--finishing' : ''?>">
            <?=$time_left[2] .':' . $time_left[3]; ?>
        <?php endif;?>
        </div>
      </td>
      <td class="rates__price">
        <?=formatPrice(strip_tags($bet['price']))?>
      </td>
      <td class="rates__time">
        <?=getTimeStr($bet['dt_add'], $bet['dt_expired'])?>
      </td>
    </tr>
  <?php endforeach; ?>
  </table>
</section>
