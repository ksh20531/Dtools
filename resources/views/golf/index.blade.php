\@extends('layouts.app')

@section('style')
<style type="text/css">
	.info-area{
		display: inline-block;
		margin: 10px 0px 10px 0px;
		width: 100%;
		font-size: 20px;
	}
	.result-area{
		display: inline-block;
		background-color: green;
		width: calc(50% - 5px);
		height: 100%;
	}
	.info-elem{
		margin: 5px 0px 5px 5px;
	}
	.info-label{
		display: inline-block;
		width: 130px;
	}
	.table{
		font-size: 20px;
	}
	.modal-elem{
		display: inline-block;
		margin-right: 10px;
	}
</style>
@endsection

@component('components.modal', [
    'id'    => 'modal-id',
    'class' => 'additional classes',
])
    @slot('title','예약 시간 변경')

    @slot('body')

    @endslot

    @slot('footer')
		<button class="btn btn-danger btn-sm" onclick="closeModal()">닫기</button>
    @endslot
@endcomponent

@section('content')
<div class="info-area">
	<div class="info-elem">
		<div class="info-label">골프장</div>
		<select class="fields" onchange="selectField(this)">
			@foreach($fields as $field)
			<option value="{{ $field->id }}" data-value="{{ $field->time }}">{{ $field->name }}</option>
			@endforeach
		</select>
	</div>
	<div class="info-elem">
		<div class="info-label">예약 시작 시간</div>
		<div class="info-label reserve-start-time">09:00</div>
	</div>
	<div class="info-elem">
		<div class="info-label">예약 시간</div>
		<input type="text" class="reserve-time" placeholder="2023-08-10 11:30:00">
	</div>
	<button class="btn btn-primary btn-sm info-elem" style="font-size: 20px;" onclick="makeReservation()">예약</button>
	<button class="btn btn-primary btn-sm info-elem" style="font-size: 20px;" onclick="openResult()">결과</button>
</div>
<div class="reservation-list">
	<table class="table">
		<tr>
			<th>id</th>
			<th>골프장</th>
			<th>예약시간</th>
			<th>상태</th>
			<th>취소</th>
		</tr>
	</table>
</div>
@endsection

@section('script')
<script type="text/javascript">
	$(function(){
		getField();
		$(".reserve-time").keypress(function(e){
			if(e.keyCode && e.keyCode == 13){
				makeReservation();
			}
		});
	});

	function getField(url = "/api/golfs"){
		$.ajax({    
			type : 'get',
			url : url,
			data : {},    
			success : function(result) {
				var data = result.response;
				data.forEach(elem => {
                    $(".table").append(`<tr>
                    	<td>${elem.id}</td><td>${elem.field.name}</td>
                    	<td><div style="display:inline-block; width: 200px;">${elem.reservation_time}</div>
                    	${elem.is_played == 0 ? '<button class="btn btn-primary btn-sm" onclick="openModal('+`${elem.id}`+')">변경' : ''}
                    	<td>${elem.is_played == 0 ? '예정' : '<del>완료'}</td>
                    	<td>${elem.is_played == 0 ? '<button class="btn btn-danger btn-sm" onclick="cancleReservation('+`${elem.id}`+')">취소' : ''}</td>
                    	</tr>`);
				})
			},    
			error : function(request, status, error) {
				console.log(error)    
			}
		})
	}

	function selectField(elem){
		var field_id = $(elem).val();
		var field_name = $(elem).find("option:selected").text();
		var field_reserve_start_time = $(elem).find("option:selected").data("value");

		$(".reserve-start-time").text(field_reserve_start_time);

		if(field_id == 5 || field_id == 6)
			alert(field_name+'용입니다.\n다른 골프장을 선택해주세요.');
	}

	function makeReservation(){
		var field_id = $('.fields').val();
		var field_name = $('.fields').find("option:selected").text();
		var field_reserve_start_time = $('.fields').find("option:selected").data("value");
		var reserve_time = $('.reserve-time').val();

		if(reserve_time == '' || reserve_time == null || reserve_time == undefined){
			alert('시간을 입력 해주세요.');
			return;
		}

		if(field_id == 5 || field_id == 6){
			alert(field_name+'용입니다.\n다른 골프장을 선택해주세요.');
			return;
		}

		ajax_data = {
			field_id : field_id,
			field_reserve_start_time : field_reserve_start_time,
			reserve_time : reserve_time,
		}

		$.ajax({    
			type : 'post',
			url : 'api/golfs',
			data : ajax_data,    
			success : function(result) {
				var reservation = result.response;
				alert(
						reservation.field.name
						+ "  "
						+ reservation.reservation_time
						+ "\n등록 되었습니다."
					)
				location.reload();
			},    
			error : function(request, status, error) {
				console.log(error)    
			}
		})
	}

	function cancleReservation(id){
		$.ajax({    
			type : 'delete',
			url : 'api/golfs/'+id,
			data : {},    
			success : function(result) {
				var reservation = result.response;
				if(result.success == 'success'){
					alert(
							reservation.field.name
							+ "  "
							+ reservation.reservation_time
							+ "\n취소 되었습니다."
						)
					location.reload();
				}
			},    
			error : function(request, status, error) {
				console.log(error)    
			}
		})
	}

	function modifyReservation(id){
		var field_id = $('.modal-field-id').text().trim();
		var reserve_time = $('.modal-reserve-time').val();

		if(reserve_time == '' || reserve_time == null || reserve_time == undefined){
			alert('시간을 입력 해주세요.');
			return;
		}

		ajax_data = {
			field_id : field_id,
			reserve_time : reserve_time,
		}

		$.ajax({    
			type : 'put',
			url : 'api/golfs/'+id,
			data : ajax_data,    
			success : function(result) {
				console.log(result);
				if(result.success == 'success'){
				var reservation = result.response;
					alert(
							reservation.field.name
							+ "  "
							+ reservation.reservation_time
							+ "\n변경 되었습니다."
						)
					closeModal();
					location.reload();
				}
			},    
			error : function(request, status, error) {
				console.log(error)    
			}
		})
	}


	function openModal(id){
    	$(".modal").modal("show");

    	$.ajax({    
			type : 'get',
			url : 'api/golfs/'+id,
			data : {},    
			success : function(result) {
				var reservation = result.response;
				$('.modal-body').append(`<div>
						<div class="modal-elem ">${reservation.id}</div>
						<div class="modal-elem modal-reservation-name">${reservation.field.name}</div>
						<div class="modal-elem modal-field-id" style="display:none;">${reservation.field.id}</div>
						<div class="modal-elem"><input class="modal-reserve-time" type="text" value="${reservation.reservation_time}" placeholder="${reservation.reservation_time}"></div>
					</div>`)
				$('.modal-footer').append(`<button class="btn btn-primary btn-sm" onclick="modifyReservation(${reservation.id})">변경</button>`)

			},    
			error : function(request, status, error) {
				console.log(error)    
			}
		})
	}

	function closeModal(){
		$('.modal-body').empty();
		$(".modal").modal("hide");
	}

	function openResult(){
		window.location = "/result";
	}
	
</script>
@endsection
