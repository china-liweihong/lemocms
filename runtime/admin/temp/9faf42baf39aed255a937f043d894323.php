<?php /*a:3:{s:50:"E:\lemocms1.0\app\admin\view\sys\attach\index.html";i:1583037531;s:47:"E:\lemocms1.0\app\admin\view\common\header.html";i:1583038741;s:47:"E:\lemocms1.0\app\admin\view\common\footer.html";i:1582960894;}*/ ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title><?php echo config('admin.sys_name'); ?>后台管理</title>
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="format-detection" content="telephone=no">
    <link rel="stylesheet" href="/static/plugins/layui/css/layui.css" media="all" />
    <link rel="stylesheet" href="/static/admin/css/main.css?v=<?php echo time(); ?>" media="all">
    <link rel="stylesheet" href="/static/plugins/font-awesome-4.7.0/css/font-awesome.min.css" media="all">
    <!--[if lt IE 9]>
    <script src="https://cdn.staticfile.org/html5shiv/r29/html5.min.js"></script>
    <script src="https://cdn.staticfile.org/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
    <style id="lemo-bg-color">
    </style>
</head>
<style type="text/css">
    /*{设置table每一行的height #}*/
    .layui-table-cell {
        height: auto;
        line-height: 50px;
    }
</style>
<div class="lemo-container">
    <div class="lemo-main">
            <fieldset class="layui-elem-field layui-field-title">
                <legend><?php echo lang('attach'); ?><?php echo lang('list'); ?></legend>
                <blockquote class="layui-elem-quote">
                    <div class="lemo-table">
                        <div class="layui-inline">
                            <input type="text" id='keys' name="keys" lay-verify="required"
                                   placeholder="<?php echo lang('pleaseEnter'); ?>" autocomplete="off" class="layui-input">
                        </div>
                        <a  href="javascript:;" class="layui-btn data-add-btn layui-btn-sm" lay-submit="" lay-filter="search" id="search">
                            <?php echo lang('search'); ?>
                        </a>
                        <a href="javascript:;" data-href="<?php echo url('add'); ?>" class="layui-btn layui-btn-sm layui-btn-warm add"><?php echo lang('add'); ?><?php echo lang('attach'); ?></a>
                        <a href="javascript:;"  class="layui-btn layui-btn-sm layui-btn-danger" id="delAll"><?php echo lang('delete checked'); ?></a>
                    </div>
                </blockquote>
            </fieldset>
            <table class="layui-table" id="list" lay-filter="list"></table>
    </div>
</div>

<script type="text/html" id="action">
    <a  class="layui-btn layui-btn-danger layui-btn-xs" data-href="<?php echo url('delete'); ?>" lay-event="del"><?php echo lang('del'); ?></a>
</script>
<script type="text/html" id="status">
    <input type="checkbox" name="status" data-href="<?php echo url('state'); ?>" value="{{d.id}}" lay-skin="switch" lay-text="开启|关闭" lay-filter="status" {{ d.status == 1 ? 'checked' : '' }}>
</script>
<script type="text/html" id="path">
    <a href="{{d.path}}" target="_blank"><img class="layui-bg-green" src="{{d.path}}" width="50" ></a>
</script>
<script type="text/html" id="create_time">
    {{layui.util.toDateString(d.create_time*1000, 'yyyy-MM-dd HH:mm:ss')}}
</script>
<script type="text/html" id="mime">

    {{# if(d.mime=='image/jpeg' || d.mime=='image/gif' || d.mime=='image/png' || d.mime=='image/webp' || d.mime=='image/bmp')  { }}
    <img src="/static/admin/images/filetype/image.jpg" alt="图片" width="50">
    {{# }else if(d.mime=='application/pdf') { }}
    <img src="/static/admin/images/filetype/pdf.jpg" alt="pdf" width="50">
    {{# }else if(d.mime=='application/zip') { }}
    <img src="/static/admin/images/filetype/zip.jpg" alt="zip" width="50">
    {{# }else if(d.mime=='application/msexcel' || d.mime=='application/mspowerpoint' || d.mime=='application/msword') { }}
    <img src="/static/admin/images/filetype/office.jpg" alt="文档" width="50">
    {{# }else{ }}
    <img src="/static/admin/images/filetype/file.jpg" alt="文件" width="50">
    {{# } }}



</script>

<script type="text/html" id="toolbar">
    <div class="layui-btn-container">
<!--        <button class="layui-btn layui-btn-sm" lay-event="getCheckData">获取选中行数据</button>-->
<!--        <button class="layui-btn layui-btn-sm" lay-event="getCheckLength">获取选中数目</button>-->
<!--        <button class="layui-btn layui-btn-sm" lay-event="isAll">验证是否全选</button>-->
    </div>
</script>
<script src="/static/plugins/layui/layui.js" charset="utf-8"></script>
<!--<script>-->
<!--    layui.config({-->
<!--        base: "/static/admin/js/",-->
<!--        version: true-->
<!--    }).extend({-->
<!--        Admin: 'Admin'-->
<!--    }).use(['Admin'], function () {-->
<!--        Admin = layui.Admin;-->
<!--    });-->
<!--</script>-->

<script>
    var tableIn;
    layui.config({
        base: "/static/admin/js/",
        version: true
    }).extend({
        Admin: 'Admin'
    }).use(['element', 'layer', 'Admin'], function () {
        var $ = layui.jquery,
            form = layui.form,
            table = layui.table;
         tableIn = table.render({
            elem: '#list',
            url: '<?php echo url("index"); ?>'+'?mime='+'<?php echo htmlentities($mime); ?>',
            method: 'post',
            title: '附件数据表',
            toolbar: '#toolbar', //开启头部工具栏，并为其绑定左侧模板
             // height: 'full-20', //高度最大化减去差值
            defaultToolbar: ['filter', 'exports', 'print', { //自定义头部工具栏右侧图标。如无需自定义，去除该参数即可
                title: '提示'
                ,layEvent: 'LAYTABLE_TIPS'
                ,icon: 'layui-icon-tips' }],
            cols: [[
                {checkbox: true, fixed: true},
                {field: 'id', title: 'ID', width: 80, fixed: true, sort: true},
                {field: 'name', title: '文件名', width: 120,},
                {field: 'mime', title: '文件类型', width: 80, templet:'#mime'},
                {field: 'path', title: '路径', minWidth: 120,  templet:'#path'},
                {field: 'ext', title: '后缀', width: 120,},
                {field: 'size', title: '大小', width: 80, },
                {field: 'driver', title: '驱动', width: 80, },
                {field: 'status', title: '状态', width: 180, templet:'#status'},
                {field: 'create_time', title: '添加时间', width: 180,templet:'#create_time'},
                {title:'操作',width:150, toolbar: '#action',align:"center"},

            ]],
            limits: [10, 15, 20, 25, 50, 100],
            limit: 15,
            page: true
        });



    });
</script>