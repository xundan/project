;$(function(){

	//全选的实现
	$(".check-all").click(function(){
		$(".ids").attr("checked",this.checked);
	});

	$(".ids").click(function(){
		var option=$(".ids");

		option.each(function(i){
			if(!this.checked){
				$(".check-all").attr('checked',false);
				return false;
			}else{
				$(".check-all").attr('checked',true);
			}
		})
	})

	$(".confirm").click(function(){
		if(!confirm('确认要执行该操作吗?')){
			return false;
		}
	})
})