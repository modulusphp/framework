<?php

namespace ModulusPHP\Framework\Core;

use Dotenv;

class Logger
{
  /**
   * Output
   * 
   * @param  string  $text
   * @param  boolean $trace
   * @param  string  $type
   * @return
   */
  public static function output($text = null, $trace = false, $type = '.INFO:')
  {
    /**
     * ['file']
     * ['line']
     * ['function']
     * ['args']
     */
    $logfile = '../storage/logs/modulus.log';

    $dotenv = new Dotenv\Dotenv(__DIR__.'/../../');
    $dotenv->load();

    $currentdate = date("Y-m-d").' '.date("G:i:s");

    $key = array_search(__FUNCTION__, array_column(debug_backtrace(), 'function'));
    $file_trace = debug_backtrace()[$key]['file'];
    $line_trace = debug_backtrace()[$key]['line'];

    ($trace == true) ? $track_back = '['.$file_trace.'][line: '.$line_trace.']' : $track_back = '';

    $text = print_r($text, true);

    file_put_contents($logfile, $track_back.'['.$currentdate.'] '.config('app.env').$type.' '.$text.PHP_EOL, FILE_APPEND);
  }
}