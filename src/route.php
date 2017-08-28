<?php
/**
 * 路由
 */

\think\Route::get('api/documents',"\\Reflection\\Api\\Doc\\Documents@run");