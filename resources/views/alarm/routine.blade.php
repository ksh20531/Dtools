<style type="text/css">
	.modal-header{
		padding: 0.7rem 0.5rem 0.7rem 1rem;
	}
	.modal-title-input{
		width: 80%;
	}
	.btn-deleteRoutine{
		display: none;
	}
</style>
@foreach($routines as $routine)
<div class="routine">
	<div class="routine-header">
		<input class="routine-title" value="{{ $routine[0]->title }}" disabled="true">
	</div>
	<div class="routine-body">
		<div class="alarm-area">
		@if($routine[0]->alarm_id)
			@foreach($routine as $alarm)
			<div class="alarm" onclick="openModal(this,{{ $routine[0]->id }},{{ $alarm->alarm_id }})">
				<div class="alarm-title">{{ $alarm->alarm_title }}</div>
				<div class="alarm-day">
					@if($alarm->day == 0)
					일
					@elseif($alarm->day == 1)
					월
					@elseif($alarm->day == 2)
					화
					@elseif($alarm->day == 3)
					수
					@elseif($alarm->day == 4)
					목
					@elseif($alarm->day == 5)
					금
					@elseif($alarm->day == 6)
					토
					@else

					@endif
				</div>
				<div class="alarm-time">{{ $alarm->hour }}:{{ $alarm->minute }}</div>
			</div>
			@endforeach
		@endif
		</div>
		<div class="alarm-btn-area">
			<button class="btn btn-primary btn-sm" onclick="openModal(this,{{ $routine[0]->id }},0)">추가</button>
			<button class="btn btn-primary btn-sm" onclick="modifyRoutine(this,{{ $routine[0]->id }})">수정</button>
			<button class="btn btn-danger btn-sm" onclick="deleteRoutine({{ $routine[0]->id }})">삭제</button>
		</div>
	</div>
</div>
@endforeach

<script type="text/javascript">
	$(function(){
    	ringing();
    });

	function openModal(elem,routine_id,alarm_id){
		if(alarm_id != 0){
			console.log(alarm_id);
			$(".modal").find('.modal-header').append('<button class="btn btn-danger btn-sm alarm-delete" onclick="deleteAlarm('+alarm_id+')">삭제</button>');
		}
		else
			console.log('0')
		$(".modal").find('.modal-title').wrap('<input class="modal-title-input" id="modal-title-input" placeholder="Title">');
		$(".modal").find('.alarm-day').removeClass('alarm-day-selected');

		if(alarm_id == undefined)
			alarm_id = 0;

		var url = 'alarm/'+alarm_id;

		$.ajax({    
			type : 'get',
			url : url,
			data : {},    
			success : function(result) {
				if(result != ''){
					$(".modal").find(".alarm-day").each(function(index,day){
						if($(day).data('value') == result.day){
							$(day).addClass('alarm-day-selected');
						}
					});
					$(".modal").find('.modal-title-input').attr('value',result.title);
					$(".modal").find('.alarm-hour').attr('value',result.hour);
					$(".modal").find('.alarm-minute').attr('value',result.minute);
					$(".modal").find('#btn-submit').attr('routine_id',routine_id);
					$(".modal").find('#btn-submit').attr('alarm_id',alarm_id);
				}else{
					$(".modal").find('#btn-submit').attr('routine_id',routine_id);
					$(".modal").find('#btn-submit').attr('alarm_id',alarm_id);
				}
			},    
			error : function(request, status, error) {
				console.log(error)    
			}
		});

    	$(".modal").modal("show");
	}

	function selectDay(elem){
		var value = $(elem).data('value');
		var checkBox = $(elem).prev();

		$(".modal").find('.alarm-day').removeClass('alarm-day-selected');

		if(checkBox.is(":checked") == true){
			checkBox.attr("checked", false);
		}else if(checkBox.is(":checked") == false){
			$(".modal").find('.day-checkbox').attr("checked", false);
			$(elem).addClass('alarm-day-selected');
			checkBox.attr("checked", true);
		}
    }

	function submitAlarm(){
		var routine_id = $(".modal").find('#btn-submit').attr('routine_id');
		var alarm_id = $(".modal").find('#btn-submit').attr('alarm_id');
		var title = $(".modal").find('.modal-title-input').val();
		var day = $(".modal").find('.alarm-day-selected').data('value');
		var hour = parseInt($(".modal").find('.alarm-hour').val());
		var minute = parseInt($(".modal").find('.alarm-minute').val());

		if(title == '' || day == '' || isNaN(hour) == true || isNaN(minute) == true){
			if(isNaN(hour) == true || isNaN(minute) == true){
				alert("숫자를 입력해주세요");
			}else{
				alert("필수 값을 입력해주세요.");
			}
		}
		if(alarm_id != 0){
			var url = 'alarm/'+alarm_id;
			var ajax_type = 'put';
		}
		else if(alarm_id == 0){
  			var url = 'alarm/create';
			var ajax_type = 'get';
		}

		var ajax_data = {
			'type': 'alarm',
			'routine_id': routine_id,
			'title': title,
			'day': day,
			'hour': hour,
			'minute': minute,
		};

		$.ajax({    
			type : ajax_type,
			url : url,
			data : ajax_data,    
			success : function(result) {				
				if(result == 'success'){
					closeModal();
					getRoutine();
				}
				else{
					alert('오류 발생');
				}
			},    
			error : function(request, status, error) {
				console.log(error)    
			}
		});
	}

	function deleteRoutine(routine_id){
    	var url = 'alarm/'+routine_id;
		var ajax_data = {
			'routine_id': routine_id,
		};

		$.ajax({    
			type : 'delete',
			url : url,
			data : ajax_data,    
			success : function(result) {				
				if(result == 'success')
					getRoutine();
				else
					alert('오류 발생');
			},    
			error : function(request, status, error) {
				console.log(error)    
			}
		});
    }

    function ringing(){
    	var alarms = {!! $alarms !!};

        var interval = setInterval(function(){
	    	var date = new Date();
        	
        	$.each(alarms,function(idx,alarm){
	        	if(alarm.hour == date.getHours() && alarm.minute == date.getMinutes()){
	        		alarms.shift();
	        		var audio = new Audio("{{ asset('alarm_1.mp3') }}");
					audio.volume = 0.1;
					audio.play();
	        	}
        	});
        },1000);
    }

    function modifyRoutine(elem,routine_id){
    	var title = $(elem).parents('.routine').find('.routine-title');

    	if($(elem).text() == '수정'){
	    	title.attr('disabled',false);
	    	$(elem).html('완료');
    	}else if($(elem).text() == '완료'){
			var url = 'alarm/'+routine_id;
			var title = $(title).val();

			var ajax_data = {
				'type': 'routine',
				'routine_id': routine_id,
				'title': title,
			};

			$.ajax({    
				type : 'put',
				url : url,
				data : ajax_data,    
				success : function(result) {				
					if(result == 'success')
						getRoutine();
					else
						alert('오류 발생');
				},    
				error : function(request, status, error) {
					console.log(error)    
				}
			});
    	}
    }

    function deleteAlarm(alarm_id){
    	var url = 'alarm/'+alarm_id;
    	var ajax_data = {
			'type': 'alarm',
			'alarm_id': alarm_id,
		};

    	$.ajax({    
			type : 'delete',
			url : url,
			data : ajax_data,    
			success : function(result) {				
				if(result == 'success'){
					closeModal();
					getRoutine();
				}
				else{
					alert('오류 발생');
				}
			},    
			error : function(request, status, error) {
				console.log(error)    
			}
		});
    }
</script>