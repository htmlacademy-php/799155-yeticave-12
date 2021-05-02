<main class="container">
    <section class="promo">
        <h2 class="promo__title">Нужен стафф для катки?</h2>
        <p class="promo__text">На нашем интернет-аукционе ты найдёшь самое эксклюзивное сноубордическое и горнолыжное снаряжение.</p>
        <ul class="promo__list">
            <!--заполните этот список из массива категорий-->
            <?php foreach ($cats as $cat): ?>
            <li class="promo__item promo__item--boards">
                <a class="promo__link" href="pages/all-lots.html"><?=htmlspecialchars($cat)?></a>
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
            <?php foreach ($lots as $lot): ?>
            <li class="lots__item lot">
                <div class="lot__image">
                    <img src="<?=htmlspecialchars($lot['url'])?>" width="350" height="260" alt="">
                </div>
                <div class="lot__info">
                    <span class="lot__category"><?=htmlspecialchars($lot['cat'])?></span>
                    <h3 class="lot__title"><a class="text-link" href="pages/lot.html"><?=htmlspecialchars($lot['name'])?></a></h3>
                    <div class="lot__state">
                        <div class="lot__rate">
                            <span class="lot__amount">Стартовая цена</span>
                            <span class="lot__cost"><?=format_price(strip_tags($lot['price']))?></span>
                        </div>
                        <?php $time_left = get_time_left(strip_tags($lot['expired'])); ?>
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
</main>
