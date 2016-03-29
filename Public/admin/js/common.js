
/**** 此类函数为后台公共函数 ***/

/*** 全局ajax加载start  ***/	
var hzj_loading; //全局变量
	
function ajax_start_loading(){
	hzj_loading = layer.load(0,{shade:[0.3,'#676767']});
}

function ajax_stop_loading(){
	layer.close(hzj_loading);
}

jQuery(function($) {
	$( document ).ajaxStart(function() {
  		ajax_start_loading();
	}).ajaxStop(function() {
  		ajax_stop_loading();
	}).ajaxError(function() {
  		ajax_stop_loading();
	});
});

/*** 全局ajax加载end  ***/

/*** 勾选表格start  ***/	
//全选
$(function(){
	
	$('table th input:checkbox').on('click' , function(){
		$(this).parent('span').toggleClass('checked');
		$('#dataTable').find('.checker span').toggleClass('checked');  
	});
	$('#dataTable').find('.checker').on('click',function(){
		$(this).find('span').toggleClass('checked'); 
	})
	
})

/*** 勾选表格end  ***/	






				