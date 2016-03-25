function empty(val){return val==undefined||val==""}function in_array(val,arr){for(var i in arr){if(arr[i]===val){return true}}return false}function handleParams(params){var other="";if(params!=undefined&&typeof params=="object"){for(var i in params){other+=" "+i+'="'+params[i]+'" '}}return other}function Label(content,params){return"<label "+handleParams(params)+"> "+content+" </label>"}function createInput(type,params){return'<input type="'+type+'" '+handleParams(params)+" />"}function createRadio(data,checked,params){var html="",params=handleParams(params);if(data!=undefined&&typeof data=="object"){for(var i in data){var check=checked==i?' checked="checked" ':"";html+='<label> <input type="radio" '+params+check+' value="'+i+'" /> '+data[i]+" </label> "}}return html}function createSelect(data,selected,params){var html="",params=handleParams(params);if(data!=undefined&&typeof data=="object"){html+="<select "+params+">";for(var i in data){var select=i==selected?' selected="selected" ':"";html+='<option value="'+i+'" '+select+" >"+data[i]+"</option>"}html+="</select>"}return html}function createFile(params){var html="";if(params==undefined){params={}}html+='<div id="uniform-fileInput" class="uploader">';html+=createInput("hidden",params);params["class"]="input-file uniform_on fileUpload";params["name"]="file"+params["name"];html+=createInput("file",params);html+='<span class="filename" style="-moz-user-select: none;">请选择文件</span>';html+='<span class="action" style="-moz-user-select: none;">上传文件</span>';html+="</div>";return html}function verifyUpload(uploadObj,size,allowType,fileurl){var obj=uploadObj.files[0],arr=[false,"对不起！上传文件超过指定值..."],num=obj.name.indexOf("."),fileext=obj.name.substr(num+1).toLocaleLowerCase();if(allowType==undefined){allowType=["jpeg","jpg","gif","png"]}if(obj.size<size){arr[1]="对不起！上传文件类型错误...";if(in_array(fileext,allowType)){if(fileurl!=undefined){var link=uploadObj.url.indexOf("?")>=0?"&":"?";uploadObj.url+=link+"fileurl="+fileurl}arr=[true,"文件上传成功！"]}}return arr}function stringTo(type,value){switch(type){case"int":case"int8":case"int16":case"int32":case"int64":case"uint":case"uint8":case"uint16":case"uint32":case"uint64":return parseInt(value);case"bool":return value==="true"||value===true||value===1||value=="1";case"float32":case"float64":}return value}Date.prototype.Format=function(fmt){var o={"M+":this.getMonth()+1,"d+":this.getDate(),"h+":this.getHours(),"m+":this.getMinutes(),"s+":this.getSeconds(),"q+":Math.floor((this.getMonth()+3)/3),"S":this.getMilliseconds()};if(/(y+)/.test(fmt)){fmt=fmt.replace(RegExp.$1,(this.getFullYear()+"").substr(4-RegExp.$1.length))}for(var k in o){if(new RegExp("("+k+")").test(fmt)){fmt=fmt.replace(RegExp.$1,(RegExp.$1.length==1)?(o[k]):(("00"+o[k]).substr((""+o[k]).length)))}}return fmt};function timeToString(time){var date=new Date(time*1000);return date.Format("yyyy-MM-dd hh:mm:ss")}function statusToString(td,data,rowdatas,row,col){var str='<span class="label label-'+(data==1?'success">启用':'important">停用')+"</span>";$(td).html(str)}function dateTimeString(td,cellData,rowData,row,col){$(td).html(timeToString(cellData))}function setOperate(td,data,rowdata,row,col){var str='<a class="btn btn-success" href="javascript:myTable.views('+row+');"><i class="icon-zoom-in "></i></a> ';str+='<a class="btn btn-info" href="javascript:myTable.update('+row+');"><i class="icon-edit "></i></a> ';str+='<a class="btn btn-danger" href="javascript:myTable.delete('+row+');"><i class="icon-trash "></i></a>';$(td).html(str)};