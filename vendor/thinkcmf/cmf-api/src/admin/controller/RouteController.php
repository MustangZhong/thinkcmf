<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-present http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: Dean <zxxjjforever@163.com>
// +----------------------------------------------------------------------
namespace api\admin\controller;

use app\admin\model\RecycleBinModel;
use app\admin\model\RouteModel;
use app\admin\model\SlideItemModel;
use app\admin\model\SlideModel;
use app\admin\service\AdminMenuService;
use cmf\controller\RestAdminBaseController;
use cmf\controller\RestBaseController;
use OpenApi\Annotations as OA;
use think\facade\Db;
use think\facade\Validate;

class RouteController extends RestAdminBaseController
{
    /**
     * 路由列表
     * @throws \think\exception\DbException
     * @OA\Get(
     *     tags={"admin"},
     *     path="/admin/routes",
     *     summary="路由列表",
     *     description="路由列表",
     *     @OA\Response(
     *          response="1",
     *          description="success",
     *          @OA\JsonContent(example={"code": 1,"msg": "success","data":{
     *              "routes":{
     *                  {"id": 1,"list_order": 10000,"status": 1,"type": 1,
     *                      "full_url": "demo/List/index","url": "list/:id"
     *                  }
     *              }
     *          }})
     *     ),
     *     @OA\Response(
     *          response="0",
     *          @OA\JsonContent(example={"code": 0,"msg": "error!","data":""})
     *     ),
     * )
     */
    public function index()
    {
        global $CMF_GV_routes;
        $routeModel = new RouteModel();
        $routes     = RouteModel::order("list_order asc")->select();
        $routeModel->getRoutes(true);
        unset($CMF_GV_routes);
        $this->success("success", ['routes' => $routes]);
    }

    /**
     * 添加路由
     * @throws \think\exception\DbException
     * @OA\Post(
     *     tags={"admin"},
     *     path="/admin/routes",
     *     summary="添加路由",
     *     description="添加路由",
     *     @OA\RequestBody(
     *         description="请求参数",
     *         @OA\MediaType(
     *             mediaType="application/x-www-form-urlencoded",
     *             @OA\Schema(ref="#/components/schemas/AdminRouteSaveRequest")
     *         ),
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(ref="#/components/schemas/AdminRouteSaveRequest")
     *         )
     *     ),
     *     @OA\Response(
     *          response="1",
     *          description="success",
     *          @OA\JsonContent(example={"code": 1,"msg": "success","data":{
     *              "slide":{"id": 1,"status": 1,"delete_time": 0,"name": "又菜又爱玩","remark": ""}
     *          }})
     *     ),
     *     @OA\Response(
     *          response="0",
     *          @OA\JsonContent(example={"code": 0,"msg": "error!","data":""})
     *     ),
     * )
     */
    public function save()
    {
        $data       = $this->request->param();
        $routeModel = new RouteModel();
        $result     = $this->validate($data, 'Route');
        if ($result !== true) {
            $this->error($result);
        }
        $routeModel->save($data);
        $this->success(lang('ADD_SUCCESS'), ['route' => $routeModel]);
    }

    /**
     * 获取路由信息
     * @throws \think\exception\DbException
     * @OA\Get(
     *     tags={"admin"},
     *     path="/admin/routes/{id}",
     *     summary="获取路由信息",
     *     description="获取路由信息",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="路由id",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *         )
     *     ),
     *     @OA\Response(
     *          response="1",
     *          description="success",
     *          @OA\JsonContent(example={"code": 1,"msg": "success","data":{
     *              "slide":{"id": 1,"status": 1,"delete_time": 0,"name": "又菜又爱玩","remark": ""}
     *          }})
     *     ),
     *     @OA\Response(
     *          response="0",
     *          @OA\JsonContent(example={"code": 0,"msg": "not found!","data":""})
     *     ),
     * )
     */
    public function read($id)
    {
        $id    = $this->request->param("id", 0, 'intval');
        $route = RouteModel::find($id);

        if (empty($route)) {
            $this->error('not found!');
        } else {
            $this->success('success', ['route' => $route]);
        }
    }

    /**
     * 编辑路由
     * @throws \think\exception\DbException
     * @OA\Put(
     *     tags={"admin"},
     *     path="/admin/routes/{id}",
     *     summary="编辑路由",
     *     description="编辑路由",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="路由id",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *         )
     *     ),
     *     @OA\RequestBody(
     *         description="请求参数",
     *         @OA\MediaType(
     *             mediaType="application/x-www-form-urlencoded",
     *             @OA\Schema(ref="#/components/schemas/AdminRouteSaveRequest")
     *         ),
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(ref="#/components/schemas/AdminRouteSaveRequest")
     *         )
     *     ),
     *     @OA\Response(
     *          response="1",
     *          description="success",
     *          @OA\JsonContent(example={"code": 1,"msg": "保存成功","data":""})
     *     ),
     *     @OA\Response(
     *          response="0",
     *          @OA\JsonContent(example={"code": 0,"msg": "error!","data":""})
     *     ),
     * )
     */
    public function update($id)
    {
        $data   = $this->request->param();
        $result = $this->validate($data, 'Route');
        if ($result !== true) {
            $this->error($result);
        }
        $route = RouteModel::find($data['id']);
        if (empty($route)) {
            $this->error('路由未找到!');
        }
        $route->save($data);
        $this->success(lang('EDIT_SUCCESS'));
    }

    /**
     * 删除路由
     * @throws \think\exception\DbException
     * @OA\Delete(
     *     tags={"admin"},
     *     path="/admin/routes/{id}",
     *     summary="删除路由",
     *     description="删除路由",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="路由id",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *         )
     *     ),
     *     @OA\Response(
     *          response="1",
     *          description="success",
     *          @OA\JsonContent(example={"code": 1,"msg": "删除成功!","data":""})
     *     ),
     *     @OA\Response(
     *          response="0",
     *          @OA\JsonContent(example={"code": 0,"msg": "error","data":""})
     *     ),
     * )
     */
    public function delete($id)
    {
        $id = $this->request->param('id', 0, 'intval');
        RouteModel::destroy($id);

        $this->success(lang('DELETE_SUCCESS'));
    }

}