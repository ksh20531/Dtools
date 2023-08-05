@extends('layouts.app')

@section('style')
@endsection

@section('content')
<div class="menu-list"></div>
@endsection

@section('script')
<script type="text/javascript">
	$(function(){
		getMenu();
	});

	function getMenu(url = "/getMenu"){
		console.log('getMenu');
		// var ajax_data = {
		
		// }

		$.ajax({    
			type : 'get',
			url : url,
			data : {},    
			success : function(result) {
				$('.menu-list').html(result);
				$(".pagination").unbind('click').on('click','.page-item a', function(e){
					var url = $(this).attr('href');
					// console.log(url);
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