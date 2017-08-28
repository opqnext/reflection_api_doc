<?php
/**
 * 路由
 */

\think\Route::get('api/documents',"\\Reflection\\Api\\Doc\\Documents@run");
\think\Route::get('api/detail:name',"\\Reflection\\Api\\Doc\\Documents@detail");