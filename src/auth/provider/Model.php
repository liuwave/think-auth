<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: yunwuxin <448901948@qq.com>
// +----------------------------------------------------------------------
namespace yunwuxin\auth\provider;

use yunwuxin\auth\interfaces\StatefulProvider;
use yunwuxin\auth\interfaces\StatefulUser;

class Model implements StatefulProvider
{

    protected $model;

    public function __construct($config)
    {
        $this->model = $config['model'];
    }

    /**
     * 根据用户ID取得用户
     * @param $id
     * @return mixed
     */
    public function retrieveById($id)
    {
        return $this->createModel()->find($id);
    }

    /**
     * 根据令牌获取用户
     * @param $id
     * @param $token
     * @return mixed
     */
    public function retrieveByToken($id, $token)
    {
        $model = $this->createModel();

        return $model->where($model->getPk(), $id)
            ->where($model->getRememberTokenName(), $token)
            ->find();
    }

    /**
     * 更新“记住我”的token
     * @param StatefulUser|\think\Model    $user
     * @param                              $token
     * @return mixed
     */
    public function updateRememberToken(StatefulUser $user, $token)
    {
        $user->setRememberToken($token);
        $user->save();
    }

    /**
     * 根据用户输入的数据获取用户
     * @param array $credentials
     * @return StatefulUser
     */
    public function retrieveByCredentials(array $credentials)
    {
        if (empty($credentials)) {
            return null;
        }

        $data = [];

        foreach ($credentials as $key => $value) {
            if (strpos($key, 'password') === false) {
                $data[$key] = $value;
            }
        }

        return $this->createModel()->where($data)->find();
    }

    /**
     * 验证密码
     * @param       $user
     * @param array $credentials
     * @return mixed
     */
    public function validateCredentials(StatefulUser $user, array $credentials)
    {
        $plain = $credentials['password'];

        return password_verify($plain, $user->getAuthPassword());
    }

    protected function createModel()
    {
        $class = '\\' . ltrim($this->model, '\\');

        return new $class;
    }
}
