<?php
/**
 * Created by PhpStorm.
 * User: momo
 * Date: 2017/8/28
 * Time: 下午5:41
 */

\think\Route::get('api/documents',"\\Reflection\\Api\\Doc\\Documents@run");