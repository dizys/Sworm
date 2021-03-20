# Sworm - 基于Swoole的异步MySQL数据库查询构造器
[![License](https://img.shields.io/badge/license-MIT-blue.svg)](http://opensource.org/licenses/MIT)
[![PRs Welcome](https://img.shields.io/github/issues/heikezy/Sworm.svg)](https://github.com/heikezy/Sworm/issues)
[![GitHub stars](https://img.shields.io/github/stars/heikezy/Sworm.svg)](https://github.com/heikezy/Sworm)
[![GitHub forks](https://img.shields.io/github/forks/heikezy/Sworm.svg)](https://github.com/heikezy/Sworm)

Sworm是一个基于Swoole的异步MySQL调用的数据库查询构造器。该框架封装了swoole_mysql，API与NotORM很相似。使用Sworm能更加轻松地生成查询语句，使代码结构更加清晰，返回更加规范。
## 环境要求
Sworm的至少工作在以下环境:

* PHP 5.3.10 +
* Swole 1.7 +

## 快速入门
### 初始化
在使用Sworm前，请确保已先include源码包根目录下的Sworm.php

``` php
$mySworm = new Sworm();
```
### 连接

``` php
$server = array(
     'host' => '192.168.56.102',
     'port' => 3306,
     'user' => 'test',
     'password' => 'test',
     'database' => 'test',
     'charset' => 'utf8', //指定字符集
     'timeout' => 2,  // 可选：连接超时时间（非查询超时时间），默认为SW_MYSQL_CONNECT_TIMEOUT（1.0）
	'prefix' => 'sw_', //可选：表前缀
     'debug' => true //调试模式，开启会在执行查询时输出查询语句
);

$mySworm->connect($server, function($ret){
	if($ret->status){
		printf("连接成功\n");
	}else{
		var_dump($ret->errorCode, $ret->errorMsg);
	}
});
```

### 断开连接

``` php
$mySworm->disconnect();
```

### 徒手Query
``` php
$mySworm->query("SELECT * FROM sw_user WHERE id = '1'", function($ret){
	if($ret->status){
		var_dump($ret->result);
	}else{
		var_dump($ret->errorCode, $ret->errorMsg);
	}
});
```

### 获取表对象
成员属性方式获取
``` php
$user = $mySworm->user;
```
table方法获取
``` php
$user = $mySworm->table('user');
```
**注意：**这里表名前会自动加上connect时填写的prefix前缀参数。则实际上这里访问的表是sw_user。

### 表对象：基本操作
以下方法使用没有先后顺序限制，Sworm会在最终查询时自动生成正确的顺序。

(1) SELECT
``` php
$user->select("username, password, gender")
//或者
$user->select(['username', 'password', 'gender'])
```
(2) WHERE
``` php
//直接填写完整WHERE语句（需手动进行过滤，不推荐）
$user->where("id = '2' AND username = 'yeahyeah'")

//使用预处理占位符
$user->where("id = ? AND username = ?", 2, 'yeahyeah')

//使用数组
$user->where(array(
	'id'=>2,
	'username'=>'yeahyeah'
))

/*数组的更多高级用法*/

//WHERE id IN ('1','2','3')
$user->where(array(
	'id'=>new Sworm_In([1,2,3])
))

//WHERE username LIKE '%张%'
$user->where(array(
	'id'=>new Sworm_Like('%张%')
))

```
同理还有Sworm_NotIn、Sworm_NotLike、Sworm_RegExp(正则表达式)、Sworm_Literal(原式)

(3) ORDER BY

单个字段排序：
``` php
//ORDER BY age
$user->order('age')
//ORDER BY age DESC
$user->order('age DESC')
```
多个字段排序：
``` php
//ORDER BY age
$user->order('age')
//ORDER BY id, age DESC
$user->order('id')->order('age DESC')
/*或者*/ $user->order('id, age DESC')
```

(4) LIMIT

按数量限制：
``` php
// LIMIT 10
$user->limit(10)
```
按数量和偏移量限制（请注意：先数量、再偏移量，与MySQL语句顺序相反）：
``` php
// LIMIT 2,10 
$user->limit(10, 2)//从位置为2的记录开始取出10条记录
```

(5) GROUP BY和HAVING

不带HAVING：
``` php
// GROUP BY note
$user->group('note')
```
带HAVING：
``` php
// GROUP BY note HAVING age > 10
$user->group('note', 'age > 10')
```


### 表对象：查询 (Retrieve)
(1) 获取结果数组：fetch
``` php
$user->select('username, password')
     ->where('id = ?', 1)
     ->fetch(function($ret){
	     if($ret->status){
		     var_dump($ret->result);//成功返回结果数组
	     }else{
			 var_dump($ret->errorCode, $ret->errorMsg);//失败返回错误码和错误信息
		 }
	 });
```
(2) 计数：count
``` php
$user->where('age >= ?', 18)
     ->count(function($ret){
	     if($ret->status){
		     var_dump($ret->result);//成功返回个数(int)
	     }else{
			 var_dump($ret->errorCode, $ret->errorMsg);//失败返回错误码和错误信息
		 }
	 });
```
(2) 求和：sum
``` php
$user->where('age < ?', 18)
     ->sum('money',function($ret){
	     if($ret->status){
		     var_dump($ret->result);//成功返回总和(number)
	     }else{
			 var_dump($ret->errorCode, $ret->errorMsg);//失败返回错误码和错误信息
		 }
	 });
```
(3) 最大值：max
``` php
$user->where('age < ?', 18)
     ->max('money',function($ret){
	     if($ret->status){
		     var_dump($ret->result);//成功返回最大值(number)
	     }else{
			 var_dump($ret->errorCode, $ret->errorMsg);//失败返回错误码和错误信息
		 }
	 });
```
(4) 最小值：min
``` php
$user->where('age < ?', 18)
     ->min('money',function($ret){
	     if($ret->status){
		     var_dump($ret->result);//成功返回最小值(number)
	     }else{
			 var_dump($ret->errorCode, $ret->errorMsg);//失败返回错误码和错误信息
		 }
	 });
```

### 表对象：插入 (Insert)
``` php
$user->insert(array(
		'username'=>'yeahyeah',
		'password'=>'uNsJ2k8mQz'
	),function($ret){
    if($ret->status){
	    var_dump($ret->result);//成功返回影响的记录数
		printf("插入成功\n");
    }else{
		var_dump($ret->errorCode, $ret->errorMsg);//失败返回错误码和错误信息
	}
});
```

### 表对象：更新 (Update)
``` php
$user->where('id = ?', 1)
	 ->update(array(
			 'age'=>19,
			 'password'=>'lmaolmao'
		 ),function($ret){
		     if($ret->status){
			     var_dump($ret->result);//成功返回影响的记录数
			     printf("更新成功\n");
			 }else{
				 var_dump($ret->errorCode, $ret->errorMsg);//失败返回错误码和错误信息
		 }
	 });
```
### 表对象：删除 (Delete)
``` php
$user->where('id = ?', 1)
	 ->delete(function($ret){
		     if($ret->status){
			     var_dump($ret->result);//成功返回影响的记录数
			     printf("删除成功\n");
			 }else{
				 var_dump($ret->errorCode, $ret->errorMsg);//失败返回错误码和错误信息
		 }
	 });
```

### 统一的回调函数参数 $ret
Sworm使用回调函数实现异步，所有回调函数都应统一接受一个 `$ret` 参数。当请求执行完成后进行回调时，Sworm将会把请求结果封装成一个 `Sworm_Result` 对象并作为参数传递给回调函数。

`Sworm_Result` 对象的成员如下：
``` php
/* status 
 * 表示请求是否成功，布尔值，真为成功，假为失败
 */
$ret->status 
//或者
$ret->getStatus()

/* result
 * 表示请求结果，类型根据具体请求而定
 */
$ret->result
//或者
$ret->getResult()

/* errorCode
 * 表示失败错误码
 */
$ret->errorCode
//或者
$ret->getErrorCode()


/* errorMsg
 * 表示失败错误信息
 */
$ret->errorMsg
//或者
$ret->getErrorMsg()

/* sworm
 * 当前Sworm对象，方便回调函数闭包内使用
 */
$ret->sworm
```

### 事务
``` php
$mySworm->begin(function($ret){
	$data = [...];
	$ret->sworm->user->update($data, function($ret){
		if($ret->status){
			//更新成功则提交事务
			$ret->sworm->commit(function($ret){
				echo "事务提交完成\n";
			});
		}else{
			//失败则回滚事务
			$ret->sworm->rollback(function($ret){
				echo "事务回滚完成\n";
			});
		}
	})
})
```
