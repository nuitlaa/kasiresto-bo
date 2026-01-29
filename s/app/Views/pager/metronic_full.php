<?php
$pager->setSurroundCount(2);
?>
<nav aria-label="<?= lang('Pager.pageNavigation') ?>">
    <ul class="pagination pagination-sm">
        <li class="page-item<?= $pager->hasPrevious() ? '' : ' disabled' ?>">
            <a class="page-link" href="<?= $pager->getFirst() ?>" aria-label="<?= lang('Pager.first') ?>">&laquo;</a>
        </li>
        <li class="page-item<?= $pager->hasPrevious() ? '' : ' disabled' ?>">
            <a class="page-link" href="<?= $pager->getPrevious() ?>" aria-label="<?= lang('Pager.previous') ?>">&lsaquo;</a>
        </li>

        <?php foreach ($pager->links() as $link) : ?>
            <li class="page-item<?= $link['active'] ? ' active' : '' ?>">
                <a class="page-link" href="<?= $link['uri'] ?>"><?= $link['title'] ?></a>
            </li>
        <?php endforeach ?>

        <li class="page-item<?= $pager->hasNext() ? '' : ' disabled' ?>">
            <a class="page-link" href="<?= $pager->getNext() ?>" aria-label="<?= lang('Pager.next') ?>">&rsaquo;</a>
        </li>
        <li class="page-item<?= $pager->hasNext() ? '' : ' disabled' ?>">
            <a class="page-link" href="<?= $pager->getLast() ?>" aria-label="<?= lang('Pager.last') ?>">&raquo;</a>
        </li>
    </ul>
</nav>
