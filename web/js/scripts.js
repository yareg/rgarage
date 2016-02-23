$(document).ready(function () {
    if ($('#page').val() === 'index') {
        getProjectList();

        // initiate dialog
        jQuery('#dialog_create_update_project').dialog({
            "autoOpen": false,
            "modal": true,
            "width": 500,
            "buttons": [{
                "text": "OK", "click": function () {
                    // determine create or update project we need
                    if (false === $(this).data('projectId')) {
                        // create
                        updateProject();
                    } else {
                        // update
                        updateProject($(this).data('projectId'));
                    }

                    $(this).dialog("close");
                }
            }, {
                "text": "Cancel", "click": function () {
                    $(this).dialog("close");
                }
            }]
        });
    }
});

/**
 * Acceptable values: head, toolbar, task
 */
function getTemplate(part) {
    var projectTemplate = $('#template').children().children().clone();
    if ('head' === part) {
        return $(projectTemplate[0]);
    } else if ('toolbar' === part) {
        return $(projectTemplate[1]);
    } else if ('task' === part) {
        return $(projectTemplate[2]);
    }
}

function getProjectList() {
    // get project list
    $.get(urlList.project.index, function (data) {
        var projectTemplates = createProjectHtmlSections(data);
        // draw existing projects
        if (projectTemplates.length) {
            for (var i in projectTemplates) {
                projectHTML = createProjectHtml(projectTemplates[i].head, projectTemplates[i].toolbar, projectTemplates[i].tasks);
                drawProject(projectHTML);
            }
        }
    });
}

/**
 * Substitutes data into project html templates
 * @param data
 * @returns {Array}
 */
function createProjectHtmlSections(data) {
    var projectTemplates = [];
    var $head = getTemplate('head');
    var $toolbar = getTemplate('toolbar');
    var $task = getTemplate('task');

    // process each project
    $.each(data, function (index, project) {
        project.tasks = project.tasks || [];
        var projectTemplate = {};
        // update html project form
        // set project ID
        $head.attr('data-project-id', index);
        // set project name - to display
        $head.find('div.project-name').html(project.name);
        projectTemplate.tasks = [];
        $.each(project.tasks, function (idx, task) {
            $task.attr('data-task-id', task.task_id);
            $task.attr('data-task-status-id', task.task_status_id);
            $task.attr('data-task-deadline', task.task_deadline ? task.task_deadline : "");
            $task.find('div.task-title').html(task.task_description);
            projectTemplate.tasks.push($task.get(0).outerHTML);
        });
        projectTemplate.head = $head.get(0).outerHTML;
        projectTemplate.toolbar = $toolbar.get(0).outerHTML;
        projectTemplates.push(projectTemplate);
    });

    return projectTemplates;
}
/**
 * Generates HTML for project to display om page
 * @param string head
 * @param string toolbar
 * @param string tasks
 * @returns string
 */
function createProjectHtml(head, toolbar, tasks) {
    tasks = tasks || [];
    var projectHTML = '<div class="row project-section">' + head + toolbar;
    for (var j in tasks) {
        projectHTML += tasks[j];
    }
    projectHTML += '</div>';

    return projectHTML;
}

/**
 * Draw project HTML
 *
 * @param string html
 */
function drawProject(html) {
    $body = $('.body-content');
    // get last .project section
    $children = $body.children('.project-section');
    if ($children.length) {
        $($children[$children.length - 1]).after(html);
    } else {
        // insert first
        $body.prepend(html);
    }
}

function deleteProject(projectId) {
    var url = substituteParams(urlList.project.delete, {"id": projectId});
    $.ajax({
        "type": "POST",
        "url": url,
        "success": function (data) {
            if ('success' === data.status) {
                // delete on UI side
                var $project = uiGetProjectById(projectId).parent();
                $project.hide('slow', function () {
                    $project.remove();
                });
            }
        },
        "error": function () {
            $('#dialog_smth_wrong').dialog('open');
        }
    });
}

function updateProject(projectId) {
    var update = (undefined === projectId) ? false : true;
    if (!update) {
        // create
        var url = urlList.project.create;
    } else {
        // update
        var url = substituteParams(urlList.project.update, {"id": projectId});
    }
    var projectName = $('#project_name_edit').val();
    $.ajax({
        type: "POST",
        url: url,
        data: {
            'Project[name]': projectName
        },
        success: function (data) {
            if ('success' === data.status) {
                if (!update) {
                    // generate HTML
                    var projectHTML = createProjectHtmlSections(data.project);
                    projectHTML = createProjectHtml(projectHTML[0].head, projectHTML[0].toolbar);
                    // display on page
                    drawProject(projectHTML);
                }else {
                    // update on UI side
                    uiGetProjectById(projectId).find('div.project-name').html(projectName);
                }
            }
        },
        error: function () {
            $('#dialog_smth_wrong').dialog('open');
        }
    });
}

function deleteTask(taskId) {
    var url = substituteParams(urlList.task.delete, {"id": taskId});
    $.ajax({
        "type": "POST",
        "url": url,
        "success": function (data) {
            if ('success' === data.status) {
                // delete on UI side
                var $task = uiGetTaskById(taskId);
                $task.hide('slow', function () {
                    $task.remove();
                });
            }
        },
        "error": function () {
            $('#dialog_smth_wrong').dialog('open');
        }
    });
}

function editTask(taskId) {
    var url = substituteParams(urlList.task.update, {"id": taskId});
    var name = $('#task-edit-name').val();
    var statusId = $('#task-edit-status').val();
    var deadline = $('#task-edit-deadline').datepicker('getDate') ? $('#task-edit-deadline').datepicker('getDate')/1000 : null;

    $.ajax({
        "type": "POST",
        "url": url,
        "data": {
            "Task[description]": name,
            "Task[status_id]": statusId,
            "Task[dt_deadline]": deadline
        },
        "success": function (data) {
            if ('success' === data.status) {
                // update on UI side
                var $task = uiGetTaskById(taskId);
                $task.find('.task-title').html(data.task.description);
                $task.attr('data-task-status-id', data.task.status_id);
                $task.attr('data-task-deadline', data.task.dt_deadline);
            }
        },
        "error": function () {
            $('#dialog_smth_wrong').dialog('open');
        }
    });
}

function uiGetProjectById(projectId) {
    return $('[data-project-id=' + projectId + ']');
}

function uiGetTaskById(taskId) {
    return $('[data-task-id=' + taskId + ']');
}
function substituteParams(url, params) {
    var result = decodeURIComponent(url);
    $.each(params, function(key, value) {
        result = result.replace('{{' + key + '}}', value);
    });
    return result;
}

// bind project controls
$('div.body-content').on('click', 'div.control-box-project .delete', function () {
    var projectId = $(this).closest('.project-title').attr('data-project-id');
    // set project name to dialog
    $('#dialog_confirm_delete')
        .dialog('option', 'title', 'Do you want to delete project ' + $(this).parent().prev('.project-name').html() + '?')
        .data('projectId', projectId)
        .dialog('open');
});
$('div.body-content').on('click', 'div.control-box-project .edit', function () {
    var projectId = $(this).closest('.project-title').attr('data-project-id');
    // set project name to dialog
    $('#project_name_edit').val($(this).parent().prev('.project-name').html());
    $('#dialog_create_update_project')
        .dialog('option', 'title', 'Project editing')
        .data('projectId', projectId)
        .dialog('open');
});
// bind new task button
$('div.body-content').on('click', 'div.project-task .new-task-btn', function () {
    $projectTask = $(this).closest('.project-task');
    var projectId = $projectTask.prev().attr('data-project-id');
    var $taskInput = $projectTask.find('input.new-task-name');
    var taskName = $taskInput.val();
    $.ajax({
        type: "POST",
        url: urlList.task.create,
        data: {
            'Task[project_id]': projectId,
            'Task[description]': taskName
        },
        success: function (data) {
            if ('success' === data.status) {
                var $taskTemplate = getTemplate('task');
                $taskTemplate.attr('data-task-id', data.task.id);
                $taskTemplate.find('div.task-title').html(data.task.description);
                // get last existing task to append to
                var existingTasks = $projectTask.parent().find('.task-item');
                if (existingTasks.length) {
                    $taskTemplate.insertAfter(existingTasks[existingTasks.length - 1]);
                } else {
                    // append as first task
                    $taskTemplate.insertAfter($projectTask);
                }
                // clear field on UI
                $taskInput.val('');
            }
        },
        error: function () {
            $('#dialog_smth_wrong').dialog('open');
        }
    });
});
// bind delete task button
$('div.body-content').on('click', 'div.task-item .delete', function () {
    var taskId   = $(this).closest('.task-item').attr('data-task-id');
    $('#dialog_confirm_delete')
        .dialog('option', 'title', 'Do you want to delete selected task?')
        .data('taskId', taskId)
        .dialog('open');
});
// bind edit task button
$('div.body-content').on('click', 'div.task-item .edit', function () {
    var $taskItem = $(this).closest('.task-item');
    var taskId   = $taskItem.attr('data-task-id');
    var deadline = $taskItem.attr('data-task-deadline');
    // set current value
    $('#task-edit-name').val($taskItem.find('div.task-title').html());
    // set status
    $('#task-edit-status').val($taskItem.attr('data-task-status-id'));
    console.log($taskItem.attr('data-task-deadline'));
    // set deadline
    if (deadline) {
        $('#task-edit-deadline').datepicker('setDate', new Date(deadline*1000));
    } else {
        $('#task-edit-deadline').datepicker('setDate', null);
    }
    // open
    $('#task-edit-deadline').datepicker
    $('#dialog_edit_task')
        .data('taskId', taskId)
        .dialog('open');
});
// bind priority task button
$('div.body-content').on('click', 'div.task-item .ch-priority', function (e) {
    const ARROW_UP = 1;
    const ARROW_DOWN = 2;

    var $taskItem = $(this).closest('.task-item');
    var taskId   = $taskItem.attr('data-task-id');
    var offset = $(this).offset();
    offset = e.pageY - offset.top;
    var direction = (offset < 10) ? ARROW_UP : ARROW_DOWN;
    //substituteParams(urlList.task.update, {"id": taskId})
    var url = (ARROW_UP == direction) ? urlList.task.priorityUp : urlList.task.priorityDown;
    url = substituteParams(url, {"id": taskId});
    $.ajax({
        "type": "POST",
        "url": url,
        "success": function (data) {
            if ('success' === data.status) {
                // update on UI side
                if (ARROW_UP == direction) {
                    $taskItem.insertBefore($taskItem.prev())
                } else if (ARROW_DOWN == direction) {
                    $taskItem.insertAfter($taskItem.next())
                }
            }
        },
        "error": function () {
            $('#dialog_smth_wrong').dialog('open');
        }
    });
});


$('#new_project').click(function () {
    $('#project_name_edit').val('');
    $('#dialog_create_update_project')
        .dialog('option', 'title', 'New project name')
        // to correctly recognize set variable or no
        .data('projectId', false)
        .dialog('open');
});