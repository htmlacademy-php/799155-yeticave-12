<?=$nav;?>
<div class="container">
	<section class="lots">
		<h2>Результаты поиска по запросу «<span><?=htmlspecialchars($search);?></span>»</h2>
		<?php if (count($lots) == 0): ?>
		<h2>По запросу ничего не найдено</h2>
		<?php endif; ?>
		<ul class="lots__list">
			<?php foreach ($lots as $lot): ?>
			<?php $bet = $bets[$lot['id']]['price']; 
			$price = intVal($lot['price']) > $bet ? intVal($lot['price']) : $bet;
			?>
			<li class="lots__item lot">
				<div class="lot__image">
					<img src="<?=htmlspecialchars($lot['img_url'])?>" width="350" height="260" alt="">
				</div>
				<div class="lot__info">
					<span class="lot__category"><?=htmlspecialchars($lot['cat_name'])?></span>
					<h3 class="lot__title"><a class="text-link" href="lot.php?id=<?=$lot['id']?>"><?=htmlspecialchars($lot['name'])?></a></h3>
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
						<div class="lot__timer timer timer--end">
							<?='Торги окончены';?>
						<?php else:?>
						<div class="lot__timer timer <?php if ($time_left[0] < 1):?>timer--finishing<?php endif;?>">
							<?=$time_left[2] .':' . $time_left[3]; ?>
						<?php endif; ?>
						</div>
					</div>
				</div>
			</li>
			<?php endforeach; ?>            
		</ul>
	</section>
</div>
