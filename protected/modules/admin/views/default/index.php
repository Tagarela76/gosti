<?php
/* @var $this DefaultController */


$this->breadcrumbs=array(
	$this->module->id,
);
?>

<style>

    .k-edit-form-container {
        position: relative;
        width: auto;
    }
    .k-edit-form-container .k-edit-buttons {
        border-style: solid;
        border-width: 1px 0 0;
        bottom: 0;
        clear: both;
        padding: 0.6em;
        position: relative;
        text-align: right;
    }
</style>

<div class="k-block">
    <ul id="menu-images" style="height: 20px;padding-left: 5px; padding-top: 0;font-size: 12px; width: 99.6%;">
        <li>Folders
            <ul>
                <li>Add Folder</li>
                <li>Edit Folder</li>
                <li>Delete folder</li>
            </ul>
        </li>
        <li>Files
            <ul>
                <li>Add File</li>
                <li>Delete File</li>
            </ul>
        </li>
    </ul>
    <div id="horizontal"  style="height: 567px;">
        <div>
            <div class="pane-content">
                <div id="treeview" class="demo-section"></div>
            </div>
        </div>
        <div>
            <div class="pane-content content2">
                    <div id="listView"></div>
                    <!--<div id="pager" class="k-pager-wrap"></div>-->
            </div>
        </div>
    </div>
</div>

<script type="text/x-kendo-template" id="template">
    <div class="product">
        <img src="<?=Yii::app()->createUrl('/images/png.png'); ?>" alt="#: name # image" />
        <h3>#:name#</h3>
    </div>
</script>






<script>
    $(function() {
        var dataSource = new kendo.data.DataSource({
            transport: {
                read: {
                    url: "admin/default/getfiles",
                    dataType: "json"
                }
            },
            //pageSize: 15
        });
/*
        $("#pager").kendoPager({
            dataSource: dataSource
        });
*/
        $("#listView").kendoListView({
            dataSource: dataSource,
            selectable: "multiple",
            template: kendo.template($("#template").html())
        });
    });
</script>

<script>
    var serviceRoot = "";
    function onSelect(e) {
        var treeview = $("#treeview").data("kendoTreeView");
        var dataItem = treeview.dataItem(e.node);
        var dataSource = new kendo.data.DataSource({
            transport: {
                read: {
                    url: "<?=Yii::app()->createUrl('admin/default/getfiles'); ?>?id="+dataItem.id,
                    dataType: "json"
                }
            },
           // pageSize: 15
        });
/*
        $("#pager").kendoPager({
            dataSource: dataSource
        });
*/
        $("#listView").kendoListView({
            dataSource: dataSource,
            selectable: "multiple",
            template: kendo.template($("#template").html())
        });

        /*

        $.post('admin/default/getfiles',{id:dataItem.id}) .done(function( data ) {
            $('.content2').html(data);
        });
        */
    }
    homogeneous = new kendo.data.HierarchicalDataSource({
        transport: {
            read: {
                url: serviceRoot + "admin/default/GetTree",
                dataType: "json"
            }
        },
        schema: {
            model: {
               children: "items"
            }
        }
    });
    $("#treeview").kendoTreeView({
        dataSource: homogeneous,
        dataTextField: "name",
        loadOnDemand: false,
        select: onSelect,
    });
    $("#treeview").data("kendoTreeView").select(".k-item:first")
</script>





<script>
    $(document).ready(function() {
        var window = $("#window");
        function onSelect(e) {
            //alert("Selected: " + $(e.item).children(".k-link").text());
            //var treeview = $("#treeview").data("kendoTreeView");
            //var dataItem = treeview.dataItem(e.node);
            switch ($(e.item).children(".k-link").text()) {
                case "Add Folder":
                    //alert("here");
                    doAddFolder();
                    break;
                case "Edit Folder":
                    //alert("here");
                    doEditFolder();
                    break;
                case "Delete folder":
                    var x = confirm('Will be deleted?');
                    if (x) {
                        var treeview = $("#treeview").data("kendoTreeView");
                        var bar = treeview.select();
                        var dataItem = treeview.dataItem(bar);
                        if (!dataItem) return;
                        treeview.remove(bar);
                        $.ajax({
                            url: 'admin/default/delfolder/'+dataItem.id,
                            type: 'GET',
                            success: function(result) {
                                var listView = $("#listView").data("kendoListView");
                                listView.dataSource.read();
                                listView.refresh();
                            }
                        });
                    }
                    break;
                case "Add File":
                    addFiles();
                    //alert('Add File');
                    break;
                case "Delete File":
                    deleteFiles();
                    //alert('Delete file');
                    break;
                default:
                    //alert("there");
            }
        }

        function addFiles() {
            var treeview = $("#treeview").data("kendoTreeView");
            var dataItem = treeview.dataItem(treeview.select());
            //alert(dataItem.id);
            if (!dataItem) return;






            $('body').append("<div class='addfilewindow'><div class='k-edit-form-container'><form id='fileuploadform' method='post' action='admin/File/Save' enctype='multipart/form-data' encoding='multipart/form-data'>" +
                "<input id='action' type='hidden' name='Action' value='addfile'> " +
                "<input id='id' type='hidden' name='id' value='"+dataItem.id+"'> " +
                "<input name='files[]' id='files' type='file' /></form></div></div>");
            $('div.addfilewindow .k-edit-form-container form').append('<div class="k-edit-buttons k-state-default"><a href="#" class="k-button k-button-icontext k-window-update"><span class="k-icon k-update"></span>Update</a><a href="#" class="k-button k-button-icontext k-window-cancel"><span class="k-icon k-cancel"></span>Cancel</a></div>');
            $('.k-window-cancel').bind('click',function(){$("div.addfilewindow").data("kendoWindow").close();})
            $('.k-window-update').bind('click',function(){doUpload();})
            var window2 = $('div.addfilewindow');
            window2.kendoWindow({
                width: "400px",
                title: "Add files to folder",
                modal: true,
                resizable: false,
                actions: [
                    "Close"
                ],
                close: function(){
                    $('.k-window-cancel').unbind();
                    var dialog = $("div.addfilewindow").data("kendoWindow");
                    dialog.destroy();
                }
            });
            $("#files").kendoUpload();
            var dialog = $("div.addfilewindow").data("kendoWindow");
            dialog.center();


        }

        function deleteFiles() {
            var listView = $("#listView").data("kendoListView");
            //listView.select(listView.element.children().first());
            var data = listView.dataSource.view();
            var selected = $.map(listView.select(), function(item) {
                return data[$(item).index()].id;
            });
            $.ajax({
                url: 'admin/default/delfiles/'+selected.join("__"),
                type: 'GET',
                success: function(result) {
                    var listView = $("#listView").data("kendoListView");
                    listView.dataSource.read();
                    listView.refresh();
                }
            });
        }

        function doUpload() {
            document.getElementById('fileuploadform').submit();
        }

        function doAddFolder() {
            var treeview = $("#treeview").data("kendoTreeView");
            var dataItem = treeview.dataItem(treeview.select());
            //alert(dataItem.id);
            if (typeof dataItem !== "undefined") {
                dataItemid  = dataItem.id;
            } else {
                dataItemid  = 1;
            }
            $('body').append("<div class='addfolderwindow'><div class='k-edit-form-container'>" +
                "<input id='action' type='hidden' name='Action' value='add'> " +
                "<input id='parentid' type='hidden' name='parentid' value='"+dataItemid+"'> " +
            "<div class='k-edit-label'><label for='FolderName'>Folder Name</label></div><div class='k-edit-field' data-container-for='FolderName'><input type='text' class='k-input k-textbox' name='FolderName' required='required' data-bind='value:FolderName'></div></div></div>");
            $('div.addfolderwindow .k-edit-form-container').append('<div class="k-edit-buttons k-state-default"><a href="#" class="k-button k-button-icontext k-window-update"><span class="k-icon k-update"></span>Update</a><a href="#" class="k-button k-button-icontext k-window-cancel"><span class="k-icon k-cancel"></span>Cancel</a></div>');
            $('.k-window-cancel').bind('click',function(){$("div.addfolderwindow").data("kendoWindow").close();})
            $('.k-window-update').bind('click',function(){doAction();})
            var window2 = $('div.addfolderwindow');
            window2.kendoWindow({
                width: "400px",
                title: "Add folder",
                modal: true,
                resizable: false,
                actions: [
                    "Close"
                ],
                close: function(){
                    $('.k-window-cancel').unbind();
                    var dialog = $("div.addfolderwindow").data("kendoWindow");
                    dialog.destroy();
                }
            });
            var dialog = $("div.addfolderwindow").data("kendoWindow");
            dialog.center();
        }

        function doEditFolder() {
            var treeview = $("#treeview").data("kendoTreeView");
            var dataItem = treeview.dataItem(treeview.select());
            //alert(dataItem.id);
            if (!dataItem) return;
            $('body').append("<div class='addfolderwindow'><div class='k-edit-form-container'>" +
                "<input id='action' type='hidden' name='Action' value='edit'> " +
                "<input id='id' type='hidden' name='id' value='"+dataItem.id+"'> " +
                "<div class='k-edit-label'><label for='FolderName'>Folder Name</label></div><div class='k-edit-field' data-container-for='FolderName'><input type='text' class='k-input k-textbox' name='FolderName' required='required' data-bind='value:FolderName' value='"+dataItem.name+"'></div></div></div>");
            $('div.addfolderwindow .k-edit-form-container').append('<div class="k-edit-buttons k-state-default"><a href="#" class="k-button k-button-icontext k-window-update"><span class="k-icon k-update"></span>Update</a><a href="#" class="k-button k-button-icontext k-window-cancel"><span class="k-icon k-cancel"></span>Cancel</a></div>');
            $('.k-window-cancel').bind('click',function(){$("div.addfolderwindow").data("kendoWindow").close();})
            $('.k-window-update').bind('click',function(){doAction();})
            var window2 = $('div.addfolderwindow');
            window2.kendoWindow({
                width: "400px",
                title: "Edit folder",
                modal: true,
                resizable: false,
                actions: [
                    "Close"
                ],
                close: function(){
                    $('.k-window-cancel').unbind();
                    var dialog = $("div.addfolderwindow").data("kendoWindow");
                    dialog.destroy();
                }
            });
            var dialog = $("div.addfolderwindow").data("kendoWindow");
            dialog.center();
        }

        function doAction() {
           o = $('div.addfolderwindow .k-edit-form-container input').serializeArray();
          // alert(o.toSource());
            switch (o[0]['value']) {
                case 'add':
                    var url = 'admin/default/AddFolder';
                    break;
                case 'edit':
                    var url = 'admin/default/EditFolder';
                    break;
            }
            $.post(url,o).done(function(data){
                $("div.addfolderwindow").data("kendoWindow").close();
                $("#treeview").data("kendoTreeView").dataSource.read();
            });
        }

        $("#horizontal").kendoSplitter({
            orientation: "horizontal",
            panes: [
                { collapsible: false, size: "314px" },
                { collapsible: false },
            ]
        });
        $("#menu-images").kendoMenu({
            select: onSelect,
        })
    });
</script>


<style scoped>
    .demo-section {

    }
    #listView {
        padding: 10px;
        margin-bottom: -1px;
        min-width: 555px;
        min-height: 510px;
    }
    .product {
        float: left;
        position: relative;
        width: 111px;
        height: 170px;
        margin: 0;
        padding: 0;
    }
    .product img {
        width: 110px;
        height: 110px;
    }
    .product h3 {
        margin: 0;
        padding: 3px 5px 0 0;
        max-width: 96px;
        overflow: hidden;
        line-height: 1.1em;
        font-size: .9em;
        font-weight: normal;
        text-transform: uppercase;
        color: #999;
    }
    .product p {
        visibility: hidden;
    }
    .product:hover p {
        visibility: visible;
        position: absolute;
        width: 110px;
        height: 110px;
        top: 0;
        margin: 0;
        padding: 0;
        line-height: 110px;
        vertical-align: middle;
        text-align: center;
        color: #fff;
        background-color: rgba(0,0,0,0.75);
        transition: background .2s linear, color .2s linear;
        -moz-transition: background .2s linear, color .2s linear;
        -webkit-transition: background .2s linear, color .2s linear;
        -o-transition: background .2s linear, color .2s linear;
    }
    .k-listview:after {
        content: ".";
        display: block;
        height: 0;
        clear: both;
        visibility: hidden;
    }
</style>

<div class="console"></div>