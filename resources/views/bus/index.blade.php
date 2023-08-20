@extends('layouts.app')

@section('style')
<style type="text/css">
	.info-area{
		margin: 10px 0px 10px 0px;
		height: 50px;
		border: 1px solid black;
	}
	.select-area{
		height: 80vh;
	}
	.bus-select-area{
		float: left;
		width: calc(50% - 5px);
		height: 80vh;
		border: 1px solid black;
	}
	.bus-stop-select-area{
		float: right;
		width: calc(50% - 5px);
		height: 80vh;
		border: 1px solid black;
	}
	.search-area{
		height: 50px;
	}
	.search{
		display: inline-block;
		width: calc(100% - 100px);
		height: 32px;
		margin: 6px 0px 0px 5px;
	}
	.bus-list-area{
		height: calc(80vh - 50px);
		width: 100%;
		margin: 0px 0px 0px 5px;
	}
	.route-list-area{
		height: 80vh;
		width: 100%;
		margin: 0px 0px 0px 5px;
		overflow: scroll !important;
	}
	.bus-list-item{
		cursor: pointer;
	}
	.route-list-item{
		cursor: pointer;
	}
</style>
@endsection

@section('content')
<div class="info-area"></div>
<div class="select-area">
	<div class="bus-select-area">
		<div class="search-area">
			<input class="search" placeholder="Bus Number">
			<button class="btn btn-primary btn-sm" onclick="searchBus()">search</button>
		</div>
		<div class="bus-list-area"></div>
	</div>
	<div class="bus-stop-select-area">
		<div class="route-list-area"></div>
	</div>
</div>
@endsection

@section('script')
<script type="text/javascript">
	$(function(){
		$(".search").keypress(function(e){
			if(e.keyCode && e.keyCode == 13){
				searchBus();
			}
		})
	});

	function searchBus(url = "/searchBus"){
		$('.bus-list-area').empty();

		var busId = $(".search").val();
		ajax_data = {
			busId: busId,
		};

		$.ajax({    
			type : 'get',
			url : url,
			data : ajax_data,    
			success : function(result) {				
				result.forEach (function (bus, idx){
					$('.bus-list-area').append("<div class='bus-list-item' onclick='selectBus("+busId+","+bus[1]+")'>"+bus[1]+"</div>");
				});
			},    
			error : function(request, status, error) {
				console.log(error)    
			}
		});
	}
	
	function selectBus(busRouteId,selectedBusRouteId){
		$('.route-list-area').empty();

		var url = "/selectBus";
		ajax_data = {
			busId: busRouteId,
			selectedBusRouteId: selectedBusRouteId,
		};

		$.ajax({    
			type : 'get',
			url : url,
			data : ajax_data,    
			success : function(result) {
				var routeList = result['routeList'];
				// var selectedBusRouteId = result['selectedBusRouteId'];

				routeList.forEach (function (route, idx){
					$('.route-list-area').append("<div class='route-list-item' onclick='selectStation("+route[0]+","+route[2]+","+route[3]+","+selectedBusRouteId+")'>"+route[1]+"</div>");
				});
			},    
			error : function(request, status, error) {
				console.log(error)    
			}
		});
	}


	function selectStation(stationId,ord,busRouteId,selectedBusRouteId){
		console.log("selectStation");

		var timer = 15;

		var url = "/selectStation";
		ajax_data = {
			busId: $(".search").val(),
			stationId: stationId,
			ord: ord,
			busRouteId: busRouteId,
			selectedBusRouteId: selectedBusRouteId,
		};

		var ajaxCall = function(){
			console.log('ajaxCall');
			$.ajax({    
				type : 'get',
				url : url,
				data : ajax_data,    
				success : function(result) {
					console.log(result);
					var stationNm = result['stationNm'];
					var arrMsg = result['arrMsg'];
					// var leftStations = result['leftStations'];
					$('.info-area').html('선택한 버스 : '+selectedBusRouteId+" / 선택한 정류장 : "+stationNm+"<br>도착 정보 : "+arrMsg);
					if(result['arrMsg'].indexOf('도착') > -1){
						//ringing, web push
						var audio = new Audio("{{ asset('alarm_1.mp3') }}");
						audio.volume = 0.1;
						audio.play();
					}
				},    
				error : function(request, status, error) {
					console.log(error)    
				}
			});
		};
		
		ajaxCall();
		setInterval(ajaxCall,timer * 1000);
	}

</script>
@endsection
