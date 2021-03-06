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
namespace yunwuxin\auth\guard;

use think\helper\Str;
use think\Request;
use yunwuxin\auth\interfaces\Guard;
use yunwuxin\auth\interfaces\Provider;
use yunwuxin\auth\interfaces\StatefulUser;
use yunwuxin\auth\traits\GuardHelpers;

class Token implements Guard
{
    use GuardHelpers;

    /** @var Provider */
    protected $provider;

    protected $request;

    public function __construct(Request $request, Provider $provider)
    {
        $this->provider = $provider;
        $this->request  = $request;
    }

    /**
     * 获取通过认证的用户
     *
     * @return StatefulUser|null
     */
    public function user()
    {
        if (!is_null($this->user)) {
            return $this->user;
        }

        $user = null;

        $token = $this->getTokenFromRequest();

        if (!empty($token)) {
            $user = $this->provider->retrieveByCredentials(
                ['token' => $token]
            );
        }

        return $this->user = $user;
    }

    protected function getTokenFromRequest()
    {
        $token = $this->request->param('access-token');
        if (empty($token)) {
            $header = $this->request->header('Authorization');
            if (!empty($header)) {
                if (Str::startsWith($header, 'Bearer ')) {
                    $token = Str::substr($header, 7);
                }
            }
        }

        return $token;
    }

    /**
     * 认证用户
     *
     * @param array $credentials
     * @return bool
     */
    public function validate(array $credentials = [])
    {
        if ($this->provider->retrieveByCredentials($credentials)) {
            return true;
        }

        return false;
    }

}
