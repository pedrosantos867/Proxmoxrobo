<? if (!$hide) { ?>
    <nav class="text-center">
        <ul class="pagination">

            <li <?= (($current - 1) <= 0 ? 'class="disabled"' : '') ?>>
                <a href="#" class="change-page" data-page="<?= (($current) <= 1 ? $current : ($current - 1)) ?>"
                   aria-label="Previous">
                    <span aria-hidden="true">&laquo;</span>
                </a>
            </li>
            <? $show = 3; ?>
            <? for ($i = 1; $i < $pages + 1; $i++) { ?>
                <? if (abs($current - $i) > $show) { ?>
                    <? continue; ?>
                <? } ?>

                <li <?= ($current == $i ? 'class="active"' : '') ?>>
                    <a href="#" class="change-page" data-page="<?= $i ?>">
                        <span><?= $i ?> </span>
                    </a>
                </li>

            <? } ?>
            <li <?= (($current) > $pages ? 'class="disabled"' : '') ?>>
                <a href="#"
                   aria-label="Next" class="change-page"
                   data-page="<?= (($current) > $pages ? $current : ($current + 1)) ?>">
                    <span aria-hidden="true">&raquo;</span>
                </a>
            </li>
        </ul>
    </nav>
<? } ?>