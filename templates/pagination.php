<?php if ($pages_count > 1) :?>
<ul class="pagination-list">
    <?php if ($cur_page > 1) :?>
        <?php $prev_page = $cur_page - 1;?>
    <?php endif;?>
    <?php if ($cur_page < $pages_count) :?>
        <?php $next_page = $cur_page + 1;?>
    <?php endif;?>
    <li class="pagination-item pagination-item-prev">
        <a <?=($cur_page > 1) ? "href=$url?page=$prev_page" : ""?>>Назад</a>
    </li>
    <?php foreach ($pages as $page) : ?>
    <li class="pagination-item <?=($page == $cur_page) ? 'pagination-item-active' : ''?>">
        <a <?=($page != $cur_page) ? "href=$url?page=$page":""?>><?=$page?></a>
    </li>   
    <?php endforeach; ?>
    <li class="pagination-item pagination-item-next">
        <a <?=($cur_page < $pages_count) ? "href=$url?page=$next_page" : ""?>>Вперед</a>
    </li>
</ul>
<?php endif;?>
