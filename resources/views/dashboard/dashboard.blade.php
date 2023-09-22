@extends('layouts.app')

@section('style')
<style type="text/css">
	#content{
		width: 100%;
		height: 80vh;
		vertical-align: top;
	}
	#btn-delete{
		display: none;
	}
	.title-area{
		margin: 10px 0px 5px 0px;
	}
	.btn-area{
		margin-top: 5px;
		text-align: right;
	}
</style>
@endsection

@section('content')
<div class="title-area">
	<input id="title" type="text" style="width: 100%;" value="@if($type == 'modify'){{ $dashboard->title }}@endif">
</div>
<div class="content-area">
	<textarea id="content">@if($type == 'modify'){{ $dashboard->content }}@endif</textarea>
</div>
<div class="btn-area">
	<button class="btn btn-primary btn-sm" id="btn-submit" onclick="submitPost()">확인</button>
	<button class="btn btn-danger btn-sm" id="btn-delete" onclick="deletePost()">삭제</button>
</div>
@endsection

@section('script')
<script type="text/javascript">
	$(function(){
		authCheck();
	});

	function submitPost(url = "/dashboard/"+{{ $id }}+"/edit"){
		console.log("submitPost");

		if(elemntCheck() == 'success'){
			var ajax_data = {
				title: $("#title").val(),
				content: $("#content").val()
			}
			$.ajax({    
				type : 'get',
				url : url,
				data : ajax_data,    
				success : function(result) {
					if(result['result'] == 'create_success'){
						alert('success');
						window.open('/dashboard/'+result['return_id'], '_self');
					}else if(result['result'] == 'edit_success'){
						alert('success');
						location.reload();
					}else{
						alert('fail');
					}
				},    
				error : function(request, status, error) {
					console.log(error)    
				}
			})
		}
	}

	function elemntCheck(){
		if($("#title").val() == null || $("#title").val() == '' || $("#title").val() == undefined){
			alert('제목을 입력해 주세요.');
			return 'fail';	
		}else if($("#content").val() == null || $("#content").val() == '' || $("#content").val() == undefined){
			alert('내용을 입력해 주세요.');
			return 'fail';
		}else{
			return 'success';
		}
	}

	function deletePost(url = {{ $id }}){
		$.ajax({
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},
			type : 'delete',
			url : url,
			data : {},    
			success : function(result) {
				if(result == 'success'){
					alert('삭제 성공')
					window.open('/dashboard', '_self');
				}else{
					alert('삭제 실패')
				}
			},    
			error : function(request, status, error) {
				console.log(error)    
			}
		})
	}

	function authCheck(){
		console.log("authCheck");
		@if(!empty($dashboard) && $dashboard->user_id != auth()->id())
		$("#title").attr('readonly', true);
		$("#content").attr('readonly', true);
		$("#btn-submit").attr('disabled', true);
		@elseif(!empty($dashboard) && $dashboard->user_id == auth()->id())
		$("#btn-delete").show();
		@endif
	}
</script>
@endsection