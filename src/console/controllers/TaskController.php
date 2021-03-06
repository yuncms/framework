<?php
/**
 * @link http://www.tintsoft.com/
 * @copyright Copyright (c) 2012 TintSoft Technology Co. Ltd.
 * @license http://www.tintsoft.com/license/
 */

namespace yuncms\console\controllers;

use yii\console\Controller;
use yii\console\ExitCode;
use yuncms\models\Task;

/**
 * Class CronController
 *
 * * * * * * cd /path/to && ./yii task/index >/dev/null 2>&1
 *
 * @author jlb
 *
 * @property float $currentTime
 */
class TaskController extends Controller
{
    public $defaultAction = 'run';

    /**
     * 定时任务入口
     * @return int Exit code
     */
    public function actionRun()
    {
        $crontab = Task::findAll(['switch' => Task::SWITCH_ACTIVE]);
        $tasks = [];

        foreach ($crontab as $task) {
            // 第一次运行,先计算下次运行时间
            if (!$task->next_rundate) {
                $task->next_rundate = $task->getNextRunDate();
                $task->save(false);
                continue;
            }
            // 判断运行时间到了没
            if ($task->next_rundate <= date('Y-m-d H:i:s')) {
                $tasks[] = $task;
            }
        }

        $this->executeTask($tasks);
        return ExitCode::OK;
    }

    /**
     * @param  array $tasks 任务列表
     */
    public function executeTask(array $tasks)
    {

        $pool = [];
        $startExecTime = $this->getCurrentTime();

        foreach ($tasks as $task) {

            $pool[] = proc_open("php yii $task->route", [], $pipe);
        }

        // 回收子进程
        while (count($pool)) {
            foreach ($pool as $i => $result) {
                $etat = proc_get_status($result);
                if ($etat['running'] == FALSE) {
                    proc_close($result);
                    unset($pool[$i]);
                    # 记录任务状态
                    $tasks[$i]->exectime = round($this->getCurrentTime() - $startExecTime, 2);
                    $tasks[$i]->last_rundate = date('Y-m-d H:i');
                    $tasks[$i]->next_rundate = $tasks[$i]->getNextRunDate();
                    $tasks[$i]->status = 0;
                    // 任务出错
                    if ($etat['exitcode'] !== ExitCode::OK) {
                        $tasks[$i]->status = 1;
                    }

                    $tasks[$i]->save(false);
                }
            }
        }
    }

    /**
     * 获取当前时间
     * @return float
     */
    private function getCurrentTime()
    {
        list ($msec, $sec) = explode(" ", microtime());
        return (float)$msec + (float)$sec;
    }
}