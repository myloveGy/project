<?php use yii\helpers\Url; ?>
<h3 class="header smaller lighter blue"><?php echo $this->params['title']; ?></h3>
<table id="grid-table"></table>
<div id="grid-pager"></div>
<script type="text/javascript">
    $(function($) {
        // 初始化定义
        var grid_selector  = "#grid-table"; // 定义选择信息表格
        var pager_selector = "#grid-pager"; // 定义分页信息

        // 窗口改变大小
        $(window).on('resize.jqGrid', function () {
            $(grid_selector).jqGrid( 'setGridWidth', $(".page-content").width() );
        });

        // 调整在侧边栏折叠/展开
        var parent_column = $(grid_selector).closest('[class*="col-"]');
        $(document).on('settings.ace.jqGrid' , function(ev, event_name, collapsed) {
            if( event_name === 'sidebar_collapsed' || event_name === 'main_container_fixed' ) {
                // 及时调整大小
                setTimeout(function() {
                    $(grid_selector).jqGrid( 'setGridWidth', parent_column.width() );
                }, 0);
            }
        });

        // 提交验证
        function beforeValidate(postdata, form)
        {
            return [form.validate().form(), '请正确填写表单'];
        }

        // 提交数据成功
        function afterSuccess(response, postdata)
        {
            var json   = $.parseJSON(response.responseText),
                isTrue = json.status == 1 ? true : false;
            return [isTrue, json.msg];
        }

        var mcolNames = <?php echo isset($colNames) ? $colNames : '{}'; ?>;
        var mcolModel = <?php echo isset($colModel) ? $colModel : '{}'; ?>;

        // 重新赋值
        mcolModel[0].formatoptions.delOptions.beforeShowForm = beforeDeleteCallback;
        mcolModel[0].formatoptions.delOptions.afterSubmit    = afterSuccess;

        // 初始化表格
        jQuery(grid_selector).jqGrid({
            caption: '<?= $this->params['title'] ?>列表',                               // 表格名称
            url:'<?php echo Url::to([\Yii::$app->controller->id.'/get-data']); ?>',      // 获取数据的URL
            datatype: 'json',                                                           // 数据类型
            editurl: '<?php echo Url::to([\Yii::$app->controller->id.'/update']); ?>',  // 修改的连接信息
            height: '80%',                                                  // 高度
            width: '96%',
            colNames:mcolNames,                                             // 显示的列信息
            colModel:mcolModel,                                             // 字段信息
            subGrid : true,                                                 // 是否运行查看详情
            subGridOptions : {                                              // 按钮信息
                plusicon : "ace-icon fa fa-plus center bigger-110 blue",    // 查看
                minusicon: "ace-icon fa fa-minus center bigger-110 blue",   // 修改
                openicon : "ace-icon fa fa-chevron-right center orange"     // 删除
            },

            // 服务器返回信息
            jsonReader:{
                root:'rows',        // 数据字段名
                page:'page',        // 当前页
                total:'total',      // 总页数
                records:'records',  // 查询出的记录数
                id:'id',            // 行ID
                cell:'data',        // 行数据
            },

            // 详情信息
            subGridRowExpanded: function (subgridDivId, rowId) {
                var subgridTableId = subgridDivId + "_t";
                $("#" + subgridDivId).html("<table id='" + subgridTableId + "'></table>");
                $("#" + subgridTableId).jqGrid({
                    url:'/Admin/Index/getDetail.html?id='+rowId,
                    datatype: 'json',
                    colNames: <?php echo isset($detailColNames) ? $detailColNames : '[]'; ?>,
                    colModel: <?php echo isset($detailColModel) ? $detailColModel : '[]'; ?>,
            });
            },

            viewrecords : true,
            rowNum:10,
            rowList:[10,20,30],
            pager : pager_selector,
            altRows: true,
            multiselect: true,      // 可以多选
            multiboxonly: true,
            loadComplete : function() {
                var table = this;
                setTimeout(function(){
                    updatePagerIcons(table);
                    enableTooltips(table);
                }, 0);
            },
        });

        $(window).triggerHandler('resize.jqGrid');

        // 选择框
        function aceSwitch(cellvalue, options, cell ){
            setTimeout(function(){
                $(cell) .find('input[type=checkbox]')
                    .addClass('ace ace-switch ace-switch-5')
                    .after('<span class="lbl"></span>');
            }, 0);
        }

        // 处理时间
        function pickDate( cellvalue, options, cell ) {
            setTimeout(function(){
                $(cell) .find('input[type=text]')
                    .datepicker({
                        format:'yyyy-mm-dd' ,
                        autoclose:true,
                        language: 'zh-CN',
                        todayBtn:'linked',
                    });
            }, 0);
        }

        // 按钮信息
        jQuery(grid_selector).jqGrid('navGrid',pager_selector,
            { 	//navbar options
                edit: true,
                editicon : 'ace-icon fa fa-pencil blue',
                add: true,
                addicon : 'ace-icon fa fa-plus-circle purple',
                del: true,
                delicon : 'ace-icon fa fa-trash-o red',
                search: true,
                searchicon : 'ace-icon fa fa-search orange',
                refresh: true,
                refreshicon : 'ace-icon fa fa-refresh green',
                view: true,
                viewicon : 'ace-icon fa fa-search-plus grey',
            },
            {
                width: 'auto',
                recreateForm: true,
                // 显示表单之前
                beforeShowForm : function(e) {
                    var form = $(e[0]);
                    form.closest('.ui-jqdialog').find('.ui-jqdialog-titlebar').wrapInner('<div class="widget-header" />')
                    style_edit_form(form);
                },

                // 提交表单之前(数据信息, 表单信息)数据的验证
                beforeSubmit:beforeValidate,
                afterSubmit:afterSuccess,
            },
            {
                // 新增数据
                closeAfterAdd: true,
                recreateForm: true,
                viewPagerButtons: false,
                beforeShowForm : function(e) {
                    var form = $(e[0]);
                    form.closest('.ui-jqdialog').find('.ui-jqdialog-titlebar')
                        .wrapInner('<div class="widget-header" />')
                    style_edit_form(form);
                },

                // 提交之前验证
                beforeSubmit:beforeValidate,
                // 数据提交之后
                afterSubmit:afterSuccess,
            },
            {
                // 删除
                recreateForm: true,
                beforeShowForm : function(e) {
                    var form = $(e[0]);
                    if(form.data('styled')) return false;
                    form.closest('.ui-jqdialog').find('.ui-jqdialog-titlebar').wrapInner('<div class="widget-header" />')
                    style_delete_form(form);
                    form.data('styled', true);
                },
                afterSubmit:afterSuccess,
            },
            {
                recreateForm: true,
                afterShowSearch: function(e){
                    var form = $(e[0]);
                    form.closest('.ui-jqdialog').find('.ui-jqdialog-title').wrap('<div class="widget-header" />')
                    style_search_form(form);
                },
                afterRedraw: function(){
                    style_search_filters($(this));
                }
                ,
                multipleSearch: true,
            },
            {
                recreateForm: true,
                beforeShowForm: function(e){
                    var form = $(e[0]);
                    form.closest('.ui-jqdialog').find('.ui-jqdialog-title').wrap('<div class="widget-header" />')
                }
            }
        )


        // 修改数据
        function style_edit_form(form)
        {
            // 初始化信息
            form.find('input[name=createTime]').datepicker({format:'yyyy-mm-dd' , autoclose:true})
                .end().find('input[name=status]')
                .addClass('ace ace-switch ace-switch-5').after('<span class="lbl"></span>');

            // 表单信息
            var buttons = form.next().find('.EditButton .fm-button');
            buttons.addClass('btn btn-sm').find('[class*="-icon"]').hide();//ui-icon, s-icon
            buttons.eq(0).addClass('btn-primary').prepend('<i class="ace-icon fa fa-check"></i>');
            buttons.eq(1).prepend('<i class="ace-icon fa fa-times"></i>')

            buttons = form.next().find('.navButton a');
            buttons.find('.ui-icon').hide();
            buttons.eq(0).append('<i class="ace-icon fa fa-chevron-left"></i>');
            buttons.eq(1).append('<i class="ace-icon fa fa-chevron-right"></i>');
        }

        // 删除数据
        function style_delete_form(form) {
            var buttons = form.next().find('.EditButton .fm-button');
            buttons.addClass('btn btn-sm btn-white btn-round').find('[class*="-icon"]').hide();//ui-icon, s-icon
            buttons.eq(0).addClass('btn-danger').prepend('<i class="ace-icon fa fa-trash-o"></i>');
            buttons.eq(1).addClass('btn-default').prepend('<i class="ace-icon fa fa-times"></i>')
        }

        // 查询信息
        function style_search_filters(form) {
            form.find('.delete-rule').val('X');
            form.find('.add-rule').addClass('btn btn-xs btn-primary');
            form.find('.add-group').addClass('btn btn-xs btn-success');
            form.find('.delete-group').addClass('btn btn-xs btn-danger');
        }

        // 执行查询
        function style_search_form(form) {
            var dialog = form.closest('.ui-jqdialog');
            var buttons = dialog.find('.EditTable')
            buttons.find('.EditButton a[id*="_reset"]').addClass('btn btn-sm btn-info').find('.ui-icon').attr('class', 'ace-icon fa fa-retweet');
            buttons.find('.EditButton a[id*="_query"]').addClass('btn btn-sm btn-inverse').find('.ui-icon').attr('class', 'ace-icon fa fa-comment-o');
            buttons.find('.EditButton a[id*="_search"]').addClass('btn btn-sm btn-purple').find('.ui-icon').attr('class', 'ace-icon fa fa-search');
        }

        // 删除之前执行函数
        function beforeDeleteCallback(e)
        {
            var form = $(e[0]);
            if(form.data('styled')) return false;
            form.closest('.ui-jqdialog').find('.ui-jqdialog-titlebar').wrapInner('<div class="widget-header" />')
            style_delete_form(form);
            form.data('styled', true);
        }

        // 分页设置信息
        function updatePagerIcons(table) {
            var replacement =
            {
                'ui-icon-seek-first' : 'ace-icon fa fa-angle-double-left bigger-140',
                'ui-icon-seek-prev' : 'ace-icon fa fa-angle-left bigger-140',
                'ui-icon-seek-next' : 'ace-icon fa fa-angle-right bigger-140',
                'ui-icon-seek-end' : 'ace-icon fa fa-angle-double-right bigger-140'
            };

            $('.ui-pg-table:not(.navtable) > tbody > tr > .ui-pg-button > .ui-icon').each(function(){
                var icon = $(this);
                var $class = $.trim(icon.attr('class').replace('ui-icon', ''));
                if($class in replacement) icon.attr('class', 'ui-icon '+replacement[$class]);
            })
        }

        function enableTooltips(table) {
            $('.navtable .ui-pg-button').tooltip({container:'body'});
            $(table).find('.ui-pg-div').tooltip({container:'body'});
        }

        $(document).on('ajaxloadstart', function(e) {
            $(grid_selector).jqGrid('GridUnload');
            $('.ui-jqdialog').remove();
        });
    });
</script>