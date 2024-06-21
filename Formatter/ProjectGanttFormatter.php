<?php

namespace Kanboard\Plugin\Gantt\Formatter;

use Kanboard\Core\Filter\FormatterInterface;
use Kanboard\Formatter\BaseFormatter;

/**
 * Gantt chart formatter for projects
 *
 * @package  formatter
 * @author   Frederic Guillot
 */
class ProjectGanttFormatter extends BaseFormatter implements FormatterInterface
{
    /**
     * Format projects to be displayed in the Gantt chart
     *
     * @access public
     * @return array
     */
    public function format()
    {
        $projects = $this->query->findAll();
        $colors = $this->colorModel->getDefaultColors();
        $bars = array();

        foreach ($projects as $project) {
            $start = empty($project['start_date']) ? time() : strtotime($project['start_date']);
            $end = empty($project['end_date']) ? $start : strtotime($project['end_date']);
            $color = next($colors) ?: reset($colors);

            $bars[] = array(
                'id' => $project['id'],
                'name' => $project['name'],
                'start' => date('Y-m-d', $start),
                'end' => date('Y-m-d', $end),
                'popup' => '<div class="title">' . $project['name'] . '</div><div class="subtitle"><strong>' . t("Start") . ':</strong> ' . date(t("Y-m-d"), $start) . '<br><strong>' . t("End") . ':</strong> ' . date(t("Y-m-d"), $end) . '<br><br><a class="button" href="' . $this->helper->url->href('BoardViewController', 'show', array('project_id' => $project['id'])) . '">' . t("Board") . '</a>&nbsp;<a class="button" href="' . $this->helper->url->href('TaskGanttController', 'show', array('project_id' => $project['id'], 'plugin' => 'Gantt')) . '">' . t("Gantt") . '</a></div>',
            );
        }

        return $bars;
    }
}
