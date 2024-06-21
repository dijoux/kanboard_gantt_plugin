<section id="main">
    <?= $this->projectHeader->render($project, 'TaskGanttController', 'show', false, 'Gantt') ?>

    <div class="menu-inline">
        <ul>
            <li <?= $sorting === 'board' ? 'class="active"' : '' ?>>
                <?= $this->url->icon('sort-numeric-asc', t('Sort by position'), 'TaskGanttController', 'show', array('project_id' => $project['id'], 'sorting' => 'board', 'plugin' => 'Gantt')) ?>
            </li>
            <li <?= $sorting === 'date' ? 'class="active"' : '' ?>>
                <?= $this->url->icon('sort-amount-asc', t('Sort by date'), 'TaskGanttController', 'show', array('project_id' => $project['id'], 'sorting' => 'date', 'plugin' => 'Gantt')) ?>
            </li>
            <li>
                <?= $this->modal->large('plus', t('Add task'), 'TaskCreationController', 'show', array('project_id' => $project['id'])) ?>
            </li>
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

    <?php if (! empty($tasks)): ?>
        <div
            id="gantt-chart"
            data-records='<?= json_encode($tasks, JSON_HEX_APOS) ?>'
            data-save-url="<?= $this->url->href('TaskGanttController', 'save', array('project_id' => $project['id'], 'plugin' => 'Gantt')) ?>"
            data-label-start-date="<?= t('Start date:') ?>"
            data-label-end-date="<?= t('Due date:') ?>"
            data-label-assignee="<?= t('Assignee:') ?>"
            data-label-not-defined="<?= t('There is no start date or due date for this task.') ?>"
        ></div>
    <?php else: ?>
        <p class="alert"><?= t('There is no task in your project.') ?></p>
    <?php endif ?>
</section>
