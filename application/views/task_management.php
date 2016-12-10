<?php $this->load->view('frame/header_view'); ?>

        <div id="page-wrapper">
            <div class="row">
                <div class="col-lg-12 text-center">
                	<div class="col-lg-12 text-center menu-list">
                		<a href="#" class="btn btn-primary btn-primary" data-toggle='modal' data-target='#addTask'><span class="fa fa-plus"></span> Add Task</a>
                		<a href="<?=base_url();?>" class="btn btn-primary btn-primary"><span class="fa fa-tasks"></span> Refresh List</a>
                		<a href="#" id="tree-list-button" class="btn btn-primary btn-primary"><span class="fa fa-tasks"></span> Tree List</a>
                	</div>
                	<div class="col-lg-12 text-center">
                	<?php if($this->session->flashdata('success')): ?>
                        <div class="alert alert-success">
                        <a href="#" class="close" data-dismiss="alert">&times;</a>
                        <strong><?php echo $this->session->flashdata('success'); ?></strong>
                        </div>
                    <?php elseif($this->session->flashdata('error')): ?>
                        <div class="alert alert-warning">
                        <a href="#" class="close" data-dismiss="alert">&times;</a>
                        <strong><?php echo $this->session->flashdata('error'); ?></strong>
                        </div>
                    <?php endif; ?>
                	</div>
                </div>
                <div id="tree-list" class="col-lg-12 hide-div"></div>
                <div id="table-list" class="col-lg-12">
	                <div id="parent-task-list" class="col-lg-6 text-center">
		                <div class="col-lg-12 text-right">
			                <div class="col-lg-6">
			                	*click # child value to show child table
			                </div>
			                <div class="col-lg-6">
				                <select class="form-control" name="task-list-filter" id="task-list-filter">
									<option value="all">STATUS FILTER</option>
									<option value="0">IN PROGRESS</option>
									<option value="1">DONE</option>
									<option value="2">COMPLETED</option>
								</select>
							</div>
						</div>
						&nbsp;
	                 	<table class="table table-striped table-bordered table-hover">
                            <thead>
                                <tr>
                                	<th></th>
									<th>ID</th>
									<th>Name</th>
									<th>Parent ID</th>
									<th>Status</th>
									<th>#Child</th>
									<th>Done</th>
									<th>Completed</th>
								</tr>
                            </thead>
                            <tbody id="task-list-data"></tbody>
                        </table>
	                </div>
	                <div id="child-task-list" class="col-lg-6 text-center hide-div">
	                   <div class="col-lg-12 text-right">
			                <div class="col-lg-6">
			                	<label><a onclick="hide_child_list();">Hide Child Table</a></label>
			                </div>
			                <div class="col-lg-6">
				                <select class="form-control" name="task-list-filter-child" id="task-list-filter-child">
									<option value="all">STATUS FILTER</option>
									<option value="0">IN PROGRESS</option>
									<option value="1">DONE</option>
									<option value="2">COMPLETED</option>
								</select>
							</div>
						</div>
						&nbsp;
	                 	<table class="table table-striped table-bordered table-hover" >
                            <thead>
                                <tr>
                                	<th></th>
									<th>ID</th>
									<th>Name</th>
									<th>Parent ID</th>
									<th>Status</th>
									<th>#Child</th>
									<th>Done</th>
									<th>Completed</th>
								</tr>
                            </thead>
                            <tbody id="task-list-data-child"></tbody>
                        </table>
	                </div>
                </div>
                <!-- /.col-lg-12 -->
            </div>
            <!-- /.row -->
        </div>

		<!-- Add task modal -->
		<div class="modal fade" id="addTask" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header modal-primary">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h4 class="modal-title" id="myModalLabel">ADD TASK</h4>
                    </div>
                    <div class="modal-body">
	                    <div class='form-group'>
                            <div class="col-lg-3">
                                <label for="newTask-name" class="control-label">Task Name</label>
                            </div>
                            <div class="col-lg-9">
                            	<input class="form-control" type="text" name="newTask-name" id="newTask-name" value="" placeholder="Task Name" maxlength="255" />
                            </div>
                        </div><br><br><br>

                        <div class='form-group'>
                            <div class="col-lg-3">
                                <label for="newTask-parent-id" class="control-label">Parent ID</label>
                            </div>
                            <div class="col-lg-9">
                                <input class="form-control" type="text" name="newTask-parent-id" id="newTask-parent-id" value="" placeholder="Parent ID" maxlength="11" />
                            </div>
                        </div><br><br>

                        <div class='alert alert-danger  alert-dismissible hide-div' id='error-message-newTask-main' >
                            <div id='error-message-newTask-html' ></div>
                        </div>
				
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">CANCEL</button>
                        <button id="newTaskSubmit" type="button" class="btn btn-primary" >ADD</button>
                    </div>
                </div>
            </div>
        </div>





    </div>
    </div>
    <!-- /#page-wrapper -->
<?php $this->load->view('frame/footer_view'); ?>



