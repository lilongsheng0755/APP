<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>System Error</title>
        <meta name="robots" content="noindex,nofollow" />
        <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
        <style>
            /* Base */
            body {
                color: #333;
                font: 14px Verdana, "Helvetica Neue", helvetica, Arial, 'Microsoft YaHei', sans-serif;
                margin: 0;
                padding: 0 20px 20px;
                word-break: break-all;
            }
            h1{
                margin: 10px 0 0;
                font-size: 28px;
                font-weight: 500;
                line-height: 32px;
                color:red;
            }
            h2{
                color: #4288ce;
                font-weight: 400;
                padding: 6px 0;
                margin: 6px 0 0;
                font-size: 18px;
                border-bottom: 1px solid #eee;
            }
            h3.subheading {
                color: #4288ce;
                margin: 6px 0 0;
                font-weight: 400;
            }
            h3{
                margin: 12px;
                font-size: 16px;
                font-weight: bold;
            }
            abbr{
                cursor: help;
                text-decoration: underline;
            }
            a{
                color: #868686;
                cursor: pointer;
            }
            a:hover{
                text-decoration: underline;
            }
            .line-error{
                background: #f8cbcb;
            }
            /* Layout */
            .col-md-3 {
                width: 25%;
            }
            .col-md-9 {
                width: 75%;
            }
            [class^="col-md-"] {
                float: left;
            }
            .clearfix {
                clear:both;
            }
            @media only screen 
            and (min-device-width : 375px) 
            and (max-device-width : 667px) { 
                .col-md-3,
                .col-md-9 {
                    width: 100%;
                }
            }
            /* Exception Info */
            .exception {
                margin-top: 20px;
            }
            .exception .message{
                padding: 12px;
                border: 1px solid #ddd;
                border-bottom: 0 none;
                line-height: 18px;
                font-size:16px;
                border-top-left-radius: 4px;
                border-top-right-radius: 4px;
                font-family: Consolas,"Liberation Mono",Courier,Verdana,"微软雅黑";
            }

            .exception .code{
                float: left;
                text-align: center;
                color: #fff;
                margin-right: 12px;
                padding: 16px;
                border-radius: 4px;
                background: #999;
            }
            .exception .source-code{
                padding: 6px;
                border: 1px solid #ddd;

                background: #f9f9f9;
                overflow-x: auto;

            }
            .exception .source-code pre{
                margin: 0;
            }
            .exception .source-code pre ol{
                margin: 0;
                color: #4288ce;
                display: inline-block;
                min-width: 100%;
                box-sizing: border-box;
                font-size:14px;
                font-family: "Century Gothic",Consolas,"Liberation Mono",Courier,Verdana;
                padding-left: 56px;
            }
            .exception .source-code pre li{
                border-left: 1px solid #ddd;
                height: 18px;
                line-height: 18px;
            }
            .exception .source-code pre code{
                color: #333;
                height: 100%;
                display: inline-block;
                border-left: 1px solid #fff;
                font-size:14px;
                font-family: Consolas,"Liberation Mono",Courier,Verdana,"微软雅黑";
            }
            .exception .trace{
                padding: 6px;
                border: 1px solid #ddd;
                border-top: 0 none;
                line-height: 16px;
                font-size:14px;
                font-family: Consolas,"Liberation Mono",Courier,Verdana,"微软雅黑";
            }
            .exception .trace ol{
                margin: 12px;
            }
            .exception .trace ol li{
                padding: 2px 4px;
            }
            .exception div:last-child{
                border-bottom-left-radius: 4px;
                border-bottom-right-radius: 4px;
            }

            /* Exception Variables */
            .exception-var table{
                width: 100%;
                margin: 12px 0;
                box-sizing: border-box;
                table-layout:fixed;
                word-wrap:break-word;            
            }
            .exception-var table caption{
                text-align: left;
                font-size: 16px;
                font-weight: bold;
                padding: 6px 0;
            }
            .exception-var table caption small{
                font-weight: 300;
                display: inline-block;
                margin-left: 10px;
                color: #ccc;
            }
            .exception-var table tbody{
                font-size: 13px;
                font-family: Consolas,"Liberation Mono",Courier,"微软雅黑";
            }
            .exception-var table td{
                padding: 0 6px;
                vertical-align: top;
                word-break: break-all;
            }
            .exception-var table td:first-child{
                width: 28%;
                font-weight: bold;
                white-space: nowrap;
            }
            .exception-var table td pre{
                margin: 0;
            }

            /* Copyright Info */
            .copyright{
                margin-top: 24px;
                padding: 12px 0;
                border-top: 1px solid #eee;
            }
        </style>
    </head>
    <body>
        <?php if(isset($error_level)):?>
        <div class="exception">
            <div class="message">
                <div class="info">
                    <div>
                        <h2><abbr title="<?php echo dirname($file);?>">Error</abbr> in <a class="toggle" title="<?php echo $file.' line '.$line;?>"><?php echo $file.' line '.$line;?></a></h2>
                    </div>
                    <div><h1><?php echo $error_message;?></h1></div>
                </div>

            </div>
            <?php $e = new Exception(); if($trace = $e->getTrace()):?>
            <div class="trace">
                <h2>Call Stack</h2>
                <ol>
                    <?php foreach($trace as $row):?>
                    <?php if(!isset($row['file'])) continue;?>
                    <li>in <a class="toggle" title="<?php echo $row['file'].' line '.$row['line'];?>"><?php echo $row['file'].' line '.$row['line'];?></a></li>
                    <?php endforeach;?>
                </ol>
            </div>
            <?php endif;?>
        </div>
        <?php endif; ?>
        <?php if(isset($e)):?>
        <div class="exception">
            <div class="message">
                <div class="info">
                    <div>
                        <h2><abbr title="<?php echo dirname($e->getFile());?>">ErrorException</abbr> in <a class="toggle" title="<?php echo $e->getFile().' line '.$e->getLine();?>"><?php echo $e->getFile().' line '.$e->getLine();?></a></h2>
                    </div>
                    <div><h1><?php echo $e->getMessage();?></h1></div>
                </div>

            </div>
            <?php if($trace = $e->getTrace()):?>
            <div class="trace">
                <h2>Call Stack</h2>
                <ol>
                    <?php foreach($trace as $row):?>
                    <?php if(!isset($row['file'])) continue;?>
                    <li>in <a class="toggle" title="<?php echo $row['file'].' line '.$row['line'];?>"><?php echo $row['file'].' line '.$row['line'];?></a></li>
                    <?php endforeach;?>
                </ol>
            </div>
            <?php endif;?>
        </div>
        <?php endif; ?>


        <div class="exception-var">
            <h2>Environment Variables</h2>
            <!--           GET参数             -->
            <?php if($_GET):?>
            <h3 class="subheading">GET Data</h3>
            <?php foreach($_GET as $k=>$v):?>
            <div>
                <div class="clearfix">
                    <div class="col-md-3"><strong><?php echo $k;?></strong></div>
                    <div class="col-md-9"><small><?php echo ($v===false?'false':($v===true?'true':$v));?></small></div>
                </div>
            </div>
            <?php endforeach;?>
            <?php else:?>
            <div>
                <div class="clearfix">
                    <div class="col-md-3"><strong>GET Data</strong></div>
                    <div class="col-md-9"><small>empty</small></div>
                </div>
            </div>
            <?php endif;?>
            <!--           POST参数             -->
            <?php if($_POST):?>
            <h3 class="subheading">POST Data</h3>
            <?php foreach($_POST as $k=>$v):?>
            <div>
                <div class="clearfix">
                    <div class="col-md-3"><strong><?php echo $k;?></strong></div>
                    <div class="col-md-9"><small><?php echo ($v===false?'false':($v===true?'true':$v));?></small></div>
                </div>
            </div>
            <?php endforeach;?>
            <?php else:?>
            <div>
                <div class="clearfix">
                    <div class="col-md-3"><strong>POST Data</strong></div>
                    <div class="col-md-9"><small>empty</small></div>
                </div>
            </div>
            <?php endif;?>
            <!--             FILES参数             -->
            <?php if($_FILES):?>
            <h3 class="subheading">Files</h3>
            <?php foreach($_FILES as $k=>$v):?>
            <div>
                <div class="clearfix">
                    <div class="col-md-3"><strong><?php echo $k;?></strong></div>
                    <div class="col-md-9"><small><?php echo json_encode($v);?></small></div>
                </div>
            </div>
            <?php endforeach;?>
            <?php else:?>
            <div>
                <div class="clearfix">
                    <div class="col-md-3"><strong>Files</strong></div>
                    <div class="col-md-9"><small>empty</small></div>
                </div>
            </div>
            <?php endif;?>
            <!--          Cookies参数             -->
            <?php if($_COOKIE):?>
            <h3 class="subheading">Cookies</h3>
            <?php foreach($_COOKIE as $k=>$v):?>
            <div>
                <div class="clearfix">
                    <div class="col-md-3"><strong><?php echo $k;?></strong></div>
                    <div class="col-md-9"><small><?php echo ($v===false?'false':($v===true?'true':$v));?></small></div>
                </div>
            </div>
            <?php endforeach;?>
            <?php else:?>
            <div>
                <div class="clearfix">
                    <div class="col-md-3"><strong>Cookies</strong></div>
                    <div class="col-md-9"><small>empty</small></div>
                </div>
            </div>
            <?php endif;?>
            <!--          Session参数             -->
            <?php if($_SESSION):?>
            <h3 class="subheading">Session</h3>
            <?php foreach($_SESSION as $k=>$v):?>
            <div>
                <div class="clearfix">
                    <div class="col-md-3"><strong><?php echo $k;?></strong></div>
                    <div class="col-md-9"><small><?php echo ($v===false?'false':($v===true?'true':$v));?></small></div>
                </div>
            </div>
            <?php endforeach;?>
            <?php else:?>
            <div>
                <div class="clearfix">
                    <div class="col-md-3"><strong>Session</strong></div>
                    <div class="col-md-9"><small>empty</small></div>
                </div>
            </div>
            <?php endif;?>
            <h3 class="subheading">Server</h3>
            <?php if($_SERVER):?>
            <?php foreach($_SERVER as $k=>$v):?>
            <div>
                <div class="clearfix">
                    <div class="col-md-3"><strong><?php echo $k;?></strong></div>
                    <div class="col-md-9"><small><?php echo ($v===false?'false':($v===true?'true':$v));?></small></div>
                </div>  
            </div>
            <?php endforeach;?>
            <?php endif;?>
            <h3 class="subheading">APP Constants</h3>
            <?php $const = (array)get_defined_constants(true)['user'];?>
            <?php foreach($const as $k=>$v):?>
            <?php if(!defined($k)) continue;?>
            <div>
                <div class="clearfix">
                    <div class="col-md-3"><strong><?php echo $k;?></strong></div>
                    <div class="col-md-9"><small><?php echo ($v===false?'false':($v===true?'true':$v));?></small></div>
                </div>
            </div>
            <?php endforeach;?>
        </div>
        <div>
            <div class="copyright">
                <a title="源码下载" href="https://github.com/lilongsheng0755/APP">APP</a> 
                <span>V1.0</span> 
                <span>{ 轻量级APP框架web }</span>
            </div>
        </div>
    </body>
</html>
<?php exit; ?>
