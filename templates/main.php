<section class="promo">
    <h2 class="promo__title">Нужен стафф для катки?</h2>
    <p class="promo__text">На нашем интернет-аукционе ты найдёшь самое эксклюзивное
    сноубордическое и горнолыжное снаряжение.</p>
    <ul class="promo__list">
        <!--заполните этот список из массива категорий-->
        <?php foreach ($cats as $cat) :?>
        <li class="promo__item promo__item--<?=htmlspecialchars($cat['code'])?>">
            <a class="promo__link" href="all-lots.php?cat=<?=intVal($cat['id'])?>">
            <?=htmlspecialchars($cat['name'])?></a>
        </li>
        <?php endforeach; ?>
    </ul>
</section>
<section class="lots">
    <div class="lots__header">
        <h2>Открытые лоты</h2>
    </div>
    <ul class="lots__list">
        <!--заполните этот список из массива с товарами-->
        <?php foreach ($lots as $lot) :?>
            <?php $price = (intVal($lot['price']) > $bets[$lot['id']]['price']) ?
            intVal($lot['price']) : $bets[$lot['id']]['price'];?>
        <li class="lots__item lot">
            <div class="lot__image">
                <img src="<?=htmlspecialchars($lot['img_url'])?>" width="350" height="260" alt="">
            </div>
            <div class="lot__info">
                <span class="lot__category"><?=htmlspecialchars($lot['cat_name'])?></span>
                <h3 class="lot__title"><a class="text-link" href="lot.php?id=<?=intVal($lot['id'])?>">
                <?=htmlspecialchars($lot['name'])?></a>
                </h3>
                <div class="lot__state">
                    <div class="lot__rate">
                    <?php if ($lot['bets_count'] === 0) :?>
                    <span class="lot__amount">Стартовая цена</span>
                    <?php else :?>
                    <span class="lot__amount">
                        <?=$lot['bets_count'] .
                        getNounPluralForm($lot['bets_count'], ' ставка', ' ставки', ' ставок')?>
                    </span>
                    <?php endif;?>
                    <span class="lot__cost"><?=formatPrice(strip_tags($price))?></span>
                    </div>
                    <?php $time_left = getTimeLeft(strip_tags($lot['dt_expired'])); ?>
                    <?php if ($time_left === false) : ?>
                    <div class="lot__timer timer timer--end">
                        <?='Торги окончены';?>
                    <?php else :?>
                    <div class="lot__timer timer <?=($time_left[0] < 1) ? 'timer--finishing' : '';?>">
                        <?=$time_left[2] .':' . $time_left[3]; ?>
                    <?php endif; ?>
                    </div>
                </div>
            </div>
        </li>
        <?php endforeach; ?>
    </ul>
</section>
