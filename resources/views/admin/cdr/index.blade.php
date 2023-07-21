@extends('admin.base')

@section('content')
    <div class="layui-card">
        <div class="layui-card-header layuiadmin-card-header-auto">
            <form  class="layui-form">
                <button class="layui-btn layui-btn-sm" lay-submit lay-filter="*" >搜索</button>
                <div class="layui-form-item">
                    <div class="layui-inline">
                        <label for="" class="layui-form-label">主叫号码</label>
                        <div class="layui-input-block">
                            <input type="text" name="caller_id_number" class="layui-input" placeholder="主叫号码">
                        </div>
                    </div>
                    <div class="layui-inline">
                        <label for="" class="layui-form-label">被叫号码</label>
                        <div class="layui-input-block">
                            <input type="text" name="destination_number" class="layui-input" placeholder="被叫号码">
                        </div>
                    </div>
                    <div class="layui-inline">
                        <label for="" class="layui-form-label">呼叫时间</label>
                        <div class="layui-input-inline" style="width: 160px">
                            <input type="text" name="start_stamp_start" id="start_stamp_start" class="layui-input" placeholder="开始时间">
                        </div>
                        <div class="layui-form-mid layui-word-aux">-</div>
                        <div class="layui-input-inline" style="width: 160px">
                            <input type="text" name="start_stamp_end" id="start_stamp_end" class="layui-input" placeholder="结束时间">
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <div class="layui-card-body">
            <table id="dataTable" lay-filter="dataTable"></table>
            <script type="text/html" id="options">
                <div class="layui-btn-group">
                    <a class="layui-btn layui-btn-sm" lay-event="show">通话详单</a>
                    <a class="layui-btn layui-btn-sm" lay-event="play">播放</a>
                    <a class="layui-btn layui-btn-sm" lay-event="download">下载</a>
                </div>
            </script>
        </div>
    </div>
@endsection

@section('script')
    <script>
        layui.use(['layer','table','form','laydate'],function () {
            var layer = layui.layer;
            var form = layui.form;
            var table = layui.table;
            var laydate = layui.laydate;
            //用户表格初始化
            var dataTable = table.render({
                elem: '#dataTable'
                ,height: 500
                ,url: "{{ route('admin.cdr.data') }}" //数据接口
                ,page: true //开启分页
                ,cols: [[ //表头
                    //{checkbox: true,fixed: true}
                    {field: 'id', title: 'ID', sort: true,width:80,fixed:'left'}
                    ,{field: 'caller_id_number', title: '主叫号码',width:100, style:'color:green'}
                    ,{field: 'destination_number', title: '被叫号码',width:120, style:'color:#2F4056'}
                    ,{field: 'start_stamp', title: '呼叫时间', sort: true,width:160}
                    ,{field: 'answer_stamp', title: '应答时间', sort: true,width:160}
                    ,{field: 'end_stamp', title: '挂断时间', sort: true,width:160}
                    ,{field: 'duration', title: '主叫时长(秒)', sort: true, width:120, style:'color:#2F4056'}
                    ,{field: 'billsec', title: '被叫时长(秒)', sort: true, width:120, style:'color: green'}
                    ,{field: 'hangup_cause', title: '挂断原因', width:200}
                    ,{field: 'aleg_uuid', title: '主叫UUID', width:300}
                    ,{field: 'bleg_uuid', title: '被叫UUID', width:300}
                    ,{field: 'caller_id_name', title: '主叫名称', width:160}
                    ,{fixed: 'right', width: 220, align:'center', toolbar: '#options', title:'操作',fixed:'right'}
                ]]
            });

            //监听工具条
            table.on('tool(dataTable)', function(obj){ //注：tool是工具条事件名，dataTable是table原始容器的属性 lay-filter="对应的值"
                var data = obj.data //获得当前行数据
                    ,layEvent = obj.event; //获得 lay-event 对应的值
                if(layEvent === 'show'){
                    layer.open({
                        title : '通话详单',
                        shadeClose : true,
                        type : 2,
                        area : ['800px','600px'],
                        content : '/admin/cdr/'+data.id+'/show'
                    })
                } else if (layEvent === 'play'){
                    var index = layer.load()
                    $.get('/admin/cdr/'+data.aleg_uuid+'/play',function (res) {
                        layer.close(index);
                        if (res.code==0){
                            var _html = '<div style="padding:20px;">';
                            _html += '<audio controls="controls" autoplay src="'+res.data+'"></audio>';
                            _html += '</div>';
                            layer.open({
                                title : '播放录音',
                                type : 1,
                                area : ['360px','auto'],
                                content : _html
                            })
                        }else {
                            layer.msg(res.msg,{icon:5})
                        }
                    })
                } else if (layEvent === 'download'){
                    location.href = '/admin/cdr/'+data.aleg_uuid+'/download';
                }
            });

            //时间选择
            laydate.render({type: 'datetime', elem: '#start_stamp_start'});
            laydate.render({type: 'datetime', elem: '#start_stamp_end'});

            //监听搜索提交
            form.on('submit(*)', function(data){
                dataTable.reload({
                    where: data.field,
                    page: {curr:1}
                })
                return false;
            });

        })
    </script>
@endsection