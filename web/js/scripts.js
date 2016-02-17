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
    $.get(urlList.project.index, function(data) {
        var projectTemplates = [];
        var $head = get_template('head');
        var $toolbar=get_template('toolbar');
        var $task=get_template('task');


        $.each(data, function(index, project) {
            var projectTemplate = {};
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
$('#new_project').click(function() {

});