$(document).ready(function () {
    if ($('#page').val() === 'index') {
        getProjectList();

        // initiate dialog
        jQuery('#dialog_create_update_project').dialog({
            "autoOpen": false,
            "modal": true,
            "width": 500,
            "buttons": [{
                "text": "Yes", "click": function () {
                    updateProject($(this).data('projectId'));
                    $(this).dialog("close");
                }
            }, {
                "text": "No", "click": function () {
                    $(this).dialog("close");
                }
            }]
        });
    }
});

/**
 * Acceptable values: head, toolbar, task
 */
function get_template(part) {
    var projectTemplate = $('#template').children().children();
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
        var projectTemplates = [];
        var $head = get_template('head');
        var $toolbar = get_template('toolbar');
        var $task = get_template('task');

        // process each project
        $.each(data, function (index, project) {
            var projectTemplate = {};
            // update html project form
            // set project ID
            $head.attr('data-project-id', index);
            // set project name - to display
            $head.find('div.project-name').html(project.name);
            projectTemplate.tasks = [];
            $.each(project.tasks, function (idx, task) {
                $task.find('div.task-title').html(task.task_description);
                projectTemplate.tasks.push($task.get(0).outerHTML);
            });
            projectTemplate.head = $head.get(0).outerHTML;
            projectTemplate.toolbar = $toolbar.get(0).outerHTML;
            projectTemplates.push(projectTemplate);
        });
        // draw existing projects
        if (projectTemplates.length) {
            for (var i in projectTemplates) {
                var projectHTML = '<div class="row project-section">' +
                    projectTemplates[i].head +
                    projectTemplates[i].toolbar;
                for (var j in projectTemplates[i].tasks) {
                    projectHTML += projectTemplates[i].tasks[j];
                }
                projectHTML += '</div>';

                $body = $('.body-content');
                // get last .project section
                $children = $body.children('.project-section');
                if ($children.length) {
                    $($children[$children.length - 1]).after(projectHTML);
                } else {
                    // insert first
                    $body.prepend(projectHTML);
                }
            }
        }
    });
}

function deleteProject(projectId) {
    var url = substitute_params(urlList.project.delete, {"id": projectId});
    $.ajax({
        "type": "POST",
        "url": url,
        "success": function (data) {
            if ('success' === data.status) {
                // delete on UI side
                var $project = ui_get_project_by_id(projectId).parent();
                $project.hide('slow', function () {
                    $project.remove()
                });
            }
        },
        "error": function () {
            $('#dialog_smth_wrong').dialog('open');
        }
    });
}

function updateProject(projectId) {
    var url = substitute_params(urlList.project.update, {"id": projectId});
    var projectName = $('#project_name_edit').val();
    $.ajax({
        type: "POST",
        url: url,
        data: {
            'Project[name]': projectName
        },
        success: function (data) {
            if ('success' === data.status) {
                // update on UI side
                ui_get_project_by_id(projectId).find('div.project-name').html(projectName);
            }
        },
        error: function () {
            $('#dialog_smth_wrong').dialog('open');
        }
    });
}

function ui_get_project_by_id(projectId) {
    return $('[data-project-id=' + projectId + ']');
}
function substitute_params(url, params) {
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
        .dialog('option', 'title', 'New project name')
        .data('projectId', projectId)
        .dialog('open');
});

$('#new_project').click(function () {

});