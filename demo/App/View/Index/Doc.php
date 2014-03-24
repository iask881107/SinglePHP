<?php
$data = array(
    'title' => '详细文档',
);
View::tplInclude('Public/header', $data); ?>
    <div class="bs-header" id="content">
      <div class="container">
        <h1>详细文档</h1>
        <p>完整介绍SinglePHP的使用。</p>
      </div>
    </div>
    <div class="container bs-docs-container">
      <div class="row">
        <div class="col-md-3">
          <div class="bs-sidebar hidden-print" role="complementary">
            <ul class="nav bs-sidenav">
                <li>
                  <a href="#conf">配置</a>
                </li>
                <li>
                  <a href="#router">路由</a>
                </li>
                <li>
                  <a href="#controller">控制器</a>
                </li>
                <li>
                  <a href="#db">数据库操作</a>
                </li>
                <li>
                  <a href="#view">视图引擎</a>
                </li>
                <li>
                  <a href="#widget">Widget功能</a>
                </li>
                <li>
                  <a href="#log">日志</a>
                </li>
            </ul>
          </div>
        </div>
        <div class="col-md-9" role="main">
            <div class="bs-docs-section">
              <div class="page-header">
                <h1 id="conf">配置</h1>
              </div>
              <p class="lead">项目配置需要在入口文件传递给SinglePHP，目前支持的配置如下：</p>
              <div class='highlight'>
              <pre><code class='language-php'>$config = array(
    'APP_PATH'    =&gt; './App/',       #APP业务代码文件夹
    'DB_HOST'     =&gt; '127.0.0.1',    #数据库主机地址
    'DB_PORT'     =&gt; '3306',         #数据库端口，默认为3306
    'DB_USER'     =&gt; 'root',         #数据库用户名
    'DB_PWD'      =&gt; 'toor',         #数据库密码
    'DB_NAME'     =&gt; 'singlephp',    #数据库名
    'DB_CHARSET'  =&gt; 'utf8',         #数据库编码，默认utf8
    'PATH_MOD'    =&gt; 'NORMAL',       #路由方式，支持NORMAL和PATHINFO，默认NORMAL
    'USE_SESSION' =&gt; true,           #是否开启session，默认false
);
SinglePHP::getInstance($config)-&gt;run();
              </code></pre>
              </div>
            </div>
            <div class="bs-docs-section">
              <div class="page-header">
                <h1 id="router">路由</h1>
              </div>
              <p class="lead">目前路由支持NORMAL和PATHINFO两种方式</p>
              <h3>NORMAL方式</h3>
              <p class="lead">在NORMAL方式下，必须通过url的c和a参数来指定对应的controller和action，默认都是Index。url的路由关系示例：</p>
                <div class="highlight">
                <pre><code>index.php                //IndexController-&gt;IndexAction
index.php?a=Test         //IndexController-&gt;TestAction
index.php?c=Test         //TestController-&gt;IndexAction
index.php?c=Test&amp;a=Test  //TestController-&gt;TestAction
</code></pre>
                </div>
              <h3>PATHINFO方式</h3>
              <p class="lead">PATHINFO方式需要webserver支持PATHINFO，可以通过var_dump($_SERVER['PATH_INFO']);来查看。如果webserver不支持PATHINFO，而又配置成了PATHINFO方式的路由，SinglePHP将会忽略此项配置而采用NORMAL方式路由。示例如下：</p>
                <div class="highlight">
                <pre><code>index.php            //IndexController-&gt;IndexAction
index.php/Test       //TestController-&gt;IndexAction
index.php/Test/Test  //TestController-&gt;TestAction
</code></pre>
                </div>
              <p class='lead'>同时可以配合webserver的rewrite功能将index.php去掉，美化url。</p>
            </div>
            <div class="bs-docs-section">
              <div class="page-header">
                <h1 id="controller">控制器</h1>
              </div>
              <p class="lead">所有的控制器必须继承Controller类或其子类，并且类名必须以Controller结尾。</p>
              <p class="lead">每一个Action对应控制器类的一个方法，方法名必须以Action结尾，同时必须是public权限。</p>
              <p class="lead">示例代码如下：</p>
              <div class='highlight'>
              <pre><code class="language-php">&lt;?php
class IndexController extends Controller {  //控制器必须继承Controller类或其子类
    public function IndexAction(){          //public权限，方法名以Action结尾
    }
    public function other(){                //方法名不是以Action结尾，不可以被直接路由到
    }
}</code></pre>
              </div>
              <p class="lead">同时控制器提供了几个方法用来简化操作：</p>
              <div class='highlight'>
              <pre><code class="language-php">&lt;?php
class IndexController extends Controller { 
    public function RedirectAction(){ 
        $this-&gt;redirect('http://www.baidu.com'); //302跳转到百度
    }
    public function AjaxAction(){
        $ret = array(
            'result' =&gt; true,
            'data'   =&gt; 123,
        );
        $this-&gt;AjaxReturn($ret);                //将$ret格式化为json字符串后输出到浏览器
    }
}</code></pre>
              </div>
            </div>
            <div class="bs-docs-section">
              <div class="page-header">
                <h1 id="db">数据库操作</h1>
              </div>
              <p class="lead">SinglePHP不能称为一个MVC框架的原因就是SinglePHP没有M。为了精简代码，SinglePHP只提供了简单的query和execute，并没有做模型和数据表的映射。</p>
              <p class='lead'>可以通过M函数来便捷的获取数据库对象（没错就是抄的thinkphp你咬我啊）。代码如下：</p>
              <div class='highlight'>
              <pre><code class="language-php">&lt;?php
$db = M();  //获取数据库对象，前提是在入口文件配好数据库相关的配置
$name = $db-&gt;escape($_GET['name']);  //转义字符
//查询，失败返回false，否则返回数据
$ret = $db-&gt;query("select * from user where name = $name");
echo $db-&gt;getRows();  //获得返回的行数
echo $db-&gt;getLastSql();  //获得上一次执行的sql
//执行增删改语句，失败返回false，否则返回影响的行数
$count = $db-&gt;execute("insert user (name, email) values ('leo108', 'leo108@qq.com')");
echo $db-&gt;getRows();  //获得返回的行数
echo $db-&gt;getInsertId();  //获得自增ID
echo $db-&gt;getError();  //如果执行sql失败，可以获得失败原因</code></pre>
              </div>
            </div>

        </div>
      </div>

    </div>

<?php View::tplInclude('Public/footer'); ?>
