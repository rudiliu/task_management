<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Task Management System</title>

    <!-- Bootstrap Core CSS -->
    <link href="<?=base_url()?>assets/css/bootstrap.min.css" rel="stylesheet">

    <!-- MetisMenu CSS -->
    <link href="<?=base_url()?>assets/css/metisMenu.min.css" rel="stylesheet">

    <!-- DataTables CSS -->
    <link href="<?=base_url()?>assets/css/dataTables.bootstrap.css" rel="stylesheet">

    <link href="<?=base_url()?>assets/css/bootstrap-editable.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="<?=base_url()?>assets/css/main.css" rel="stylesheet">

    <!-- Custom Fonts -->
    <link href="<?=base_url()?>assets/css/font-awesome.min.css" rel="stylesheet" type="text/css">

    <!-- Custom tab icons -->
    <link rel="shortcut icon" href="<?=base_url()?>assets/images/favicon.ico" type="image/x-icon">

    <link href="<?=base_url()?>assets/js/jquery-ui-1.11.4.custom/jquery-ui.css" rel="stylesheet" type="text/css" />
    <link href="<?=base_url()?>assets/js/jquery-ui-1.11.4.custom/jquery-ui-custom-datepicker.css" rel="stylesheet" type="text/css" />



    <input type="hidden"  id="base-url" value="<?=base_url()?>"/>
    <input type="hidden"  id="current-page" value=""/>
    <input type="hidden"  id="current-status-filter" value=""/>
    <input type="hidden"  id="current-page-child" value=""/>
    <input type="hidden"  id="current-parentID" value=""/>
    <input type="hidden"  id="current-status-filter-child" value=""/>
    <input type="hidden"  id="current-child-parentID" value=""/>
    

    <style>
    .inline-block{display:inline-block;}
    </style>

</head>

<body>

    <div id="wrapper">

        <!-- Navigation -->
        <nav class="navbar navbar-inverse navbar-static-top text-center" role="navigation" style="margin-bottom: 0">
            <div class="navbar-header">
                <a class="navbar-brand" href="<?=base_url();?>">
                <div class="inline"> Welcome to Streamfream Task Management System </div>
                </a>

            </div>
        </nav>
            <!-- /.navbar-header -->
            
            
            <!-- /.navbar-top-links -->