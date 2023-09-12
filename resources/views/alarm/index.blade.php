@extends('layouts.app')

@section('style')
<style type="text/css">
	.btn-area{
		margin: 5px 0px 0px 0px;
		text-align: right;
	}
	.routine-container{
		margin: 5px 0px 0px 0px;
		height: 80vh;
		overflow: scroll;
	}
	.routine{
		margin: 0px 0px 10px 0px;
		border: 1px solid black;
		width: 100%;
		height: 150px;
	}
	.routine-body{
		height: 102px;
	}
	.add-alarm{
		display: inline-block;
		border: 1px solid black;
		margin: 0px 5px 0px 5px;
		width: 70px;
		height: 102px;
	}
	.alarm-area{
		display: inline-block;
		width: calc(100% - 100px);
		overflow-x: scroll;
		white-space: nowrap;
	}
	.alarm-btn-area{
		display: inline-block;
		width: 50px;
		height: 100%;
		vertical-align: top;
	}
	.wrap-vertical::-webkit-scrollbar{
		display: none; 
	}
	.alarm{
		display: inline-block;
		border: 1px solid black;
		width: 70px;
		height: 100px;
	}
	.alarm-day-area{
		margin-bottom: 1rem;
	}
	.day-checkbox{
		display: none;
	}
	.alarm-day{
		display: inline-block;
		cursor: pointer;
	}
	.alarm-day-selected{
		background-color: red;
	}
	.alarm-hour{
		width: 50px;
	}
	.alarm-minute{
		width: 50px;
	}
</style>

@component('components.modal', [
    'id'    => 'modal-id',
    'class' => 'additional classes',
])
    @slot('title')

    @slot('body')
    	<div class="routine-id" style="display: none;"></div>
    	<div class="alarm-day-area">
    		<input type="checkbox" class="day-checkbox" id="day-checkbox">
    		<div class="alarm-day" onclick="selectDay(this)" data-value="0">일</div>
    		<input type="checkbox" class="day-checkbox" id="day-checkbox">
    		<div class="alarm-day" onclick="selectDay(this)" data-value="1">월</div>
    		<input type="checkbox" class="day-checkbox" id="day-checkbox">
    		<div class="alarm-day" onclick="selectDay(this)" data-value="2">화</div>
    		<input type="checkbox" class="day-checkbox" id="day-checkbox">
    		<div class="alarm-day" onclick="selectDay(this)" data-value="3">수</div>
    		<input type="checkbox" class="day-checkbox" id="day-checkbox">
    		<div class="alarm-day" onclick="selectDay(this)" data-value="4">목</div>
    		<input type="checkbox" class="day-checkbox" id="day-checkbox">
    		<div class="alarm-day" onclick="selectDay(this)" data-value="5">금</div>
    		<input type="checkbox" class="day-checkbox" id="day-checkbox">
    		<div class="alarm-day" onclick="selectDay(this)" data-value="6">토</div>
    	</div>
    	<div class="alarm-time">
    		<input class="alarm-hour" type="text">
    		:
    		<input class="alarm-minute" type="text">
    	</div>
    @endslot

    @slot('footer')
    	<button class="btn btn-primary btn-sm" id="btn-submit" onclick="submitAlarm()">submit</button>
		<button class="btn btn-danger btn-sm" onclick="closeModal()">close</button>
    @endslot
@endcomponent

@section('content')
<div class="btn-area">
	<button class="btn btn-primary btn-sm" onclick="addRoutine()">루틴추가</button>
</div>
<div class="routine-container"></div>

<template id="rc" class="rc">
	<div class="routine">
		<div class="routine-header">
			<input class="routine-title">
			<button class="btn btn-primary btn-sm" onclick="createRoutine(this)">create</button>
		</div>
		<div class="routine-body">
		</div>
	</div>
</template>
@endsection

@section('script')
<script type="text/javascript">
    $(function(){
    	$('.modal').modal({backdrop: 'static'});
    	getRoutine();
    });

    function getRoutine(url = "/getRoutine"){
		$.ajax({    
			type : 'get',
			url : url,
			data : {},    
			success : function(result) {				
				$('.routine-container').html(result);
			},    
			error : function(request, status, error) {
				console.log(error)    
			}
		});
    }

    function addRoutine(){
    	var template = document.getElementById('rc');
    	var body = document.getElementsByClassName('routine-container')[0];
    	body.prepend(template.content);
    }

    function createRoutine(elem){
  		var url = 'alarm/create';
    	var title = $(elem).prev().val();

    	ajax_data = {
    		'type': 'routine',
    		'title': title,
		};

		$.ajax({    
			type : 'get',
			url : url,
			data : ajax_data,    
			success : function(result) {				
				if(result == 'success')
					getRoutine();
			},    
			error : function(request, status, error) {
				console.log(error)    
			}
		});
    }

    function closeModal(){
    	var modalDelete = $(".alarm-delete");
    	var modalTitle = $(".modal-title-input");
    	var modalCheckBox = $(".day-checkbox");
    	var modalDate = $(".alarm-day");
    	var modalHour = $(".alarm-hour");
    	var modalMinute = $(".alarm-minute");
    	
    	$(modalDelete).remove();
    	$(modalTitle).val(null);
    	$(modalDate).removeClass('alarm-day-selected');
    	$(modalCheckBox).attr("checked",false);
    	$(modalHour).val(null);
    	$(modalMinute).val(null);

    	$(".modal").modal("hide");
    }
</script>
@endsection
