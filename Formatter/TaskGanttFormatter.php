<?php

namespace Kanboard\Plugin\Gantt\Formatter;

use Kanboard\Formatter\BaseFormatter;
use Kanboard\Core\Filter\FormatterInterface;

/**
 * Task Gantt Formatter
 *
 * @package formatter
 * @author  Frederic Guillot
 */
class TaskGanttFormatter extends BaseFormatter implements FormatterInterface
{
    /**
     * Local cache for project columns
     *
     * @access private
     * @var array
     */
    private $columns = array();

    /**
     * Apply formatter
     *
     * @access public
     * @return array
     */
    public function format()
    {
        $bars = array();

        foreach ($this->query->findAll() as $task) {
            $bars[] = $this->formatTask($task);
        }

        return $bars;
    }

    /**
     * Format a single task
     *
     * @access private
     * @param  array  $task
     * @return array
     */
    private function formatTask(array $task)
    {
        if (! isset($this->columns[$task['project_id']])) {
            $this->columns[$task['project_id']] = $this->columnModel->getList($task['project_id']);
        }

        $now = time();
        $start = $task['date_started'] ?: $now;
        $end = $task['date_due'] ?: $now;

        if($start - $end > 0 && $start == $now) {
            $start = $end;
        } elseif($start - $end > 0 && $end == $now) {
            $end = $start;
        }

        $subtasks_str = "<table>";
        foreach($this->subtaskModel->getAll($task['id']) as $subtask) {
            $subtasks_str .= '<tr>';
            $subtasks_str .= '<td>' . t($subtask['status_name']) . '</td>';
            $subtasks_str .= '<td>' . $subtask['title'] . '</td>';
            $subtasks_str .= '<td>' . $subtask['name'] . '</td>';
            $subtasks_str .= '</tr>';
        }
        $subtasks_str .= "</table>";

        return array(
            'id' => $task['id'],
            'name' => $task['title'],
            'start' => date('Y-m-d', $start),
            'end' => date('Y-m-d', $end),
            'progress' => ((!empty($task['nb_subtasks'])) ? round($task['nb_completed_subtasks'] / $task['nb_subtasks'] * 100, 0) : '0'),
            'custom_class' => 'bar-color-' . $task['color_id'],
            'popup' => '<div class="title">' . $task['title'] . '<br>' . $task['assignee_name'] . '</div><div class="subtitle"><div class="subtitle-info"><strong>' . t("Start") . ':</strong> ' . date(t("Y-m-d"), $start) . '<br><strong>' . t("End") . ':</strong> ' . date(t("Y-m-d"), $end) . '<br>' . t("%s%% completed", ((!empty($task['nb_subtasks'])) ? round($task['nb_completed_subtasks'] / $task['nb_subtasks'] * 100, 0) : '0')) . '<br></div><div class="subtitle-button"><span class="button">' . $this->helper->url->link('<i class="fa fa-eye"></i> ' . t('Show'), 'TaskViewController', 'show', array('task_id' => $task['id'])) . '</span><span class="button">' . ($this->helper->projectRole->canUpdateTask($task) ? '&nbsp;' . $this->helper->modal->large('edit', t('Edit'), 'TaskModificationController', 'edit', array('task_id' => $task['id'])) : '') . '</span></div><div class="markdown">' . $this->helper->text->markdown($task['description']) . '</div><div class="subtitle-subtasks">' . $subtasks_str . '</div></div>',
        );
    }
}
