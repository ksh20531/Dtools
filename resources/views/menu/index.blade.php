@extends('layouts.app')

@section('style')
<style type="text/css">
</style>
@endsection

@section('content')
<div id="menu-list"></div>
@endsection

@section('script')
<script type="text/javascript">
	$(function(){
		getMenu();
	});

	function getMenu(url = "/getMenu"){
		$.ajax({    
			type : 'get',
			url : url,
			data : {},    
			success : function(result) {
				$('#menu-list').html(result);
				$(".pagination").unbind('click').on('click','.page-item a', function(e){
					var url = $(this).attr('href');
					e.preventDefault();
					getMenu(url);
				});
			},    
			error : function(request, status, error) {
				console.log(error)    
			}
		})
	}
</script>
@endsection
