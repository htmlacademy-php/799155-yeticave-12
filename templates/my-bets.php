
<?=$nav?>
<section class="rates container">
  <h2>Мои ставки</h2>
  <table class="rates__list">
  <?php foreach ($bets as $bet): ?>
    <?php $rates_class = '';?>
	<?php if ($bet['winner_id'] !== null): ?>
		<?php $rates_class = $bet['winner_id'] === $user_id ? 'rates__item--win':'rates__item--end' ?>
	<?endif;?>
	<tr class="rates__item <?=$rates_class?>">
	  <td class="rates__info">
		<div class="rates__img">
		  <img src="<?=htmlspecialchars($bet['img_url'])?>" width="54" height="40" alt="Сноуборд">
		</div>
		<div>
			<h3 class="rates__title">
			<a href="lot.php?id=<?=intVal($bet['lot_id'])?>"><?=htmlspecialchars($bet['lot_name'])?></a>
			</h3>
			<?php if ($bet['winner_id'] !== null and $bet['winner_id'] === $user_id):?>
			<p><?=htmlspecialchars($bet['contact'])?></p>
			<?php endif;?>
		</div>
	  </td>
	  <td class="rates__category">
		<?=htmlspecialchars($bet['cat_name']);?>
	  </td>
	  <td class="rates__timer">
        <?php $time_left = getTimeLeft(strip_tags($bet['dt_expired'])); ?>
	    <?php if ($bet['winner_id'] !== null and $bet['winner_id'] === $user_id) : ?>
		<div class="timer timer--win">
			<?='Ставка выиграла'?>
        <?php elseif ($time_left === false) : ?>
		<div class="timer timer--end">
            <?='Торги окончены';?>
        <?php else:?>
        <div class="timer <?php if ($time_left[0] < 1):?>timer--finishing <?php endif;?>">
            <?=$time_left[2] .':' . $time_left[3]; ?>
        <?php endif; ?>
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
