<?php
namespace app\index\controller;

use think\Config;
use think\Controller;
use think\Db;

class Index extends Controller
{
    public function index(){
        $code = input('code', '', 'trim');
        $url  = input('url', '', 'trim');
        $obj  = Db::connect('db_config2');

        if($code){
            $code = trim($code, '/');
            $code = intval(b64dec($code));
            $data = $obj->name('links')->where(['l_id' => $code])->field('l_url as url')->find();
            if(!$data){
                exit('链接错误');
            }else{
                $url = $data['url'];
            }
        }elseif($url){
            $hash = md5($url);
            $data = $obj->name('links')->where(['l_hash' => $hash])->field('l_id')->find();
            if(!$data){
                $data = [
                    'l_hash' => $hash,
                    'l_url'  => $url,
                ];
                $obj->name('links')->insert($data);
                $id = $obj->name('links')->getLastInsID();
            }else{
                $id = $data['l_id'];
            }
            $url = 'http://t.' . Config::get('url_domain_root') . '/' . decb64($id);
            echo $url;die;
//            $url = 'http://t.guazi.com/' . decb64($id);
        }
        $this->redirect($url, 302);
    }
}
