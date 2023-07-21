<?php

namespace App\Http\Controllers\Admin;

use App\Models\Action;
use App\Models\Condition;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ActionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($condition_id)
    {
        $condition = Condition::findOrFail($condition_id);
        return view('admin.dialplan.action.index',compact('condition'));
    }

    public function data(Request $request,$condition_id)
    {
        $res = Action::where('condition_id',$condition_id)->orderBy('sort')->orderBy('id')->paginate($request->get('limit', 30));
        $data = [
            'code' => 0,
            'msg' => '正在请求中...',
            'count' => $res->total(),
            'data' => $res->items(),
        ];
        return response()->json($data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($condition_id)
    {
        $condition = Condition::findOrFail($condition_id);
        return view('admin.dialplan.action.create',compact('condition'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request,$condition_id)
    {
        $condition = Condition::findOrFail($condition_id);
        $data = $request->all(['display_name','application','data','sort']);
        $data['condition_id'] = $condition->id;
        if (Action::create($data)){
            return redirect(route('admin.action',['condition_id'=>$condition->id]))->with(['success'=>'添加成功']);
        }
        return back()->withErrors(['error'=>'添加失败']);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($condition_id,$id)
    {
        $condition = Condition::findOrFail($condition_id);
        $model = Action::findOrFail($id);
        return view('admin.dialplan.action.edit',compact('condition','model'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $condition_id, $id)
    {
        $condition = Condition::findOrFail($condition_id);
        $model = Action::findOrFail($id);
        $data = $request->all(['display_name','application','data','sort']);
        if ($model->update($data)){
            return redirect(route('admin.action',['condition_id'=>$condition->id]))->with(['success'=>'更新成功']);
        }
        return back()->withErrors(['error'=>'更新失败']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $ids = $request->get('ids');
        if (empty($ids)){
            return response()->json(['code'=>1,'msg'=>'请选择删除项']);
        }
        if (Action::destroy($ids)){
            return response()->json(['code'=>0,'msg'=>'删除成功']);
        }
        return response()->json(['code'=>1,'msg'=>'删除失败']);
    }
}
