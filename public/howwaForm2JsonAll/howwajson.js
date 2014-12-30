/******************************
 *功能：处理json数据
 *1.将json字符串转换成json对象
 *2.将json对象转换成json字符串
 *howwa@sina.com,2011-3-20
 ****************************/

var howwaJson = new Object();

/**
 *  将字符串转化为json对象的方法一
 */
howwaJson.string2json1 = function(strJson){
	try
	{
		var j = "(" + strJson +")";  // 用括号将json字符串括起来
		alert(j);
		alert(strJson);
	    return eval(j); // 返回json对象
	}
	catch (e)
	{
		return null;
	}
}

/**
 *  将字符串转化为json对象的方法二
 */
howwaJson.string2json2 = function(strJson){
	try
	{
		eval("var j = " + strJson);  // 得到一个变量
		return j; // 返回变量的值
	}
	catch (e)
	{
		return null;
	}
}

/**
 *  将字符串转化为json对象的方法三
 */ 
howwaJson.string2json3 = function(strJson){
	try
	{
		var f = new Function("return " + strJson + ";"); // 得到一个函数
		return f(); // 执行函数，并返回函数的值
	}
	catch (e)
	{
		return null;
	}
}

/**
 *  将简单的json对象转换成字符串（不能处理包含数组和嵌套json对象的情况）
 */
howwaJson.simplejson2string = function (oJson) {
	var s=""
	for(var property in oJson){
		s += ( ',"'+property +'":"'+oJson[property].valueOf()+'"' );
	}
	return s.substring(1);
}

/**
 * 将json对象转换成字符串
 * 引用：http://hi.baidu.com/ruson/blog/item/a1f740540c5aee5d574e0002.html
 */
howwaJson.json2string = function(o) {
	// 闭包函数，处理不同的json属性
	var fmt = function(s) {
		if (s == null) {
			return "null";
		}
		switch (s.constructor) {
			case Array: 
				return "[" + howwaJson.json2string(s) + "]";
			case Object: 
				return howwaJson.json2string(s); 
			case Number: 
				return s; 
			case String: 
				return '\"' + s.replace(/\//g, '\\/') + '\"'; 
			case Boolean: 
				return s ? "true" : "false"; 
		} 
	}
	// 开始解析json对象
	if (o == null) return "";
	var arr = [];

	for (var i in o) {
		if (/\d+/.test(i))
			arr.push(fmt(o[i]));
		else
			arr.push('\"' + i + '\":' + fmt(o[i])); 
	}

	if (o.constructor == Object)
		return '{' + arr.join(',') + '}';
	else
		return arr.join(',');
} 

/**********************************
 * 处理json数据的对象的函数定义结束
 *********************************/
