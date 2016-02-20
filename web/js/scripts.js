$(document).ready(function() {
    if ($('#page').val() === 'index') {
        getProjectList();
    }
});

/**
 * Acceptable values: head, toolbar, task
 */
function get_template(part) {
    var projectTemplate =$('#template').children().children();
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
    $.get(urlList.project.index, function(data) {
        var projectTemplates = [];
        var $head = get_template('head');
        var $toolbar=get_template('toolbar');
        var $task=get_template('task');

        // process each project
        $.each(data, function(index, project) {
            var projectTemplate = {};
            // update html project form
            // set project ID
            $head.attr('data-project-id', index);
            // set project name - to display
            $head.find('div.project-name').html(project.name);
            projectTemplate.tasks = [];
            $.each(project.tasks, function(idx, task) {
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
    var deleteUrl = decodeURIComponent(urlList.project.delete).replace('{{id}}', projectId);
    $.ajax({
        type: "POST",
        url: deleteUrl,
        success: function(data){
            if ('success' === data.status) {
                // delete on UI side
                $project=$('[data-project-id=' + projectId + ']').parent();
                $project.hide('slow', function() {
                    $project.remove()
                });
            }
        },
        error: function() {
            $('#dialog_smth_wrong').dialog('open');
        }
    });

}

// bind project controls
$('div.body-content').on('click', 'div.control-box-project .delete', function() {
    // set project name to dialog
    var projectId = $(this).closest('.project-title').attr('data-project-id');
    $('#dialog_confirm_delete')
        .dialog('option', 'title', 'Do you want to delete project ' + $(this).parent().prev('.project-name').html() + '?')
        .data('projectId', projectId)
        .dialog('open');
});
$('#new_project').click(function() {

});