/**
* 将web Form 的数据转化成json字符串
* 将json对象的值解析后填入web Form
* howwa@sina.com 根据网上搜的资料修改而成
* 2011-5-14
*/

var howwaForm2Json = new Object();

/**
* 将web Form 采集的数据转化成json字符串
* 传入web form对象
* 输出由form元素名称及其值组成的json字符串
* 元素的值全部使用escape()进行了转意处理
* 每个元素对应的值全部是数组
* 格式形如：{"xm":["%u5F20%u4E09"],"xb":["%u7537"],"VIP":["h1","h3","h5"]}
*/
howwaForm2Json.formToJSON = function (form)  {
    var json = '{';
    var isarray = false;
	var i = 0;
	var max = form.elements.length;
	var e,name,lastarr;
	var tmpstr;
	// 循环处理form的元素
    for (i=0; i < max; i++) {
        e = form.elements[i];
		//alert("name="+e.name+"\ntype="+e.type+"\nvalue="+e.value)
        name = e.name;	
        switch (e.type) {
			case 'checkbox':
				if ( json.indexOf('"'+name+'":')<0 ) // 表示该checkbox没有处理过
				{
					tmpstr = howwaForm2Json.getCheckboxResult(form,e);
					if (tmpstr != "")   // 空值不处理
						json += tmpstr+",";
				} 
				break; 
			case 'radio':
				if (!e.checked) { break; }  // 没有被选中，就不处理
				else {json +='';}
			case 'hidden':
			case 'password':
			case 'text':
			case 'textarea':
				tmpstr = e.value;
				if (tmpstr != null )  // 空值不处理
					json += '"' + name + '":"' + escape(tmpstr) + '",';
				break;
			case 'select-one':
			case 'select-multiple':
				tmpstr = howwaForm2Json.getSelectResult(e);
				if (tmpstr != "")   // 空值不处理
					json += tmpstr+",";
				break;
			case 'button':break;
			case 'file':
			case 'image':
			case 'reset':
			case 'submit':
				break;
			default:
        }
    }
	//alert(json);
    return json.substring(0, json.length - 1) + '}'; // 返回json字符串
	
}

/**
* 取得SELECT的值组成的json字符串
* 传入SELECT对象
* 输出由SELECT元素名称及其被选取的选项的值组成的json字符串
*     形如：name:["v1","v2","v3]
*/
howwaForm2Json.getSelectResult = function (oS) {
	var l = oS.options.length;
	var i = 0;
	var eName=oS.name;
	var eValue="";
	for (i=0; i<l; i++)
	{
	  if (oS.options[i].selected)
	  {
		 eValue += ',"'+escape(oS.options[i].value)+'"';
		 
	  }
	}
	if (eValue != "")
	{
		eValue = '"'+eName+'":['+ eValue.substr(1)+"]"
	}
	return eValue;
}

/**
* 取得form中所有同名的checkbox的值组成的json字符串
* 传入form和checkbox对象
* 输出由checkbox元素名称及其值组成的json字符串
*     形如：checkboxname:["c1","c2","c3]
*/
howwaForm2Json.getCheckboxResult = function (form,e) {
	var  max = form.elements.length;
	var i=0;
	var oE;
	var strTemp="";
    for (i=0; i < max; i++) {
		oE = form.elements[i];
		if (oE.name != e.name)
			continue;   // 元素名称不同，就跳过
		if (oE.checked)  // 只有选中的才处理
			strTemp += ',"'+oE.value+'"';
	}
	if (strTemp != "")
	{
		strTemp = '"'+e.name+'":['+ strTemp.substr(1)+"]"
	}
return strTemp;
}

/**
* 从json对象中返回值，不存在或出错时返回null
*/
howwaForm2Json.getJsonItem = function (oJ, strName, iIndex){
    try {
          return unescape( oJ[strName][iIndex] );  
       } catch (e) {
          return null;     // 出错返回null
       }
}

/**
* 从json对象中返回值，不存在或出错时返回空字符串
*/
howwaForm2Json. getJsonItem2 = function(oJ, strName, iIndex){
    try {
          return unescape( oJ[strName][iIndex] );  
       } catch (e) {
          return "";  //出错返回空字符串
       }
}

/**
* 从json对象中返回值的字符串组合，不存在或出错时返回空字符串
*/
howwaForm2Json. getJsonItemStr = function(oJ, strName){
    var r="";
    try {
          r =  oJ[strName][0] ;  
       } catch (e) {
          return "";  //出错返回空字符串
       }
       r =  ","+oJ[strName].join(",");
       if  ( r.substr(r.length-1) != "," )  r += ",";   //组合成字符串返回.前后有逗号，未用unescape反转意
      return r; 
}

/**
* 将json对象的数据填入web Form中
* 传入json对象和web form对象
* 传入json对象的数据应使用escape()进行了转意处理
* 传入json对象的每个元素对应的值全部是数组
* 传入json对象形如：{"xm":["%u5F20%u4E09"],"xb":["%u7537"],"VIP":["h1","h3","h5"]}
*/
howwaForm2Json.JSONToform = function (form, oJson)  {
	var max = form.elements.length; 
	var i = 0;
	var chkRadioName = ",";
	var e,  ename,  eValue="";
	for (i=0; i<max; i++)
	{
	e = form.elements[i];
	ename=e.name;
	switch (e.type) {
			case 'checkbox':
			case 'radio':
				if ( chkRadioName.indexOf(","+ename+",")<0 ) // 表示该name的checkbox或radio没有被处理过，需要处理
				{
				    chkRadioName += ename+",";
				     howwaForm2Json.handchkradio(form, ename, howwaForm2Json. getJsonItemStr(oJson, ename));
                                                                                      }
			                     break; 
			case 'hidden':
			case 'password':
			case 'text':
			case 'textarea':
				e.value = howwaForm2Json. getJsonItem2(oJson, ename,0); //这类元素直接回填
				break;
			case 'select-one':
			case 'select-multiple':
				howwaForm2Json.handselect(e, howwaForm2Json. getJsonItemStr(oJson, ename));
				break;
			default:	
	
	}
         }
}

/**
* 回填CheckBox、Radio元素的值
*/
howwaForm2Json.handchkradio = function (form, ename,  strValue) {
	var  max = form.elements.length;
	var i=0;
	var oE;
	var strTemp="";
	for (i=0; i < max; i++) {
		oE = form.elements[i];
		if (oE.name != ename)
			continue;   // 元素名称不同，就跳过
		oE.checked = false;
		if ( strValue.indexOf(","+escape(oE.value)+",") >=0 )   // 说明该项应当被选中
                                                  oE.checked = true;
	}
}

/**
* 回填SELECT元素的值
*/
howwaForm2Json.handselect = function(e, strValue) {
	var max = e.options.length;
	var i = 0;
	for (i=0; i<max; i++)
	{
	      e.options(i).selected = false;
	      if ( strValue.indexOf(","+escape(e.options(i).value)+",") >=0 )
		 e.options(i).selected = true;
	}
}

/*********************************/