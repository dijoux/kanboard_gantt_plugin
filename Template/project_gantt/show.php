<section id="main">
    <div class="page-header">
        <ul>
            <?php if ($this->user->hasAccess('ProjectCreationController', 'create')): ?>
                <li>
                    <?= $this->modal->medium('plus', t('New project'), 'ProjectCreationController', 'create') ?>
                </li>
            <?php endif ?>
            <?php if ($this->app->config('disable_private_project', 0) == 0): ?>
                <li>
                    <?= $this->modal->medium('lock', t('New private project'), 'ProjectCreationController', 'createPrivate') ?>
                </li>
            <?php endif ?>
            <li>
                <?= $this->url->icon('folder', t('Projects list'), 'ProjectListController', 'show') ?>
            </li>
            <?php if ($this->user->hasAccess('ProjectUserOverviewController', 'managers')): ?>
                <li>
                    <?= $this->url->icon('user', t('Users overview'), 'ProjectUserOverviewController', 'managers') ?>
                </li>
            <?php endif ?>
        </ul>
    </div>
    
    <div class="table-list-header">
        <ul class="gantt views">
            <li class="gantt-change-mode" data-mode-view="Day">
                <?php echo t("Day"); ?>
            </li>
            <li class="gantt-change-mode" data-mode-view="Week">
                <?php echo t("Week"); ?>
            </li>
            <li class="gantt-change-mode active" data-mode-view="Month">
                <?php echo t("Month"); ?>
            </li>
            <li class="gantt-change-mode" data-mode-view="Year">
                <?php echo t("Year"); ?>
            </li>
        </ul>
    </div>
    
    <section>
        <?php if (empty($projects)): ?>
            <p class="alert"><?= t('No project') ?></p>
        <?php else: ?>
            <svg id="gantt-chart" data-records='<?= json_encode($projects, JSON_HEX_APOS) ?>'></svg>
        <?php endif ?>
    </section>
</section>
