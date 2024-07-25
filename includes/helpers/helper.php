<?php

if(!function_exists('config')){
    function config(string $key){
                
              $config =  explode('.'  , $key) ;
              if(count($config)>0 ){
                $result = include base_path('config/'.$config[0]);
                return $result[$config[1]];
              }
              return null ;
    }
}


if(!function_exists('base_path')){
  function base_path($path){
 
         return "./../".$path.".php" ;
  }
}


/**
 * Get the public path for the application.
 *
 * @param string $path The additional path to append to the public path.
 * @return string The full path including the public path and additional path.
 */
if (!function_exists('public_path')) {
  function public_path($path)
  {
      return getcwd() . "/" . $path;
  }
}




/**
* Get the public path for the application.
*
* @param string $path The additional path to append to the public path.
* @return string The full path including the public path and additional path.
*/
if (!function_exists('public_')) {
  function public_()
  {
      return 'public';
  }
}