
    /******************************************
    *         Task Management System          *
    *   Developer  :  rudiliucs1@gmail.com    *
    *        Copyright © 2015 Rudi Liu        *
    *******************************************/

(function($) {

    $(function() {

        $("#parent-task-list").removeClass("col-lg-6");
        $("#parent-task-list").addClass("col-lg-12");
        create_task_list('first','all','none');



        $('#newTaskSubmit').on('click', function(event) {
            new_task_init();
            data = {    
                        taskName:$('#newTask-name').val(), 
                        parentID:$('#newTask-parent-id').val(), 
                    };

            var prom = new_task(data);
            prom.then(function(result){
                var result = $.parseJSON(result);
                if(result.status=='success'){
                    location.reload();
                }
                else if(result.status=='error'){
                    new_task_error(result.message);
                }
                else{
                    alert("Oops there is something wrong!");
                }
            },
            function(err){
                alert('Oops there is something wrong! '+ err);
            });
        });



        // Show loading overlay when ajax request starts
        $( document ).ajaxStart(function() {
            $('.loading-overlay').show();
        });
        // Hide loading overlay when ajax request completes
        $( document ).ajaxStop(function() {
            $('.loading-overlay').hide();
        });

    });

    $( "#task-list-filter" ).click(function() {
        create_task_list('first',$(this).val(),'none');
    });

    $( "#task-list-filter-child" ).click(function() {
        create_task_list('first',$(this).val(),$("#current-child-parentID").val());
    });

    $( "#tree-list-button" ).click(function() {
        $("#tree-list").removeClass("hide-div");
        $("#table-list").addClass("hide-div");
        $.ajax({
            type:'POST',
            url: $("#base-url").val() + "task_management/tree_list",
            success:function(data) { 
                $( "#tree-list" ).html( data );
            }
          });
    });

})(jQuery);

    function hide_child_list(){
        $("#parent-task-list").removeClass("col-lg-6");
        $("#parent-task-list").addClass("col-lg-12");
        $("#child-task-list").addClass("hide-div");
    }


    function custom_task_list(page,filterStatus){
        create_task_list(page,filterStatus,'none');
    }

    function create_child_list(parentID){
        create_task_list('first','all',parentID);
        $("#parent-task-list").removeClass("col-lg-12");
        $("#parent-task-list").addClass("col-lg-6");
        $("#child-task-list").removeClass("hide-div");
    }

    function update_task_status_checkbox(taskID){
        inputID = "#task-edit-checkbox-"+taskID;
        numChildID = "#task-num-child-"+taskID;
        doneChildID = "#task-done-child-"+taskID;
        completedChildID = "#task-completed-child-"+taskID;
        if($(inputID).attr('checked')) {
            $(inputID).attr("checked",false);
            status = 0;
        }
        else{
            $(inputID).attr("checked",true);
            status = 1;
        }
        data = {
                    taskID:taskID, 
                    status:status,
                    numChild: $(numChildID).text().trim(),
                    doneChild: $(doneChildID).text().trim(),
                    completedChild: $(completedChildID).text().trim() 
                };
        var prom = update_task_status(data);
        prom.then(function(result){
            var result = $.parseJSON(result);
            if(result.status=='success'){
                create_task_list($("#current-page").val(),$("#current-status-filter").val(),'none');
                if($("#current-status-filter-child").val() != "")
                    create_task_list($("#current-page-child").val(),$("#current-status-filter-child").val(),$("#current-child-parentID").val());
                
            }
            else if(result.status=='error'){
                alert("Internal server error!");
            }
            else{
                alert("Internal server error!");
            }
        },
        function(err){
            alert('Internal server error! '+ err);
        });
    }

    function create_task_list(page,filterStatus, parentID){
        data = {page:page,filterStatus:filterStatus,parentID:parentID};
        var prom = task_list(data);
        prom.then(function(result){
            var result = $.parseJSON(result);
            var num = result.data.length;
            var contentDiv = '#'+result.contentDiv;
            $(contentDiv).html("");
            for(i=0; i< num; i++){

                var taskID = result.data[i]['id'];
                var currentParentID = result.data[i]['parent_id'];
                var currentStatus = result.data[i]['status'];

                currentStatus==0 ? check="":check="checked";
                (result.data[i]['child']==0 || currentStatus==0) ? disable="":disable="disabled";

                childOnclick = "";
                if(result.parentID == "none" || result.parentID == 0)
                    childOnclick = "onclick='create_child_list("+taskID+")'";
                
                
                $(contentDiv).append("<tr>"+
                                            "<td><input onclick='update_task_status_checkbox("+taskID+")' id='task-edit-checkbox-"+taskID+"' type='checkbox' "+disable+" "+check+"></td>"+
                                            "<td>"+result.data[i]['id']+"</td>"+
                                            "<td><a href='#'' id='task-edit-name-"+result.parentID+taskID+"' >"+result.data[i]['title']+"</a></td>"+
                                            "<td><a href='#'' id='task-edit-parent-"+result.parentID+taskID+"' >"+result.data[i]['parent_id']+"</a></td>"+
                                            "<td><text id='task-edit-status-"+result.parentID+taskID+"' >"+result.data[i]['status_name']+"</text></td>"+
                                            "<td id='task-num-child-"+taskID+"' "+childOnclick+" ><a>"+result.data[i]['child']+"</a></td>"+
                                            "<td id='task-done-child-"+taskID+"'>"+result.data[i]['child_done']+"</td>"+
                                            "<td id='task-completed-child-"+taskID+"'>"+result.data[i]['child_completed']+"</td>"+
                                            "</tr>");

                $('#task-edit-name-'+result.parentID+taskID).editable({
                    type: 'text',
                    pk: 1,
                    url: $('#base-url').val() + 'task_management/update_task_name/' + result.data[i]['id'],
                    title: 'Enter new task name:',
                    validate: function(value) {
                        if($.trim(value) == '') 
                            return 'Task name cannot be empty!';
                    }
                });

                $('#task-edit-parent-'+result.parentID+taskID).editable({
                    type: 'text',
                    pk: 1,
                    url: $('#base-url').val() + 'task_management/update_task_parent/' + taskID ,
                    title: 'Enter new parent ID:',
                    success: function(result){
                        var result = $.parseJSON(result);
                        if(result.status=='success'){
                            create_task_list($("#current-page").val(),$("#current-status-filter").val(),'none');
                            if($("#current-status-filter-child").val() != "")
                                create_task_list($("#current-page-child").val(),$("#current-status-filter-child").val(),$("#current-child-parentID").val());
                        }
                        else if(result.status=='error'){
                            return result.message;
                        }
                        else{
                            return 'Internal server error!';
                        }
                    },
                    error: function (response) {
                        return 'Internal server error!';
                    }
                });
            }

            if(result.parentID == 'none'){
                $("#current-parentID").val(result.parentID);
                $("#current-page").val(result.currentPage);
                $("#current-status-filter").val(result.currentStatusFilter);
            }else{
                $("#current-child-parentID").val(result.parentID);
                $("#current-page-child").val(result.currentPage);
                $("#current-status-filter-child").val(result.currentStatusFilter);
            }

            if(result.data==false)
                $(contentDiv).append("<tr><td colspan='8'>No Data</td></tr>");
            else
                $(contentDiv).append("<tr><td colspan='8'>"+result.links+"</td></tr>");

        },
        function(err){
            alert('Oops there is something wrong! '+ err); 
        });
    }


    function new_task_init(){
        $('#newTaskSubmit').prop("disabled",true);
        $('#error-message-newTask-main').hide();
    }

    function new_task_error(message){
        $('#error-message-newTask-html').html(message);
        $('#error-message-newTask-main').show();
        $('#newTaskSubmit').prop("disabled",false);
    }

    function new_task(data, successFn){
        var prom = $.ajax({
            url: $("#base-url").val() + "task_management/new_task",
            traditional: true,
            type: "post",
            dataType: "text",
            data: data,
        });
        return prom;
    }

    function task_list(data,successFn){
        var prom = $.ajax({
            url: $("#base-url").val() + "task_management/task_list",
            traditional: true,
            type: "post",
            dataType: "text",
            data: data,
        });
        return prom;
    }

    function update_task_status(data,successFn){
        var prom = $.ajax({
            url: $("#base-url").val() + "task_management/update_task_status",
            traditional: true,
            type: "post",
            dataType: "text",
            data: data,
        });
        return prom;
    }

    function is_parent_valid(data,successFn){
        var prom = $.ajax({
            url: $("#base-url").val() + "task_management/is_parent_valid",
            traditional: true,
            type: "post",
            dataType: "text",
            data: data,
        });
        return prom;
    }

    function ajax_error_handling(jqXHR, exception){
        if (jqXHR.status === 0) {
            alert('Not connect.\n Verify Network.');
        } else if (jqXHR.status == 404) {
            alert('Requested page not found. [404]');
        } else if (jqXHR.status == 500) {
            alert('Internal Server Error [500].');
        } else if (exception === 'parsererror') {
            alert('Requested JSON parse failed.');
        } else if (exception === 'timeout') {
            alert('Time out error.');
        } else if (exception === 'abort') {
            alert('Ajax request aborted.');
        } else {
            alert('Uncaught Error.\n' + jqXHR.responseText);
        }
        hide_loading();
    }
