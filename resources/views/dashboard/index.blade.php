@extends('layouts.app')

@section('style')
@endsection

@section('content')
<div>
	<button class="btn btn-primary btn-sm" onclick="createDashboard()">create</button>
</div>
<div class="dashboard-list"></div>
@endsection

@section('script')
<script type="text/javascript">
	$(function(){
		getDashboard();
	});

	function getDashboard(url = "/getDashboard"){
		console.log('getDashboard');
		// var ajax_data = {
		
		// }

		$.ajax({    
			type : 'get',
			url : url,
			data : {},    
			success : function(result) {
				$('.dashboard-list').html(result);
				$(".pagination").unbind('click').on('click','.page-item a', function(e){
					var url = $(this).attr('href');
					// console.log(url);
					e.preventDefault();
					getDashboard(url);
				});
			},    
			error : function(request, status, error) {
				console.log(error)    
			}
		})
	}

	function createDashboard(){
		$.ajax({
			type : 'get',
			url : '/dashboard/0',
			data : {},    
			success : function(result) {
				window.open('/dashboard/0')
			},    
			error : function(request, status, error) {
				console.log(error)    
			}
		})
	}
</script>
@endsection