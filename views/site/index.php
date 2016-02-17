<?php

/* @var $this yii\web\View */
use yii\helpers\Url;

$this->title = 'RubyGarage Application';

?>
<?php // create url list for ajax queries ?>
<script type="text/javascript">
    var urlList = {
        "project": {
            "create": "<?=Url::toRoute('project/create'); ?>",
            "index": "<?=Url::toRoute('project/index'); ?>",
            "update": "<?=Url::toRoute(['project/update', 'id' => ':id']); ?>",
            "delete": "<?=Url::toRoute(['project/delete', 'id' => ':id']); ?>"
        },
        "task": {
            "create": "<?=Url::toRoute('task/create'); ?>",
            "index": "<?=Url::toRoute('task/index'); ?>",
            "update": "<?=Url::toRoute(['task/update', 'id' => ':id']); ?>",
            "delete": "<?=Url::toRoute(['task/delete', 'id' => ':id']); ?>"
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
            <div class="row project-title">
                <div class="col-lg-1"><img src="images/icons/Text-Edit-icon.png" alt="" height="32" width="32" /></div>
                <div class="col-lg-9 project-name">Complete the task</div>
                <div class="col-lg-2 control-box control-box-project">
                    <img src="images/icons/pencil-icon.png" alt="" height="32" width="32" />
                    <img src="images/icons/edit-trash-icon.png" alt="" height="32" width="32" />
                </div>
            </div>
            <div class="row project-task">
                <div class="col-lg-1"><img src="images/icons/Add-icon.png" class="add-task" alt="" height="32" width="32" /></div>
                <div class="col-lg-9"><input type="text" placeholder="Start typing here to create a task..." /></div>
                <div class="col-lg-2"><button type="button" class="btn btn-success">Add Task</button></div>
            </div>
            <div class="row task-item">
                <div class="col-lg-1"><input type="checkbox" /></div>
                <div class="col-lg-9 task-title">Task description</div>
                <div class="col-lg-2 control-box control-box-task">
                    <img src="images/icons/Arrow-Up-3-icon.png" alt="" height="16" width="16" />
                    <img src="images/icons/Editing-Edit-icon.png" alt="" height="16" width="16" />
                    <img src="images/icons/Trash-full-icon.png" alt="" height="16" width="16" />
                </div>
            </div>
        </div>
    </div>
    <input id="page" type="hidden" value="index" />
</div>
