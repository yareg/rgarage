<?php

/* @var $this yii\web\View */
use yii\helpers\Url;
use yii\web\JsExpression;

$this->title = 'RubyGarage Application';

?>
<?php // create url list for ajax queries ?>
<script type="text/javascript">
    var urlList = {
        "project": {
            "create": "<?=Url::toRoute('project/create'); ?>",
            "index": "<?=Url::toRoute('project/index'); ?>",
            "update": "<?=Url::toRoute(['project/update', 'id' => '{{id}}']); ?>",
            "delete": "<?=Url::toRoute(['project/delete', 'id' => '{{id}}']); ?>"
        },
        "task": {
            "create": "<?=Url::toRoute('task/create'); ?>",
            "index": "<?=Url::toRoute('task/index'); ?>",
            "update": "<?=Url::toRoute(['task/update', 'id' => '{{id}}']); ?>",
            "priorityUp": "<?=Url::toRoute(['task/priority-up', 'id' => '{{id}}']); ?>",
            "priorityDown": "<?=Url::toRoute(['task/priority-down', 'id' => '{{id}}']); ?>",
            "delete": "<?=Url::toRoute(['task/delete', 'id' => '{{id}}']); ?>"
        },
    }
</script>
<div class="site-index">

    <div class="jumbotron">
        <h1>My projects</h1>
    </div>

    <div class="body-content">
        <div class="row">
            <p class="text-center"><a id="new_project" class="btn btn-primary add-project" href="javascript:void(0)">Add TODO List &raquo;</a></p>
        </div>
    </div>
    <div id="template">
        <div class="row project-section">
            <div class="row project-title" data-project-id="">
                <div class="col-lg-1"><img src="images/icons/Text-Edit-icon.png" alt="" height="32" width="32" /></div>
                <div class="col-lg-9 project-name html_editable"></div>
                <div class="col-lg-2 control-box control-box-project">
                    <img src="images/icons/pencil-icon.png" alt="" class="edit" height="32" width="32" />
                    <img src="images/icons/edit-trash-icon.png" alt="" class="delete" height="32" width="32" />
                </div>
            </div>
            <div class="row project-task">
                <div class="col-lg-1"><img src="images/icons/Add-icon.png" class="add-task" alt="" height="32" width="32" /></div>
                <div class="col-lg-9"><input type="text" class="new-task-name" placeholder="Start typing here to create a task..." /></div>
                <div class="col-lg-2"><button type="button" class="btn btn-success new-task-btn">Add Task</button></div>
            </div>
            <div class="row task-item" data-task-id="" data-task-status-id="" data-task-deadline="">
                <div class="col-lg-1"><input type="checkbox" /></div>
                <div class="col-lg-9 task-title"></div>
                <div class="col-lg-2 control-box control-box-task">
                    <img src="images/icons/Arrow-Up-3-icon.png" alt="" class="ch-priority" height="16" width="16" />
                    <img src="images/icons/Editing-Edit-icon.png" alt="" class="edit" height="16" width="16" />
                    <img src="images/icons/Trash-full-icon.png" alt="" class="delete" height="16" width="16" />
                </div>
            </div>
        </div>
    </div>
    <input id="page" type="hidden" value="index" />
</div>
<?= yii\jui\Dialog::widget([
        'id' => 'dialog_confirm_delete',
        'clientOptions' => [
            'autoOpen' => false,
            'modal' => true,
            'width' => 500,
            'buttons' => [
                [
                    'text' => 'Yes',
                    'click' => new JsExpression('function(){
                        if ($(this).data(\'projectId\') !== undefined) {
                            deleteProject($(this).data(\'projectId\'));
                        } else if ($(this).data(\'taskId\') !== undefined) {
                            deleteTask($(this).data(\'taskId\'))
                        }
                        $(this).dialog("close");
                    }'),
                ],
                [
                    'text' => 'No',
                    'click' => new JsExpression('function(){$(this).dialog("close");}'),
                ]
            ]
        ]
    ]
); ?>
<?= yii\jui\Dialog::widget([
        'id' => 'dialog_smth_wrong',
        'clientOptions' => [
            'autoOpen' => false,
            'modal' => true,
            'title' => 'Something weng wrong...',
            'buttons' => [
                [
                    'text' => 'OK',
                    'click' => new JsExpression('function(){$(this).dialog("close");}'),
                ]
            ]
        ]
    ]
); ?>
<div id="dialog_create_update_project">
    <input type="text" class="dialog-edit" id="project_name_edit">
</div>
<?php $editTaskDialog = yii\jui\Dialog::begin([
    'id' => 'dialog_edit_task',
    'clientOptions' => [
        'autoOpen' => false,
        'modal' => true,
        'width' => 500,
        'title' => 'Task editing',
        'buttons' => [
            [
                'text' => 'OK',
                'click' => new JsExpression('function(){ editTask($(this).data(\'taskId\')); $(this).dialog("close");}'),
            ],
            [
                'text' => 'Cancel',
                'click' => new JsExpression('function(){$(this).dialog("close");}'),
            ],
        ]
    ]
]);
?>
<div class="row">
    <span>Task name: </span><input type="input" id="task-edit-name" />
</div>
<div class="row">
    <span>Status: </span><?=\yii\helpers\BaseHtml::dropDownList('task_status', '', $taskStatusList, ['id' => 'task-edit-status']); ?>
</div>
<div class="row">
    <span>Deadline: </span><?= \yii\jui\DatePicker::widget(['id' => 'task-edit-deadline', 'dateFormat' => 'dd.MM.yy'
    ]) ?>
</div>
<?php \yii\jui\Dialog::end() ?>
